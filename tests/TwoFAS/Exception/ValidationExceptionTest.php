<?php

use TwoFAS\Account\Exception\ValidationException;
use TwoFAS\ValidationRules\ValidationRules;

class ValidationExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testRequiredValidationRule()
    {
        $errors = ['error' => [
            'code' => 9030,
            'msg'  => [
                'code' => [
                    'validation.required'
                ]
            ]
        ]];

        $exception = $this->getException($errors);
        $this->assertEquals([ValidationRules::REQUIRED], $exception->getError('code'));
    }

    public function testRequiredWithValidationRule()
    {
        $errors = ['error' => [
            'code' => 9030,
            'msg'  => [
                'code' => [
                    'validation.required_with:sms'
                ]
            ]
        ]];

        $exception = $this->getException($errors);
        $this->assertEquals([ValidationRules::REQUIRED_WITH], $exception->getError('code'));
    }

    public function testRequiredIfValidationRule()
    {
        $errors = ['error' => [
            'code' => 9030,
            'msg'  => [
                'code' => [
                    'validation.required_if:method,sms,call'
                ]
            ]
        ]];

        $exception = $this->getException($errors);
        $this->assertEquals([ValidationRules::REQUIRED_IF], $exception->getError('code'));
    }

    public function testUniqueValidationRule()
    {
        $errors = ['error' => [
            'code' => 9030,
            'msg'  => [
                'code' => [
                    'validation.unique'
                ]
            ]
        ]];

        $exception = $this->getException($errors);
        $this->assertEquals([ValidationRules::UNIQUE], $exception->getError('code'));
    }

    public function testUniquePhoneNumberValidationRule()
    {
        $errors = ['error' => [
            'code' => 9030,
            'msg'  => [
                'code' => [
                    'validation.unique_phone_number'
                ]
            ]
        ]];

        $exception = $this->getException($errors);
        $this->assertEquals([ValidationRules::UNIQUE_PHONE_NUMBER], $exception->getError('code'));
    }

    public function testRegexDot()
    {
        $errors = ['error' => [
            'code' => 9030,
            'msg'  => [
                'code' => [
                    'validationDstring'
                ]
            ]
        ]];

        $exception = $this->getException($errors);
        $this->assertEquals(['validation.unsupported'], $exception->getError('code'));
    }

    public function testUnsupportedValidationRule()
    {
        $errors = ['error' => [
            'code' => 9030,
            'msg'  => [
                'code' => [
                    'validation.new_not_existing'
                ]
            ]
        ]];

        $exception = $this->getException($errors);
        $this->assertEquals(['validation.unsupported'], $exception->getError('code'));
    }

    public function testGetErrors()
    {
        $errors = ['error' => [
            'code' => 9030,
            'msg'  => [
                'code'        => [
                    'validation.required'
                ],
                'method'      => [
                    'validation.string',
                    'validation.client_has_no_integrations_with_paid_method_if_card_is_primary'
                ],
                'phone'       => [
                    'validation.two_f_a_s_formattable'
                ],
                'totp_secret' => [
                    'validation.not_exists'
                ],
                'email'       => [
                    'validation.required_if:method,email'
                ],
                'call'        => [
                    'validation.max,string',
                    'validation.regex'
                ],
                'channel'     => [
                    'validation.channel_enabling_rules'
                ],
                'source'      => [
                    'validation.valid_client_source'
                ],
                'password'    => [
                    'validation.client_password_equals'
                ],
                'key'         => [
                    'validation.valid_key_type'
                ]
            ]
        ]];

        $expectedErrors = [
            'code'        => [
                'validation.required'
            ],
            'method'      => [
                'validation.string',
                'validation.client_has_no_integrations_with_paid_method_if_card_is_primary'
            ],
            'phone'       => [
                'validation.two_f_a_s_formattable'
            ],
            'totp_secret' => [
                'validation.unsupported'
            ],
            'email'       => [
                'validation.required_if'
            ],
            'call'        => [
                'validation.unsupported',
                'validation.regex'
            ],
            'channel'     => [
                'validation.channel_enabling_rules'
            ],
            'source'      => [
                'validation.valid_client_source'
            ],
            'password'    => [
                'validation.client_password_equals'
            ],
            'key'         => [
                'validation.valid_key_type'
            ]
        ];

        $exception = $this->getException($errors);
        $this->assertEquals($expectedErrors, $exception->getErrors());
    }

    public function testGetBareError()
    {
        $errors = ['error' => [
            'code' => 9030,
            'msg'  => [
                'totp_secret' => [
                    'validation.string,unique'
                ],
                'email'       => [
                    'validation.required_if:method,email'
                ],
                'call'        => [
                    'validation.max,string',
                    'validation.regex'
                ]
            ]
        ]];

        $exception = $this->getException($errors);
        $this->assertEquals(['validation.string,unique'], $exception->getBareError('totp_secret'));
        $this->assertEquals(['validation.required_if:method,email'], $exception->getBareError('email'));
        $this->assertEquals(['validation.max,string', 'validation.regex'], $exception->getBareError('call'));
    }

    public function testHasError()
    {
        $errors = ['error' => [
            'code' => 9030,
            'msg'  => [
                'totp_secret' => [
                    'validation.string'
                ],
                'email'       => [
                    'validation.required_if:method,email'
                ],
                'call'        => [
                    'validation.max,string',
                    'validation.regex'
                ]
            ]
        ]];

        $exception = $this->getException($errors);
        $this->assertTrue($exception->hasError('totp_secret', ValidationRules::STRING));
        $this->assertTrue($exception->hasError('email', ValidationRules::REQUIRED_IF));
        $this->assertFalse($exception->hasError('email', ValidationRules::REQUIRED));
        $this->assertTrue($exception->hasError('call', ValidationRules::REGEX));
        $this->assertFalse($exception->hasError('call', ValidationRules::MAX));
    }

    public function testStrictMode()
    {
        $errors = [
            'field' => ['validation.required']
        ];

        $exception = new ValidationException($errors);
        $this->assertEquals([ValidationRules::REQUIRED], $exception->getError('field'));
        $this->assertTrue($exception->hasKey('field'));
        $this->assertFalse($exception->hasKey('Field'));
        $this->assertTrue($exception->hasError('field', 'validation.required'));
        $this->assertFalse($exception->hasError('Field', 'validation.required'));
        $this->assertFalse($exception->hasError('field', 'validation.Required'));
    }

    private function getException(array $errors)
    {
        return new ValidationException($errors['error']['msg']);
    }
}
