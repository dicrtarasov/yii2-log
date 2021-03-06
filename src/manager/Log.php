<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 14.05.21 04:04:05
 */

declare(strict_types = 1);
namespace dicr\log\manager;

use Yii;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\log\FileTarget;

use function file_exists;
use function fopen;
use function is_string;
use function rtrim;

/**
 * Log.
 */
class Log extends BaseObject
{
    /** @var string */
    public $key;

    /** @var FileTarget */
    public $target;

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init() : void
    {
        parent::init();

        if (! $this->target instanceof FileTarget) {
            throw new InvalidConfigException('target');
        }

        if (! is_string($this->key) || $this->key === '') {
            throw new InvalidConfigException('key');
        }
    }

    /**
     * Список настроенных файловых логов.
     *
     * @return array
     */
    public static function list() : array
    {
        static $list;

        if ($list === null) {
            $list = [];

            foreach (Yii::$app->log->targets as $key => $target) {
                if ($target instanceof FileTarget) {
                    $list[$key] = new self([
                        'key' => $key,
                        'target' => $target
                    ]);
                }
            }
        }

        return $list;
    }

    /**
     * Возвращает лог по ключу.
     *
     * @param string $key
     * @return ?static
     */
    public static function byKey(string $key) : ?self
    {
        $list = static::list();

        return $list[$key] ?? null;
    }

    /**
     * Парсит лог.
     *
     * @param ?callable $filter function(self $log, Message $message)
     * @return Message[]
     * @throws Exception
     */
    public function parse(?callable $filter = null) : array
    {
        if (! file_exists($this->target->logFile)) {
            return [];
        }

        /** @var Message[] $messages */
        $messages = [];

        /** @noinspection FopenBinaryUnsafeUsageInspection */
        $f = fopen($this->target->logFile, 'rt');
        if (! $f) {
            throw new Exception('Ошибка открытия файла: ' . $this->target->logFile);
        }

        /** @var ?Message $message */
        $message = null;

        while (true) {
            $line = fgets($f);
            if ($line === false) {
                break;
            }

            $line = rtrim($line);
            $config = $line !== '' ? Message::parseLine($line) : null;
            if ($config !== null) {
                // сохраняем текущее сообщение
                if ($message !== null) {
                    if ($filter === null || $filter($message)) {
                        $messages[] = $message;
                    }
                }

                // создаем новое текущее
                $message = new Message(array_merge($config, [
                    'log' => $this
                ]));
            } elseif ($message !== null) {
                $message->lines[] = $line;
            }
        }

        if ($message !== null && ($filter === null || $filter($message))) {
            $messages[] = $message;
        }

        fclose($f);

        return $messages;
    }
}
