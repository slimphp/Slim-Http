<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim-Http/blob/master/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Http;

use finfo;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Slim\Http\Interfaces\FileInterface;

class File implements FileInterface
{
    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string|null
     */
    private $path;

    /**
     * @var string|null
     */
    private $contentType;

    /**
     * @var string
     */
    private $contents;

    /**
     * @param string $fileName
     * @param string|null $path
     * @param string|null $contentType
     * @param string $contents
     */
    public function __construct(
        string $fileName,
        ?string $path,
        ?string $contentType,
        string $contents
    ) {
        $this->fileName = $fileName;
        $this->path = $path;
        $this->contentType = $contentType;
        $this->contents = $contents;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @return string|null
     */
    public function getContentType(): ?string
    {
        $contentType = $this->contentType;

        if (empty($contentType) && $this->contents) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $contentType = $finfo->buffer($this->contents);
        }

        if (empty($contentType) && $this->path) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $contentType = $finfo->file($this->path);
        }

        return $contentType;
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        return $this->contents;
    }

    /**
     * @param string $path
     *
     * @return File
     */
    public static function fromPath(string $path): FileInterface
    {
        if (!file_exists($path)) {
            throw new RuntimeException(sprintf('`%s` does not exist.', $path));
        }

        $fileName = basename($path);
        $contentType = mime_content_type($path);
        $contents = file_get_contents($path);

        return new File($fileName, $path, $contentType, $contents);
    }

    /**
     * @param StreamInterface $stream
     * @param string $fileName
     *
     * @return File
     */
    public static function fromStream(StreamInterface $stream, string $fileName): FileInterface
    {
        $contents = (string)$stream;

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $contentType = $finfo->buffer($contents);

        return new File($fileName, null, $contentType, $contents);
    }
}
