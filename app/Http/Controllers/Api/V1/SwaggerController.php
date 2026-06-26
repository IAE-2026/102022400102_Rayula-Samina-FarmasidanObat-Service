<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class SwaggerController extends Controller
{
    public function index(): Response
    {
        return response()->view('swagger-ui', [
            'specUrl' => url('/api/documentation/spec'),
        ]);
    }

    public function spec(): JsonResponse
    {
        $path = public_path('api-docs.json');
        if (file_exists($path)) {
            $spec = json_decode(file_get_contents($path), true);
            return response()->json($spec);
        }

        return response()->json(['error' => 'Specification file not found'], 404);
    }
}