<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

use AdrianSuter\Autoload\Override\Override;
use Slim\Http\ServerRequest;

$classLoader = require __DIR__ . '/../vendor/autoload.php';

//require __DIR__ . '/Assets/PhpFunctionOverrides.php';

Override::apply($classLoader, [
    ServerRequest::class => [
        'preg_split' => function (string $pattern, string $subject, int $limit = -1, int $flags = 0) {
            if (isset($GLOBALS['preg_split_return'])) {
                return $GLOBALS['preg_split_return'];
            }

            return preg_split($pattern, $subject, $limit, $flags);
        }
    ]
]);
