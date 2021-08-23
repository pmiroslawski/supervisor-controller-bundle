<?php

namespace Bit9\SupervisorControllerBundle\Service\Supervisor;

use HelpPC\Bundle\SupervisorBundle\Manager\SupervisorManager;
use Supervisor\Supervisor;

class ProgramStatus
{
    private SupervisorManager $supervisorManager;

    public function __construct(SupervisorManager $supervisorManager)
    {
        $this->supervisorManager = $supervisorManager;
    }

    public function execute(string $program) : array
    {
        $programs = [];
        foreach($this->supervisorManager->getSupervisors() as $key => $supervisor) {
            $processes = $this->getProcessesStates($supervisor, $program);
            if (!empty($processes)) {
                $programs[$program][$key] = $this->getProcessesStates($supervisor, $program);
            }
        }

        if (empty($programs)) {
            throw new \RuntimeException(sprintf("Program '%s' has not beed defined in any supervisor instance.", $program));
        }

        return $programs;
    }

    private function getProcessesStates(Supervisor $supervisor, string $program) : array
    {
        $states = [];

        $processes = $supervisor->getAllProcesses();
        foreach($processes as $process) {
            $payload = $process->getPayload();
            if ($program == $payload['group']) {
                $states[$payload['state']][] = $process;
            }
        }

        return $states;
    }
}