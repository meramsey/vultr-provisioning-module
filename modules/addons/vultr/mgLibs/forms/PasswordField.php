<?php

namespace MGModule\vultr\mgLibs\forms;

/**
 * Password Form Field
 */
class PasswordField extends AbstractField
{
    public $showPassword = false;
    public $type = 'password';

    public function prepare()
    {
        if (!$this->showPassword) {
            self::asteriskVar($this->value);
        }
    }

    public static function asteriskVar($input): string
    {
        $num = strlen($input);
        $input = '';

        for ($i = 0; $i < $num; $i++) {
            $input .= '*';
        }

        return $input;
    }
}
