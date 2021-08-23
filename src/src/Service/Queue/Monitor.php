<?php

namespace Bit9\SupervisorControllerBundle\Service\Queue;

use Bit9\SupervisorControllerBundle\Service\Supervisor\ProgramUpdate;

class Monitor
{
    private Configuration $configuration;
    private ProgramUpdate $updateService;

    public function __construct(Configuration $configuration, ProgramUpdate $updateService)
    {
        $this->configuration = $configuration;
        $this->updateService = $updateService;
    }

    public function execute(string $queue, int $messages_num) : int
    {
        $config = $this->configuration->execute($queue);

        $thresholds = [];
        foreach($config['thresholds'] as $threshold) {
            $thresholds[$threshold['messages']] = $threshold['num'];
        }

        ksort($thresholds);

        $set = $config['numprocs'];
        foreach ($thresholds as $num => $numproc) {
            if ($num >= $messages_num) {
                $set = $numproc;
                break;
            }
        }

        return $set;
    }
}