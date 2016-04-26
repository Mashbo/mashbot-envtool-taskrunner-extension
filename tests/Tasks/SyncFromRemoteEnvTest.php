<?php

namespace Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tests\Tasks;

use Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tasks\SyncFromRemoteEnv;
use Mashbo\Mashbot\TaskRunner\Tests\Functional\TaskTest;

class SyncFromRemoteEnvTest extends TaskTest
{
    public function test_nothing_happens_with_no_databases_or_files()
    {
        $this->invoke([
            'remote' => [
                'host' => 'example.com',
                'user' => 'remote_user'
            ],
            'databases' => [],
            'paths'     => []
        ]);
    }

    public function test_databases_are_pulled()
    {
        $this->runner->invoke('env:database:sync:pull', [
            'remote' => [
                'connection' => [
                    'host' => 'example.com',
                    'user' => 'remote_user'
                ],
                'database' => [
                    'name' => 'remote_db_name_1',
                    'user' => 'remote_db_user_1'
                ]
            ],
            'local' => [
                'database' => [
                    'name' => 'local_db_name_1',
                    'user' => 'local_db_user_1'
                ]
            ]
        ])->shouldBeCalled();
        $this->runner->invoke('env:database:sync:pull', [
            'remote' => [
                'connection' => [
                    'host' => 'example.com',
                    'user' => 'remote_user'
                ],
                'database' => [
                    'name' => 'remote_db_name_2',
                    'user' => 'remote_db_user_2'
                ]
            ],
            'local' => [
                'database' => [
                    'name' => 'local_db_name_2',
                    'user' => 'local_db_user_2'
                ]
            ]
        ])->shouldBeCalled();

        $this->invoke([
            'remote' => [
                'host' => 'example.com',
                'user' => 'remote_user'
            ],
            'databases' => [
                [
                    'remote' => [
                        'name' => 'remote_db_name_1',
                        'user' => 'remote_db_user_1'
                    ],
                    'local' => [
                        'name' => 'local_db_name_1',
                        'user' => 'local_db_user_1'
                    ]
                ],
                [
                    'remote' => [
                        'name' => 'remote_db_name_2',
                        'user' => 'remote_db_user_2'
                    ],
                    'local' => [
                        'name' => 'local_db_name_2',
                        'user' => 'local_db_user_2'
                    ]
                ]

            ],
            'paths'     => []
        ]);
    }

    public function test_files_are_pulled()
    {
        $this->runner->invoke('env:files:sync:pull', [
            'remote' => [
                'connection' => [
                    'host' => 'example.com',
                    'user' => 'remote_user'
                ],
                'path' => '/remote/path'
            ],
            'local' => [
                'path' => '/local/path'
            ]
        ])->shouldBeCalled();
        $this->runner->invoke('env:files:sync:pull', [
            'remote' => [
                'connection' => [
                    'host' => 'example.com',
                    'user' => 'remote_user'
                ],
                'path' => '/remote/path/2'
            ],
            'local' => [
                'path' => '/local/path/2'
            ]
        ])->shouldBeCalled();

        $this->invoke([
            'remote' => [
                'host' => 'example.com',
                'user' => 'remote_user'
            ],
            'databases' => [],
            'paths'     => [
                [
                    'remote' => '/remote/path',
                    'local'  => '/local/path'
                ],
                [
                    'remote' => '/remote/path/2',
                    'local'  => '/local/path/2'
                ]
            ]
        ]);
    }

    /**
     * @return callable
     */
    protected function getTask()
    {
        return new SyncFromRemoteEnv;
    }
}