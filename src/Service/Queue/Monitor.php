<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Service\Queue;

use Bit9\SupervisorControllerBundle\Service\Supervisor\ProgramUpdate;
use Bit9\SupervisorControllerBundle\Service\Queue\Monitor\MonitorInterface;
use Bit9\SupervisorControllerBundle\Exception\SupervisorControllerException;

class Monitor
{
    public const MONITOR_RABBITMQ = 'rabbitmq';

    private Configuration $configuration;
    private ProgramUpdate $updateService;

    private array $monitors = [];

    public function __construct(Configuration $configuration, ProgramUpdate $updateService)
    {
        $this->configuration = $configuration;
        $this->updateService = $updateService;
    }

    private function isTypeAllowed(string $type) : bool
    {
        $allowed = [
            self::MONITOR_RABBITMQ,
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

    public function addMonitor(string $type, MonitorInterface $monitor) : void
    {
        if (!$this->isTypeAllowed($type)) {
            throw new SupervisorControllerException(sprintf('Monitor type "%s" is not allowed.', $type));
        }

        $this->monitors[$type] = $monitor;
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