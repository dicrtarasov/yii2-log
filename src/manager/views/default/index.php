<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 27.12.20 07:07:17
 */

/** @noinspection PhpUnhandledExceptionInspection */
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
    <?php foreach ($logs as $log) {
        $file = $log->target->logFile;
        $exists = is_file($file) && is_readable($file);
        $size = $exists ? filesize($file) : null;
        $time = $exists ? filemtime($file) : null;
        ?>
        <tr class="header">
            <th colspan="2">★
                <?= $exists ?
                    Html::a(Html::esc($file), ['view', 'logKey' => $log->key]) :
                    Html::tag('span', Html::esc($file) . ' (не существует)')
                ?>
            </th>
        </tr>
        <tr>
            <th>Key:</th>
            <td><?= Html::esc($log->key) ?></td>
        </tr>
        <tr>
            <th>Size:</th>
            <td><?= Yii::$app->formatter->asShortSize($size) ?></td>
        </tr>
        <tr>
            <th>Time:</th>
            <td><?= Yii::$app->formatter->asDatetime($time) ?></td>
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

