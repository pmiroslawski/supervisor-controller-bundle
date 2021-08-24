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
    protected int $stopped_num;

    /**
     * Number of total running processes
     */
    protected int $num;

    public function __construct(string $program_name, int $stopped_num, int $num)
    {
        $this->program_name = $program_name;
        $this->stopped_num = $stopped_num;
        $this->num = $num;
    }

    public function getProgramName(): string
    {
        return $this->program_name;
    }

    public function getStoppedProcessesNum(): int
    {
        return $this->stopped_num;
    }

    public function getProcessesNum(): int
    {
        return $this->num;
    }
}