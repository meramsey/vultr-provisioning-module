<?php

namespace MGModule\vultr\mgLibs\whmcsAPI;

use MGModule\vultr as main;

class WhmcsAPI
{
    /**
     * @throws \MGModule\vultr\mgLibs\exceptions\System
     * @throws \MGModule\vultr\mgLibs\exceptions\WhmcsAPI
     */
    public static function request($command, $config)
    {
        $result = localAPI($command, $config, self::getAdmin());

        if ($result['result'] == 'error') {
            throw new main\mgLibs\exceptions\WhmcsAPI($result['message']);
        }

        return $result;
    }

    /**
     * @throws \MGModule\vultr\mgLibs\exceptions\System
     */
    public static function getAdmin()
    {
        static $username;

        if (empty($username)) {
            $data = main\mgLibs\MySQL\Query::select(array('username'), 'tbladmins', array(), array(), 1)->fetch();
            $username = $data['username'];
        }

        return $username;
    }

    /**
     * @throws \MGModule\vultr\mgLibs\exceptions\System
     * @throws \MGModule\vultr\mgLibs\exceptions\WhmcsAPI
     */
    public static function getAdminDetails($adminId)
    {
        $data = main\mgLibs\MySQL\Query::select(array('username'), 'tbladmins', array("id" => $adminId), array(), 1)->fetch();
        $username = $data['username'];

        $result = localAPI("getadmindetails", array(), $username);
        if ($result['result'] == 'error') {
            throw new main\mgLibs\exceptions\WhmcsAPI($result['message']);
        }

        $result['allowedpermissions'] = explode(",", $result['allowedpermissions']);
        return $result;
    }
}
