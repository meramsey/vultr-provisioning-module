<?php

namespace MGModule\vultr;

use MGModule\vultr as main;

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * Module Class Loader
 *
 * @SuppressWarnings(PHPMD)
 */
if (!class_exists(__NAMESPACE__ . '\Loader')) {
    class Loader
    {
        public static $whmcsDir;
        public static $myName;
        public static $availableDirs = array();

        /**
         * Set Paths
         *
         * @param string $dir
         */
        public function __construct($dir = null)
        {
            if (empty($dir)) {
                $checkDirs = array(
                    'modules' . DS . 'addons' . DS
                , 'modules' . DS . 'servers' . DS
                );

                self::$myName = substr(__NAMESPACE__, 9);
                foreach ($checkDirs as $dir) {
                    if ($pos = strpos(__DIR__, $dir . self::$myName)) {
                        self::$whmcsDir = substr(__DIR__, 0, $pos);

                        break;
                    }
                }

                if (self::$whmcsDir) {
                    foreach ($checkDirs as $dir) {
                        $tmp = self::$whmcsDir . $dir . self::$myName;
                        if (file_exists($tmp)) {
                            self::$availableDirs[] = $tmp . DS;
                        }
                    }
                }
            } else {
                self::$mainDir = $dir;
            }

            spl_autoload_register(array($this, 'loader'));
        }

        /**
         * Load Class File
         *
         * @param string $className
         * @return bool
         * @throws main\mgLibs\exceptions\base
         * @throws \Exception
         */
        public static function loader(string $className): bool
        {
            if (strpos($className, __NAMESPACE__) === false) {
                return;
            }

            $className = substr($className, strlen(__NAMESPACE__));
            $originClassName = $className;
            $className = ltrim($className, '\\');
            $fileName = '';
            $namespace = '';
            if ($lastNsPos = strrpos($className, '\\')) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName = str_replace('\\', DS, $namespace) . DS;
            }

            $fileName .= str_replace('_', DS, $className) . '.php';
            $foundFile = false;
            $error = array();
            foreach (self::$availableDirs as $dir) {
                $tmp = $dir . $fileName;
                if (file_exists($tmp)) {
                    if (!$foundFile) {
                        $foundFile = $tmp;
                    }
                }
            }

            if ($foundFile) {
                require_once $foundFile;

                if (!class_exists(__NAMESPACE__ . $originClassName) && !interface_exists(__NAMESPACE__ . $originClassName)) {
                    $error['message'] = 'Unable to find class:' . $originClassName . ' in file:' . $foundFile;
                    $error['code'] = main\mgLibs\exceptions\Codes::MISSING_FILE_CLASS;
                }
            }

            if ($error) {
                if (class_exists(__NAMESPACE__ . '\mgLibs\exceptions\Base', false)) {
                    throw new main\mgLibs\exceptions\Base($error['message'], $error['code']);
                } else {
                    throw new \Exception($error['message'], $error['code']);
                }
            }
            return true;
        }

        public static function listClassesInNamespace($className)
        {
            $originClassName = $className;
            $className = ltrim($className, '\\');
            $fileName = '';
            $namespace = '';
            if ($lastNsPos = strrpos($className, '\\')) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }

            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className);
            foreach (self::$availableDirs as $dir) {
                $tmp = $dir . $fileName;
                if (file_exists($tmp)) {
                    $foundFile = $tmp;
                }
            }

            $files = array();
            if ($handle = opendir($foundFile)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != ".." && strpos($entry, '.php') === (strlen($entry) - 4)) {
                        $files[] = __NAMESPACE__ . '\\' . $originClassName . '\\' . substr($entry, 0, strlen($entry) - 4);
                    }
                }

                closedir($handle);
            }

            return $files;
        }
    }
}
