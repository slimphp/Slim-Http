<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Http;

/**
 * Return global set value if set or value from php built-in function.
 *
 * @param string $pattern
 * @param string $subject
 * @param int    $limit
 * @param int    $flags
 *
 * @return array[]|false|mixed|string[]
 */
function preg_split(string $pattern, string $subject, int $limit = -1, int $flags = 0)
{
    if (isset($GLOBALS['preg_split_return'])) {
        return $GLOBALS['preg_split_return'];
    }

    return \preg_split($pattern, $subject, $limit, $flags);
}
