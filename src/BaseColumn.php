<?php
/**
 * Created by PhpStorm.
 * User: hanwenbo
 * Date: 2018/10/1
 * Time: 09:10
 */

namespace BowensH\LaravelGraphQLExtend;

class BaseColumn
{
    public function getName()
    {
        return $this->attributes['name'] ?? '';
    }
}
