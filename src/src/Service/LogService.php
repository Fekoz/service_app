<?php

namespace App\Service;

use App\Util\Constant;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpKernel\KernelInterface;

class LogService
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var bool
     */
    private $isClient;

    public function __construct(KernelInterface $kernel)
    {
        $formatter = new JsonFormatter();
        $stream = new StreamHandler($kernel->getProjectDir() . Constant::LOG_FILE);
        $this->logger = new Logger(Constant::LOG_COLLECTION);
        $stream->setFormatter($formatter);
        $this->logger->pushHandler($stream);
        $this->isClient = Constant::LOG_KEY_CONSOLE !== PHP_SAPI;
    }

    /**
     * @param int $type
     * @param int $key
     * @param array $array
     */
    public function register(int $type, int $key, array $array = [])
    {
        $this->persistLog($type, Constant::LOG[$key], $array);
    }

    /**
     * @param int $type
     * @param int $key
     * @param \Exception|null $exception
     * @param array $array
     */
    public function registerException(int $type, int $key, ?\Exception $exception = null, array $array = [])
    {
        $this->persistLog($type, Constant::LOG[$key], $array, $exception);
    }

    public function pointClear()
    {
        $this->logger->reset();
    }

    /**
     * @param int $type
     * @param string $key
     * @param array $array
     * @param \Exception|null $exception
     */
    private function persistLog(int $type, string $key, array $array, ?\Exception $exception = null)
    {
        switch ($type) {
            case Logger::DEBUG:
                $this->logger->debug($key, $this->formatter($array, $exception));
                break;
            case Logger::INFO:
                $this->logger->info($key, $this->formatter($array, $exception));
                break;
            case Logger::WARNING:
                $this->logger->warning($key, $this->formatter($array, $exception));
                break;
            case Logger::ERROR:
                $this->logger->error($key, $this->formatter($array, $exception));
                break;
            case Logger::CRITICAL:
                $this->logger->critical($key, $this->formatter($array, $exception));
                break;
        }
    }

    /**
     * @param array $array
     * @param \Exception|null $exception
     * @return array
     */
    private function formatter(array $array, ?\Exception $exception = null): array
    {
        return [
            'shot' => $this->isClient ? 'Client' : 'Console',
            'trace' => $exception ? [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ] : null,
            'data' => $array,
        ];
    }

}
