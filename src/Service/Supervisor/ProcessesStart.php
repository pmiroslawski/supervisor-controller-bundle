<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Service\Supervisor;

use Bit9\SupervisorControllerBundle\Event\ProcessStartedEvent;
use HelpPC\Bundle\SupervisorBundle\Manager\SupervisorManager;
use Psr\EventDispatcher\EventDispatcherInterface;
use Supervisor\Process;
use Bit9\SupervisorControllerBundle\Exception\SupervisorControllerException;

class ProcessesStart
{
    private SupervisorManager $supervisorManager;
    private EventDispatcherInterface $dispatcher;

    public function __construct(SupervisorManager $supervisorManager, EventDispatcherInterface $dispatcher)
    {
        $this->supervisorManager = $supervisorManager;
        $this->dispatcher = $dispatcher;
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
                        $process_name = sprintf("%s:%s", $process['group'], $process->getName());

                        try {
                            if ($supervisor->startProcess($process_name)) {
                                $this->dispatcher->dispatch(new ProcessStartedEvent($process_name));
                            }
                        }
                        catch (\Exception $e) {
                            throw new SupervisorControllerException($e->getMessage(), $e->getCode(), $e);
                        }

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