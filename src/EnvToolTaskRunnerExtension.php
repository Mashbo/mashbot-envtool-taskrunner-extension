<?php

namespace Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension;

use Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tasks\CopyFilesFromRemote;
use Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tasks\ImportRemoteDatabase;
use Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tasks\Mysql\BuildMysqlDumpCommand;
use Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tasks\Mysql\BuildMysqlImportCommand;
use Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tasks\SyncFromRemoteEnv;
use Mashbo\Mashbot\TaskRunner\TaskRunner;
use Mashbo\Mashbot\TaskRunner\TaskRunnerExtension;

class EnvToolTaskRunnerExtension implements TaskRunnerExtension
{

    public function __construct()
    {
    }

    public function amendTasks(TaskRunner $taskRunner)
    {
        $taskRunner->add('env:database:sync:pull',                  new ImportRemoteDatabase());
        $taskRunner->add('env:files:sync:pull',                     new CopyFilesFromRemote());
        $taskRunner->add('env:database:mysql:import:command:build', new BuildMysqlImportCommand());
        $taskRunner->add('env:database:mysql:dump:command:build',   new BuildMysqlDumpCommand());

        $taskRunner->add('env:sync:pull',                           new SyncFromRemoteEnv());
    }
}
