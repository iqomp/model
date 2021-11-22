# iqomp/model

The other way to manage database with style. The main purpose of this module is
to create a way to interact with database without handle connection and the
manegement it self. This is other way to communicate with database other than
the way hyperf use it.

## Installation

```bash
composer require iqomp/model
```

## Publishing Config

```bash
php bin/hyperf.php vendor:publish iqomp/model
```

## Configuration

The configurasion is saved at `config/autoload/model.php` that contain which model
connection type to use for some model. The database connection it self is return
back to hyperf style.

```php
<?php

return [
    'drivers' => [
        'pdo' => 'PDO\\Driver\\Class',
        // ...
    ],
    'models' => [
        'Model\\Class\\Name' => [
            'read' => 'slave',
            'write' => 'master'
        ],
        'Model\\Wildcard\\*' => [
            'read' => 'slave',
            'write' => 'master'
        ]
    ],
    'chains' => [
        'Model\\Class\\Name' => [
            '/field/' => [
                'model' => 'Model\\Other\\Class',
                'self' => 'id',
                'children' => 'wallet_id',
                'type' => 'left'
            ]
        ]
    ]
];
```

### drivers

All known drivers. Most of the time, this property registered by module.

### models

Configuration to decide database connection to use for each models. If the model
is not registered here, it will use `default` connection if exists, or throw an
`ConnectionNotFoundException` exception. The model name accept wildcard ( `*` )
that match any part of the model name. Each model should have property `read` and
`write` that define connection for read and write.

## Model Structure

Each model class should extends from `Iqomp\Model\Model` and has at least one
public static property named `table`. Below is mostly how model look like:

```php
<?php

namespace Company\Project;

class Product extends \Iqomp\Model\Model
{
    public static $table = 'product';

    public static $chains = [
        '/field/' => [
            'model' => 'Model\\Other\\Class',
            'self' => 'id',
            'children' => 'wallet_id',
            'type' => 'left'
        ]
    ];
}
```

## Drivers

Driver is the one that manage connection to database and communication with the
database. Each driver should register it self as model driver with content config
as file `config/autoload/model.php` as below:

```php
<?php

return [
    'drivers' => [
        'name' => 'Class'
    ]
];
```

Driver class should implement interface `Iqomp\Model\DriverInterface`. Below are
list of method that should be implemented by the driver:

