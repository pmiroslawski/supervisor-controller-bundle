<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Service\Supervisor;

use Supervisor\Process;
use Psr\EventDispatcher\EventDispatcherInterface;
use Bit9\SupervisorControllerBundle\Event\ProcessesStoppedEvent;
use Bit9\SupervisorControllerBundle\Event\ProcessesStartedEvent;

class ProgramUpdate
{
    public const PROGRAM_PROCESSES_INCREASED = 1;
    public const PROGRAM_PROCESSES_NOT_CHANGED = 0;
    public const PROGRAM_PROCESSES_DECREASED = -1;

    private ProgramStatus $statusService;
    private ProcessesStart $processesStartService;
    private ProcessesStop $processesStopService;

    private EventDispatcherInterface $dispatcher;

    public function __construct(ProgramStatus $status, ProcessesStart $processesStartService, ProcessesStop $processesStopService, EventDispatcherInterface $dispatcher)
    {
        $this->statusService = $status;
        $this->processesStartService = $processesStartService;
        $this->processesStopService = $processesStopService;

        $this->dispatcher = $dispatcher;
    }

    public function execute(string $program, int $running = 3) : int
    {
        $states = $this->statusService->execute($program);

        $active = 0;
        foreach($states[$program] as $server) {
            $active += $this->countActiveProcessNum($server);
        }

        if ($active > $running) {
            $num = $active - $running;

            $this->processesStopService->execute($states[$program], $num);
            $this->dispatcher->dispatch(new ProcessesStoppedEvent($states[$program], $num));

            return self::PROGRAM_PROCESSES_DECREASED;
        }

        if ($active < $running) {
            $num = $running - $active;

            $this->processesStartService->execute($states[$program], $num);
            $this->dispatcher->dispatch(new ProcessesStartedEvent($states[$program], $num));

            return self::PROGRAM_PROCESSES_INCREASED;
        }

        return self::PROGRAM_PROCESSES_NOT_CHANGED;
    }

    private function countActiveProcessNum(array $server): int
    {
        $total = 0;

        if (!empty($server[Process::STARTING])) {
            $total += count($server[Process::STARTING]);
        }

        if (!empty($server[Process::RUNNING])) {
            $total += count($server[Process::RUNNING]);
        }

        return $total;
    }
}