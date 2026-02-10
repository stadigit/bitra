<?php

namespace Legacy\Api\Methods\Deploy;

use Legacy\Api\Api;

class GitStatus extends Api
{
    public function init()
    {
        // Простейшая защита для GitHub
        $token = $_GET['token'] ?? '';

        if ($token !== 'your-token') {
            $this->setFields([
                'status' => 'forbidden',
                'code'   => 403
            ]);
            return;
        }

        $projectRoot = $_SERVER['DOCUMENT_ROOT'];

        $cmd = 'cd ' . escapeshellarg($projectRoot) . ' && git status --porcelain';

        $output = [];
        $exitCode = 0;

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            $this->setFields([
                'status' => 'error',
                'code'   => 2,
                'error'  => 'git command failed'
            ]);
            return;
        }

        if (empty($output)) {
            $this->setFields([
                'status' => 'clean',
                'code'   => 0
            ]);
        } else {
            $this->setFields([
                'status' => 'dirty',
                'code'   => 1,
                'files'  => $output
            ]);
        }
    }
}
