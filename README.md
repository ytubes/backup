# ytubes

Бекап модуль

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
\ytubes\backendcron\jobs\Backup 01 00 * * *
```