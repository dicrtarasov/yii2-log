<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 30.11.20 04:17:31
 */

declare(strict_types = 1);
namespace dicr\log\manager;

use Yii;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\log\FileTarget;

use function fopen;

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

        if ((string)$this->key === '') {
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
        /** @noinspection FopenBinaryUnsafeUsageInspection */
        $f = fopen($this->target->logFile, 'rt');
        if (! $f) {
            throw new Exception('Ошибка открытия файла: ' . $this->target->logFile);
        }

        /** @var Message[] $messages */
        $messages = [];

        /** @var ?Message $message */
        $message = null;

        while (true) {
            $line = fgets($f);
            if ($line === false) {
                break;
            }

            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $config = Message::parseLine($line);
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

        fclose($f);

        return $messages;
    }
}
