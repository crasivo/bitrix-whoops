🐞 Bitrix Whoops
===

Адаптер популярной библиотеки [Whoops](https://github.com/filp/whoops) для 1C-Bitrix & Bitrix24.
Используется для визуальной отладки ошибок в публичной части проекта.

Особенности реализации:

- [x] Простое и быстрое внедрение в существующий проект
- [x] Отлично подойдет для старых редакций
- [x] Скрытие "секретных фраз" переменных окружения (YourAwesomePa55 > ********)

<u>Минимальные</u> требования для установки:

- Версия ядра 1C-Bitrix (main): `v20.5.400`
- Версия PHP: `v7.2`
- Версия Whoops: `v2.10`

# 🚀 Быстрый старт

Для работы библиотеки необходимо установить [Composer](https://getcomposer.org/) пакет через команду:

```shell
$ cd /path/to/project
$ composer require crasivo/bitrix-whoops
```

Далее необходимо зарегистрировать обработчик через [init.php](https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2916).
Для этого достаточно добавить в указанный файл одну строчку кода.

```php
Crasivo\Bitrix\Whoops\ExceptionHandlerOutput::register();
```

Для проверки работоспособности необходимо активировать режим отладки через параметр `exception_handling.debug` в файле `.settings.php`
и вручную вызвать произвольный `Exception` в публичной части сайта.

---

## 📜 Лицензия

Данный проект распространяется по лицензии [MIT](https://en.wikipedia.org/wiki/MIT_License).
Полный текст лицензии можно прочитать в файле [LICENSE](LICENSE).
