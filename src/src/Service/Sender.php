<?php

namespace App\Service;

use App\Util\Constant;
use Swift_Attachment;
use Swift_Image;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Swift_TransportException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Sender
{
    const DEFAULT_TITLE = 'CARPETTI - Информация';

    /**
     * @var $param array
     */
    private $param;

    /**
     * @var $mailer Swift_Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $certificate;

    /**
     * @var int
     */
    private $lastConnectTime;

    public function __construct(ParameterBagInterface $param)
    {
        $this->param = $param->get(Constant::CONFIG_NAME[__CLASS__]);
    }

    public function init()
    {
        $this->mailer = new Swift_Mailer(
            (new Swift_SmtpTransport($this->param["server"], $this->param["port"]))
            ->setUsername($this->param["login"])
            ->setPassword($this->param["password"])
        );

        $this->certificate = file_get_contents($this->param["kernel_dir"] . '/import/dynamic/carpetti.crt');
        $this->lastConnectTime = time();
    }

    /**
     * @param string $title
     * @param string $email
     * @param string $body
     */
    public function message(string $email, string $title, string $body)
    {
        if (!$email || !$body) {
            return;
        }

        $title = !$title ? self::DEFAULT_TITLE : $title;

        $message = (new Swift_Message($title))
            ->setFrom([$this->param["login"] => $this->param["name"]])
            ->setTo([$email])
            ->setBody('')
            ->addPart($body, 'text/html')
        ;
        $message->setSubject($title);
        $this->send($message);
    }

    /**
     * @param string $title
     * @param string $email
     * @param string $body
     * @param string $document
     */
    public function document(string $title, string $email, string $body, string $document)
    {
        if (!$email || !$body || !$document) {
            return;
        }

        $title = !$title ? self::DEFAULT_TITLE : $title;

        $message = (new Swift_Message($title))
            ->setFrom([$this->param["login"] => $this->param["name"]])
            ->setTo([$email])
            ->setBody('')
            ->addPart($body, 'text/html')
            ->attach(Swift_Attachment::fromPath($document))
        ;
        $this->send($message);
    }

    private function send(Swift_Message $message)
    {
        try {
            if ((time() - $this->lastConnectTime) > $this->param["timeout"]) {
                $this->mailer = null;
                $this->init();
            }
            $message->getHeaders()->addTextHeader('X-DKIM-Result', 'pass');
            $message->getHeaders()->addTextHeader('X-SPF-Result', 'pass');
            $message->getHeaders()->addTextHeader('DKIM-Signature', 'v=1; a=rsa-sha256; s=selector; d=example.com; c=relaxed/simple; q=dns/txt; h=from:to:subject:date:message-id:mime-version:content-type:content-transfer-encoding; bh=hash; b=signature');
            $message->getHeaders()->addTextHeader('DMARC-Filter', 'v=DMARC1; p=quarantine; sp=none; pct=100; rua=mailto:' . $this->param["login"]);
            $message->getHeaders()->addTextHeader('X-Certificate', $this->certificate);
            $this->mailer->send($message);
        } catch (Swift_TransportException $e) {
            $this->mailer = null;
            $this->init();
            return;
        }
    }



}
