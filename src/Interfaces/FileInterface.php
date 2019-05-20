<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Http\Interfaces;

use Psr\Http\Message\StreamInterface;

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

    /**
     * @param string $path
     *
     * @return FileInterface
     */
    public static function fromPath(string $path): FileInterface;

    /**
     * @param StreamInterface $stream
     * @param string $fileName
     *
     * @return FileInterface
     */
    public static function fromStream(StreamInterface $stream, string $fileName): FileInterface;
}
