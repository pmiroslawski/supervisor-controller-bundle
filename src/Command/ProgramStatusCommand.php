<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Command;

use Bit9\SupervisorControllerBundle\Service\Supervisor\ProgramStatus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Supervisor\Process;

class ProgramStatusCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'supervisor:program:status';

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Get the supervisor program prcocesses statuses')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to check number of processes and their states for given program name.')

            ->addArgument('name', InputArgument::REQUIRED, 'Program name')
        ;
    }

    private ProgramStatus $service;

    public function __construct(string $name = null, ProgramStatus $service)
    {
        parent::__construct($name);

        $this->service = $service;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $statuses = $this->service->execute($input->getArgument('name'));

        $table = new Table($output);

        foreach ($statuses as $hosts) {
            foreach ($hosts as $host => $processes) {
                $table->setHeaders([
                    [new TableCell('Host: ' . $host, ['colspan' => 2])],
//                     ['Process name', 'Status']
                ]);

                $rows = [];
                foreach ($processes as $status => $list) {
                    foreach ($list as $process) {
                        $rows[] = [
                            $process,
                            $this->readable($status),
                        ];
                    }
                }
                $table->setRows($rows);
            }
        }

        $table->render();

        return Command::SUCCESS;
    }

    private function readable(int $status) : string
    {
        switch($status) {
            case Process::RUNNING:
                return 'RUNNING';
            case Process::STOPPED:
                return 'STOPPED';
            case Process::BACKOFF:
                return 'BACKOFF';
            case Process::EXITED:
                return 'EXITED';
            case Process::FATAL:
                return 'FATAL';
            case Process::STARTING:
                return 'STARTING';
            case Process::STOPPING:
                return 'STOPPING';
            case Process::UNKNOWN:
                return 'UNKNOWN';
        }

        return '';
    }
}