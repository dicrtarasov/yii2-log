<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 30.11.20 05:21:32
 */

/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types = 1);

use dicr\log\manager\Message;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;

/**
 * Страница сообщения.
 *
 * @var View $this
 * @var Message $message
 */

$this->title = 'Сообщение ' . basename($message->log->target->logFile) . ' ' .
    Yii::$app->formatter->asDate($message->date, 'php:d.m.y H:i:s');
$this->params['h1'] = $this->title;
$this->params['breadcrumbs'] = [
    ['label' => 'Журналы', 'url' => ['index']],
    ['label' => basename($message->log->target->logFile), 'url' => ['view', 'logKey' => $message->log->key]],
    ['label' => Yii::$app->formatter->asDate($message->date, 'php:d.m.y H:i:s'), 'url' => Url::current()]
];
?>
<main class="log-default-detail">
    <?= DetailView::widget([
        'model' => $message,
        'attributes' => [
            [
                'attribute' => 'log',
                'value' => static function (Message $message) : string {
                    return $message->log->target->logFile;
                }
            ],
            [
                'attribute' => 'date',
                'format' => ['date', 'php:d.m.Y H:i:s']
            ],
            'ip', 'userId', 'sessionId', 'level', 'category', 'text'
        ],
        'options' => ['class' => 'table table-sm details mb-3']
    ]) ?>

    <div class="lines"><?= trim(implode("\n", $message->lines)) ?></div>
</main>
