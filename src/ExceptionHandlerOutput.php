<?php

namespace Crasivo\Bitrix\Whoops;

use Bitrix\Main\Application;
use Bitrix\Main\Diag\IExceptionHandlerOutput;
use Bitrix\Main\HttpResponse;
use Whoops\Run as WhoopsRun;
use Whoops\RunInterface;

class ExceptionHandlerOutput implements IExceptionHandlerOutput
{
    /** @var WhoopsRun */
    protected $whoopsRun;

    /**
     * Output handler constructor.
     *
     * @param RunInterface|null $run
     * @throws \Throwable
     */
    public function __construct(?RunInterface $run = null)
    {
        if ($GLOBALS['APPLICATION'] === null) {
            throw new \RuntimeException('Bitrix application is not installed.');
        }

        $this->whoopsRun = $run ?? RunFactory::createDefault();
    }

    /**
     * Registering a handler in the Kernel via ServiceLocator.
     *
     * @see https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=14032
     *
     * @return void
     * @throws \Throwable
     */
    public static function register()
    {
        Application::getInstance()
            ->getExceptionHandler()
            ->setHandlerOutput(new static());
        @define('WHOOPS_REGISTERED', true);
    }

    /**
     * Render output HTML page
     *
     * @internal
     *
     * @param \Error|\Exception|mixed $exception
     * @param bool|string|mixed $debug
     * @return void
     * @throws \Throwable
     */
    public function renderExceptionMessage($exception, $debug = false)
    {
        $response = new HttpResponse();
        $response->setStatus('500 Internal Server Error');
        $response->writeHeaders();

        // delegate output
        if (in_array($_ENV['APP_DEBUG'] ?? false, ['true', true]) || $debug === true) {
            $this->whoopsRun->handleException($exception);
            die();
        }

        // default output
        $errorFile = Application::getDocumentRoot() . DIRECTORY_SEPARATOR . 'error.php';
        if (file_exists($errorFile)) {
            include "$errorFile";
        } else {
            echo "A error occurred during execution of this script. You can turn on extended error reporting in .settings.php file.";
        }
    }
}
