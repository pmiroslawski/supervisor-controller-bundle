<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Command;

use Bit9\SupervisorControllerBundle\Service\Queue\Conductor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class QueueConductorCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'supervisor:queue:conductor';

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Command run/stop some number of processes depends on queue\'s elements number.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows to run/stop (or do nothing) some number of processes depends on passed number of queue elements and defined thresholds.')

            ->addArgument('queue', InputArgument::REQUIRED, 'Queue name')
            ->addArgument('messages_num', InputArgument::REQUIRED, 'Number of messages in queue')
        ;
    }

    private Commander $service;

    public function __construct(string $name = null, Conductor $service)
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