<?php

namespace Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tests\Tasks;

use Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Process\BlockingProcessRunner;
use Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tasks\ImportRemoteDatabase;
use Mashbo\Mashbot\Extensions\ProcessTaskRunnerExtension\Command\Command;
use Mashbo\Mashbot\Extensions\ProcessTaskRunnerExtension\Command\CommandResult;
use Mashbo\Mashbot\TaskRunner\Tests\Functional\TaskTest;
use Prophecy\Argument;
use Symfony\Component\Process\Process;

class ImportRemoteDatabaseTest extends TaskTest
{
    public function test_local_database_is_overwritten()
    {
        $mysqlDumpCommand = new Command('mysqldump -u remote_db_user -h 127.0.0.1 remote_db_name');
        $mysqlImportCommand = new Command('mysql -u local_db_user -h 127.0.0.1 local_db_user');
        $mysqlDumpOverSshCommand = new Command("ssh remote_user@remote.example.com '{$mysqlDumpCommand->getCommandLine()}'");

        $this->runner->invoke('env:database:mysql:dump:command:build', [
            'host' => '127.0.0.1',
            'user' => 'remote_db_user',
            'name' => 'remote_db_name'
        ])->shouldBeCalled()->willReturn($mysqlDumpCommand);

        $this->runner->invoke('env:database:mysql:import:command:build', [
            'host' => '127.0.0.1',
            'user' => 'local_db_user',
            'name' => 'local_db_name'
        ])->shouldBeCalled()->willReturn($mysqlImportCommand);

        $this->runner->invoke('ssh:command:build', [
            'connection' => [
                'user' => 'remote_user',
                'host' => 'remote.example.com'
            ],
            'command' => $mysqlDumpCommand
        ])->shouldBeCalled()->willReturn($mysqlDumpOverSshCommand);

        $commandToBeRun = new Command("{$mysqlDumpOverSshCommand->getCommandLine()} | {$mysqlImportCommand->getCommandLine()}");

        $this->runner->invoke('process:pipe', ['from' => $mysqlDumpOverSshCommand, 'to' => $mysqlImportCommand])
            ->shouldBeCalled()
            ->willReturn($commandToBeRun);

        $this->runner->invoke('process:command:run', Argument::that(function($actualCalledArg) use ($commandToBeRun) {
            $this->assertEquals($commandToBeRun, $actualCalledArg['command']);
            $this->assertArrayHasKey('directory', $actualCalledArg);

            return true;
        }))
            ->shouldBeCalled()
            ->willReturn(new CommandResult(0, '', ''));

        $result = $this->invoke([
            'remote' => [
                'connection' => [
                    'user' => 'remote_user',
                    'host' => 'remote.example.com'
                ],
                'database' => [
                    'user' => 'remote_db_user',
                    'name' => 'remote_db_name'
                ]
            ],
            'local' => [
                'database' => [
                    'user' => 'local_db_user',
                    'name' => 'local_db_name'
                ]
            ]
        ]);
        $this->assertEquals(new CommandResult(0, '', ''), $result);
    }

    /**
     * @return callable
     */
    protected function getTask()
    {
        return new ImportRemoteDatabase();
    }
}
