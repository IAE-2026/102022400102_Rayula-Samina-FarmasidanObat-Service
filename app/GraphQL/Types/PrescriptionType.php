<?php

namespace App\GraphQL\Types;

use App\Models\Prescription;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PrescriptionType extends GraphQLType
{
    protected $attributes = [
        'name'  => 'Prescription',
        'model' => Prescription::class,
    ];

    public function fields(): array
    {
        return [
            'id'          => ['type' => Type::int()],
            'id_pasien'   => ['type' => Type::int()],
            'id_kunjungan'=> ['type' => Type::int()],
            'nama_dokter' => ['type' => Type::string()],
            'status'      => ['type' => Type::string()],
        ];
    }
}
