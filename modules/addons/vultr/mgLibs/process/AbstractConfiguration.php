<?php

namespace MGModule\vultr\mgLibs\process;

abstract class AbstractConfiguration
{
    public $debug = false;

    public $systemName = false;

    public $name = false;

    public $description = false;

    public $clientareaName = false;

    public $encryptHash = false;

    public $version = false;

    public $author = '<a href="https://www.vultr.com" target="_blank">Vultr</a>';

    public $tablePrefix = false;

    public $modelRegister = array();

    private $_customConfigs = array();

    public function __isset($name)
    {
        return isset($this->_customConfigs[$name]);
    }

    public function __get($name)
    {
        if (isset($this->_customConfigs[$name])) {
            return $this->_customConfigs[$name];
        }
    }

    public function __set($name, $value)
    {
        $this->_customConfigs[$name] = $value;
    }

    public function getAddonMenu(): array
    {
        return array();
    }

    public function getAddonWHMCSConfig(): array
    {
        return array();
    }

    public function getServerConfigController(): string
    {
        return 'configuration';
    }

    public function getServerActionsController(): string
    {
        return 'actions';
    }

    public function getServerCAController(): string
    {
        return 'home';
    }

    public function getAddonAdminController(): string
    {
        return 'actions';
    }

    public function getAddonCAController(): string
    {
        return 'home';
    }
}
