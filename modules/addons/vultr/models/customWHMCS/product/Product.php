<?php

namespace MGModule\vultr\models\customWHMCS\product;

/**
 * @SuppressWarnings(PHPMD)
 */
class Product extends MGModule\vultr\models\whmcs\product\product
{
    /**
     * @throws \MGModule\vultr\mgLibs\exceptions\System
     */
    public function loadConfiguration($params)
    {
        return new Configuration($this->id);
    }
}
