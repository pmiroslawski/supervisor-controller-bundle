<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ProcessStartedEvent extends Event
{
    public const NAME = 'supervisor_process.started';

    protected string $process_name;

    protected int $timestamp;

    public function __construct(string $process_name, ?int $timestamp = null)
    {
        $this->process_name = $process_name;

        if ($timestamp === null) {
            $timestamp = time();
        }

        $this->timestamp = $timestamp;
    }

    public function getProcessName(): string
    {
        return $this->process_name;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }
}