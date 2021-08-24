<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Service\Supervisor;

use HelpPC\Bundle\SupervisorBundle\Manager\SupervisorManager;
use Supervisor\Process;

class ProcessesStop
{
    private SupervisorManager $supervisorManager;

    public function __construct(SupervisorManager $supervisorManager)
    {
        $this->supervisorManager = $supervisorManager;
    }

    public function execute(array $processes, int $stop_num) : array
    {
        $stopped = 0;
        $running = [];
        foreach ($processes as $host => $list) {
            $supervisor = $this->supervisorManager->getSupervisorByKey($host);

            foreach($list as $status => $plist) {
                if (in_array($status, [Process::RUNNING, Process::STARTING])) {
                    $plist = array_reverse($plist);
                    foreach($plist as $process) {
                        $supervisor->stopProcess(sprintf("%s:%s", $process['group'], $process->getName()));
                        $running[] = $process;
                        if (++$stopped >= $stop_num) {
                            return $running;
                        }
                    }
                }
            }
        }

        return $running;
    }
}