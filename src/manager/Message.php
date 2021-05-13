<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 14.05.21 04:04:05
 */

declare(strict_types = 1);
namespace dicr\log\manager;

use yii\base\InvalidConfigException;
use yii\base\Model;

use function is_string;
use function md5;
use function preg_match;

/**
 * Элемент лога.
 *
 * @property-read Log $log
 */
class Message extends Model
{
    /** @var string[] */
    public const LEVELS = [
        'error', 'warning', 'info', 'trace', 'profile'
    ];

    /** @var Log */
    public $log;

    /** @var string идентификатор сообщения */
    public $key;

    /** @var string дата */
    public $date;

    /** @var string IP */
    public $ip;

    /** @var ?string */
    public $userId;

    /** @var string */
    public $sessionId;

    /** @var string */
    public $level;

    /** @var string */
    public $category;

    /** @var string текст сообщения */
    public $text;

    /** @var string[] остальные строки сообщения */
    public $lines = [];

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        if (! $this->log instanceof Log) {
            throw new InvalidConfigException('log');
        }

        if (! is_string($this->key) || $this->key === '') {
            throw new InvalidConfigException('key');
        }

        if (empty($this->date)) {
            throw new InvalidConfigException('date');
        }
    }

    /**
     * Парсит строку лога.
     *
     * @param string $line строка начала сообщения
     * @return array|null конфиг Message если распознано начало лога или null
     */
    public static function parseLine(string $line): ?array
    {
        $matches = null;

        return preg_match(
            '~^(\d+\-\d+\-\d+\s+\d+\:\d+\:\d+)\s+\[([^\]]+)\]\[([^\]]+)\]\[([^\]]+)\]\[(\S+)\]\[([^\]]+)\]\s+(.+)$~u',
            $line, $matches
        ) ? [
            'key' => md5($line),
            'date' => $matches[1],
            'ip' => $matches[2],
            'userId' => $matches[3] === '-' ? null : $matches[3],
            'sessionId' => $matches[4] === '-' ? null : $matches[4],
            'level' => $matches[5],
            'category' => $matches[6],
            'text' => $matches[7],
            'lines' => []
        ] : null;
    }
}
