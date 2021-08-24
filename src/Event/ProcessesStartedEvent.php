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
    protected int $started_num;

    /**
     * Number of total running processes
     */
    protected int $num;

    public function __construct(string $program_name, int $started_num)
    {
        $this->program_name = $program_name;
        $this->started_num = $started_num;
    }

    public function getProgramName(): string
    {
        return $this->program_name;
    }

    public function getStartedProcessesNum(): int
    {
        return $this->started_num;
    }

    public function getProcessesNum(): int
    {
        return $this->num;
    }
}