<?php

namespace Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tasks;

use Mashbo\Mashbot\TaskRunner\TaskContext;

class SyncFromRemoteEnv
{
    public function __invoke(TaskContext $context, array $remote, array $databases, array $paths)
    {
        $runner = $context->taskRunner();

        foreach ($databases as $db) {
            $runner->invoke('env:database:sync:pull', [
                'remote' => [
                    'database' => $db['remote'],
                    'connection' => $remote
                ],
                'local' => ['database' => $db['local']]
            ]);
        }

        foreach ($paths as $path) {
            $runner->invoke('env:files:sync:pull', [
                'remote' => [
                    'path' => $path['remote'],
                    'connection' => $remote
                ],
                'local' => [
                    'path' => $path['local']
                ]
            ]);
        }
    }
}