<?php
namespace MGModule\vultr\models\whmcs\addons;

/**
 * Description of Repository
 */
class Repository extends \MGModule\vultr\mgLibs\models\Repository
{
    public function getModelClass(): string
    {
        return __NAMESPACE__ . '\Addon';
    }

    /**
     * @return Addon[]
     * @throws \MGModule\vultr\mgLibs\exceptions\System
     */
    public function get(): array
    {
        return parent::get();
    }
}
