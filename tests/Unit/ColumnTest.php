<?php

namespace BowensH\LaravelGraphQLExtend\Tests\Unit;

use BowensH\LaravelGraphQLExtend\Column;
use BowensH\LaravelGraphQLExtend\Tests\Objects\ExampleColumn;
use Orchestra\Testbench\TestCase;

class ColumnTest extends TestCase
{

    /**
     * Test getFields
     *
     * @test
     * @throws \Exception
     */
    public function testMake()
    {
        $columns = Column::make(ExampleColumn::class);

        $this->assertArrayHasKey('test', $fields);

    }

}
