<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Service\Queue;

class Watchdog
{
    private Monitor $monitorService;
    private Conductor $conductorService;

    private array $monitors = [];

    public function __construct(Monitor $monitorService, Conductor $conductorService)
    {
        $this->monitorService = $monitorService;
        $this->conductorService = $conductorService;
    }

    public function execute(string $queue) : int
    {
        return $this->conductorService->execute(
            $queue, $this->monitorService->execute($queue)
        );
    }
}