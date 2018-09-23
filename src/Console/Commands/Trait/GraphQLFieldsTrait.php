<?php
/**
 * Created by PhpStorm.
 * User: hanwenbo
 * Date: 2018/9/20
 * Time: 14:17
 */

namespace BowensH\LaravelGraphQLExtend\Console\Commands\Trail;

trait GraphQLFieldsTrait
{
    use TableInfoTrait;
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

}
