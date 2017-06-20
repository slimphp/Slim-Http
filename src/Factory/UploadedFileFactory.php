<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2017 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Http\Factory;

use Interop\Http\Factory\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\UploadedFile;

class UploadedFileFactory implements UploadedFileFactoryInterface
{
    /**
     * Create a new uploaded file.
     *
     * If a string is used to create the file, a temporary resource will be
     * created with the content of the string.
     *
     * If a size is not provided it will be determined by checking the size of
     * the file.
     *
     * @see http://php.net/manual/features.file-upload.post-method.php
     * @see http://php.net/manual/features.file-upload.errors.php
     *
     * @param string|resource $file
     * @param integer $size in bytes
     * @param integer $error PHP file upload error
     * @param string $clientFilename
     * @param string $clientMediaType
     *
     * @return UploadedFileInterface
     *
     * @throws \InvalidArgumentException
     *  If the file resource is not readable.
     */
    public function createUploadedFile(
        $file,
        $size = null,
        $error = \UPLOAD_ERR_OK,
        $clientFilename = null,
        $clientMediaType = null
    )
    {
        if (is_resource($file)) {
            if (!isset($size)) {
                $size = fstat($file)['size'];
            }

            $file = stream_get_meta_data($file)['uri'];
        } elseif (is_string($file)) {
            if (!isset($size)) {
                $size = filesize($file);
            }
        }

        return new UploadedFile($file, $clientFilename, $clientMediaType, $size, $error);
    }
}