<?php

namespace MGModule\vultr\mgLibs;

/**
 * Smarty Wrapper
 *
 * @SuppressWarnings(PHPMD)
 */
class Smarty
{
    private static $_instance;

    private $_smarty;
    private $_templateDIR;

    final private function __construct()
    {
    }

    /**
     * Set Template Dir
     *
     * @param string $dir
     * @throws \MGModule\vultr\mgLibs\Exceptions\System
     */
    public function setTemplateDir(string $dir)
    {
        if (is_array($dir)) {
            throw new Exceptions\System('Wrong Template Path');
        }
        self::I()->_templateDIR = $dir;
    }

    /**
     * Get Single-top Instance
     *
     * @return smarty
     */
    public static function I()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self();

            if (!class_exists('Smarty')) {
                if (file_exists(ROOTDIR . DS . 'includes' . DS . 'smarty' . DS . 'Smarty.class.php')) {
                    require_once(ROOTDIR . DS . 'includes' . DS . 'smarty' . DS . 'Smarty.class.php');
                } else {
                    die('Smarty does not exists!');
                }
            }

            self::$_instance->_smarty = new \Smarty();
        }

        return self::$_instance;
    }

    /**
     * Parse Template File
     *
     * @param string $template
     * @param array $vars
     * @param string $customDir
     * @return string
     * @throws exceptions\System
     * @global string $templates_compiledir
     */
    public function view(string $template, $vars = array(), $customDir = false): string
    {
        if (is_array($customDir)) {
            throw new Exceptions\System('Wrong Template Path');
        }

        global $templates_compiledir;
        if ($customDir) {
            self::I()->_smarty->template_dir = $customDir;
        } else {
            self::I()->_smarty->template_dir = self::I()->_templateDIR;
        }

        self::I()->_smarty->compile_dir = $templates_compiledir;
        self::I()->_smarty->force_compile = 1;
        self::I()->_smarty->caching = 0;

        $this->clear();

        self::I()->_smarty->assign('MGLANG', lang::getInstance());

        if (is_array($vars)) {
            foreach ($vars as $key => $val) {
                self::I()->_smarty->assign($key, $val);
            }
        }

        if (is_array(self::I()->_smarty->template_dir)) {
            $file = self::I()->_smarty->template_dir[0] . DS . $template . '.tpl';
        } else {
            $file = self::I()->_smarty->template_dir . DS . $template . '.tpl';
        }

        if (!file_exists($file)) {
            throw new exceptions\System('Unable to find Template:' . $file);
        }

        return self::I()->_smarty->fetch($template . '.tpl', uniqid());
    }

    protected function clear()
    {
        if (method_exists(self::I()->_smarty, 'clearAllAssign')) {
            self::I()->_smarty->clearAllAssign();
        } elseif (method_exists(self::I()->_smarty, 'clear_all_assign')) {
            self::I()->_smarty->clear_all_assign();
        }
    }

    final private function __clone()
    {
    }
}
