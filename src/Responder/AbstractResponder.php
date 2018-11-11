<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2018 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */

namespace Slim\Http\Responder;

use Slim\Http\Interfaces\ResponderInterface;

/**
 * Class DefaultResponder
 * @package Slim\Http\Responder
 */
abstract class AbstractResponder implements ResponderInterface
{
    /**
     * @var array
     */
    protected $settings = [
        'responseChunkSize' => 4096,
    ];

    /**
     * AbstractResponder constructor.
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->addSettings($settings);
    }

    /********************************************************************************
     * Settings management
     *******************************************************************************/

    /**
     * Does app have a setting with given key?
     *
     * @param string $key
     * @return bool
     */
    public function hasSetting(string $key): bool
    {
        return isset($this->settings[$key]);
    }

    /**
     * Get app settings
     *
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * Get app setting with given key
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getSetting(string $key, $defaultValue = null)
    {
        return $this->hasSetting($key) ? $this->settings[$key] : $defaultValue;
    }

    /**
     * Merge a key-value array with existing app settings
     *
     * @param array $settings
     */
    public function addSettings(array $settings)
    {
        $this->settings = array_merge($this->settings, $settings);
    }

    /**
     * Add single app setting
     *
     * @param string $key
     * @param mixed $value
     */
    public function addSetting(string $key, $value)
    {
        $this->settings[$key] = $value;
    }
}