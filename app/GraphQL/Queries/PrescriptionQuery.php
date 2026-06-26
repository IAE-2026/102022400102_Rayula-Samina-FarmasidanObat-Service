<?php

namespace App\GraphQL\Queries;

use App\Models\Prescription;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class PrescriptionQuery extends Query
{
    protected $attributes = ['name' => 'prescription'];

    public function type(): Type
    {
        return GraphQL::type('Prescription');
    }

    public function args(): array
    {
        return [
            'id' => ['type' => Type::nonNull(Type::int())],
        ];
    }

    public function resolve($root, array $args)
    {
        return Prescription::find($args['id']);
    }
}
