<?php

namespace TwoFAS\Account;

/**
 * List of error codes returned by API.
 *
 * @package TwoFAS\Account
 */
class Errors
{
    const USER_INPUT_ERROR              = 10001;
    const TOKEN_SHOULD_NOT_BE_REFRESHED = 10100;
    const MODEL_NOT_FOUND               = 10404;
    const PASSWORD_RESET_ATTEMPTS_LIMIT = 14403;
    const UNAUTHORIZED                  = 14007;
}
