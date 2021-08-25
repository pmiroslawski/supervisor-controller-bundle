<?php declare(strict_types=1);

namespace Bit9\SupervisorControllerBundle\Command;

use Bit9\SupervisorControllerBundle\Service\Queue\Configuration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;

class QueueConfigurationCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'supervisor:queue:config';

    protected function configure(): void
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Get configuration for the given queue name')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to check an app configuration by checking queue name to consumers names mapping.')

            ->addArgument('queue', InputArgument::OPTIONAL, 'Queue name')
        ;
    }

    private Configuration $service;

    public function __construct(string $name = null, Configuration $service)
    {
        parent::__construct($name);

        $this->service = $service;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $queue_name = $input->getArgument('queue');

        $consumers = $this->service->execute($queue_name);
        if ($queue_name) {
            $consumers = [$consumers];
        }

        $rows = [];
        foreach($consumers as $consumer) {
            $rows[] = [$consumer['name'], $consumer['consumer'] ?? '', $consumer['numprocs'] ?? ''];
            $rows[] = new TableSeparator();
            foreach ($consumer['thresholds'] as $threashold) {
                $rows[] = [
                    new TableCell(
                        (string) $threashold['messages'],
                        [
                            'colspan' => 2,
                            'style' => new TableCellStyle(['align' => 'right', 'cellFormat' => '<info>%s</info>'])
                        ]
                    ),
                    (string) $threashold['num']
                ];
            }
            $rows[] = new TableSeparator();
        }

        unset($rows[count($rows)-1]);

        $table = new Table($output);
        $table
            ->setHeaders(['Queue name', 'Consumer name', 'numprocs'])
            ->setRows($rows)
        ;
        $table->render();


        return Command::SUCCESS;
    }
}