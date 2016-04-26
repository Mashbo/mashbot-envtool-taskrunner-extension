<?php

namespace Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tasks;

use Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Process\BlockingProcessRunner;
use Mashbo\Mashbot\TaskRunner\TaskContext;
use Symfony\Component\Process\Process;

/**
 * env:database:sync:pull
 *   remote:
 *     connection:
 *       user: deploy
 *       host: host.example.com
 *     database:
 *       user: remotedbuser
 *       name: remotedbname
 *   local:
 *     database:
 *       user: localdbuser
 *       name: localdbname
 *
 */
class ImportRemoteDatabase
{
    public function __invoke(TaskContext $context, $remote, $local)
    {
        $runner = $context->taskRunner();

        // This roughly translates to: ssh user@host "mysqldump -u dbuser dbname" | mysql -u localdbuser localdbname

        $exportOverSshCommand   = $runner->invoke('ssh:command:build', [
            'connection'    => $remote['connection'],
            'command'       => $runner->invoke('env:database:mysql:dump:command:build', [
                'host'  => '127.0.0.1',
                'user'  => $remote['database']['user'],
                'name'  => $remote['database']['name']
            ])
        ]);

        $importToLocalDbCommand = $runner->invoke('env:database:mysql:import:command:build', [
            'host'  => '127.0.0.1',
            'user'  => $local['database']['user'],
            'name'  => $local['database']['name']
        ]);

        return $runner->invoke(
            'process:command:run', [
                'command' => $runner->invoke('process:pipe', [
                    'from'  => $exportOverSshCommand,
                    'to'    => $importToLocalDbCommand
                ]),
                'directory' => sys_get_temp_dir()
            ]
        );
    }

}
