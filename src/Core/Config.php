<?php

namespace Core;

class Config
{

    public static function Load(string $config_file = '../config.json') {
        $config_raw = file_get_contents($config_file);
        $config_json = json_decode($config_raw);
        return $config_json;
    }

}