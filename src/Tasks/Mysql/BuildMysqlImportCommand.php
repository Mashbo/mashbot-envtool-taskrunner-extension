<?php

namespace Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tasks\Mysql;

class BuildMysqlImportCommand
{
    public function __invoke($host, $user, $name)
    {
        return sprintf("mysql -u %s -h %s %s", escapeshellarg($user), escapeshellarg($host), escapeshellarg($name));
    }
}
