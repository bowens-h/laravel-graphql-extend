<?php

namespace BowensH\LaravelGraphQLExtend\Console\Commands;

use BowensH\LaravelGraphQLExtend\Console\Commands\Traits\GraphQLFieldsTrait;
use Doctrine\DBAL\Schema\Column;
use Illuminate\Console\GeneratorCommand;

class ColumnMakeCommand extends GeneratorCommand
{

    use GraphQLFieldsTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:graphql:column {name} {--table=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new GraphQL column class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Column';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/column.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\GraphQL\Columns';
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

        $stub = $this->replaceColumns($stub, $table);

        return $this->replaceColumn($stub, $name);
    }

    /**
     * Replace the name for the given stub.
     *
     * @param  string $stub
     * @param  string $name
     *
     * @return $this
     */
    protected function replaceColumn($stub, $name)
    {
        preg_match('/([^\\\]+)$/', $name, $matches);
        $stub = str_replace('DummyColumn', $matches[1], $stub);

        return $stub;
    }

    /**
     * Replace the columns for the given stub.
     *
     * @param  string $stub
     * @param         $table
     *
     * @return $this
     */
    protected function replaceColumns($stub, $table)
    {
        $graphql_fields = $table ? $this->getGraphQLFields($table) : [];

        $stub = str_replace('DummyColumns', $this->graphQLFieldsToFile($graphql_fields), $stub);

        return $stub;
    }

}
