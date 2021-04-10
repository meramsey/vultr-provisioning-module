<?php

namespace MGModule\vultr\mgLibs\models;

use MGModule\vultr as main;

/**
 * Description of abstractModel
 *
 * @SuppressWarnings(PHPMD)
 */
abstract class Base
{
    /**
     * Normalized Time Stamp
     *
     * @param string $strTime
     * @return string
     */
    public static function timeStamp($strTime = 'now'): string
    {
        return date('Y-m-d H:i:s', strtotime($strTime));
    }

    /**
     * Disable Get Function
     *
     * @param string $property
     * @throws main\mgLibs\exceptions\System
     */
    public function __get(string $property)
    {
        throw new main\mgLibs\exceptions\System('Property: ' . $property . ' does not exits in: ' . get_called_class(), main\mgLibs\exceptions\Codes::PROPERTY_NOT_EXISTS);
    }

    /**
     * Disable Set Function
     *
     * @param string $property
     * @param string $value
     * @throws main\mgLibs\exceptions\System
     */
    public function __set(string $property, $value)
    {
        throw new main\mgLibs\exceptions\System('Property: ' . $property . ' does not exits in: ' . get_called_class(), main\mgLibs\exceptions\Codes::PROPERTY_NOT_EXISTS);
    }

    /**
     * Disable Call Function
     *
     * @param string $function
     * @param string $params
     * @throws main\mgLibs\exceptions\System
     */
    public function __call(string $function, string $params)
    {
        throw new main\mgLibs\exceptions\System('Function: ' . $function . ' does not exits in: ' . get_called_class(), main\mgLibs\exceptions\Codes::PROPERTY_NOT_EXISTS);
    }

    /**
     * Cast To array
     *
     * @param string $container
     * @return array
     */
    public function toArray($container = true): array
    {
        $className = get_called_class();

        $fields = get_class_vars($className);

        foreach (explode('\\', $className) as $className) {
            ;
        }

        $data = array();

        foreach ($fields as $name => $defult) {
            if (isset($this->{$name})) {
                $data[$name] = $this->{$name};
            }
        }

        if ($container === true) {
            return array(
                $className => $data
            );
        } elseif ($container) {
            return array(
                $container => $data
            );
        } else {
            return $data;
        }
    }

    /**
     * Encrypt String using Hash from configuration
     *
     * @param string $input
     * @return string
     */
    public function encrypt(string $input)
    {
        if (empty($input)) {
            return false;
        }

        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, main\mgLibs\process\MainInstance::I()->getEncryptKey(), $input, MCRYPT_MODE_ECB));
    }

    /**
     * Decrypt String using Hash from configuration
     *
     * @param string $input
     * @return string
     */
    public function decrypt(string $input)
    {
        if (empty($input)) {
            return false;
        }

        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, main\mgLibs\process\MainInstance::I()->getEncryptKey(), base64_decode($input), MCRYPT_MODE_ECB));
    }

    public function serialize($input): string
    {
        return base64_encode(serialize($input));
    }

    public function unserialize($input)
    {
        return unserialize(base64_decode($input));
    }
}