```php
/**
 * Construct new model object
 * @param array DB connection options
 *  @param string $model Model name
 *  @param string $table Table name
 *  @param array $chains?
 *  @param array $q_field?
 *  @param array $connections
 *    @param array $read List of connection for read
 *    @param array $write List of connection for write
 */
__construct(array $options);

/**
 * Count average value of field
 * @param string $field the field sum total to average
 * @param array $where Where condition
 * @return float Average value of the column
 */
avg(string $field, array $where = []): float;

/**
 * Count total rows in table
 * @param array $where Where condition
 * @return int Total row
 */
count(array $where = []): int;

/**
 * Insert single data to database
 * @param array $row Array column-value pair of data to insert
 * @param bool $ignore Ignore error data already there
 * @return int Last inserted id on success, null otherwise
 */
create(array $row, bool $ignore = false): ?int;

/**
 * Insert many data at once
 * @param array $rows List of array list data to insert
 * @param bool $ignore Ignore exists data if possible
 * @return boolean true on success false otherwise.
 */
createMany(array $rows, bool $ignore = false): bool;

/**
 * Decrease multiple columns with condition
 * @param array $fields List of field-value pair of column to decrease by value
 * @param $where Where condition
 */
dec(array $fields, array $where = []): bool;

/**
 * Escape string to use in raw query
 * @param string $str String to escape
 * @return escaped string
 */
escape(string $str): string;

/**
 * Get single row from table
 * @param array $where Where condition
 * @param array $order Array list of field-direction pair of sort
 * @return object if exists or null
 */
getOne(array $where = [], array $order = ['id' => false]): ?object;

/**
 * Get multiple rows from database
 * @param array $where Where condition
 * @param int $rpp Result per page, default 0 which is all.
 * @param int $page Page number, default 1.
 * @param array $order Array list of field-direction pair of sort
 * @return array list of object or empty array
 */
get(array $where = [], int $rpp = 0, int $page = 1, array $order = ['id' => false]): array;

/**
 * Get connection object
 * @param string $target Connection type target
 * @return resource connection
 */
getConnection(string $target = 'read');

/**
 * Get connection name in config that the model use for $target connection
 * @param string $target Connection type target
 * @return string connection config name
 */
getConnectionName(string $target = 'read'): ?string;

/**
 * Get current connection database name
 * @param string $target Connection type target
 * @return string database name
 */
getDBName(string $target = 'read'): ?string;

/**
 * Get the driver name used for this model
 * @return string driver name
 */
getDriver(): ?string;

/**
 * Get the model name of current model
 * @return string model name
 */
getModel(): string;

/**
 * Get the tabel name that this model handle
 * @return string
 */
getTable(): string;

/**
 * Increase multiple columns with condition
 * @param array $fields List of field-value pair of column to increase by value
 * @param $where Where condition
 */
inc(array $fields, array $where = []): bool;

/**
 * Return last error accured
 * @return string error message or null
 */
lastError(): ?string;

/**
 * Return last id inserted to database
 * @return int last inserted id, or null otherwise
 */
lastId(): ?int;

/**
 * Return the most last executed query
 * @return string if exists, null otherwise
 */
lastQuery(): ?string;

/**
 * Get the maximum value of field from table
 * @param string $field The field to process
 * @param array $where Where condition
 * @return int The max value of field.
 */
max(string $field, array $where = []): int;

/**
 * Get the minimum value of field from table
 * @param string $field THe field to process
 * @param array $where Where condition
 * @return int The smallest value of field.
 */
min(string $field, array $where = []): int;

/**
 * Remove row from table
 * @param array $where Where condition
 * @return boolean true on success, false otherwise.
 */
remove(array $where = []): bool;

/**
 * Update table
 * @param array $fields List of field-value pair of data to update
 * @param array $where Where condition.
 * @return boolean true on success false otherwise.
 */
set(array $fields, array $where = []): bool;

/**
 * Sum table single field.
 * @param string $field The field to sum
 * @param array $where Where conditon.
 * @return int total sum of the field value.
 */
sum(string $field, array $where = []): int;

/**
 * Truncate the table
 * @param string $target Connection target
 */
truncate(string $target = 'write'): bool;
```

All of above method is `public` method.

## Usage

After creating the model, it's now easy to use it from app:

```php
<?php

use Company\Project\Model\Product;

$id = Product::create($array);
$product = Product::getOne(['id'=>$id]);
```

### Sorting

Method `get` and `getOne` has `$order` argument that can be used to sort the result.
The value is an array field-order pair where `field` is column name of the table,
and `order` is boolean `false` for descending, and `true` is ascending.

### Where Condition

Some method accept argument `$where` that filter the action execution. This part
explain where usage:

#### Standard

The most simple way to create a where condition is as below:

```php
$where = [
    'id'    => 1,
    'name'  => 'User'
];
// `id` = 1 AND `name` = 'User'
```

#### IN Query

Combain multiple filter value in an array to use them in `in` operator:

```php
$where = [
    'id' => [1,2,3],
    'status' => 1
];
// `id` IN (1,2,3) AND `status` = 1
```

#### Operator

To use other than `=` for operator comparation, use below style:

```php
$where = [
    'id' => ['__op', '!=', 12],
    'status' => 1
];
// `id` != 12 AND `status` = 1

$where = [
    'status' => ['__op', '>', 0]
];
// `status` > 0

$where = [
    'meta' => ['__op', '!=', NULL]
];
// `meta` IS NOT NULL

$where = [
    'status' => ['__op', 'NOT IN', [1,2]]
];
// `status` NOT IN (1,2)
```

Known operator so far are `>`, `<`, `<=`, `>=`, `!=`, and `NOT IN`.


#### BETWEEN

To use between operator, use it as below:

```php
$where = [
    'status' => ['__between', 1, 5]
];
// `status` BETWEEN 1 AND 5
```

