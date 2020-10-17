<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 17.10.20 08:32:15
 */

declare(strict_types = 1);
namespace dicr\log;

use Yii;
use yii\helpers\Console;
use yii\log\Logger;
use yii\log\Target;

use function array_slice;

/**
 * Лог сообщений в консоль.
 */
class ConsoleTarget extends Target
{
    /** @var array ANSI-стили */
    public $styles = [
        Logger::LEVEL_ERROR => [Console::FG_RED, Console::BOLD, Console::UNDERLINE],
        Logger::LEVEL_WARNING => [Console::FG_YELLOW, Console::BOLD],
        Logger::LEVEL_INFO => [Console::FG_CYAN],
        Logger::LEVEL_TRACE => [Console::FG_GREY, Console::ITALIC]
    ];

    /** @var array ограничения размера трассировки стека */
    public $traceLimits = [
        Logger::LEVEL_ERROR => 2,
        Logger::LEVEL_WARNING => 0,
        Logger::LEVEL_INFO => 0,
        Logger::LEVEL_TRACE => 0,
    ];

    /** @var array дескрипторы вывода */
    public $streams = [
        Logger::LEVEL_ERROR => STDERR,
        Logger::LEVEL_WARNING => STDERR,
        Logger::LEVEL_INFO => STDOUT,
        Logger::LEVEL_TRACE => STDOUT
    ];

    /** @inheritDoc */
    public $logVars = [];

    /** @inheritDoc */
    public $exportInterval = 1;

    /**
     * @inheritDoc
     */
    public function init() : void
    {
        parent::init();

        // устанавливаем минимальное значение для сброса логов
        Yii::$app->log->flushInterval = 1;
    }

    /**
     * {@inheritDoc}
     * В консоли нет сессии, пользователя и IP.
     */
    public function getMessagePrefix($message) : string
    {
        return 'console';
    }

    /**
     * Выводит сообщение.
     *
     * @param array $message
     */
    public function exportMessage(array $message) : void
    {
        $level = $message[1] ?? null;
        if (! isset($level)) {
            return;
        }

        // ограничиваем уровень трассировки
        if (isset($this->traceLimits[$level]) && ! empty($message[4])) {
            $message[4] = array_slice($message[4], 0, (int)$this->traceLimits[$level], true);
        }

        // получаем текст сообщения
        $text = $this->formatMessage($message);

        // поток вывода
        $stream = $this->streams[$level] ?? STDOUT;
        if (! empty($stream)) {
            // разукрашиваем
            if (! empty($this->styles[$level]) && Console::streamSupportsAnsiColors($stream)) {
                $text = Console::ansiFormat($text, $this->styles[$level]);
            }

            // выводим
            fwrite($stream, $text . "\n");
            fflush($stream);
        }
    }

    /**
     */
    public function export() : void
    {
        foreach ($this->messages ?: [] as $message) {
            $this->exportMessage($message);
        }
    }
}
