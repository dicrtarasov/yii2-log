<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 27.12.20 07:14:43
 */

/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types = 1);

use dicr\helper\Html;
use dicr\log\manager\Log;
use yii\helpers\Json;
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

        <?php if ($exists) { ?>
            <tr>
                <th>Size:</th>
                <td><?= Yii::$app->formatter->asShortSize($size) ?></td>
            </tr>
            <tr>
                <th>Time:</th>
                <td><?= Yii::$app->formatter->asDatetime($time) ?></td>
            </tr>
        <?php } ?>

        <?php if (! empty($log->target->categories)) { ?>
            <tr>
                <th>Categories:</th>
                <td><?= Json::encode($log->target->categories) ?></td>
            </tr>
        <?php } ?>

        <?php if (! empty($log->target->except)) { ?>
            <tr>
                <th>Except:</th>
                <td><?= Json::encode($log->target->except) ?></td>
            </tr>
        <?php } ?>
    <?php } ?>
    </table>
</main>

