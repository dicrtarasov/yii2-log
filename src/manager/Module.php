<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 30.11.20 04:36:01
 */

declare(strict_types = 1);
namespace dicr\log\manager;

/**
 * Модуль работы с логами.
 */
class Module extends \yii\base\Module
{
    /** @inheritDoc */
    public $layout = 'main';

    /** @inheritDoc */
    public $controllerNamespace = __NAMESPACE__;
}
