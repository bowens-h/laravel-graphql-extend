<?php

namespace BowensH\LaravelGraphQLExtend\Tests\Objects;

use GraphQL\Type\Definition\Type;

class ExampleColumn
{

    protected $attributes = [
        'name' => 'ExampleColumn',
        'description' => 'A example column'
    ];

    public function columns()
    {
        return [
            'id'             => [
                'type'        => Type::id(),
                'description' => '',
            ],
            'title'          => [
                'type'        => Type::string(),
                'description' => '标题',
            ],
            'pics'           => [
                'type'        => [
                    'url'   => [
                        'type'        => Type::string(),
                        'description' => '图片路径',
                    ],
                ],
                'description' => '图片',
            ],
        ];
    }
}
