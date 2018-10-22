<?php

namespace BowensH\LaravelGraphQLExtend\Console\Commands;

use BowensH\LaravelGraphQLExtend\Console\Commands\Traits\GraphQLFieldsTrait;
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

}
