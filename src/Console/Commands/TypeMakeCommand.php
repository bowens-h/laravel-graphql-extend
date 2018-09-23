<?php

namespace BowensH\LaravelGraphQLExtend\Console\Commands;

use BowensH\LaravelGraphQLExtend\Console\Commands\Trail\GraphQLFieldsTrait;
use Doctrine\DBAL\Schema\Column;
use Folklore\GraphQL\Console\TypeMakeCommand as BaseTypeMakeCommand;

class TypeMakeCommand extends BaseTypeMakeCommand
{

    use GraphQLFieldsTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:graphql:type {name} {--table=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new GraphQL type class with Database';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/type.stub';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $table = $this->option('table');

        return $this->replaceFields($stub, $table);

        //return $this->replaceType($stub, $name);
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string $stub
     * @param         $table
     *
     * @return $this
     */
    protected function replaceFields($stub, $table)
    {
        $graphql_fields = $table ? $this->getGraphQLFields($table) : [];

        $stub = str_replace('DummyFields', $this->graphQLFieldsToFile($graphql_fields), $stub);

        return $stub;
    }

    /**
     * 根据多个表字段获取多个graphql字段
     *
     * @param $table
     *
     * @return array
     */
    private function getGraphQLFields($table)
    {
        $graphql_fields = [];

        $tableColumns = $this->getTableColumns($table, true);

        foreach ($tableColumns as $tableColumn) {
            $graphql_fields = array_merge($this->getGraphQLField($table, $tableColumn), $graphql_fields);
        }

        return $graphql_fields;
    }

    /**
     * 根据表字段获取graphql字段
     *
     * @param string $table
     * @param Column $tableColumn
     *
     * @return array
     */
    protected function getGraphQLField($table, Column $tableColumn)
    {
        return [
            $this->getColumnName($tableColumn) => [
                'type'        => $this->getGraphQlTypeByColumn($table, $tableColumn),
                'description' => $this->getCommentByColumn($tableColumn),
            ],
        ];
    }

    /**
     * 将graphql数组转换成文本，用于文件替换
     *
     * @param $graphql_fields
     *
     * @return string
     */
    private function graphQLFieldsToFile($graphql_fields)
    {
        $file = "[\n";

        foreach ($graphql_fields as $key => $graphql_field) {
            $type        = $graphql_field['type'];
            $description = $graphql_field['description'];

            $file .= <<<EOF
            '$key' => [
                'type' => $type,
                'description' => '$description',
            ],

EOF;
        }

        return $file."\t\t]";
    }

}
