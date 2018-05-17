<?php
/**
 * Slim Framework (https://slimframework.com)
 *
 * @link      https://github.com/slimphp/Slim-Http
 * @copyright Copyright (c) 2011-2017 Josh Lockhart
 * @license   https://github.com/slimphp/Slim-Http/blob/master/LICENSE (MIT License)
 */
namespace Slim\Http;

use InvalidArgumentException;

/**
 * Headers
 *
 * This class represents a collection of HTTP headers
 * that is used in both the HTTP request and response objects.
 * It also enables header name case-insensitivity when
 * getting or setting a header value.
 *
 * Each HTTP header can have multiple values. This class
 * stores values into an array for each header name. When
 * you request a header value, you receive an array of values
 * for that header.
 */
class Headers extends Collection implements HeadersInterface
{
    /**
     * Special HTTP headers that do not have the "HTTP_" prefix
     *
     * @var array
     */
    protected static $special = [
        'CONTENT_TYPE' => 1,
        'CONTENT_LENGTH' => 1,
        'PHP_AUTH_USER' => 1,
        'PHP_AUTH_PW' => 1,
        'PHP_AUTH_DIGEST' => 1,
        'AUTH_TYPE' => 1,
    ];

    /**
     * Create new headers collection with data extracted from
     * the PHP global environment
     *
     * @param array $globals Global server variables
     *
     * @return self
     */
    public static function createFromGlobals(array $globals)
    {
        $data = [];
        $globals = self::determineAuthorization($globals);
        foreach ($globals as $key => $value) {
            $key = strtoupper($key);
            if (isset(static::$special[$key]) || strpos($key, 'HTTP_') === 0) {
                if ($key !== 'HTTP_CONTENT_LENGTH') {
                    $data[self::reconstructOriginalKey($key)] =  $value;
                }
            }
        }

        return new static($data);
    }

    /**
     * If HTTP_AUTHORIZATION does not exist tries to get it from
     * getallheaders() when available.
     *
     * @param array $globals The Slim application Environment
     *
     * @return array
     */

    public static function determineAuthorization(array $globals)
    {
        $authorization = isset($globals['HTTP_AUTHORIZATION']) ? $globals['HTTP_AUTHORIZATION'] : null;

        if (empty($authorization) && is_callable('getallheaders')) {
            $headers = getallheaders();
            $headers = array_change_key_case($headers, CASE_LOWER);
            if (isset($headers['authorization'])) {
                $globals['HTTP_AUTHORIZATION'] = $headers['authorization'];
            }
        }

        return $globals;
    }

    /**
     * Return array of HTTP header names and values.
     * This method returns the _original_ header name
     * as specified by the end user.
     *
     * @return array
     */
    public function all()
    {
        $all = parent::all();
        $out = [];
        foreach ($all as $key => $props) {
            $out[$props['originalKey']] = $props['value'];
        }

        return $out;
    }

    /**
     * Set HTTP header value
     *
     * This method sets a header value. It replaces
     * any values that may already exist for the header name.
     *
     * @param string $key   The case-insensitive header name
     * @param string $value The header value
     */
    public function set($key, $value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }
        $value = $this->validateValue($value);

        parent::set($this->normalizeKey($key), [
            'value' => $value,
            'originalKey' => $key
        ]);
    }

    /**
     * Get HTTP header value
     *
     * @param  string  $key     The case-insensitive header name
     * @param  mixed   $default The default value if key does not exist
     *
     * @return string[]
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return parent::get($this->normalizeKey($key))['value'];
        }

        return $default;
    }

    /**
     * Get HTTP header key as originally specified
     *
     * @param  string   $key     The case-insensitive header name
     * @param  mixed    $default The default value if key does not exist
     *
     * @return string
     */
    public function getOriginalKey($key, $default = null)
    {
        if ($this->has($key)) {
            return parent::get($this->normalizeKey($key))['originalKey'];
        }

        return $default;
    }

    /**
     * Add HTTP header value
     *
     * This method appends a header value. Unlike the set() method,
     * this method _appends_ this new value to any values
     * that already exist for this header name.
     *
     * @param string       $key   The case-insensitive header name
     * @param array|string $value The new header value(s)
     */
    public function add($key, $value)
    {
        $oldValues = $this->get($key, []);
        $newValues = is_array($value) ? $value : [$value];
        $this->set($key, array_merge($oldValues, array_values($newValues)));
    }

    /**
     * Does this collection have a given header?
     *
     * @param  string $key The case-insensitive header name
     *
     * @return bool
     */
    public function has($key)
    {
        return parent::has($this->normalizeKey($key));
    }

    /**
     * Remove header from collection
     *
     * @param  string $key The case-insensitive header name
     */
    public function remove($key)
    {
        parent::remove($this->normalizeKey($key));
    }

    /**
     * Normalize header name
     *
     * This method transforms header names into a
     * normalized form. This is how we enable case-insensitive
     * header names in the other methods in this class.
     *
     * @param  string $key The case-insensitive header name
     *
     * @return string Normalized header name
     */
    public function normalizeKey($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException('Header name must be a string');
        }
        if (!preg_match('/^[a-zA-Z0-9\'`#$%&*+.^_|~!-]+$/', $key)) {
            throw new InvalidArgumentException("'$key' is not valid header name");
        }

        $key = strtr(strtolower($key), '_', '-');
        if (strpos($key, 'http-') === 0) {
            $key = substr($key, 5);
        }

        return $key;
    }

    /**
     * Reconstruct original header name
     *
     * This method takes an HTTP header name from the Environment
     * and returns it as it was probably formatted by the actual client.
     *
     * @param string $key An HTTP header key from the $_SERVER global variable
     *
     * @return string The reconstructed key
     *
     * @example CONTENT_TYPE => Content-Type
     * @example HTTP_USER_AGENT => User-Agent
     */
    private static function reconstructOriginalKey($key)
    {
        if (strpos($key, 'HTTP_') === 0) {
            $key = substr($key, 5);
        }
        return strtr(ucwords(strtr(strtolower($key), '_', ' ')), ' ', '-');
    }

    /**
     * Ensure the header value is valid
     *
     * Ensure we only have visible ASCII characters, spaces, and horizontal
     * tabs. Also a header continuation is "\r\n " or "\r\n\t".
     *
     * @param  array $value
     * @return string
     */
    private function validateValue($value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('Header value must be a string or numeric');
        }
        foreach ($value as $v) {
            if (!is_string($v) && ! is_numeric($v)) {
                throw new InvalidArgumentException('Header value must be a string or numeric');
            }

            $v = (string) $v;
            if (preg_match("#(?:(?:(?<!\r)\n)|(?:\r(?!\n))|(?:\r\n(?![ \t])))#", $v)) {
                throw new InvalidArgumentException("'$v' is not valid header value");
            }
            if (preg_match('/[^\x09\x0a\x0d\x20-\x7E\x80-\xFE]/', $v)) {
                throw new InvalidArgumentException("'$value' is not valid header value");
            }
        }

        return $value;
    }
}
