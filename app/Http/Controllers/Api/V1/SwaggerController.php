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
        $spec = [
            'openapi' => '3.0.0',

            'info' => [
                'title'       => 'Farmasi Obat API',
                'version'     => '1.0.0',
                'description' => 'Dokumentasi API Farmasi Obat. Gunakan header X-IAE-KEY: 102022400102',
            ],

            'servers' => [
                [
                    'url' => url('/'),
                ],
            ],

            'security' => [
                [
                    'ApiKeyAuth' => [],
                ],
            ],

            'components' => [
                'securitySchemes' => [
                    'ApiKeyAuth' => [
                        'type' => 'apiKey',
                        'in'   => 'header',
                        'name' => 'X-IAE-KEY',
                    ],
                ],
            ],

            'paths' => [

    // Root API
                '/api/v1' => [
                    'get' => [
                        'summary' => 'API Pharmacy Service',
                        'responses' => [
                            '200' => [
                                'description' => 'API berhasil diakses',
                            ],
                        ],
                    ],
                ],

                // Daftar obat
                '/api/v1/medicines' => [
                    'get' => [
                        'summary' => 'Daftar obat',

                        'security' => [
                            [
                                'ApiKeyAuth' => [],
                            ],
                        ],

                        'responses' => [
                            '200' => [
                                'description' => 'Berhasil mengambil data obat',
                            ],
                        ],
                    ],
                ],

            // Detail obat
            '/api/v1/medicines/{id}' => [
                'get' => [
                    'summary' => 'Detail obat',

                    'security' => [
                        [
                            'ApiKeyAuth' => [],
                        ],
                    ],

                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'schema' => [
                                'type' => 'integer',
                            ],
                        ],
                    ],

                    'responses' => [
                        '200' => [
                            'description' => 'Berhasil mengambil detail obat',
                        ],
                        '404' => [
                            'description' => 'Obat tidak ditemukan',
                        ],
                    ],
                ],
            ],

                '/api/v1/prescriptions' => [

                    'get' => [
                        'summary' => 'Daftar resep',

                        'responses' => [
                            '200' => [
                                'description' => 'Berhasil mengambil data resep',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],

                    'post' => [
                        'summary' => 'Buat resep',
                        'security' => [
                            [
                                'ApiKeyAuth' => [],
                            ],
                        ],

                        'requestBody' => [
                            'required' => true,

                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',

                                        'properties' => [
                                            'id_pasien' => [
                                                'type' => 'integer',
                                            ],

                                            'id_kunjungan' => [
                                                'type' => 'integer',
                                            ],

                                            'nama_dokter' => [
                                                'type' => 'string',
                                            ],

                                            'items' => [
                                                'type' => 'array',

                                                'items' => [
                                                    'type' => 'object',

                                                    'properties' => [
                                                        'id_obat' => [
                                                            'type' => 'integer',
                                                        ],

                                                        'jumlah' => [
                                                            'type' => 'integer',
                                                        ],
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],

                        'responses' => [
                            '201' => [
                                'description' => 'Resep berhasil dibuat',
                            ],

                            '400' => [
                                'description' => 'Validasi gagal',
                            ],
                        ],
                    ],
                ],

                '/api/v1/prescriptions/{id}' => [
                    'get' => [
                        'summary' => 'Detail resep',

                        'parameters' => [
                            [
                                'name'     => 'id',
                                'in'       => 'path',
                                'required' => true,
                                'schema'   => [
                                    'type' => 'integer',
                                ],
                            ],
                        ],

                        'responses' => [
                            '200' => [
                                'description' => 'Berhasil mengambil detail resep',
                            ],

                            '404' => [
                                'description' => 'Resep tidak ditemukan',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        
        return response()->json($spec);
    }
}