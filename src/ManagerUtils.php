<?php

namespace DRI\SugarCRM\Component\Database;

/**
 * @author Emil Kilhage
 */
class ManagerUtils
{

    /**
     * @param string $userMessage
     * @param string $message
     * @param bool $dieOnError
     *
     * @throws Exception
     */
    public static function registerError($userMessage, $message, $dieOnError = false)
    {
        if(empty($message)) {
            $message = "Database error";
        }

        if(!empty($userMessage)) {
            $message = "$userMessage: $message";
        }

        $exception = new Exception($message);

        \LoggerManager::getLogger()->fatal($exception);

        throw $exception;
    }

}
