<?php

namespace App\GraphQL\Mutations;

use App\Models\Medicine;
use App\Models\Prescription;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Illuminate\Support\Facades\DB;

class CreatePrescriptionMutation extends Mutation
{
    protected $attributes = ['name' => 'createPrescription'];

    public function type(): Type
    {
        return GraphQL::type('Prescription');
    }

    public function args(): array
    {
        return [
            'id_pasien'    => ['type' => Type::nonNull(Type::int())],
            'id_kunjungan' => ['type' => Type::nonNull(Type::int())],
            'nama_dokter'  => ['type' => Type::nonNull(Type::string())],
            'items'        => ['type' => Type::nonNull(Type::listOf(GraphQL::type('PrescriptionItemInput')))],
        ];
    }

    public function resolve($root, array $args)
    {
        DB::beginTransaction();
        try {
            $resep = Prescription::create([
                'id_pasien'    => $args['id_pasien'],
                'id_kunjungan' => $args['id_kunjungan'],
                'nama_dokter'  => $args['nama_dokter'],
                'status'       => 'pending',
            ]);

            foreach ($args['items'] as $item) {
                $obat = Medicine::lockForUpdate()->find($item['id_obat']);
                if (!$obat) throw new \Exception('Obat tidak ditemukan');
                if ($obat->stock < $item['jumlah']) throw new \Exception("Stok {$obat->nama} tidak mencukupi");
                $obat->stock -= $item['jumlah'];
                $obat->save();
                $resep->items()->create([
                    'id_resep' => $resep->id,
                    'id_obat'  => $item['id_obat'],
                    'jumlah'   => $item['jumlah'],
                    'dosis'    => $item['dosis'],
                ]);
            }

            DB::commit();
            return $resep;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
