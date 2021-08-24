<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Command;

use Bit9\SupervisorControllerBundle\Service\Queue\Monitor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class QueueMonitorCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'supervisor:queue:monitor';

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Monitor a specified queue name')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows monitor spcified queue and strat/stop extra consumers if needed.')

            ->addArgument('queue', InputArgument::REQUIRED, 'Queue name')
            ->addArgument('messages_num', InputArgument::REQUIRED, 'Number of messages in queue')
        ;
    }

    private Monitor $service;

    public function __construct(string $name = null, Monitor $service)
    {
        parent::__construct($name);

        $this->service = $service;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $num = $this->service->execute($input->getArgument('queue'), (int) $input->getArgument('messages_num'));

        $output->writeln(sprintf('<info>Num of processes: %s</info>', $num));

        return Command::SUCCESS;
    }
}