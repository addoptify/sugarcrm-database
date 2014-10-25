<?php

namespace DRI\SugarCRM\Component\Database\MySQL;

require_once "include/database/MysqliManager.php";

use DRI\SugarCRM\Component\Database\ManagerUtils;
use DRI\SugarCRM\Component\Database\TransactionalManager;

/**
 * Enables transaction support for MySQL databases
 *
 * Improves error handling by trowing an exception as soon as a database error occur
 *
 * @author Emil Kilhage
 */
class Manager extends \MysqliManager implements TransactionalManager
{

    /**
     * @param string $userMessage
     * @param string $message
     * @param bool $dieOnError
     *
     * @see ManagerUtils::registerError
     */
    protected function registerError($userMessage, $message, $dieOnError = false)
    {
        ManagerUtils::registerError($userMessage, $message, $dieOnError);
    }

    /**
     * @return bool|\mysqli_result
     */
    public function beginTransaction()
    {
        if ($this->database) {
            $success = $this->query("START TRANSACTION");
            return $success;
        }

        return false;
    }

    /**
     * @param bool $enabled
     *
     * @return bool
     */
    private function setAutocommit($enabled = true)
    {
        if ($this->database) {
            $enabled = $enabled ? 1 : 0;
            $success = $this->query("SET AUTOCOMMIT = $enabled");
            return $success;
        }

        return false;
    }

    /**
     * Commits pending changes to the database when the driver is setup to support transactions.
     *
     * @return bool true if commit succeeded, false if it failed
     *
     * @see TransactionalManager::commit
     */
    public function commit()
    {
        if ($this->database) {
            $success = $this->query("COMMIT");
            return $success;
        }

        return true;
    }

    /**
     * Rollsback pending changes to the database when the driver is setup to support transactions.
     *
     * @return bool true if rollback succeeded, false if it failed
     *
     * @see TransactionalManager::rollback
     */
    public function rollback()
    {
        if ($this->database) {
            $success = $this->query("ROLLBACK");
            return $success;
        }

        return false;
    }

    /**
     * @return bool
     *
     * @see TransactionalManager::disableAutocommit
     */
    public function disableAutocommit()
    {
        return $this->setAutocommit(false);
    }

    /**
     * @return bool
     *
     * @see TransactionalManager::enableAutocommit
     */
    public function enableAutocommit()
    {
        return $this->setAutocommit(true);
    }

    /**
     * @return bool
     *
     * @see TransactionalManager::isAutocommitEnabled
     */
    public function isAutocommitEnabled()
    {
        if ($this->database) {
            $result = $this->query("SELECT @@AUTOCOMMIT FROM DUAL;");
            $row = $this->fetchByAssoc($result);
            $success = isset($row['@@AUTOCOMMIT']) ? $row['@@AUTOCOMMIT'] : false;
            return $success == "1";
        }

        return false;
    }

}
