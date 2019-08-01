# Getting started with 2FAS Account SDK

This SDK is used to create and manage an account in 2FAS from your PHP application.
The latest version of the 2FAS SDK can be found on [Github](https://github.com/twofas/account-sdk). 
The 2FAS SDK requires PHP version 5.3.3 or higher. 

> **Note**: API reference for this SDK is available [here](https://docs.2fas.com/account)

Follow these steps to create an account in 2FAS:

1. [Installation and creating account](#installation-and-creating-account)
2. [Managing account](#managing-account)

**Full documentation for our SDK can be found [here](https://docs.2fas.com/apigen/Account)**

## Installation and creating account

The SDK can only be installed using a composer. 
You can add the PHP SDK to your [composer.json](https://getcomposer.org/doc/04-schema.md) 
file with the [require](https://getcomposer.org/doc/03-cli.md#require) command:

```php
composer require twofas/account-sdk : "2.*"
```

If you are using a framework like Symfony or Laravel, the 2FAS SDK may be automatically loaded for you and ready to use in your application. 
If you're using Composer in an environment that doesn't handle autoloading, 
you can require the autoload file from the "vendor" directory created by Composer if you used the install command above.

### Creating SDK client

Before you start using SDK, you have to write some code. 
We use [OAuth](https://oauth.net/) for authentication, and you have to store tokens in your storage (eg. database).
All you have to do is implement `TwoFAS\Account\OAuth\Interfaces\TokenStorage` and use `TwoFAS\Account\OAuth\TokenType::api()` token type:

```php

<?php
// Required if your environment does not handle autoloading
require __DIR__ . '/vendor/autoload.php';

//class MyTokenStorage implements \TwoFAS\Account\OAuth\Interfaces\TokenStorage {...}
$tokenStorage = new MyTokenStorage();
$tokenType = TokenType::api();

$twoFAS = new \TwoFAS\Account\TwoFAS($tokenStorage, $tokenType);
```

### Creating Account

Instead of creating an account with our [dashboard](https://dashboard.2fas.com/#/register), you can use these few lines of code:

```php

<?php
// SDK client has been created

$email = 'foo@example.com';
$password = $passwordConfirmation = 'You secret password';
$source = 'api';

$client = $twofas->createClient($email, $password, $passwordConfirmation, $source);
```

### Creating OAuth Tokens

Now that you have created an account, you need to create 2 types of tokens to authenticate in the API: *setup* and *api*.
The first one is used only to create integration while the second is used in other cases. 
You do not have to worry about which one to use, because TokenStorage will do it for you.

```php

<?php
// SDK client and Account has been created

$email = 'foo@example.com';
$password = 'Your secret password';
$name = 'My Site';

$twoFAS->generateOAuthSetupToken($email, $password);
$integration = $twoFAS->createIntegration($name);
$twoFAS->generateIntegrationSpecificToken($email, $password, $integration->getId());
```

## Managing account

### Managing integration

After creating integration, you can update its attributes (e.g. name) 
or decide which channels should be enabled for this integration.

```php

<?php
// SDK client and Account has been created

$integrationId = 123;
$integration = $twoFAS->getIntegration($integrationId);
$integration
  ->setName('My New Site')
  ->enableChannel('email');
  
// Update integration  
$twoFAS->updateIntegration($integration);

// Delete Integration
$twoFAS->deleteIntegration($integration);
```

### Creating Key

To use our main [SDK](https://github.com/twofas/sdk) you have to create a Key which is used in it to authenticate:

```php

<?php
// SDK client has been created

$keyName = 'My Key';
$integrationId = 123;
$integration = $twoFAS->getIntegration($integrationId);

$twoFAS->createKey($integration->getId(), $keyName);
```