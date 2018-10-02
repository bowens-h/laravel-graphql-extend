<?php

namespace BowensH\LaravelGraphQLExtend\Tests\Unit;

use BowensH\LaravelGraphQLExtend\Column;
use BowensH\LaravelGraphQLExtend\Tests\Objects\ExampleColumn;
use BowensH\LaravelGraphQLExtend\Tests\TestCase;
use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ColumnTest extends TestCase
{

    /**
     * test make
     *
     * @test
     * @throws \Exception
     */
    public function testMake()
    {
        $column = $this->getColumnInstance();

        $this->assertTrue($column instanceof Column);
    }

    /**
     * test Append
     *
     * @throws \Exception
     */
    public function testAppend()
    {
        $column = $this->getColumnInstance()->append([
            'test' => [
                'type'        => 'test',
                'description' => 'test'
            ]
        ])->result();

        $this->assertArrayHasKey('test', $column);
    }

    /**
     * test NonNull
     *
     * @throws \Exception
     */
    public function testNonNull()
    {
        $column = $this->getColumnInstance()->nonNull('id')->result();
        $this->assertTrue($column['id']['type'] instanceof NonNull);
    }

    /**
     * test only
     *
     * @throws \Exception
     */
    public function testOnly()
    {
        //测试以及field
        $column = $this->getColumnInstance()->only('id')->result();
        $this->assertArrayHasKey('id', $column);

        //测试二级field
        $column = $this->getColumnInstance()->only([ 'id', 'pics.url' ])->result();
        $this->assertArrayHasKey('id', $column);
        $this->assertArrayHasKey('pics', $column);
        $this->assertTrue($column['pics']['type'] instanceof ObjectType);
        $this->assertTrue($column['pics']['type']->getField('url') instanceof FieldDefinition);
        $this->assertTrue(count($column['pics']['type']->getFields()) === 1);
    }

    /**
     * test only
     *
     * @throws \Exception
     */
    public function testExcept()
    {
        //测试以及field
        $column = $this->getColumnInstance()->except(['id'])->result();
        $this->assertArrayNotHasKey('id', $column);

        //测试二级field
        $column = $this->getColumnInstance()->except([ 'id', 'pics.url' ])->result();
        $this->assertArrayNotHasKey('id', $column);
        $this->assertArrayHasKey('pics', $column);
        $this->assertTrue($column['pics']['type'] instanceof ObjectType);
        $this->assertTrue($column['pics']['type']->getField('name') instanceof FieldDefinition);
        $this->assertTrue(count($column['pics']['type']->getFields()) === 1);
    }

    /**
     * 获取 ExampleColumn 实例
     *
     * @return Column
     * @throws \Exception
     */
    private function getColumnInstance()
    {
        return Column::make(ExampleColumn::class);
    }
}

