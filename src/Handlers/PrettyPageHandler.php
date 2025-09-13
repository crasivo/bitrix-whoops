<?php

declare (strict_types=1);

namespace Crasivo\Bitrix\Whoops\Handlers;

class PrettyPageHandler extends \Whoops\Handler\PrettyPageHandler
{
    /** @var string */
    const HIDDEN_KEY_REGEX = '.*?\_?(dsn|pass|key|secret|token).*?';

    /**
     * Add specified for 1C-Bitrix global constants.
     *
     * @param array $constants
     * @param string|null $tableName
     * @return void
     */
    public function addGlobalConstantsTable(array $constants, string $tableName = null)
    {
        $kernelConstants = array_fill_keys($constants, null);
        @array_walk($kernelConstants, function(&$v, $k) { $v = defined($k) ? constant($k) : null; });
        $this->addDataTable($tableName ?: 'Global constants', $kernelConstants);
    }

    /**
     * Hide secret phrases from global variables.
     *
     * @param string $superGlobalName
     * @param string $keyRegex
     * @return void
     */
    public function hideSuperGlobalKeyRegex(string $superGlobalName, string $keyRegex = self::HIDDEN_KEY_REGEX)
    {
        foreach ($GLOBALS[$superGlobalName] as $key => $value) {
            if (preg_match('/' . $keyRegex . '/', strtolower($key))) {
                $this->hideSuperglobalKey($superGlobalName, $key);
            }
        }
    }
}
