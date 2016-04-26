<?php

namespace Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tests;

use Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\EnvToolTaskRunnerExtension;
use Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tasks\ImportRemoteDatabase;
use Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tasks\Mysql\BuildMysqlDumpCommand;
use Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tasks\Mysql\BuildMysqlImportCommand;
use Mashbo\Mashbot\Extensions\ProcessTaskRunnerExtension\Process\BlockingProcessRunner;
use Mashbo\Mashbot\TaskRunner\TaskRunner;

class EnvToolTaskRunnerExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testItAddsTasks()
    {
        $taskRunner = $this
            ->getMockBuilder(TaskRunner::class)
            ->disableOriginalConstructor()
            ->getMock();

        $taskRunner
            ->expects($this->at(0))
            ->method('add')
            ->with('env:database:sync:pull', $this->callback(function($arg) {
                return $arg instanceof ImportRemoteDatabase;
            }));

        $taskRunner
            ->expects($this->at(1))
            ->method('add')
            ->with('env:database:mysql:import:command:build', $this->callback(function($arg) {
                return $arg instanceof BuildMysqlImportCommand;
            }));

        $taskRunner
            ->expects($this->at(2))
            ->method('add')
            ->with('env:database:mysql:dump:command:build', $this->callback(function($arg) {
                return $arg instanceof BuildMysqlDumpCommand;
            }));

        $sut = new EnvToolTaskRunnerExtension();
        $sut->amendTasks($taskRunner);
    }
}
