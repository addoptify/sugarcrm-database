<?php

namespace DRI\SugarCRM\Component\Database;

/**
 * @author Emil Kilhage
 */
interface TransactionalManager
{
    /**
     * Start a Transaction.
     *
     * @return mixed
     */
    public function beginTransaction();

    /**
     * Disables autocommit.
     *
     * @return bool
     */
    public function disableAutocommit();

    /**
     * Enables autocommit.
     *
     * @return bool
     */
    public function enableAutocommit();

    /**
     * Checks if the autocommit is enabled.
     *
     * @return bool
     */
    public function isAutocommitEnabled();

    /**
     * @return bool
     */
    public function commit();

    /**
     * @return bool
     */
    public function rollback();
}
