<?php

namespace BowensH\LaravelGraphQLExtend\Tests\Unit;

use BowensH\LaravelGraphQLExtend\Column;
use BowensH\LaravelGraphQLExtend\Tests\Objects\ExampleColumn;
use BowensH\LaravelGraphQLExtend\Tests\TestCase;
use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ListOfType;
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
            ],
            'test1' => [
                'type'        => 'test1',
                'description' => 'test1'
            ]
        ])->result();

        $this->assertArrayHasKey('test', $column);
        $this->assertArrayHasKey('test1', $column);
    }

    /**
     * test NonNull
     *
     * @throws \Exception
     */
    public function testNonNull()
    {
        $column = $this->getColumnInstance()->nonNull(['id', 'pics.url', 'pics1.url'])->result();
        $this->assertTrue($column['id']['type'] instanceof NonNull);
        $this->assertTrue($column['pics']['type'] instanceof ObjectType);
        $this->assertTrue($column['pics']['type']->getField('url')->getType() instanceof NonNull);
        $this->assertTrue($column['pics1']['type'] instanceof ListOfType);
        $this->assertTrue($column['pics1']['type']->getWrappedType() instanceof ObjectType);
        $this->assertTrue($column['pics1']['type']->getWrappedType()->getField('url')->getType() instanceof NonNull);

        $column = $this->getColumnInstance(true)->nonNull(['id', 'pics.url', 'pics1.url'])->result();
        $this->assertTrue($column['pics']['type'] instanceof InputObjectType);
        $this->assertTrue($column['pics1']['type']->getWrappedType() instanceof InputObjectType);
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
        $column = $this->getColumnInstance()->only([ 'id', 'pics.url', 'pics1.url' ])->result();
        $this->assertArrayHasKey('id', $column);
        $this->assertArrayHasKey('pics', $column);
        $this->assertArrayHasKey('pics1', $column);
        $this->assertTrue($column['pics']['type'] instanceof ObjectType);
        $this->assertTrue($column['pics']['type']->getField('url') instanceof FieldDefinition);
        $this->assertTrue(count($column['pics']['type']->getFields()) === 1);
        $this->assertTrue($column['pics1']['type'] instanceof ListOfType);
        $this->assertTrue($column['pics1']['type']->getWrappedType() instanceof ObjectType);
        $this->assertTrue($column['pics1']['type']->getWrappedType()->getField('url') instanceof FieldDefinition);
        $this->assertTrue(count($column['pics1']['type']->getWrappedType()->getFields()) === 1);

        $column = $this->getColumnInstance(true)->only(['id', 'pics.url', 'pics1.url'])->result();
        $this->assertTrue($column['pics']['type'] instanceof InputObjectType);
        $this->assertTrue($column['pics1']['type']->getWrappedType() instanceof InputObjectType);
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
        $column = $this->getColumnInstance()->except([ 'id', 'pics.url', 'pics1.url' ])->result();
        $this->assertArrayNotHasKey('id', $column);
        $this->assertArrayHasKey('pics', $column);
        $this->assertArrayHasKey('pics1', $column);
        $this->assertTrue($column['pics']['type'] instanceof ObjectType);
        $this->assertTrue($column['pics']['type']->getField('name') instanceof FieldDefinition);
        $this->assertTrue(count($column['pics']['type']->getFields()) === 1);
        $this->assertTrue($column['pics1']['type'] instanceof ListOfType);
        $this->assertTrue($column['pics1']['type']->getWrappedType() instanceof ObjectType);
        $this->assertTrue($column['pics1']['type']->getWrappedType()->getField('name') instanceof FieldDefinition);
        $this->assertTrue(count($column['pics1']['type']->getWrappedType()->getFields()) === 1);

        $column = $this->getColumnInstance(true)->except(['id', 'pics.url', 'pics1.url'])->result();
        $this->assertTrue($column['pics']['type'] instanceof InputObjectType);
        $this->assertTrue($column['pics1']['type']->getWrappedType() instanceof InputObjectType);
    }

    /**
     * 获取 ExampleColumn 实例
     *
     * @param bool $inputObject
     *
     * @return Column
     * @throws \Exception
     */
    private function getColumnInstance($inputObject = false)
    {
        return Column::make(ExampleColumn::class, $inputObject);
    }
}

