<?php
/*
 * @copyright 2019-2021 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 12.08.21 23:12:51
 */

declare(strict_types = 1);
namespace dicr\log\manager;

use dicr\asset\FontAwesomeAsset;
use dicr\site\admin\AdminAsset;
use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * Class LogAsset
 */
class LogAsset extends AssetBundle
{
    /** @inheritDoc */
    public $sourcePath = __DIR__ . '/assets';

    /** @inheritDoc */
    public $css = ['style.scss'];

    /** @inheritDoc */
    public $depends = [
        BootstrapAsset::class, JqueryAsset::class, FontAwesomeAsset::class, AdminAsset::class
    ];
}
