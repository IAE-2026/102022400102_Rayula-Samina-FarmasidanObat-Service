<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Services\EventPublisherService;
use App\Services\SoapAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    protected SoapAuditService $soapAuditService;
    protected EventPublisherService $eventPublisher;

    public function __construct(
        SoapAuditService $soapAuditService,
        EventPublisherService $eventPublisher
    ) {
        $this->soapAuditService = $soapAuditService;
        $this->eventPublisher = $eventPublisher;
    }

    public function index()
    {
        $daftarResep = Prescription::with('items.obat')->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data berhasil diambil',
            'data'    => $daftarResep,
            'meta'    => [
                'nama_service' => 'pharmacy-service',
                'versi_api'    => 'v1'
            ]
        ]);
    }

    public function show($id)
    {
        $resep = Prescription::with('items.obat')->find($id);

        if (!$resep) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Resep tidak ditemukan',
                'errors'  => null
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data berhasil diambil',
            'data'    => $resep,
            'meta'    => [
                'nama_service' => 'pharmacy-service',
                'versi_api'    => 'v1'
            ]
        ]);
    }

    public function store(Request $request)
    {
        $tervalidasi = $request->validate([
            'id_pasien'        => 'required|integer',
            'id_kunjungan'     => 'required|integer',
            'nama_dokter'      => 'required|string',
            'items'            => 'required|array',
            'items.*.id_obat'  => 'required|exists:medicines,id',
            'items.*.jumlah'   => 'required|integer|min:1',
            'items.*.dosis'    => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $resep = Prescription::create([
                'id_pasien'    => $tervalidasi['id_pasien'],
                'id_kunjungan' => $tervalidasi['id_kunjungan'],
                'nama_dokter'  => $tervalidasi['nama_dokter'],
                'status'       => 'pending',
            ]);

            foreach ($tervalidasi['items'] as $item) {
                $obat = Medicine::lockForUpdate()->find($item['id_obat']);

                if (!$obat) {
                    throw new \Exception('Data obat tidak ditemukan.');
                }

                if ($obat->stock < $item['jumlah']) {
                    DB::rollBack();

                    return response()->json([
                        'status'  => 'error',
                        'message' => "Stok obat '{$obat->nama}' tidak mencukupi. Stok tersedia: {$obat->stock}.",
                        'errors'  => null
                    ], 400);
                }

                // Kurangi stok obat
                $obat->stock -= $item['jumlah'];
                $obat->save();

                // Simpan detail resep
                $resep->items()->create([
                    'id_resep' => $resep->id,
                    'id_obat'  => $item['id_obat'],
                    'jumlah'   => $item['jumlah'],
                    'dosis'    => $item['dosis'],
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memproses pembuatan resep obat.',
                'errors'  => $e->getMessage()
            ], 500);
        }

        // SOAP Audit
        $resepDataForAudit = [
            'id'            => $resep->id,
            'id_pasien'     => $resep->id_pasien,
            'id_kunjungan'  => $resep->id_kunjungan,
            'nama_dokter'   => $resep->nama_dokter,
            'items'         => $resep->items
                ->load('obat')
                ->map(fn ($item) => [
                    'id_obat'   => $item->id_obat,
                    'nama_obat' => $item->obat->nama ?? 'Obat',
                    'jumlah'    => $item->jumlah,
                    'dosis'     => $item->dosis,
                ])
                ->toArray()
        ];

        $receiptNumber = $this->soapAuditService->sendAudit(
            'PrescriptionCreated',
            $resepDataForAudit
        );

        if ($receiptNumber) {
            $resep->update([
                'receipt_number' => $receiptNumber
            ]);
        }

        // Publish Event RabbitMQ
        $eventPayload = array_merge($resepDataForAudit, [
            'receipt_number' => $resep->receipt_number,
            'timestamp'      => now()->toIso8601String()
        ]);

        $this->eventPublisher->publishEvent(
            'prescription.created',
            $eventPayload
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Resep berhasil dibuat dan di-audit',
            'data'    => $resep->load('items.obat'),
            'meta'    => [
                'nama_service' => 'pharmacy-service',
                'versi_api'    => 'v1'
            ]
        ], 201);
    }
}