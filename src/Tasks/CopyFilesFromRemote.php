<?php

namespace Mashbo\Mashbot\Extensions\EnvToolTaskRunnerExtension\Tasks;

use Mashbo\Mashbot\TaskRunner\TaskContext;

class CopyFilesFromRemote
{
    public function __invoke(TaskContext $context, $remote, $local)
    {
        $remoteConnection = $remote['connection'];

        $portSpec = array_key_exists('port', $remoteConnection)
            ? " -e 'ssh -p{$remoteConnection['port']}'"
            : '';

        return $context
            ->taskRunner()
            ->invoke(
                'process:command:run', [
                    'command' => sprintf(
                        "rsync -q%s %s:%s %s",
                        $portSpec,
                        escapeshellarg($remoteConnection['user'] . '@' . $remoteConnection['host']),
                        escapeshellarg($this->normalisePath($remote['path'])),
                        escapeshellarg($this->normalisePath($local['path']))
                    )
                ]
            );
    }

    private function normalisePath($path)
    {
        if (substr($path, -1, 1) !== '/') {
            return $path . '/';
        }
        return $path;
    }
}
