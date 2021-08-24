<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Service\Supervisor;

use Bit9\SupervisorControllerBundle\Event\ProcessStoppedEvent;
use HelpPC\Bundle\SupervisorBundle\Manager\SupervisorManager;
use Psr\EventDispatcher\EventDispatcherInterface;
use Supervisor\Process;

class ProcessesStop
{
    private SupervisorManager $supervisorManager;
    private EventDispatcherInterface $dispatcher;

    public function __construct(SupervisorManager $supervisorManager, EventDispatcherInterface $dispatcher)
    {
        $this->supervisorManager = $supervisorManager;
        $this->dispatcher = $dispatcher;
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
                        $process_name = sprintf("%s:%s", $process['group'], $process->getName());

                        if ($supervisor->stopProcess($process_name)) {
                            $this->dispatcher->dispatch(new ProcessStoppedEvent($process_name));
                        }

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