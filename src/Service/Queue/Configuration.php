<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Service\Queue;

use Bit9\SupervisorControllerBundle\Exception\SupervisorControllerException;

class Configuration
{
    private $queues;

    public function __construct($queues)
    {
        $this->queues = $queues;
    }

    public function execute(?string $queue) : array
    {
        if ($queue === null) {
            return $this->queues;
        }

        $queues = array_column($this->queues, null, 'name');
        if ($queue === null) {
            return $queues;
        }

        if (empty($queues[$queue])) {
            throw new SupervisorControllerException(sprintf('The queue name "%s" has not been configured.', $queue));
        }

        return $queues[$queue];
    }
}