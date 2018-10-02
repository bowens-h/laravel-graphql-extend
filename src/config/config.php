<?php

return [

	/*
     * 定义graphql的type与数据库类型的对应关系
     */
	'type_map' => [
		'default' => 'Type::string()',//默认type
		'common'  => [
			'Type::int()'     => [ 'Integer', 'SmallInt', 'BigInt' ],
			'Type::float()'   => [ 'Float', 'Decimal' ],
			'Type::boolean()' => [ 'Boolean' ],
			'[]' => [ 'Json' ],
		]
	]
];
