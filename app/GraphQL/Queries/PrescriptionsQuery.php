<?php

namespace App\GraphQL\Queries;

use App\Models\Prescription;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class PrescriptionsQuery extends Query
{
    protected $attributes = ['name' => 'prescriptions'];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Prescription'));
    }

    public function resolve($root, array $args)
    {
        return Prescription::all();
    }
}
