<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function index()
    {
        $daftarResep = Prescription::with('items.obat')->get();
        return response()->json([
            'status'  => 'success',
            'message' => 'Data berhasil diambil',
            'data'    => $daftarResep,
            'meta'    => ['nama_service' => 'pharmacy-service', 'versi_api' => 'v1']
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
            'meta'    => ['nama_service' => 'pharmacy-service', 'versi_api' => 'v1']
        ]);
    }

    public function store(Request $request)
    {
        $tervalidasi = $request->validate([
            'id_pasien'          => 'required|integer',
            'id_kunjungan'       => 'required|integer',
            'nama_dokter'        => 'required|string',
            'items'              => 'required|array',
            'items.*.id_obat'    => 'required|exists:medicines,id',
            'items.*.jumlah'     => 'required|integer',
            'items.*.dosis'      => 'required|string',
        ]);

        $resep = Prescription::create([
            'id_pasien'    => $tervalidasi['id_pasien'],
            'id_kunjungan' => $tervalidasi['id_kunjungan'],
            'nama_dokter'  => $tervalidasi['nama_dokter'],
            'status'       => 'pending',
        ]);

        foreach ($tervalidasi['items'] as $item) {
            $resep->items()->create($item);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Resep berhasil dibuat',
            'data'    => $resep->load('items.obat'),
            'meta'    => ['nama_service' => 'pharmacy-service', 'versi_api' => 'v1']
        ], 201);
    }
}
