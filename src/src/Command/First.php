<?php

namespace App\Command;

use App\Entity\Admin;
use App\Entity\DirectoryLog;
use App\Entity\DirectoryMeter;
use App\Entity\DirectorySpecification;
use App\Entity\DirectoryStorage;
use App\Entity\Field;
use App\Entity\MailTemplate;
use App\Entity\Options;
use App\Entity\Presents;
use App\Service\Bot;
use App\Util\Constant;
use App\Util\FirstIdentityFunc\CreateAdminFunctionFirst;
use App\Util\FirstIdentityFunc\CreateDirectoryFunctionFirst;
use App\Util\FirstIdentityFunc\CreateFieldFunctionFirst;
use App\Util\FirstIdentityFunc\CreateMailTemplatesFunctionFirst;
use App\Util\FirstIdentityFunc\CreateMarketMappingConstantFirst;
use App\Util\FirstIdentityFunc\CreateMarketMappingFunctionFirst;
use App\Util\FirstIdentityFunc\CreateOptionsFunctionFirst;
use App\Util\FirstIdentityFunc\CreatePresentsFunctionFirst;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;

class First extends Command
{
    const COMMAND = 'app:first';
    const DESC = 'first';

    const USAGES = [
        CreateAdminFunctionFirst::class,
        CreateDirectoryFunctionFirst::class,
        CreateFieldFunctionFirst::class,
        CreateMailTemplatesFunctionFirst::class,
        CreateOptionsFunctionFirst::class,
        CreatePresentsFunctionFirst::class,
        CreateMarketMappingConstantFirst::class,
        CreateMarketMappingFunctionFirst::class,
    ];

    /**
     * @var EntityManagerInterface
     */
    private $entity;

    /**
     * @var object
     */
    private $param;

    /**
     * @var object
     */
    private $paramParse;

    /**
     * @var object
     */
    private $parserAuth;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Bot
     */
    private $bot;

    protected function configure(): void
    {
        $this
            ->setName(self::COMMAND)
            ->setDescription(self::DESC)
        ;
    }

    public function __construct(EntityManagerInterface $entity, ParameterBagInterface $param, SerializerInterface $serializer, Bot $bot)
    {
        $this->entity = $entity;
        $this->param = (object) $param->get('system.param');
        $this->paramParse = (object) $param->get('validator.parser.param');

        $veneraAuth = $param->get('venera.parser.param');
        $this->parserAuth = (object) $veneraAuth['auth']['venera'];
        $this->serializer = $serializer;
        $this->bot = $bot;
        $this->bot->init(Constant::BOT_TELEGRAM);
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach (self::USAGES as $class) {
            $class::run(
                $output,
                $this->entity,
                $this->param,
                $this->paramParse,
                $this->parserAuth,
                $this->serializer,
                $this->bot
            );
        }

        return Command::SUCCESS;
    }

}
