<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Price;
use App\Entity\Specification;
use App\Repository\CategoryRepository;
use App\Service\CategoryFilter;
use App\Service\GarbageProduct;
use App\Service\MarketExport;
use App\Service\OfferRead;
use App\Service\ParserRunCreator;
use App\Service\SheafCollection;
use App\Service\StatsSender;
use App\Util\Constant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\VarDumper\Cloner\Data;

class System extends Command
{
    const COMMAND = 'app:system';
    const DESC = 'System Layer';

    const GARBAGE = 'garbage';
    const SHEAF = 'sheaf';
    const CATEGORY = 'category';
    const MARKET = 'market';
    const CREATOR = 'create';
    const HELP = 'helper';
    const OFFER = 'offer';
    const STATS = 'stats';

    /**
     * @var CategoryFilter
     */
    private $cf;

    /**
     * @var SheafCollection
     */
    private $sc;

    /**
     * @var GarbageProduct
     */
    private $gp;

    /**
     * @var MarketExport
     */
    private $me;

    /**
     * @var ParserRunCreator
     */
    private $pc;

    /**
     * @var OfferRead
     */
    private $or;

    /**
     * @var StatsSender
     */
    private $ss;

    public function __construct(CategoryFilter $cf, SheafCollection $sc, GarbageProduct $gp, MarketExport $me, ParserRunCreator $pc, OfferRead $or, StatsSender $ss)
    {
        ini_set('memory_limit', '9024M');
        $this->cf = $cf;
        $this->sc = $sc;
        $this->gp = $gp;
        $this->me = $me;
        $this->pc = $pc;
        $this->or = $or;
        $this->ss = $ss;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESC)
            ->addOption(
                self::GARBAGE,
                null,
                InputOption::VALUE_NONE,
                'Need start Garbage Service?'
            )
            ->addOption(
                self::SHEAF,
                null,
                InputOption::VALUE_NONE,
                'Need start Sheaf Collection Service?'
            )
            ->addOption(
                self::CATEGORY,
                null,
                InputOption::VALUE_NONE,
                'Need start Category Filter Service?'
            )
            ->addOption(
                self::MARKET,
                null,
                InputOption::VALUE_NONE,
                'Need start Market Export Service?'
            )
            ->addOption(
                self::CREATOR,
                null,
                InputOption::VALUE_NONE,
                'Need start Parser Run Creator Service?'
            )
            ->addOption(
                self::OFFER,
                null,
                InputOption::VALUE_NONE,
                'Need start Offer Read Run Service?'
            )
            ->addOption(
                self::STATS,
                null,
                InputOption::VALUE_NONE,
                'Need start Stats Service?'
            )
            ->addOption(
                self::HELP,
                null,
                InputOption::VALUE_NONE,
                'Need Help?'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption(self::HELP)) {
            $output->writeln("Command list:\r\n");
            $output->writeln("\t--" . self::HELP . "\t - Хелпер");
            $output->writeln("\t--" . self::GARBAGE . "\t - Сборщик мусора");
            $output->writeln("\t--" . self::SHEAF . "\t\t - Актуализация связок между продуктами");
            $output->writeln("\t--" . self::CATEGORY . "\t - Актуализация категорий");
            $output->writeln("\t--" . self::MARKET . "\t - Создание маркет YML и партнерского XLSX");
            $output->writeln("\t--" . self::OFFER . "\t\t - Создание или обновление офферов");
            $output->writeln("\t--" . self::CREATOR . "\t - Создание новых позиций из венеры");
            $output->writeln("\t--" . self::STATS . "\t - Вызов промежуточной статистики приложения");
            $output->writeln("\r\n");
            return Command::SUCCESS;
        }

        $now = time();
        $output->writeln('System Line::RUN');
        if ($input->getOption(self::GARBAGE)) {
            $output->writeln('Garbage Service::Starting...');
            $this->gp->run();
        }

        if ($input->getOption(self::SHEAF)) {
            $output->writeln('Sheaf Collection Service::Starting...');
            $this->sc->run();
        }

        if ($input->getOption(self::CATEGORY)) {
            $output->writeln('Category Filter::Starting...');
            $this->cf->run();
        }

        if ($input->getOption(self::MARKET)) {
            $output->writeln('Market Export::Starting...');
            $this->me->run();
        }

        if ($input->getOption(self::CREATOR)) {
            $output->writeln('Parser Run Creator::Starting...');
            $this->pc->run();
        }

        if ($input->getOption(self::OFFER)) {
            $output->writeln('Offer Read Creator::Starting...');
            $this->or->run();
        }

        if ($input->getOption(self::STATS)) {
            $output->writeln('Stats Sender::Starting...');
            $this->ss->run();
        }

        $output->writeln(sprintf('System Line::PROCESS WORK [%d sec]', time() - $now));
        return Command::SUCCESS;
    }
}
