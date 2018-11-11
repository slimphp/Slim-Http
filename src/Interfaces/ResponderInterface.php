<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2018 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */

namespace Slim\Http\Interfaces;

use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponderInterface
 * @package Slim\Http\Interfaces
 */
interface ResponderInterface
{
    /**
     * Send the response the client
     *
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function respond(ResponseInterface $response): ResponseInterface;

    /**
     * Merge a key-value array with existing responder settings
     * @param array $settings
     */
    public function addSettings(array $settings);

    /**
     * Getting responder settings
     * @return array
     */
    public function getSettings();
}
