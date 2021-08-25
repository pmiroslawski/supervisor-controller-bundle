<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Service\Queue\Monitor;

interface MonitorInterface
{
    public function check(array $config) : int;
}