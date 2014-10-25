<?php

/**
 * @author Emil Kilhage, DRI Nordic <emil.kilhage@dri-nordic.com>
 */

$hook_array['before_api_call'][] = array (
    1,
    'DRI\SugarCRM\Component\Database\LogicHook\TransactionManager::startTransaction',
    'custom/library/DRI/SugarCRM/Component/Database/LogicHook/TransactionManager.php',
    'DRI\SugarCRM\Component\Database\LogicHook\TransactionManager',
    'beforeApiCall'
);

$hook_array['handle_exception'][] = array (
    1,
    'DRI\SugarCRM\Component\Database\LogicHook\TransactionManager::handleException',
    'custom/library/DRI/SugarCRM/Component/Database/LogicHook/TransactionManager.php',
    'DRI\SugarCRM\Component\Database\LogicHook\TransactionManager',
    'handleException'
);

$hook_array['before_respond'][] = array (
    1000, // This is the last thing that should be done
    'DRI\SugarCRM\Component\Database\LogicHook\TransactionManager::lastChance',
    'custom/library/DRI/SugarCRM/Component/Database/LogicHook/TransactionManager.php',
    'DRI\SugarCRM\Component\Database\LogicHook\TransactionManager',
    'beforeRespond'
);
