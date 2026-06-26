<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\InputType;

class PrescriptionItemInputType extends InputType
{
    protected $attributes = ['name' => 'PrescriptionItemInput'];

    public function fields(): array
    {
        return [
            'id_obat' => ['type' => Type::nonNull(Type::int())],
            'jumlah'  => ['type' => Type::nonNull(Type::int())],
            'dosis'   => ['type' => Type::nonNull(Type::string())],
        ];
    }
}
