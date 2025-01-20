<?php


namespace App\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RuntimeFake extends Command
{
    const COMMAND = 'app:runtimeFake';
    const DESC = 'Runtime Fake';
    const TIME = 6000;

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESC)
        ;
    }

    public function __construct()
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Fake start. Sleep " . self::TIME);
        sleep(self::TIME);
        $output->writeln("UnSleep");
        return Command::SUCCESS;
    }
}
