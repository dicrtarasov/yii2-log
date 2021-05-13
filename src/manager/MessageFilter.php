<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 14.05.21 04:03:02
 */

declare(strict_types = 1);
namespace dicr\log\manager;

use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\ArrayDataProvider;

use function array_flip;
use function implode;
use function mb_stripos;
use function stripos;
use function strtotime;

use const SORT_ASC;
use const SORT_DESC;

/**
 * Фильтр сообщений.
 *
 * @property-read Message[] $messages
 * @property-read ArrayDataProvider $provider
 */
class MessageFilter extends Model
{
    /** @var Log */
    public $log;

    /** @var ?string */
    public $key;

    /** @var ?string */
    public $dateFrom;

    /** @var ?string */
    public $dateTo;

    /** @var ?string */
    public $ip;

    /** @var ?int */
    public $userId;

    /** @var ?string */
    public $sessionId;

    /** @var ?string минимальный уровень */
    public $level;

    /** @var ?string */
    public $category;

    /** @var ?string */
    public $text;

    /** @var ?string */
    public $lines;

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
    }

    /**
     * @inheritDoc
     *
     * @return array[]
     */
    public function rules(): array
    {
        return [
            ['key', 'trim'],
            ['key', 'default'],

            [['dateFrom', 'dateTo'], 'trim'],
            [['dateFrom', 'dateTo'], 'default'],
            [['dateFrom', 'dateTo'], 'date', 'format' => 'php:Y-m-d'],

            ['ip', 'trim'],
            ['ip', 'default'],

            ['userId', 'trim'],
            ['userId', 'default'],

            ['sessionId', 'trim'],
            ['sessionId', 'default'],

            ['level', 'trim'],
            ['level', 'default'],
            ['level', 'in', 'range' => Message::LEVELS],

            ['category', 'trim'],
            ['category', 'default'],

            ['text', 'trim'],
            ['text', 'default'],

            ['lines', 'trim'],
            ['lines', 'default'],
        ];
    }

    /**
     * Сравнивает сообщение с фильтром.
     *
     * @param Message $message
     * @return bool
     */
    public function matchMessage(Message $message): bool
    {
        $levelsMap = array_flip(Message::LEVELS);

        return
            ($this->key === null || $message->key === $this->key) &&
            ($this->userId === null || $message->userId === $this->userId) &&
            ($this->sessionId === null || stripos($message->sessionId, $this->sessionId) !== false) &&
            ($this->category === null || stripos($message->category, $this->category) === 0) &&
            ($this->text === null || mb_stripos($message->text, $this->text) !== false) &&
            ($this->level === null || $levelsMap[$message->level] >= $levelsMap[$this->level]) &&
            ($this->ip === null || stripos($message->ip, $this->ip) !== false) &&
            ($this->dateFrom === null || strtotime($message->date) >= strtotime($this->dateFrom)) &&
            ($this->dateTo === null || strtotime($message->date) <= strtotime($this->dateTo)) &&
            ($this->lines === null || mb_stripos(implode($message->lines), $this->lines) !== false);
    }

    /** @var ArrayDataProvider */
    private $_provider;

    /**
     * Сообщения.
     *
     * @return array
     * @throws Exception
     */
    public function getMessages(): array
    {
        return $this->validate() ? $this->log->parse([$this, 'matchMessage']) : [];
    }

    /**
     * Провайдер данных.
     *
     * @return ArrayDataProvider
     */
    public function getProvider(): ArrayDataProvider
    {
        if ($this->_provider === null) {
            $this->_provider = new ArrayDataProvider([
                'key' => 'key',
                'allModels' => $this->messages,
                'sort' => [
                    'attributes' => [
                        'date' => [
                            'asc' => ['date' => SORT_ASC],
                            'desc' => ['date' => SORT_DESC],
                            'default' => SORT_DESC
                        ],
                        'ip', 'userId', 'sessionId', 'level', 'category', 'text'
                    ],
                    'defaultOrder' => [
                        'date' => SORT_DESC
                    ]
                ],
                'pagination' => [
                    'pageSizeLimit' => [1, 100],
                    'defaultPageSize' => 100
                ]
            ]);
        }

        return $this->_provider;
    }
}
