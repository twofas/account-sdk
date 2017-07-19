<?php

namespace TwoFAS\Account\Exception;

class PasswordResetAttemptsRemainingIsReachedException extends Exception
{
    /**
     * @var int
     */
    private $minutesToNextReset;

    /**
     * @param string         $message
     * @param int            $code
     * @param Exception|null $previous
     * @param int            $minutesToNextReset
     */
    public function __construct($message = "", $code = 0, Exception $previous = null, $minutesToNextReset = 0)
    {
        parent::__construct($message, $code, $previous);

        $this->minutesToNextReset = $minutesToNextReset;
    }

    /**
     * @return int
     */
    public function getMinutesToNextReset()
    {
        return $this->minutesToNextReset;
    }
}