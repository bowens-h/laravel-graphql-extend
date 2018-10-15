# Laravel GraphQL Extend

Folkloreatelier/laravel-graphql 包的功能扩展。扩展功能如下：
1. 新增 Column 类型文件，用于映射数据表字段，输出的数据可以被 Type 类型文件的 fields 方法使用，主要避免数据表字段的重复定义；
2. Type 类型文件的 fields 方法中字段可以根据表字段自动生成；

## 安装

#### 依赖:

* [Laravel 5.x](https://github.com/laravel/laravel)
* [laravel graphql 1.*](https://github.com/Folkloreatelier/laravel-graphql)
* [doctrine dbal >=2.6](https://github.com/doctrine/dbal)

**1-** [安装 Folkloreatelier/laravel-graphql 包](https://github.com/Folkloreatelier/laravel-graphql)

**2-** `composer.json` 添加包

```json
{
  "require": {
    "bowens-h/laravel-graphql-extent": "~1.0.0"
  }
}
```

**3-** 进行 composer 安装
```bash
composer install
```

### Laravel >= 5.5.x

**1-** 发布配置文件

```bash
$ php artisan vendor:publish --provider="BowensH\LaravelGraphQLExtend\ServiceProvider"
```

**2-** 查看配置文件

```
config/graphql_extend.php
```

### Laravel <= 5.4.x

**1-** 向 `config/app.php` 文件添加服务提供者

```php
BowensH\LaravelGraphQLExtend\ServiceProvider::class,
```

**2-** 发布配置文件

```bash
$ php artisan vendor:publish --provider="BowensH\LaravelGraphQLExtend\ServiceProvider"
```

**3-** 查看配置文件

```
config/graphql_extend.php
```

## 命令

- [创建 Column 类型文件](#make-column)
- [创建 Type 类型文件](#make-type)

<a name="make-column"></a>
#### 创建 Column 类型文件

```php
php artisan make:graphql:column TestColumn --table=test --force
```

如果某表字段为 json 类型，则该字段的 type 属性 命令默认生成为空数组（[]），需要自己手动写入对应键值，如果是数组，请用中括号包裹。例如

```php
public function columns()
{
    return [
        'test' => [
            'name'  => [
                'type'        => Type::string(),
                'description' => '类型',
            ]
        ],
        'description' => '测试',
    ];
}
```

```php
public function columns()
{
    return [
        'test' => [
            [//数组类型
                'name'  => [
                    'type'        => Type::string(),
                    'description' => '类型',
                ]
            ]
        ],
        'description' => '测试',
    ];
}
```

<a name="make-type"></a>
#### Type 类型文件生成命令

在 folklore/graphql 包的原命令基础上，增加了 「--table=」 参数，可以根据表生成对应字段，当 Type 类型文件中的字段不需要复用时，可以使用这个命令快速根据表生成字段。

```php
php artisan make:graphql:type TestType --table=test --force
```

## 用法

- [配置](#config)
- [Column](#column)

<a name="config"></a>
### 配置

- type_map: 数据表字段类型与 GraphQL 的类型映射。

> doctrine/dbal 包自动识别 mysql 中的 TinyInt 类型为 Boolean，因此该类型对应 graphql 类型为 Type::boolean()。

### Column

- [make](#make)
- [append](#append)
- [only](#only)
- [except](#except)
- [nonNull](#nonNull)
- [result](#result)

> Column 类最后输出的结果，专用于 Type 类型的 fields 方法中。

#### make

根据 TestColumn 生成 Column 实例，第一个参数为通过 make:graphql:column 生成的 Column 类型文件，当该 Column 被用于包含 InputObject 属性的 Type 类型文件中，第二个参数则需为 true（默认 false）。只有当调用 Column::make 后才可以使用其他方法。

```php
Column::make(TestColumn::class, true);
```

#### append

新增字段。

```php
Column::make(TestColumn::class)
    ->append([
        'test' => [
            'type' => Type:int(),
            'description' => '描述'
        ]
    ]);
```

#### only

只保留 TestColumn 中的 id 字段。

```php
//支持字符串与数组
Column::make(TestColumn::class, true)->only(['id'])
Column::make(TestColumn::class, true)->only('id')
```

#### except

排除 TestColumn 中的 id 字段。

```php
//支持字符串与数组
Column::make(TestColumn::class, true)->except(['id'])
Column::make(TestColumn::class, true)->except('id')
```

#### nonNull

将 TestColumn 中的 某些字段设置为 Type::nonNull。

```php
//支持字符串与数组
Column::make(TestColumn::class, true)->nonNull(['title'])
Column::make(TestColumn::class, true)->nonNull('title')
```

#### result

输出最终结果。

```php
<?php

namespace App\GraphQL\Type\Clothes;

use App\GraphQL\Columns\TestColumn;
use BowensH\LaravelGraphQLExtend\Column;
use Folklore\GraphQL\Support\Type as BaseType;

class TestType extends BaseType
{
    protected $inputObject = true;
    
    protected $attributes = [
        'name' => 'TestType',
        'description' => '测试'
    ];

    public function fields()
    {
        return Column::make(TestColumn::class, true) // 根据 ClothesColumn 生成 Column 实例
            ->nonNull(['title']) // 可选，配置 title 字段为 nonNull
            ->except(['id']) // 可选，排除 id 字段
            ->result(); // 返回最终结果
    }
}
```
