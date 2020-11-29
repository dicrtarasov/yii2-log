<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 30.11.20 04:48:11
 */

declare(strict_types = 1);

use dicr\helper\Html;
use dicr\log\manager\Log;
use yii\web\View;

/**
 * Список логов.
 *
 * @var View $this
 * @var Log[] $logs
 */

$this->title = 'Журналы';
$this->params['h1'] = $this->title;

$this->params['breadcrumbs'] = [
    ['label' => $this->title, 'url' => ['index']]
];
?>
<main class="log-default-index">
    <table class="targets table table-sm">
    <?php foreach ($logs as $log) { ?>
        <tr class="header">
            <th colspan="2">
                <?= Html::a(Html::esc($log->target->logFile), ['view', 'logKey' => $log->key]) ?>
            </th>
        </tr>
        <tr>
            <th>Key:</th>
            <td><?= Html::esc($log->key) ?></td>
        </tr>
        <tr>
            <th>Categories:</th>
            <td><?= Yii::$app->formatter->asNtext(implode("\n", $log->target->categories)) ?></td>
        </tr>
        <tr>
            <th>Except:</th>
            <td><?= Yii::$app->formatter->asNtext(implode("\n", $log->target->except)) ?></td>
        </tr>
    <?php } ?>
    </table>
</main>

