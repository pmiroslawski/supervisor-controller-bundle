<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Command;

use Bit9\SupervisorControllerBundle\Service\Supervisor\ProgramUpdate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ProgramUpdateCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'supervisor:program:update';

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Run given number of processes for specified program')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to execute specified number of processes for given program.')

            ->addArgument('name', InputArgument::REQUIRED, 'Program name')
            ->addArgument('numprocs', InputArgument::REQUIRED, 'Number of running processes')
        ;
    }

    private ProgramUpdate $service;

    public function __construct(string $name = null, ProgramUpdate $service)
    {
        parent::__construct($name);

        $this->service = $service;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->service->execute($input->getArgument('name'), (int) $input->getArgument('numprocs'));

        return Command::SUCCESS;
    }
}