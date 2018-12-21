<?php

require('../src/autoload.php');

$config_load = Core\Config::Load('../config.json');

new Core\Initialize($config_load);