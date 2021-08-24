<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ProcessesStartedEvent extends Event
{
    public const NAME = 'supervisor_processes.started';

    protected string $program_name;

    /**
     * Number of new started processes
     */
    protected int $num_started;

    /**
     * Number of total running processes
     */
    protected int $num_running;

    public function __construct(string $program_name, int $started, int $running)
    {
        $this->program_name = $program_name;
        $this->num_started = $started;
        $this->num_running = $running;
    }

    public function getProgramName(): string
    {
        return $this->program_name;
    }

    public function getStartedProcessesNum(): int
    {
        return $this->num_started;
    }

    public function getProcessesNum(): int
    {
        return $this->num_running;
    }
}