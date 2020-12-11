<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 11.12.20 21:08:07
 */

/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types = 1);

use dicr\helper\Html;
use dicr\log\manager\Message;
use dicr\log\manager\MessageFilter;
use dicr\site\admin\FilterForm;
use dicr\site\admin\GridView;
use yii\web\View;

/**
 * Просмотр лога.
 *
 * @var View $this
 * @var MessageFilter $filter
 */

$this->title = 'Журнал ' . basename($filter->log->target->logFile);
$this->params['h1'] = $this->title;

$this->params['breadcrumbs'] = [
    ['label' => 'Журналы', 'url' => ['index']],
    ['label' => basename($filter->log->target->logFile), 'url' => ['view', 'logKey' => $filter->log->key]]
];

$this->params['control-panel'] = [
    'buttons' => [
        Html::a('<i class="fas fa-eraser"></i>', ['erase', 'logKey' => $filter->log->key], [
            'title' => 'Очистить',
            'class' => 'btn btn-sm btn-danger'
        ])
    ]
];
?>
<main class="log-default-view">
    <?php $form = FilterForm::begin([
        'action' => ['view']
    ]) ?>

    <?= Html::hiddenInput('logKey', $filter->log->key) ?>

    <div class="row">
        <div class="col-md-6 col-lg-4 col-xl-3">
            <?= $form->field($filter, 'dateFrom')->input('date') ?>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <?= $form->field($filter, 'dateTo')->input('date') ?>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <?= $form->field($filter, 'ip') ?>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <?= $form->field($filter, 'userId') ?>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <?= $form->field($filter, 'sessionId') ?>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <?= $form->field($filter, 'level')
                ->dropdownList(array_combine(Message::LEVELS, Message::LEVELS)) ?>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <?= $form->field($filter, 'category') ?>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <?= $form->field($filter, 'text') ?>
        </div>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <?= $form->field($filter, 'lines') ?>
        </div>
    </div>
    <?php $form::end() ?>

    <?= GridView::widget([
        'dataProvider' => $filter->provider,
        'columns' => [
            [
                'attribute' => 'date',
                'content' => static function (Message $message) : string {
                    return Html::a(Yii::$app->formatter->asDatetime($message->date), [
                        'detail',
                        'logKey' => $message->log->key,
                        'messageKey' => $message->key
                    ]);
                }
            ],
            'ip', 'userId', 'sessionId', 'level', 'category',
            [
                'attribute' => 'text',
                'contentOptions' => ['class' => 'text']
            ]
        ],
        'options' => ['class' => 'messages'],
        'tableOptions' => ['class' => 'table table-sm'],
        'rowOptions' => static function (Message $message) : array {
            return ['class' => ['message', $message->level]];
        }
    ]) ?>
</main>
