<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_1_1($module)
{
    return $module->getInstaller()->createTables();
}
