<?php
declare(strict_types=1);

namespace Iqomp\Model\Tests;

use PHPUnit\Framework\TestCase;
use Iqomp\Model\Model;
use Iqomp\Config\Fetcher;
use Iqomp\Formatter\Formatter;

use Iqomp\Model\Tests\Model\{
    User as M_User,
    Post as M_Post,
    PostTag as M_PostTag,
    PostTagChain as M_PostTagChain,
};

use Iqomp\Model\Tests\Driver\One as D_One;

use Iqomp\Formatter\Object\{
    Number as O_Number,
    Std as O_Std,
    Text as O_Text
};

class FormatterTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        Model::refresh();
        Fetcher::fetchConfig();
        Fetcher::addConfig([
            'formatter' => [
                'formats' => [
                    'std-user' => [
                        'id' => [
                            'type' => 'number'
                        ],
                        'name' => [
                            'type' => 'text'
                        ],
                        'created' => [
                            'type' => 'date'
                        ]
                    ],
                    'std-post-tag' => [
                        'id' => [
                            'type' => 'number'
                        ],
                        'name' => [
                            'type' => 'text'
                        ]
                    ]
                ]
            ],
            'database' => [
                'connections' => [
                    'default' => [
                        'driver' => 'one'
                    ]
                ],
                'drivers' => [
                    'one' => D_One::class
                ]
            ]
        ]);
    }

    public function testPartialCreateNewAttribute(): void
    {
        $object = (object)[
            'id' => 1,
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'user' => [
                'type' => 'partial',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object]);
        $result = $result[0];

        $this->assertObjectHasAttribute('user', $result);
    }

    public function testPartialAsNull(): void
    {
        $object = (object)[
            'id' => 1,
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'user' => [
                'type' => 'partial',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object]);
        $result = $result[0];

        $this->assertNull($result->user);
    }

    public function testPartialAsStdObject(): void
    {
        $object = (object)[
            'id' => 1,
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'user' => [
                'type' => 'partial',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['user'=>true]);
        $result = $result[0];

        $this->assertIsObject($result->user);
    }

    public function testPartialHasProperty(): void
    {
        $object = (object)[
            'id' => 1,
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'user' => [
                'type' => 'partial',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['user'=>true]);
        $result = $result[0];
        $this->assertObjectHasAttribute('name', $result->user);
    }

    public function testPartialFormat(): void
    {
        $object = (object)[
            'id' => 1,
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'user' => [
                'type' => 'partial',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ],
                'format' => 'std-user'
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['user'=>true]);
        $result = $result[0];
        $this->assertInstanceOf(O_Text::class, $result->user->name);
    }

    public function testPartialNameOnly(): void
    {
        $object = (object)[
            'id' => 1,
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'user' => [
                'type' => 'partial',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ],
                'field' => [
                    'name' => 'name'
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['user'=>true]);
        $result = $result[0];
        $this->assertEquals('User One', $result->user);
    }

    public function testPartialNameOnlyWithType(): void
    {
        $object = (object)[
            'id' => 1,
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'user' => [
                'type' => 'partial',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ],
                'field' => [
                    'name' => 'name',
                    'type' => 'text'
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['user'=>true])[0];
        $this->assertInstanceOf(O_Text::class, $result->user);
    }

    public function testPartialNameAndId(): void
    {
        $object = (object)[
            'id' => 1,
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'user' => [
                'type' => 'partial',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ],
                'fields' => [
                    ['name' => 'name'],
                    ['name' => 'id']
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['user'=>true]);
        $result = $result[0];
        $this->assertEquals((object)['id'=>1, 'name'=>'User One'], $result->user);
    }

    public function testPartialNameAndIdWithType(): void
    {
        $object = (object)[
            'id' => 1,
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'user' => [
                'type' => 'partial',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ],
                'fields' => [
                    ['name' => 'name', 'type' => 'text'],
                    ['name' => 'id', 'type' => 'number']
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['user'=>true]);
        $result = $result[0];

        $this->assertInstanceOf(O_Text::class, $result->user->name);
        $this->assertInstanceOf(O_Number::class, $result->user->id);
    }

    public function testObjectAsStdId(): void
    {
        $object = (object)[
            'id' => 1,
            'user' => 1
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'user' => [
                'type' => 'object',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object]);
        $result = $result[0];

        $this->assertInstanceOf(O_Std::class, $result->user);
    }

    public function testObjectHasProperty(): void
    {
        $object = (object)[
            'id' => 1,
            'user' => 2
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'user' => [
                'type' => 'object',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['user'=>true]);
        $result = $result[0];
        $this->assertObjectHasAttribute('name', $result->user);
    }

    public function testObjectNameOnly(): void
    {
        $object = (object)[
            'id' => 1,
            'user' => 2
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'user' => [
                'type' => 'object',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ],
                'field' => [
                    'name' => 'name'
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['user'=>true]);
        $result = $result[0];
        $this->assertEquals('User Two', $result->user);
    }

    public function testObjectNameOnlyWithType(): void
    {
        $object = (object)[
            'id' => 1,
            'user' => 2
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'user' => [
                'type' => 'object',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ],
                'field' => [
                    'name' => 'name',
                    'type' => 'text'
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['user'=>true]);
        $result = $result[0];
        $this->assertInstanceOf(O_Text::class, $result->user);
    }

    public function testObjectNameAndId(): void
    {
        $object = (object)[
            'id' => 1,
            'user' => 2
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'user' => [
                'type' => 'object',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ],
                'fields' => [
                    ['name' => 'name'],
                    ['name' => 'id']
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['user'=>true]);
        $result = $result[0];
        $this->assertEquals((object)['id'=>2,'name'=>'User Two'], $result->user);
    }

    public function testObjectNameAndIdWithType(): void
    {
        $object = (object)[
            'id' => 1,
            'user' => 2
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'user' => [
                'type' => 'object',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ],
                'fields' => [
                    ['name' => 'name', 'type' => 'text'],
                    ['name' => 'id',   'type' => 'number']
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['user'=>true]);
        $result = $result[0];
        $this->assertInstanceOf(O_Text::class, $result->user->name);
        $this->assertInstanceOf(O_Number::class, $result->user->id);
    }

    public function testObjectFormat(): void
    {
        $object = (object)[
            'id' => 1,
            'user' => 2
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'user' => [
                'type' => 'object',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ],
                'format' => 'std-user'
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['user'=>true]);
        $result = $result[0];

        $this->assertInstanceOf(O_Text::class, $result->user->name);
        $this->assertInstanceOf(O_Number::class, $result->user->id);
    }

    public function testMultipleObjectLen(): void
    {
        $object = (object)[
            'id' => 1,
            'users' => '1,2'
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'users' => [
                'type' => 'multiple-object',
                'separator' => ',',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ],
                'format' => 'std-user'
            ]
        ];

        $result = Formatter::formatApply($format, [$object]);
        $result = $result[0];
        $this->assertCount(2, $result->users);
    }

    public function testMultipleObjectJson(): void
    {
        $object = (object)[
            'id' => 100,
            'users' => '[1,2,3]'
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'users' => [
                'type' => 'multiple-object',
                'separator' => 'json',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ],
                'format' => 'std-user'
            ]
        ];

        $result = Formatter::formatApply($format, [$object]);
        $result = $result[0];
        $this->assertCount(3, $result->users);
    }

    public function testMultipleObjectStdId(): void
    {
        $object = (object)[
            'id' => 100,
            'users' => '1,2'
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'users' => [
                'type' => 'multiple-object',
                'separator' => ',',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ],
                'format' => 'std-user'
            ]
        ];

        $result = Formatter::formatApply($format, [$object]);
        $result = $result[0];

        $this->assertInstanceOf(O_Std::class, $result->users[0]);
        $this->assertInstanceOf(O_Std::class, $result->users[1]);
    }

    public function testMultipleObjectNameOnly(): void
    {
        $object = (object)[
            'id' => 100,
            'users' => '1,2'
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'users' => [
                'type' => 'multiple-object',
                'separator' => ',',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ],
                'field' => [
                    'name' => 'name'
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['users'=>true]);
        $result = $result[0];
        $user_0 = $result->users[0];

        $this->assertEquals('User One', $user_0);
    }

    public function testMultipleObjectNameOnlyWithType(): void
    {
        $object = (object)[
            'id' => 100,
            'users' => '1,2'
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'users' => [
                'type' => 'multiple-object',
                'separator' => ',',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ],
                'field' => [
                    'name' => 'name',
                    'type' => 'text'
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['users'=>true]);
        $result = $result[0];
        $user_0 = $result->users[0];

        $this->assertInstanceOf(O_Text::class, $user_0);
    }

    public function testMultipleObjectNameAndId(): void
    {
        $object = (object)[
            'id' => 100,
            'users' => '1,2'
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'users' => [
                'type' => 'multiple-object',
                'separator' => ',',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ],
                'fields' => [
                    ['name' => 'name'],
                    ['name' => 'id']
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['users'=>true]);
        $result = $result[0];
        $user_0 = $result->users[0];

        $this->assertEquals((object)['id'=>1,'name'=>'User One'], $user_0);
    }

    public function testMultipleObjectFormat(): void
    {
        $object = (object)[
            'id' => 100,
            'users' => '1,2'
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'users' => [
                'type' => 'multiple-object',
                'separator' => ',',
                'model' => [
                    'name' => M_User::class,
                    'field' => 'id'
                ],
                'format' => 'std-user'
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['users'=>true]);
        $result = $result[0];
        $user_0 = $result->users[0];

        $this->assertInstanceOf(O_Text::class, $user_0->name);
        $this->assertInstanceOf(O_Number::class, $user_0->id);
    }

    public function testObjectSwitchCaseStd(): void
    {
        $objects = [
            (object)['id'=>1, 'type'=>1, 'meta'=>1],
            (object)['id'=>2, 'type'=>2, 'meta'=>1]
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'type' => [
                'type' => 'number'
            ],
            'meta' => [
                'type' => 'object-switch',
                'field' => 'type',
                'cases' => [
                    1 => [
                        'model' => [
                            'name' => M_User::class,
                            'field' => 'id'
                        ]
                    ],
                    2 => [
                        'model' => [
                            'name' => M_Post::class,
                            'field' => 'id'
                        ]
                    ]
                ]
            ]
        ];

        $result = Formatter::formatApply($format, $objects);

        $this->assertInstanceOf(O_Std::class, $result[0]->meta);
        $this->assertInstanceOf(O_Std::class, $result[1]->meta);
    }

    public function testObjectSwitch(): void
    {
        $objects = [
            (object)['id'=>1, 'type'=>1, 'meta'=>1],
            (object)['id'=>2, 'type'=>2, 'meta'=>1]
        ];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'type' => [
                'type' => 'number'
            ],
            'meta' => [
                'type' => 'object-switch',
                'field' => 'type',
                'cases' => [
                    1 => [
                        'model' => [
                            'name' => M_User::class,
                            'field' => 'id'
                        ]
                    ],
                    2 => [
                        'model' => [
                            'name' => M_Post::class,
                            'field' => 'id'
                        ]
                    ]
                ]
            ]
        ];

        $result = Formatter::formatApply($format, $objects, ['meta'=>true]);

        $this->assertObjectHasAttribute('name', $result[0]->meta);
        $this->assertObjectHasAttribute('title', $result[1]->meta);
    }

    public function testObjectSwitchFormat(): void
    {
        $object = (object)['id'=>1, 'type'=>1, 'meta'=>1];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'type' => [
                'type' => 'number'
            ],
            'meta' => [
                'type' => 'object-switch',
                'field' => 'type',
                'cases' => [
                    1 => [
                        'model' => [
                            'name' => M_User::class,
                            'field' => 'id'
                        ],
                        'format' => 'std-user'
                    ]
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['meta'=>true]);
        $result = $result[0];

        $this->assertInstanceOf(O_Text::class, $result->meta->name);
    }

    public function testObjectSwitchIdType(): void
    {
        $object = (object)['id'=>1, 'type'=>1, 'meta'=>1];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'type' => [
                'type' => 'number'
            ],
            'meta' => [
                'type' => 'object-switch',
                'field' => 'type',
                'cases' => [
                    1 => [
                        'model' => [
                            'name' => M_User::class,
                            'field' => 'id'
                        ],
                        'field' => [
                            'name' => 'id',
                            'type' => 'number'
                        ]
                    ]
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['meta'=>true]);
        $result = $result[0];

        $this->assertInstanceOf(O_Number::class, $result->meta);
    }

    public function testObjectSwitchIdTypes(): void
    {
        $object = (object)['id'=>1, 'type'=>1, 'meta'=>1];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'type' => [
                'type' => 'number'
            ],
            'meta' => [
                'type' => 'object-switch',
                'field' => 'type',
                'cases' => [
                    1 => [
                        'model' => [
                            'name' => M_Post::class,
                            'field' => 'id'
                        ],
                        'fields' => [
                            ['name' => 'id','type' => 'number'],
                            ['name' => 'title','type' => 'text']
                        ]
                    ]
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['meta'=>true]);
        $result = $result[0];

        $this->assertInstanceOf(O_Text::class, $result->meta->title);
    }

    public function testChainEmpty(): void
    {
        $object = (object)['id'=>1];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'tags' => [
                'type' => 'chain',
                'chain' => [
                    'model' => [
                        'name' => M_PostTagChain::class,
                        'field' => 'post'
                    ],
                    'identity' => 'post_tag'
                ],
                'model' => [
                    'name' => M_PostTag::class,
                    'field' => 'id'
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object]);
        $result = $result[0];

        $this->assertIsArray($result->tags);
    }

    public function testChainCount(): void
    {
        $object = (object)['id'=>1];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'tags' => [
                'type' => 'chain',
                'chain' => [
                    'model' => [
                        'name' => M_PostTagChain::class,
                        'field' => 'post'
                    ],
                    'identity' => 'post_tag'
                ],
                'model' => [
                    'name' => M_PostTag::class,
                    'field' => 'id'
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['tags'=>true]);
        $result = $result[0];

        $this->assertCount(2, $result->tags);
    }

    public function testChainType(): void
    {
        $object = (object)['id'=>1];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'tags' => [
                'type' => 'chain',
                'chain' => [
                    'model' => [
                        'name' => M_PostTagChain::class,
                        'field' => 'post'
                    ],
                    'identity' => 'post_tag'
                ],
                'model' => [
                    'name' => M_PostTag::class,
                    'field' => 'id'
                ],
                'field' => [
                    'name' => 'id',
                    'type' => 'number'
                ]
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['tags'=>true]);
        $result = $result[0];

        $this->assertInstanceOf(O_Number::class, $result->tags[0]);
    }

    public function testChainFormat(): void
    {
        $object = (object)['id'=>1];
        $format = [
            'id' => [
                'type' => 'number'
            ],
            'tags' => [
                'type' => 'chain',
                'chain' => [
                    'model' => [
                        'name' => M_PostTagChain::class,
                        'field' => 'post'
                    ],
                    'identity' => 'post_tag'
                ],
                'model' => [
                    'name' => M_PostTag::class,
                    'field' => 'id'
                ],
                'format' => 'std-post-tag'
            ]
        ];

        $result = Formatter::formatApply($format, [$object], ['tags'=>true]);
        $result = $result[0];

        $this->assertInstanceOf(O_Text::class, $result->tags[0]->name);
    }
}
