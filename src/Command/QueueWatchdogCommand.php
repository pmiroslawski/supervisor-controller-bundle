<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Command;

use Bit9\SupervisorControllerBundle\Service\Queue\Watchdog;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class QueueWatchdogCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'supervisor:queue:watchdog';

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Command checks queue and passed readed number of elements into queue conductor.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows to run/stop (or do nothing) some number of processes depends on passed number of queue elements and defined thresholds.')

            ->addArgument('queue', InputArgument::REQUIRED, 'Queue name')
        ;
    }

    private Watchdog $service;

    public function __construct(string $name = null, Watchdog $service)
    {
        parent::__construct($name);

        $this->service = $service;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $num = $this->service->execute($input->getArgument('queue'));

        $output->writeln(sprintf('<info>Num of processes: %s</info>', $num));

        return Command::SUCCESS;
    }
}