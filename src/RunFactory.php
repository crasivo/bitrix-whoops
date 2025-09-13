<?php

declare(strict_types=1);

namespace Crasivo\Bitrix\Whoops;

use Crasivo\Bitrix\Whoops\Handlers\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\XmlResponseHandler;
use Whoops\Run;
use Whoops\RunInterface;
use Whoops\Util\SystemFacade;

class RunFactory
{
    /**
     * General kernel constants.
     *
     * @see https://dev.1c-bitrix.ru/api_help/main/general/constants.php
     */
    public const KERNEL_CONSTANTS = [
        'SITE_CHARSET',
        'SITE_ID',
        'SITE_DIR',
        'SITE_SERVER_NAME',
        'SITE_TEMPLATE_ID',
        'SITE_TEMPLATE_PATH',
        'LANGUAGE_ID',
        'SM_VERSION',
        'SM_VERSION_DATE',
    ];

    /**
     * @return JsonResponseHandler
     */
    public static function createJsonResponseHandler(): JsonResponseHandler
    {
        return (new JsonResponseHandler())
            ->addTraceToOutput(in_array($_ENV['APP_DEBUG'], ['true', true]));
    }

    /**
     * @return PrettyPageHandler
     */
    public static function createPrettyPageHandler(): PrettyPageHandler
    {
        $handler = new PrettyPageHandler();
        $handler->hideSuperGlobalKeyRegex('_ENV');
        $handler->hideSuperGlobalKeyRegex('_SERVER');
        $handler->addGlobalConstantsTable(static::KERNEL_CONSTANTS);

        return $handler;
    }

    /**
     * @return array|false
     */
    private static function getHeaders()
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }
        if (function_exists('apache_request_headers')) {
            return apache_request_headers();
        }

        return false;
    }

    /**
     * @return string
     */
    private static function getAcceptHeader(): string
    {
        return self::getHeaders()['Accept'] ?? 'text/html';
    }

    /**
     * @param SystemFacade|null $systemFacade
     * @return RunInterface
     */
    public static function createDefault(?SystemFacade $systemFacade = null): RunInterface
    {
        // check cli mode
        $run = new Run($systemFacade);
        $headers = static::getHeaders();
        if (PHP_SAPI === 'cli' || !is_array($headers)) {
            $run->pushHandler(new PlainTextHandler());

            return $run;
        }

        // check headers
        $accept = $headers['Accept'] ?? 'text/html';
        switch (true) {
            case str_contains($accept, 'text/html'):
                $run->pushHandler(static::createPrettyPageHandler());
                break;
            case str_contains($accept, 'json'):
                $run->pushHandler(static::createJsonResponseHandler());
                break;
            case str_contains($accept, 'xml'):
                $run->pushHandler(new XmlResponseHandler());
                break;
            default:
                $run->pushHandler(new PlainTextHandler());
                break;
        }

        return $run;
    }
}
