<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Service\Queue\Monitor;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Bit9\SupervisorControllerBundle\Exception\SupervisorControllerException;

class RabbitMqMonitor implements MonitorInterface
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function identifier() : string
    {
        return MonitorInterface::MONITOR_RABBITMQ;
    }

    public function check(array $config) : int
    {
        $url = sprintf("%s/%s", $config['host'], $config['name']);

        try {
            $content = $this->doRequest($url);
        }
        catch (\Exception $e) {
            throw new SupervisorControllerException(sprintf('Can\'t connect to "%s" queue (%s)', $config['name'], $url), null, $e);
        }

        if (!isset($content['messages'])) {
            throw new SupervisorControllerException(sprintf('The "message" key has not been found in returned response: %s', json_encode($content)));
        }

        return (int) $content['messages'];
    }

    private function doRequest(string $url) : array
    {
        $response = $this->httpClient->request('GET', $url);

        $statusCode = $response->getStatusCode();
        if ($statusCode != 200) {
            throw new SupervisorControllerException(sprintf('Response code must be 200 but "%s" returned.', $statusCode));
        }

        $contentType = $response->getHeaders()['content-type'][0];
        if ($contentType != 'application/json') {
            throw new SupervisorControllerException(sprintf('Content type of response must be "application/json" but "%s" returned.', $contentType));
        }

        return $response->toArray();
    }
}