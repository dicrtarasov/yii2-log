<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 31.07.21 00:56:13
 */

declare(strict_types = 1);
namespace dicr\log;

use yii\helpers\BaseConsole;
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
        Logger::LEVEL_ERROR => [BaseConsole::FG_RED, BaseConsole::BOLD, BaseConsole::UNDERLINE],
        Logger::LEVEL_WARNING => [BaseConsole::FG_YELLOW, BaseConsole::BOLD],
        Logger::LEVEL_INFO => [BaseConsole::FG_CYAN],
        Logger::LEVEL_TRACE => [BaseConsole::FG_GREY, BaseConsole::ITALIC]
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

    /**
     * {@inheritDoc}
     * Также необходимо Logger::flushInterval установить в 1
     */
    public $exportInterval = 1;

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
