🐞 Bitrix Whoops
===

Adapter for the popular [Whoops](https://github.com/filp/whoops) library for 1C-Bitrix & Bitrix24.
Used for visual debugging of errors in the public part of the project.

Implementation features:

- [x] Simple and quick integration into an existing project
- [x] Great for older versions
- [x] Hiding "secret phrases" of environment variables (YourAwesomePa55 > ********)

<u>Minimum</u> requirements for installation:

- 1C-Bitrix kernel version (main): `v20.5.400`
- PHP version: `v7.2`
- Whoops version: `v2.18`

# 🚀 Quick Start

To use the library, you need to install the [Composer](https://getcomposer.org/) package via the command:

```shell
$ cd /path/to/project
$ composer require crasivo/bitrix-whoops
```

Next, you need to register the handler via [init.php](https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=2916).
To do this, simply add one line of code to the specified file.

```php
Crasivo\Bitrix\Whoops\HttpExceptionHandlerOutput::register();
```

To check the functionality, you need to activate the debug mode via the `exception_handling.debug` parameter in the `.settings.php` file
and manually call an arbitrary `Exception` in the public part of the site.

---

## 📜 License

This project is distributed under the [MIT](https://en.wikipedia.org/wiki/MIT_License) license.
The full text of the license can be read in the [LICENSE](LICENSE) file.
