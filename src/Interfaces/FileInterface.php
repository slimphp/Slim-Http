<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Http\Interfaces;

interface FileInterface
{
    /**
     * @return string
     */
    public function getFileName(): string;

    /**
     * @return string|null
     */
    public function getPath(): ?string;

    /**
     * @return string|null
     */
    public function getContentType(): ?string;

    /**
     * @return string
     */
    public function getContents(): string;
}
