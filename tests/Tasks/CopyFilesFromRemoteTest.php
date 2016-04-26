<?php

namespace Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tests\Tasks;

use Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tasks\CopyFilesFromRemote;
use Mashbo\Mashbot\Extensions\ProcessTaskRunnerExtension\Command\CommandResult;
use Mashbo\Mashbot\TaskRunner\Tests\Functional\TaskTest;

class CopyFilesFromRemoteTest extends TaskTest
{

    public function test_it_builds_and_runs_rsync_command()
    {
        $this->runner->invoke('process:command:run', [
            'command' => "rsync -aq -e 'ssh -p2345' 'remote_user@remote.example.com':'/path/to/remote/files/' '/path/to/local/files/'",
            'directory' => sys_get_temp_dir()
        ])
            ->shouldBeCalled()
            ->willReturn(new CommandResult(0, '', ''))
        ;
        $result = $this->invoke([
            'remote' => [
                'connection' => [
                    'host' => 'remote.example.com',
                    'user' => 'remote_user',
                    'port' => 2345
                ],
                'path' => '/path/to/remote/files/'
            ],
            'local' => [
                'path' => '/path/to/local/files/'
            ]
        ]);

        $this->assertEquals(new CommandResult(0, '', ''), $result);
    }

    public function test_remote_port_can_be_omitted()
    {
        $this->runner->invoke('process:command:run', [
            'command' => "rsync -aq 'remote_user@remote.example.com':'/path/to/remote/files/' '/path/to/local/files/'",
            'directory' => sys_get_temp_dir()
        ])
            ->shouldBeCalled()
            ->willReturn(new CommandResult(0, '', ''))
        ;
        $result = $this->invoke([
            'remote' => [
                'connection' => [
                    'host' => 'remote.example.com',
                    'user' => 'remote_user',
                ],
                'path' => '/path/to/remote/files/'
            ],
            'local' => [
                'path' => '/path/to/local/files/'
            ]
        ]);

        $this->assertEquals(new CommandResult(0, '', ''), $result);
    }

    public function test_it_adds_trailing_slashes_if_not_present()
    {
        $this->runner->invoke('process:command:run', [
            'command' => "rsync -aq -e 'ssh -p2345' 'remote_user@remote.example.com':'/path/to/remote/files/' '/path/to/local/files/'",
            'directory' => sys_get_temp_dir()
        ])
            ->shouldBeCalled()
            ->willReturn(new CommandResult(0, '', ''))
        ;
        $result = $this->invoke([
            'remote' => [
                'connection' => [
                    'host' => 'remote.example.com',
                    'user' => 'remote_user',
                    'port' => 2345
                ],
                'path' => '/path/to/remote/files'
            ],
            'local' => [
                'path' => '/path/to/local/files'
            ]
        ]);

        $this->assertEquals(new CommandResult(0, '', ''), $result);
    }

    /**
     * @return callable
     */
    protected function getTask()
    {
        return new CopyFilesFromRemote;
    }
}
