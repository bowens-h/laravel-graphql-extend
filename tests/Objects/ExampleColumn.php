<?php

namespace BowensH\LaravelGraphQLExtend\Tests\Objects;

use BowensH\LaravelGraphQLExtend\BaseColumn;
use GraphQL\Type\Definition\Type;

class ExampleColumn extends BaseColumn
{

    public $attributes = [
        'name'        => 'ExampleColumn',
        'description' => 'A example column'
    ];

    public function columns()
    {
        return [
            'id'    => [
                'type'        => Type::id(),
                'description' => '',
            ],
            'title' => [
                'type'        => Type::string(),
                'description' => '标题',
            ],
            'pics'  => [
                'type'        => [
                    'url'  => [
                        'type'        => Type::string(),
                        'description' => '图片路径',
                    ],
                    'name' => [
                        'type'        => Type::string(),
                        'description' => '图片名',
                    ],
                ],
                'description' => '图片',
            ],
            'pics1'  => [
                'type'        => [
                    [
                        'url'  => [
                            'type'        => Type::string(),
                            'description' => '图片路径',
                        ],
                        'name' => [
                            'type'        => Type::string(),
                            'description' => '图片名',
                        ],
                    ]
                ],
                'description' => '图片',
            ],

        ];
    }
}
