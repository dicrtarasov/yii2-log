<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 30.11.20 04:30:41
 */

/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types = 1);

use dicr\asset\BaseResAsset;
use dicr\helper\Html;
use dicr\log\manager\LogAsset;
use dicr\site\admin\NavBar;
use dicr\widgets\Breadcrumbs;
use dicr\widgets\ToastsWidget;
use yii\web\View;

/**
 * Макет.
 *
 * @var View $this
 * @var string $content
 */

BaseResAsset::registerConfig($this, [
    'depends' => [LogAsset::class]
]);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>

    <header class="mb-2">
        <?= NavBar::widget([
            'brandLabel' => '<i class="fas fa-clipboard-list"></i> LogManager',
            'brandUrl' => ['default/index'],
            'nav' => [
                'items' => []
            ],

            'controlPanel' => $this->params['control-panel'] ?? []
        ]) ?>
    </header>

    <main class="container">
        <?php
        if (! empty($this->params['breadcrumbs'])) {
            echo Breadcrumbs::widget([
                'homeLink' => [
                    'label' => '<i class="fas fa-home"></i>',
                    'encode' => false,
                    'url' => ['/admin/default/index']
                ],
                'links' => $this->params['breadcrumbs']
            ]);
        }

        if (! empty($this->params['h1'])) {
            echo Html::tag('h1', Html::encode($this->params['h1']));
        }

        echo $content ?? '';
        ?>
    </main>

    <?= ToastsWidget::widget(Yii::$app->session->getFlash('toasts')) ?>

    <?php $this->endBody() ?>
</body>
</html>

<?php $this->endPage() ?>

