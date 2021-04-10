<?php

namespace MGModule\vultr\controllers\addon\admin;

use MGModule\vultr as main;

class Dns extends main\mgLibs\process\AbstractController
{
    public function indexHTML($input = array(), $vars = array()): array
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' and isset($input['changeNS'])) {
            $this->saveChanges($input, $vars);
            $vars['success'] = main\mgLibs\Lang::T('messages', 'NameServersChange');
        }
        $vars['nameServer'] = $this->getNameServers();
        return array(
            'tpl' => 'dns',
            'vars' => $vars,
            'input' => $input
        );
    }

    public function saveChanges($input = [], $vars = []): array
    {
        $arrayToSave = [
            'ns1' => $input['ns1'],
            'ns2' => $input['ns2'],
        ];
        $nameServerModel = new \MGModule\vultr\models\dns\Repository();
        $nsChange = $nameServerModel->updateNameServers($arrayToSave);

        if ($nsChange) {
            return [
                'success' => main\mgLibs\Lang::T('messages', '')
            ];
        }
        return [
            'error' => main\mgLibs\Lang::T('messages', '')
        ];
    }

    public function getNameServers()
    {
        $nameServerModel = new \MGModule\vultr\models\dns\Repository();

        return $nameServerModel->getNameServers();
    }
}
