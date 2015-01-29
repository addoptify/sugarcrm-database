Info
---------------------------------

Enhanced database management for SugarCRM <=7.2

Author: Emil Kilhage <emil.kilhage@dri-nordic.com>

    * Adds support for transactions for MySQL InnoDB
    * Throws errors instead of triggering a die(); upon database errors

Installation
----------------------------------

## Install dependency

### Add dependency

    "require": {
        // ...
        "dri-nordic/sugarcrm-database": "~0.1.0"
        // ...
    },

### Update dependencies

    composer update
 
### Make sure that composer autoloading is enabled

    <?php // docroot/custom/Extension/application/Ext/Utils/composer.autoloader.php

    require dirname(dirname(dirname(dirname(dirname(__DIR__))))) . "/vendor/autoload.php";

## Configure the manager

    <?php // docroot/config_override.php

    $sugar_config['dbconfig']['db_manager_class'] = 'DRI\\SugarCRM\\Component\\Database\\MySQL\\Manager';

Usage
----------------------------------

    <?php
    
    use DRI\SugarCRM\Component\Database\TransactionalManager;

    try {
        if ($db instanceof TransactionalManager) {
            $db->beginTransaction();
         }
         
         // Do your stuff

        if ($db instanceof TransactionalManager) {
            $db->commit();
        }
    } catch (\Exception $e) {
        if ($db instanceof TransactionalManager) {
            $db->rollback();
        }

        throw $e;
    }

Experimental
----------------------------------

Don't use in production until this is more tested

This logic hook enables support in the the api (v10) for full
transaction support in all api calls by hooking in to the

    src/LogicHook/TransactionManager.php
    
Install this logic hook in the same path it is placed as in ./copy

    copy/custom/Extension/application/Ext/LogicHooks/dri-transaction-management.php
