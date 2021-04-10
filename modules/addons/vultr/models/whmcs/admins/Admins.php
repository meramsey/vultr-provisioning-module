<?php
namespace MGModule\vultr\models\whmcs\admins;

use MGModule\vultr as main;

/**
 * Description of Repository
 */
class Admins extends main\mgLibs\models\Repository
{
    public function getModelClass(): string
    {
        return __NAMESPACE__ . '\Admin';
    }

    /**
     * @return Admin[]
     * @throws \MGModule\vultr\mgLibs\exceptions\System
     */
    public function get(): array
    {
        return parent::get();
    }
}
