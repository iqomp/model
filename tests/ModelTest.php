<?php
declare(strict_types=1);

namespace Iqomp\Model\Tests;

use PHPUnit\Framework\TestCase;
use Iqomp\Model\Model;
use Iqomp\Config\Fetcher;
use Iqomp\Model\ConnectionNotFoundException;
use Iqomp\Model\DriverNotInstalledException;
use Iqomp\Model\InvalidConnectionDriverException;

use Iqomp\Model\Tests\Model\User as M_User;
use Iqomp\Model\Tests\Model\Post as M_Post;
use Iqomp\Model\Tests\Driver\One as D_One;

class ModelTest extends TestCase
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

    public function testGetTable(): void
    {
        $this->assertEquals('user', M_User::getTable());
    }

    public function testConsistentGetTable(): void
    {
        $this->assertEquals('user', M_User::getTable());
        $this->assertEquals('post', M_Post::getTable());
    }

    public function testGetModel(): void
    {
        $this->assertEquals(M_User::class, M_User::getModel());
    }
}
