<?php

namespace Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tasks\Mysql;

class BuildMysqlDumpCommand
{
    public function __invoke($host, $user, $name)
    {
        return sprintf("mysqldump --single-transaction -u %s -h %s %s", escapeshellarg($user), escapeshellarg($host), escapeshellarg($name));
    }
}
