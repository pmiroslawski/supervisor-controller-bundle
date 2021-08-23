<?php

namespace Bit9\SupervisorControllerBundle\Service\Supervisor;

use Supervisor\Process;

class ProgramUpdate
{
    private ProgramStatus $statusService;

    private ProcessesStart $processesStartService;
    private ProcessesStop $processesStopService;

    public function __construct(ProgramStatus $status, ProcessesStart $processesStartService, ProcessesStop $processesStopService)
    {
        $this->statusService = $status;
        $this->processesStartService = $processesStartService;
        $this->processesStopService = $processesStopService;
    }

    public function execute(string $program, int $running = 3) : array
    {
        $states = $this->statusService->execute($program);

        $active = 0;
        foreach($states[$program] as $server) {
            $active += $this->countActiveProcessNum($server);
        }

        if ($active > $running) {
            $this->processesStopService->execute($states[$program], $active - $running);
        }

        if ($active < $running) {
            $this->processesStartService->execute($states[$program], $running - $active);
        }

        return [];
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