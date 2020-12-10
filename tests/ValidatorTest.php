<?php
declare(strict_types=1);

namespace Iqomp\Model\Tests;

use PHPUnit\Framework\TestCase;
use Iqomp\Model\Model;
use Iqomp\Config\Fetcher;
use Iqomp\Validator\Validator;

use Iqomp\Model\Tests\Model\User as M_User;
use Iqomp\Model\Tests\Model\Post as M_Post;
use Iqomp\Model\Tests\Driver\One as D_One;

class ValidatorTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        Model::refresh();
        Fetcher::fetchConfig();
        Fetcher::addConfig([
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

    public function testUniquePass(): void
    {
        $object = (object)['name' => 'User Not Exists'];
        $rules = [
            'name' => [
                'rules' => [
                    'unique' => [
                        'model' => M_User::class,
                        'field' => 'name'
                    ]
                ]
            ]
        ];

        list($result, $errors) = Validator::validate($rules, $object);

        $this->assertArrayNotHasKey('name', $errors);
    }

    public function testUniqueFail() {
        $object = (object)['name' => 'User One'];
        $rules = [
            'name' => [
                'rules' => [
                    'unique' => [
                        'model' => M_User::class,
                        'field' => 'name'
                    ]
                ]
            ]
        ];

        list($result, $error) = Validator::validate($rules, $object);
        $code = $error['name']->code;
        $this->assertEquals('14.0', $code);
    }

    public function testExistsFail() {
        $object = (object)['name' => 'User Not Exists'];
        $rules = [
            'name' => [
                'rules' => [
                    'exists' => [
                        'model' => M_User::class,
                        'field' => 'name'
                    ]
                ]
            ]
        ];

        list($result, $error) = Validator::validate($rules, $object);
        $code = $error['name']->code;
        $this->assertEquals('19.0', $code);
    }

    public function testExistsPass() {
        $object = (object)['name' => 'User One'];
        $rules = [
            'name' => [
                'rules' => [
                    'exists' => [
                        'model' => M_User::class,
                        'field' => 'name'
                    ]
                ]
            ]
        ];

        list($result, $error) = Validator::validate($rules, $object);

        $this->assertArrayNotHasKey('name', $error);
    }

    public function testExistsListPass() {
        $object = (object)['name' => ['User One', 'User Two', 'User Three']];
        $rules = [
            'name' => [
                'rules' => [
                    'exists-list' => [
                        'model' => M_User::class,
                        'field' => 'name'
                    ]
                ]
            ]
        ];

        list($result, $error) = Validator::validate($rules, $object);

        $this->assertArrayNotHasKey('name', $error);
    }

    public function testExistsListFailAll() {
        $object = (object)['name' => ['Non Exists One', 'Non Exists Two']];
        $rules = [
            'name' => [
                'rules' => [
                    'exists-list' => [
                        'model' => M_User::class,
                        'field' => 'name'
                    ]
                ]
            ]
        ];

        list($result, $error) = Validator::validate($rules, $object);
        $code = $error['name']->code;
        $this->assertEquals('20.0', $code);
    }

    public function testExistsListFailPartial() {
        $object = (object)['name' => ['User One', 'Non Exists One']];
        $rules = [
            'name' => [
                'rules' => [
                    'exists-list' => [
                        'model' => M_User::class,
                        'field' => 'name'
                    ]
                ]
            ]
        ];

        list($result, $error) = Validator::validate($rules, $object);
        $code = $error['name']->code;
        $this->assertEquals('20.0', $code);
    }
}
