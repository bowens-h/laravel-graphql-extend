<?php

namespace BowensH\LaravelGraphQLExtend;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class Column
{

    private $columnClass;

    private $inputObject;

    private $columns;

    private $name;

    public static $randoms = [];//为避免多维字段的type名称重复

    private $random;

    /**
     * Column constructor.
     *
     * @param string $column_class
     * @param bool   $inputObject
     *
     * @throws \Exception
     */
    public function __construct($column_class, $inputObject = false)
    {
        $this->columnClass = new $column_class();
        $this->inputObject = $inputObject;
        $this->columns     = $this->columnClass->columns();
        $this->name        = $this->columnClass->getName();
        $this->random      = $this->getRandom();
        self::$randoms[]   = $this->random;
    }

    /**
     * 返回实例
     *
     * @param      $column_class
     * @param bool $inputObject
     *
     * @return Column
     * @throws \Exception
     */
    public static function make($column_class, $inputObject = false)
    {
        return new static($column_class, $inputObject);
    }

    /**
     * 输出结果
     *
     * @return null
     */
    public function result()
    {
        $this->filterInput();

        return $this->columns;
    }

    /**
     * 为输出字段中新增一个自定义字段
     *
     * @param array $columns
     *
     * @return Column
     */
    public function append(array $columns)
    {
        $this->columns = array_merge($this->columns, $columns);

        return $this;
    }

    /**
     * 添加必填的字段
     *
     * @param string|array $column_names 要排除的字段，支持字符串、数组，多维请用「点」符号链接
     *
     * @return $this
     */
    public function nonNull($column_names)
    {
        if (is_string($column_names)) {
            $column_names = [ $column_names ];
        }

        foreach ($column_names as $column_name) {
            $this->nonNullOne($column_name, $this->columns);
        }

        return $this;
    }

    /**
     * 保留的字段
     *
     * @param string|array $column_names 要排除的字段，支持字符串、数组，多维请用「点」符号链接
     *
     * @return $this
     */
    public function only($column_names)
    {
        if (is_string($column_names)) {
            $column_names = [ $column_names ];
        }

        //将字段数组转换为只有键的数组
        $keys = $this->columnsToKeys($this->columns);

        //将需要保留的字段排除掉
        foreach ($column_names as $column_name) {
            array_forget($keys, $column_name);
        }

        //将剩余的键删掉
        $this->except(array_keys(array_dot($keys)));

        return $this;
    }

    /**
     * 排除不需要的字段
     *
     * @param string|array $column_names 要排除的字段，支持字符串、数组，多维请用「点」符号链接
     *
     * @return $this
     */
    public function except($column_names)
    {
        if (is_string($column_names)) {
            $column_names = [ $column_names ];
        }

        foreach ($column_names as $column_name) {
            $this->exceptOne($column_name, $this->columns);
        }

        return $this;
    }

    /**
     * 必填一个字段，进行多维数组递归
     *
     * @param $column_name
     * @param $columns
     */
    private function nonNullOne($column_name, &$columns)
    {
        //截取第一个点，及以后所有
        $column_names = explode('.', $column_name, 2);

        foreach ($columns as $key => &$column) {
            if ($column_names[0] == $key) {
                //如果排除的数组大于1，则说明要排除的是一个多维数组
                if (count($column_names) > 1) {
                    $this->nonNullOne($column_names[1], $column['type']);
                } else {
                    $column['type'] = Type::nonNull($column['type']);
                }
            }
        }
    }

    /**
     * 排除一个字段，进行多维数组递归
     *
     * @param $column_name
     * @param $columns
     */
    private function exceptOne($column_name, &$columns)
    {
        //截取第一个点，及以后所有
        $column_names = explode('.', $column_name, 2);

        foreach ($columns as $key => &$column) {
            if ($column_names[0] == $key) {
                //如果排除的数组大于1，则说明要排除的是一个多维数组
                if (count($column_names) > 1) {
                    $this->exceptOne($column_names[1], $column['type']);

                    //如果多维数据中已不存在元素，则将该字段删除
                    if (count($column['type']) == 0) {
                        unset($columns[$key]);
                    }
                } else {
                    unset($columns[$key]);
                }
            }
        }
    }

    /**
     * 将字段数组转换为只有键值的数组
     *
     * @param $columns
     *
     * @return array
     */
    private function columnsToKeys($columns)
    {
        $keys = [];

        foreach ($columns as $key => $column) {
            if (is_array($column['type'])) {
                $keys[$key] = $this->columnsToKeys($column['type']);
            } else {
                //到了最里层，将键赋值为1，占位，没什么意义
                $keys[$key] = 1;
            }
        }

        return $keys;
    }

    /**
     * 分拣input类型
     */
    private function filterInput()
    {
        foreach ($this->columns as $key => &$column) {
            if (is_array($column['type'])) {
                if ($this->inputObject) {
                    $column['type'] = new InputObjectType([
                        'name'   => $key.'_'.$this->random,
                        'fields' => $column['type']
                    ]);
                } else {
                    $column['type'] = new ObjectType([
                        'name'   => $key.'_'.$this->random,
                        'fields' => $column['type']
                    ]);
                }
            }
        }
    }

    /**
     * 二维字段的type名称不能重复，故而生成一个随机数
     *
     * @return int
     * @throws \Exception
     */
    private function getRandom()
    {
        $random = random_int(0, 999);

        if (in_array($random, self::$randoms)) {
            $random = $this->getRandom();
        }

        return $random;
    }
}
