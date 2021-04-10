<?php

namespace MGModule\vultr\helpers;

class PathHelper
{
    public static function getWhmcsPath($pathNumber = 5): string
    {
        $currentDir = __DIR__;

        for ($i = 1; $i < $pathNumber; $i++) {
            $currentDir = dirname($currentDir);
        }
        return $currentDir;
    }
}
