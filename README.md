# ytubes

Бекап модуль


## Composer
```json
"require": {
    "ytubes/backup": ">=0.0.1"
},
```

## Подключение
backend/config/components.php,
console/config/components.php,
прописать:
```php
'components' => [
    'modules' => [
        'backup' => [
            'class' => 'ytubes\backup\Module',
            'enableCompression' => true,
            'backupDirectory' => dirname(dirname(__DIR__)) . '/backup',
        ],
    ],
],
```
## Крон
Для периодического бекапа (раз в сутки):
```
\ytubes\backend\cron\jobs\Backup 01 00 * * *
```
