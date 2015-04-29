<?php

namespace DRI\SugarCRM\Component\Database\LogicHook;

use DRI\SugarCRM\Component\Database\Exception;
use DRI\SugarCRM\Component\Database\TransactionalManager;

/**
 * Experimenal logic hook, use with care!
 *
 * Read README.md
 *
 * @author Emil Kilhage
 */
class TransactionManager
{
    /**
     * @var bool
     */
    private static $inApiCall = false;

    /**
     * @var bool
     */
    private static $success = true;

    /**
     * @var array
     */
    private static $exceptions = array();

    /**
     * @var TransactionalManager
     */
    private $db;

    /**
     * @var \LoggerManager
     */
    private $log;

    /**
     *
     */
    public function __construct()
    {
        $this->db = \DBManagerFactory::getInstance();
        $this->log = \LoggerManager::getLogger();
    }

    /**
     * Checks if the database handles transactions.
     *
     * @return bool
     */
    private function checkTransactionSupport()
    {
        return $this->db instanceof TransactionalManager;
    }

    /**
     * This method is called just be before a api method is called.
     *
     * Here turn off autocommit in order to enable transaction handling the all api calls.
     *
     * This method is called from RestService::execute
     *
     * Hook Type: before_api_call
     * http://support.sugarcrm.com/02_Documentation/04_Sugar_Developer/Sugar_Developer_Guide_7.2/60_Logic_Hooks/60_API_Hooks/before_api_call/
     *
     * @param string $event
     * @param array  $arguments
     */
    public function beforeApiCall($event, array $arguments)
    {
        // If this method has been called, we can assume that we are inside a api call
        self::$inApiCall = true;
        // Set this to true initially, if TransactionManager::handleException are not called, an exception are not thrown.
        self::$success = true;

        // Does the db handles transaction?
        if ($this->checkTransactionSupport()) {
            if ($this->db->isAutocommitEnabled()) {
                $this->log->info(__METHOD__.':: Autocommit is on, turning off');
                $this->db->disableAutocommit();
            }
        }
    }

    /**
     * This method gets called if an exception is thrown during the execution of a api call.
     *
     * When this is done, set the self::$success variable to false
     * and store the exception or order for TransactionManager::beforeRespond
     * to be able to rollback the transaction instead of commit.
     *
     * This method is called from RestService::handleException
     *
     * Hook Type: handle_exception
     * http://support.sugarcrm.com/02_Documentation/04_Sugar_Developer/Sugar_Developer_Guide_7.1/60_Logic_Hooks/20_Module_Hooks/handle_exception2/
     *
     * @param string     $event
     * @param \Exception $exception
     */
    public function handleException($event, $exception)
    {
        self::$success = false;
        self::$exceptions[] = $exception;
    }

    /**
     * This method is called just before the response is sent back from the server.
     *
     * It will commit/rollback the current transaction depending on if
     * TransactionManager::handleException has been called
     * (an exception has been thrown) during the execution of a api call.
     *
     * This method is called from RestService::execute
     *
     * Hook Type: before_respond
     * http://support.sugarcrm.com/02_Documentation/04_Sugar_Developer/Sugar_Developer_Guide_7.2/60_Logic_Hooks/60_API_Hooks/before_respond/
     *
     * @param string        $event
     * @param \RestResponse $response
     */
    public function beforeRespond($event, \RestResponse $response)
    {
        // Are we in an api call and autocommit has been turned on
        if (self::$inApiCall) {

            // Does the db handles transaction?
            if ($this->checkTransactionSupport()) {
                // Are the request successful or have an error occured?
                if (self::$success) {
                    $this->log->info(__METHOD__.' :: Committing transaction to db');
                    $this->db->commit();
                } else {
                    $this->log->fatal(__METHOD__.' :: Error occurred, rolling back');

                    foreach (self::$exceptions as $i => $e) {
                        unset(self::$exceptions[$i]);
                        $this->log->fatal("$e");
                    }

                    $this->db->rollback();
                }
            }
        }
    }
}
