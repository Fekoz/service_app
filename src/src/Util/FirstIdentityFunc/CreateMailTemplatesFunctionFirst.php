<?php


namespace App\Util\FirstIdentityFunc;


use App\Entity\MailTemplate;
use App\Service\Bot;
use App\Util\FirstIdentityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

class CreateMailTemplatesFunctionFirst implements FirstIdentityInterface
{
    public static function run(
        OutputInterface $output,
        EntityManagerInterface $entity,
        object $param,
        object $paramParse,
        object $parserAuth,
        SerializerInterface $serializer,
        Bot $bot
    ): void
    {
        if (count($entity->getRepository(MailTemplate::class)->findAll()) === 0) {
            $output->writeln('Создание шаблонов email оповещений клиента');
            for ($i = 0; $i <= 3; $i++) {
                $mailTemplate = new MailTemplate();
                $mailTemplate->setTitle(\sprintf("Шаблон нового заказа %d для {name}", $i));
                $mailTemplate->setMessage(\sprintf("Шаблон[%d] Клиент {name} \ {phone}, ваш новый новый заказ {uuid}", $i));
                $mailTemplate->setDateUpdate(new \DateTime());
                $mailTemplate->setDateCreate(new \DateTime());
                $entity->persist($mailTemplate);
                $entity->flush();
                $entity->clear();
            }
        }
    }
}
