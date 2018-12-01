<?php
namespace dicr\log;

use yii\helpers\Console;
use yii\log\Logger;
use yii\log\Target;

/**
 * Лог сообщений в консоль
 * 
 * @author Igor (Dicr) Tarasov <develop@dicr.org>
 * @version 2018
 */
class ConsoleTarget extends Target {

	/** @var array ANSI-стили */
	public $styles = [
		Logger::LEVEL_ERROR => [Console::FG_RED, Console::BOLD, Console::UNDERLINE],
		Logger::LEVEL_WARNING => [Console::FG_YELLOW, Console::BOLD],
		Logger::LEVEL_INFO => [Console::FG_CYAN],
		Logger::LEVEL_TRACE => [Console::FG_GREY, Console::ITALIC]
	];
	
	/** @var array ограничения размера трассировки стека */
	public $traceLimits = [
		Logger::LEVEL_ERROR => 2,
		Logger::LEVEL_WARNING => 0,
		Logger::LEVEL_INFO => 0,
		Logger::LEVEL_TRACE => 0,
	];
	
	/** @var array дескрипторы вывода */
	public $handles = [
		Logger::LEVEL_ERROR => STDERR,
		Logger::LEVEL_WARNING => STDERR,
		Logger::LEVEL_INFO => STDOUT,
		Logger::LEVEL_TRACE => STDOUT
	];
	
	/**
	 * {@inheritDoc}
	 * Cбрасываем зачение перед конфигурацией
	 */
	public $logVars;

	/**
	 * {@inheritDoc}
	 * Cбрасываем зачение перед конфигурацией
	 */
	public $exportInterval;
	
	/**
	 * {@inheritDoc}
	 * @see \yii\base\BaseObject::init()
	 */
	public function init() {
		if (!isset($this->logVars)) {
			$this->logVars = [];
		}
		
		if (!isset($this->exportInterval)) {
			$this->exportInterval = 1;
		}
		
		parent::init();
	}
	
	/**
	 * {@inheritDoc}
	 * В консоле нет сессии, пользователя и IP
	 * @see \yii\log\Target::getMessagePrefix()
	 */
	public function getMessagePrefix($message) {
		return '';
	}
	
	/**
	 * Выводит сообщение
	 * 
	 * @param array $message
	 */
	public function exportMessage(array $message) {
		$level = $message[1] ?? null;
		if (!isset($level)) return;
		
		// ограничиваем уровень трассировки
		if (isset($this->traceLimits[$level]) && !empty($message[4])) {
			$message[4] = array_slice($message[4], 0, (int)$this->traceLimits[$level], true);
		}
		
		// получаем текст сообщения
		$text = $this->formatMessage($message);
		
		// разукрашиваем
		if (!empty($this->styles[$level])) {
			$text = Console::ansiFormat($text, $this->styles[$level]);
		}
		
		// выводим
		$handle = $this->handles[$level] ?? STDOUT;
		if (!empty($handle)) fwrite($handle, $text."\n");
	}
	
	/**
	 * {@inheritDoc}
	 * @see \yii\log\Target::export()
	 */
    public function export() {
    	if (!empty($this->messages)) {
    		foreach ($this->messages as $message) {
    			$this->exportMessage($message);
    		}
    	}
    }
}