<?php

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
     * @internal
     *
     * @param SystemFacade|null $systemFacade
     * @return RunInterface
     */
    public static function createCli(?SystemFacade $systemFacade = null): RunInterface
    {
        $run = new Run($systemFacade);
        $run->pushHandler(new PlainTextHandler());

        return $run;
    }

    /**
     * Returns the default Whoops run.
     *
     * @param SystemFacade|null $systemFacade
     * @return RunInterface
     */
    public static function createDefault(?SystemFacade $systemFacade = null): RunInterface
    {
        return PHP_SAPI === 'cli'
            ? static::createCli($systemFacade)
            : static::createHttp($systemFacade);
    }

    /**
     * @internal
     *
     * @param SystemFacade|null $systemFacade
     * @return RunInterface
     */
    public static function createHttp(?SystemFacade $systemFacade = null): RunInterface
    {
        $run = new Run($systemFacade);

        // check admin frame
        if (0 === stripos((string)$_SERVER['REQUEST_URI'], '/bitrix/admin') && (string)$_REQUEST['mode'] === 'frame') {
            $run->pushHandler(new PlainTextHandler());

            return $run;
        }

        $accept = $_SERVER['HTTP_ACCEPT'] ?? '*/*';
        switch (true) {
            case false !== stripos($accept, '*/*') || false !== stripos($accept, 'html'):
                $run->pushHandler(static::createPrettyPageHandler());
                break;
            case false !== stripos($accept, 'json'):
                $run->pushHandler(static::createJsonResponseHandler());
                break;
            case false !== stripos($accept, 'xml'):
                $run->pushHandler(new XmlResponseHandler());
                break;
            default:
                $run->pushHandler(new PlainTextHandler());
                break;
        }

        return $run;
    }
}
