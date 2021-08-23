<?php

namespace Bit9\SupervisorControllerBundle\Service\Supervisor;

use HelpPC\Bundle\SupervisorBundle\Manager\SupervisorManager;
use Supervisor\Process;

class ProcessesStart
{
    private SupervisorManager $supervisorManager;

    public function __construct(SupervisorManager $supervisorManager)
    {
        $this->supervisorManager = $supervisorManager;
    }

    public function execute(array $processes, int $start_num) : array
    {
        $started = 0;
        $running = [];
        foreach ($processes as $host => $list) {
            $supervisor = $this->supervisorManager->getSupervisorByKey($host);

            foreach($list as $status => $plist) {
                if (in_array($status, [Process::STOPPED, Process::STOPPING])) {
                    foreach($plist as $process) {
                        $supervisor->startProcess(sprintf("%s:%s", $process['group'], $process->getName()));
                        $running[] = $process;
                        if (++$started >= $start_num) {
                            return $running;
                        }
                    }
                }
            }
        }

        return $running;
    }
}