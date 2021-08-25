<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Service\Queue;

use Bit9\SupervisorControllerBundle\Service\Queue\Monitor\MonitorInterface;
use Bit9\SupervisorControllerBundle\Exception\SupervisorControllerException;

class Monitor
{
    private Configuration $configuration;
    private Conductor $updateService;

    private array $monitors = [];

    public function __construct(Configuration $configuration, Conductor $updateService)
    {
        $this->configuration = $configuration;
        $this->updateService = $updateService;
    }

    private function isTypeAllowed(string $type) : bool
    {
        $allowed = [
            MonitorInterface::MONITOR_RABBITMQ,
        ];

        return in_array($type, $allowed);
    }

    private function getMonitor(string $type) : MonitorInterface
    {
        if (empty($this->monitors[$type])) {
            throw new SupervisorControllerException(sprintf('Monitor type "%s" has not been defined.', $type));
        }

        return $this->monitors[$type];
    }

    public function addMonitor(MonitorInterface $monitor) : void
    {
        if (!$this->isTypeAllowed($monitor->identifier())) {
            throw new SupervisorControllerException(sprintf('Monitor type "%s" is not allowed.', $monitor->identifier()));
        }

        $this->monitors[$monitor->identifier()] = $monitor;
    }

    public function execute(string $queue) : int
    {
        $config = $this->configuration->execute($queue);
        $type = $config['type'];

        if (!$this->isTypeAllowed($type)) {
            throw new SupervisorControllerException(sprintf('Monitor type "%s" is not allowed.', $type));
        }

        return $this->getMonitor($type)->check($config);
    }
}