#### LIKE

Array prefix `__like` can be used to use `LIKE` operator:

```php
$where = [
    'title' => ['__like', 'name']
];
// `title` LIKE '%name%'

$where = [
    'title' => ['__like', 'name', 'left'],
    // 'title' => ['__like', 'name', 'both']
    // 'title' => ['__like', 'name', 'right']
    // 'title' => ['__like', 'name', 'none']
];
// `title` LIKE '%name'

$where = [
    'title' => ['__like', 'name', null, 'NOT']
];
// `title` NOT LIKE 'name'

$where = [
    'title' => ['__like', ['na1', 'na2', 'na3']]
];
// `title` LIKE '%na1%' OR `title` LIKE '%na2%' OR `title` LIKE '%na3%'
```

#### AND

By default, each array where is combined with `AND` operator. If your where condition
is not that standard, you can use `$and` array key to combine each of sub array
combined with `AND`:

```php
$where = [
    '$and' => [
        [
            'created' => ['__op', '!=', NULL]
        ],
        [
            'created' => ['__op', '>', '2010-02-01']
        ]
    ]
];
// ( ( `created` IS NOT NULL ) AND ( `created` > '2010-02-01' ) )
```

#### OR

Just like `$and`, you can also use `OR` to combine each condition with `$or` array
key:

```php
$where = [
    '$or' => [
        [
            'status' => 1,
            'user' => 2
        ],
        [
            'status' => 2
        ]
    ]
];
// ( ( `status` = 1 AND `user` = 2 ) OR ( `status` = 2 ) )
```

#### Unescape

If you need to unescape column name on where condition, add prefix `?` to the
column name:

```php
$where = [
    '?`user`' => 1
];
// `user` = 1

$where = [
    '?JSON_EXTRACT(`price`, \'$.anually\')' => ['__op', '!=', '']
];
// JSON_EXTRACT(`price`, '$.anually') != ''
```

## Validator

