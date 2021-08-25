<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Service\Queue\Monitor;

use Http\Message\MessageFactory;
use Http\Client\HttpClient;

class RabbitMqMonitor implements MonitorInterface
{
    private HttpClient $httpClient;
    private MessageFactory $messageFactory;

    public function __construct(HttpClient $httpClient, MessageFactory $messageFactory)
    {
        $this->httpClient = $httpClient;
        $this->messageFactory = $messageFactory;
    }

    public function check(array $config) : int
    {
        $response = $this->httpClient->sendRequest(
            $this->messageFactory->createRequest('GET', $config['host'])
        );

        dump($response);
    }
}