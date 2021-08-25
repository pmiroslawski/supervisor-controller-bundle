<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Command;

use Bit9\SupervisorControllerBundle\Service\Queue\Monitor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class QueueCheckCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'supervisor:queue:check';

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Check a specified queue to see number of elements.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows to check specified queue to get info about elements number.')

            ->addArgument('queue', InputArgument::REQUIRED, 'Queue name')
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
        $queue = $input->getArgument('queue');

        $num = $this->service->execute($queue);

        $output->writeln(sprintf('<info>Queue "%s" - number of elements is %s</info>', $queue, $num));

        return Command::SUCCESS;
    }
}