If your application use [iqomp/validator](https://github.com/iqomp/validator/)
for you object validator, this module add new validator that can be used to
validate object that related to data model.

### unique

Make sure the data is not yet on database:

```php
    // ...
    'rules' => [
        'unique' => [
            'model' => 'Company\\Project\\Model\\Product',
            'field' => 'slug',
            'where' => [ /* ... */ ] // additional where condition
        ]
    ]
    // ...
```

If there're multiple column to be test, set `field` property value as array:

```php
    // ...
    'rules' => [
        'unique' => [
            'model' => 'Company\\Project\\Model\\Product',
            'field' => [
                // same name of table column and object property
                'slug',

                // object property => table column
                'wallet' => 'wallet_id'

            ],
            'where' => [ /* ... */ ] // additional where condition
        ]
    ]
    // ...
```

### exists

Make sure the data is in database:

```php
    // ...
    'rules' => [
        'exists' => [
            'model' => 'Company\\Project\\Model\\Product',
            'field' => 'slug',
            'where' => [ /* ... */ ] // additional where condition
        ]
    ]
    // ...
```

### exists-list

Make sure all values of object property ( which is array ) already exists in
database:

```php
    // ...
    'rules' => [
        'exists-list' => [
            'model' => 'Company\\Project\\Model\\Product',
            'field' => 'slug',
            'where' => [ /* ... */ ] // additional where condition
        ]
    ]
    // ...
```

## Formatter

If your application use [iqomp/formatter](https://github.com/iqomp/formatter/)
for your object formatter, this module add a few format type.

By default, no data will taken from database, instead all property with below
format type will use `null` or `{"id": value}` as the object value.

If you need to fill the property with data from the table, add additional options
on calling your formatter:

```php
$result = Formatter::format('fmt-name', $object, ['user'=>true]);
```

By calling above function, `options.user = true` tell the handler to take the data
from database.

In case object `user` has sub-object that you want to fetch from database, use
array key-bool pair as the value of options user:

```php
$result = Formatter::format('fmt-name', $object, ['user' => ['profile'=>true]]);
```

Above action will take property `user` of current object from database, then take
property `profile` from database of `user` property.

If you need to add additional where condition while fetching the data, you can
also add it on the options:

```php
$result = Formatter::format('fmt-name', $object, [
    'user' => [
        '_where' => [
            'status' => 1
        ]
    ]
]);
```

Above action will get data from database with additional where condition `status = 1`.

### Format Types

#### multiple-object

Explode the object property that separate by some string or using json_decode
and convert them to be objects that taken from database:

```php
    // ...
    'publishers' => [
        'type'      => 'multiple-object',
        'separator' => ',', // 'json'

        'model' => [
            'name' => 'Company\\Project\\Model\\User',
            'field' => 'id'
        ],

        // optional see below
        'field' => [
            'name' => '/field/',
            'type' => '/type/'
        ],

        // optional, see below
        'fields' => [
            ['name' => '/field1/', 'type' => '/type/'],
            ['name' => '/field2/', 'type' => '/type/']
        ],

        // optional, see below
        'format' => '/object-format/'
    ]
    // ...
```

#### chain

Take objects from other table that joined by another table.

```php
    // ...
    'tags' => [
        'type' => 'chain',
        'chain' => [
            'model' => [
                'name'  => 'Company\\Project\\Model\\PostTagChain',
                'field' => 'post'
            ],
            'identity' => 'post_tag'
        ],
        'model' => [
            'name'  => 'Company\\Project\\Model\\PostTag',
            'field' => 'id'
        ],

        // optional see below
        'field' => [
            'name' => '/field/',
            'type' => '/type/'
        ],

        // optional, see below
        'fields' => [
            ['name' => '/field1/', 'type' => '/type/'],
            ['name' => '/field2/', 'type' => '/type/']
        ],

        // optional, see below
        'format' => '/object-format/'
    ]
    // ...
```

#### object

Convert the value of object property to be object data that taken from table:

```php
    // ...
    'user' => [
        'type' => 'object',
        'model' => [
            'name' => 'Company\\Project\\Model\\User',
            'field' => 'id'
        ],

        // optional see below
        'field' => [
            'name' => '/field/',
            'type' => '/type/'
        ],

        // optional, see below
        'fields' => [
            ['name' => '/field1/', 'type' => '/type/'],
            ['name' => '/field2/', 'type' => '/type/']
        ],

        // optional, see below
        'format' => '/object-format/'
    ]
    // ...
```

#### object-switch

Switch model usage based on other object property value:

```php
    // ...
    'meta' => [
        'type' => 'object-switch',
        'field' => 'type',
        'cases' => [
            1 => [
                'model' => [
                    'name' => 'Company\\Project\\Model\\User',
                    'field' => 'id'
                ],

                // optional see below
                'field' => [
                    'name' => '/field/',
                    'type' => '/type/'
                ],

                // optional, see below
                'fields' => [
                    ['name' => '/field1/', 'type' => '/type/'],
                    ['name' => '/field2/', 'type' => '/type/']
                ],

                // optional, see below
                'format' => '/object-format/'
            ],
            2 => [
                // ...
            ]
        ]
    ]
    // ...
```

#### partial

Take object from other table with relation id is as current object id. This action
will add new object property

```php
    // ...
    'content' => [
        'type' => 'partial',
        'model' => [
            'name' => 'Company\\Project\\Model\\User',
            'field' => 'id'
        ],

        // optional see below
        'field' => [
            'name' => '/field/',
            'type' => '/type/'
        ],

        // optional, see below
        'fields' => [
            ['name' => '/field1/', 'type' => '/type/'],
            ['name' => '/field2/', 'type' => '/type/']
        ],

        // optional, see below
        'format' => '/object-format/'
    ]
    // ...
```

### Additional Properties

Each format accept additional config named `field`, `fields`, or `format`. There
can only be one of them in a single property config.

#### field

After getting the data from database, take only one column ( which is `field->name` )
from the result and use the value as current object property. If property `type`
exists, apply that format type to the taken value.

#### fields

Just like `field` accept this method get multiple data from database result and
use all mentioned property as new value of current object property. If property
`type` exists, apply that format type to the taken value.

#### format

Format the data taken from database with this format name, and use the formatted
data as current object property.
