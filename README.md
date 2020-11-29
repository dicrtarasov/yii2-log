# LogManager и Console Log Target для Yii2

## Console Log Target

### Configuration

```php
use yii\log\Logger;
use yii\helpers\Console;

'components' => [
    'log' => [
        'flushInterval' => 1, // отключаем буферизацию логов
        'targets' => [  
            'console' => dicr\log\ConsoleTarget::class,
            
            // нужные уровни
            'levels' => ['error', 'warning', 'info', 'trace'],  
            
            // нужные нам категории,
            'categories' => ['app\\*', 'dicr\\*'],
  
            // цвета
            'styles' => [                           
                Logger::LEVEL_ERROR => [Console::FG_RED, Console::BOLD, Console::UNDERLINE],
                Logger::LEVEL_WARNING => [Console::FG_YELLOW, Console::BOLD],
                Logger::LEVEL_INFO => [Console::FG_CYAN],
                Logger::LEVEL_TRACE => [Console::FG_GREY, Console::ITALIC]
            ],

            // дескрипторы вывода
            'streams' => [                          
                Logger::LEVEL_ERROR => STDERR,
                Logger::LEVEL_WARNING => STDERR,
                Logger::LEVEL_INFO => STDOUT,
                Logger::LEVEL_TRACE => STDOUT
            ],

            // ограничения размера трассировки стека
            'traceLimits' => [
                Logger::LEVEL_ERROR => 2,
                Logger::LEVEL_WARNING => 0,
                Logger::LEVEL_INFO => 0,
                Logger::LEVEL_TRACE => 0,
            ]
        ]
    ]   
];
```

## LogManager

Менеджер логов для просмотра и очистки файловых логов Yii.

### Configuration

```php
'modules' => [
    'log' => [
        'class' => dicr\log\manager\Module::class
    ]
];
```

Также для модуля можно настроить фильтр доступа и авторизацию (для доступа под логином и паролем). 

Далее заходим по адресу настроенного модуля: https://mysite.ru/log/

