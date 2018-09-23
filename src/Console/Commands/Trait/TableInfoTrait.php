<?php
/**
 * Created by PhpStorm.
 * User: hanwenbo
 * Date: 2018/9/20
 * Time: 14:17
 */

namespace BowensH\LaravelGraphQLExtend\Console\Commands\Trail;

use Doctrine\DBAL\Schema\Column;

trait TableInfoTrait
{

    /**
     * 表字段与graphql字段的映射，如果没有定义统一指向 Type::string()
     */
    private $columnTypeToGraphQLType = [
        'Type::int()'     => [ 'Integer', 'SmallInt', 'BigInt' ],
        'Type::float()'   => [ 'Float', 'Decimal' ],
        'Type::boolean()' => [ 'Boolean' ],
        'Type::string()'  => [],
        'custom'          => [ 'Json' ]//自定义的特殊类型
    ];

    /**
     * 获取表的所有字段
     *
     * @param      $table
     *
     * @param bool $reverse
     *
     * @return \Doctrine\DBAL\Schema\Column[]
     */
    private function getTableColumns($table, $reverse = false)
    {
        $columns = \DB::getDoctrineSchemaManager()->listTableDetails(env('DB_PREFIX').$table)->getColumns();

        if ($reverse) {
            return array_reverse($columns);
        }

        return $columns;
    }

    /**
     * 根据字段获取对应的graphql的type
     *
     * @param string $table
     * @param Column $tableColumn
     *
     * @return string
     */
    private function getGraphQlTypeByColumn(string $table, Column $tableColumn)
    {
        $type        = '';
        $column_type = $tableColumn->getType();
        $column_name = $tableColumn->getName();

        foreach ($this->columnTypeToGraphQLType as $graphql_type => $column_types) {
            if ($graphql_type == 'custom' && in_array($column_type, $column_types)) {
                if ($column_type == 'Json') {
                    $type = '\GraphQL::type(\''.title_case($table).title_case($column_name).'Type\')';
                }
            } else {
                if ($column_name == 'id') {
                    $type = 'Type::ID()';
                } elseif (in_array($column_type, $column_types)) {
                    $type = $graphql_type;
                } else {
                    $type = 'Type::string()';
                }
            }
        }

        return $type;
    }

    /**
     * 获取字段的备注
     *
     * @param Column $tableColumn
     *
     * @return null|string
     */
    private function getCommentByColumn(Column $tableColumn)
    {
        return $tableColumn->getComment();
    }

    /**
     * 获取字段名
     *
     * @param Column $tableColumn
     *
     * @return string
     */
    private function getColumnName(Column $tableColumn)
    {
        return $tableColumn->getName();
    }

    /**
     * 获取字段类型
     *
     * @param Column $tableColumn
     *
     * @return string
     */
    private function getColumnType(Column $tableColumn)
    {
        return $tableColumn->getType();
    }

}
