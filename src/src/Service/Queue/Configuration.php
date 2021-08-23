<?php

namespace Bit9\SupervisorControllerBundle\Service\Queue;

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
        if (empty($queues[$queue])) {
            throw new \InvalidArgumentException(sprintf('The queue name "%s" has not been configured.', $queue));
        }

        return $queues[$queue];
    }
}