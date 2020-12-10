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
use Iqomp\Model\Tests\Driver\One as D_One;

class DriverTest extends TestCase
{
    public function testNoConnectionsRegistered(): void
    {
        Model::refresh();
        Fetcher::fetchConfig();
        $this->expectException(ConnectionNotFoundException::class);
        M_User::getConnectionName();
    }

    public function testConnectionNotFound(): void
    {
        Model::refresh();
        Fetcher::fetchConfig();
        Fetcher::addConfig([
            'database' => [
                'connections' => [
                    'default' => []
                ],
                'drivers' => [
                    'pdo' => '...'
                ],
                'models' => [
                    M_User::class => [
                        'read' => 'not-exists',
                        'write' => 'not-exists'
                    ]
                ]
            ]
        ]);

        $this->expectException(ConnectionNotFoundException::class);
        M_User::getConnectionName();
    }

    public function testConnectionDriversDifferent(): void
    {
        Model::refresh();
        Fetcher::fetchConfig();
        Fetcher::addConfig([
            'database' => [
                'connections' => [
                    'one' => ['driver' => 'pdo'],
                    'two' => ['driver' => 'mock']
                ],
                'drivers' => [
                    'pdo' => '...'
                ],
                'models' => [
                    M_User::class => [
                        'read'  => 'one',
                        'write' => 'two'
                    ]
                ]
            ]
        ]);

        $this->expectException(InvalidConnectionDriverException::class);
        M_User::getConnectionName();
    }

    public function testConnectionUseDefault(): void
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

        $this->assertEquals('default', M_User::getConnectionName());
    }

    public function testConnectionUseAsConfig(): void
    {
        Model::refresh();
        Fetcher::fetchConfig();
        Fetcher::addConfig([
            'database' => [
                'connections' => [
                    'defualt' => [
                        'driver' => 'one'
                    ],
                    'one' => [
                        'driver' => 'one'
                    ]
                ],
                'drivers' => [
                    'one' => D_One::class
                ],
                'models' => [
                    M_User::class => [
                        'read' => 'one',
                        'write' => 'one'
                    ]
                ]
            ]
        ]);

        $this->assertEquals('one', M_User::getConnectionName());
    }

    public function testConnectionUseWildcard(): void
    {
        Model::refresh();
        Fetcher::fetchConfig();
        Fetcher::addConfig([
            'database' => [
                'connections' => [
                    'default' => [
                        'driver' => 'one'
                    ],
                    'one' => [
                        'driver' => 'one'
                    ]
                ],
                'drivers' => [
                    'one' => D_One::class
                ],
                'models' => [
                    'Iqomp\\Model\\Tests\\*\\*' => [
                        'read' => 'one',
                        'write' => 'one'
                    ]
                ]
            ]
        ]);

        $this->assertEquals('one', M_User::getConnectionName());
    }

    public function testNoDriversRegistered(): void
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
            ]
        ]);

        $this->expectException(DriverNotInstalledException::class);
        M_User::getConnectionName();
    }

    public function testDriverNotRegistered(): void
    {
        Model::refresh();
        Fetcher::fetchConfig();
        Fetcher::addConfig([
            'database' => [
                'connections' => [
                    'default' => [
                        'driver' => 'two'
                    ]
                ],
                'drivers' => [
                    'one' => D_One::class
                ]
            ]
        ]);

        $this->expectException(DriverNotInstalledException::class);
        M_User::getConnectionName();
    }
}
