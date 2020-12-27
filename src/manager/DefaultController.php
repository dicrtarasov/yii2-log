<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license proprietary
 * @version 27.12.20 07:02:36
 */

declare(strict_types = 1);
namespace dicr\log\manager;

use yii\base\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

use function fopen;

/**
 * Default Controller.
 *
 * @property-read Request $request
 */
class DefaultController extends Controller
{
    /**
     * Список логов.
     *
     * @return string
     */
    public function actionIndex() : string
    {
        return $this->render('index', [
            'logs' => Log::list()
        ]);
    }

    /**
     * Список сообщений лога.
     *
     * @param string $logKey
     * @return string
     * @throws Exception
     */
    public function actionView(string $logKey) : string
    {
        $log = Log::byKey($logKey);
        if ($log === null) {
            throw new NotFoundHttpException('log key=' . $logKey);
        }

        $filter = new MessageFilter([
            'log' => $log
        ]);

        $filter->load($this->request->get());
        $filter->validate();

        return $this->render('view', [
            'filter' => $filter
        ]);
    }

    /**
     * Просмотр сообщения лога.
     *
     * @param string $logKey
     * @param string $messageKey
     * @return string
     * @throws Exception
     */
    public function actionDetail(string $logKey, string $messageKey) : string
    {
        $log = Log::byKey($logKey);
        if ($log === null) {
            throw new NotFoundHttpException('log key=' . $logKey);
        }

        $messages = $log->parse(function (Message $message) use ($messageKey) : bool {
            return $message->key === $messageKey;
        });

        if (empty($messages)) {
            throw new NotFoundHttpException($messages);
        }

        return $this->render('detail', [
            'message' => reset($messages)
        ]);
    }

    /**
     * Очистка лога.
     *
     * @param string $logKey
     * @return Response
     * @throws Exception
     */
    public function actionErase(string $logKey) : Response
    {
        $log = Log::byKey($logKey);
        if ($log !== null) {
            $f = fopen($log->target->logFile, 'wb');
            if (! $f) {
                throw new Exception('Ошибка открытия файла: ' . $log->target->logFile);
            }

            fclose($f);
        }

        return $this->redirect(['index'], 303);
    }
}
