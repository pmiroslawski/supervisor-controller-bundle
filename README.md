# PHP Supervisor Controller Bundle

Symfony bundle which manages supervisor processes automatically based on given thresholds configuration. Bundle is implemented using the [helppc/supervisor-bundle](https://github.com/helppc/supervisor-bundle) library.

## Status

This package is currently in the active development.


## Requirements

* [PHP 7.4](http://php.net/releases/7_4_0.php) or greater
* [Symfony 5.x](https://symfony.com/roadmap/5.0)


## Installation

1. Require the bundle and a PSR 7/17 implementation with Composer:

    ```sh
    composer require pmiroslawski/supervisor-controller-bundle
    ```
1. Create the bundle configuration file under `config/packages/bit9_supervisor_controller.yaml`. Here is a reference configuration file:

    ```yaml
    bit9_supervisor_controller:
    queues:
       - name: messages
         consumer: message_consumer
         numprocs: 50               # run 50 if more than 10000
         thresholds:
            - messages: 100         # run 3 processes if less than 100 elements in queue 
              num: 3
            - messages: 1000        # run 5 processes if less than 1000 elements in queue 
              num: 5
            - messages: 10000       # run 10 processes if less than 10000 elements in queue 
              num: 10
    ```
1. Enable the bundle in `config/bundles.php` by adding it to the array:

    ```php
    Bit9\SupervisorControllerBundle\Bit9SupervisorControllerBundle::class => ['all' => true],
    ```

## Usage

The bundle provides an extra public service which can number of processes for spcified queue

```
    Bit9\SupervisorControllerBundle\Service\Queue\Monitor
```

That service has only one method `execute` which get as arguments name of the queue and current number of messages in given queue. Base on thresholds defined in configuration for specified queue service starting extra processes or stoping already running one which consume a given queue.

Above service has been implemented in `supervisor:queue:monitor` command also delivered in this bundle.

Additionally, bundle provides bunch of extra commands in supervisor namespace which can help to manage configuration and let you easy execute basic commands:

```
    supervisor-controller
    supervisor-controller:program:status  Get the supervisor program prcocesses statuses
    supervisor-controller:program:update  Run given number of processes for specified program
    supervisor-controller:queue:config    Get configuration for the given queue name
    supervisor-controller:queue:monitor   Monitor a specified queue name
```

## Events

The bundle provides events and dispatches them in some specified situations. There are two groups of events

- when bundle does start/stop processes one of below events is dispatching:
```
    Bit9\SupervisorControllerBundle\Event\ProcessesStartedEvent 
    Bit9\SupervisorControllerBundle\Event\ProcessesStoppedEvent 
```
Both of them contains 
    - consumer name (the group name) 
    - number of started/stopped processes
    - total number of running processes (after operation)

- when bundle does start/stop a specified single process one of below events is dispatching:
```
    Bit9\SupervisorControllerBundle\Event\ProcessStartedEvent 
    Bit9\SupervisorControllerBundle\Event\ProcessStoppedEvent 
```
Both of them contains 
    - exact process name (the same as in supervisor)
    - timestamp of started/stopped given process


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.