<?php

namespace Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tests\Tasks\Mysql;

use Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tasks\Mysql\BuildMysqlDumpCommand;

class BuildMysqlDumpCommandTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_builds_with_username_host_and_db_name()
    {
        $sut = new BuildMysqlDumpCommand;
        $this->assertEquals("mysqldump --single-transaction -u 'user' -h 'localhost' 'db_name'", $sut->__invoke('localhost', 'user', 'db_name'));
    }
}