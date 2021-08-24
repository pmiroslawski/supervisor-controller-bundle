<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ProcessesStoppedEvent extends Event
{
    public const NAME = 'supervisor_processes.stopped';

    protected string $program_name;

    /**
     * Number of stopped processes
     */
    protected int $num_stopped;

    /**
     * Number of total running processes
     */
    protected int $num_running;

    public function __construct(string $program_name, int $stopped, int $running)
    {
        $this->program_name = $program_name;
        $this->num_stopped = $stopped;
        $this->num_running = $running;
    }

    public function getProgramName(): string
    {
        return $this->program_name;
    }

    public function getStoppedProcessesNum(): int
    {
        return $this->num_stopped;
    }

    public function getProcessesNum(): int
    {
        return $this->num_running;
    }
}