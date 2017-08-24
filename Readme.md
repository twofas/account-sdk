# Getting started

## Install

### via composer

```bash
composer require twofas/account-sdk : "2.*"
```

## Documentation

### Creating SDK client

```php
$twoFAs = new \TwoFAS\Account\TwoFAS($tokenStorage, $tokenType);
```

`$tokenStorage` can be any storage (db, file etc.) which implements `\TwoFAS\Account\OAuth\Interfaces\TokenStorage` interface.

`$tokenType` is a type of token which can be found in `\TwoFAS\Account\OAuth\TokenType` class.

### Methods

#### getClient

Used for get client from 2fas.

##### Example

```php
$client = $twoFAs->getClient();
```

##### Response

###### Successful

Returns [\TwoFAS\Account\Client](#client) object.

###### Unsuccessful

Method can throw exceptions:

* `Exception` in case of unspecified type of exception

```php
Exception 'TwoFAS\Account\Exception\Exception'
with message 'Unsupported response'
```

#### createClient

Used for create client in 2fas.

##### Parameters

Type | Name | Description
--- | --- | ---
string | $email | Valid e-mail address
string | $password | Client's password
string | $passwordConfirmation | Confirmation of the client's password
string | $phone | Valid phone number

##### Example

```php
$client = $twoFAs->createClient('client@example.com', 'pass123', 'pass123', '14157012311');
```

##### Response

###### Successful

Returns [\TwoFAS\Account\Client](#client) object.

###### Unsuccessful

Method can throw exceptions:

* `Exception` in case of unspecified type of exception

```php
Exception 'TwoFAS\Account\Exception\Exception'
with message 'Unsupported response'
```
* `ValidationException` if you send invalid data in request [more](#more-validationexception)

```php
Exception 'TwoFAS\Account\Exception\ValidationException'
with message 'Validation exception'
```

#### getIntegration

Used for get integration with specific ID from 2fas.

Type | Name | Description
--- | --- | ---
int | $integrationId | ID of the integration

##### Example

```php
$integration = $twoFAs->getIntegration(123);
```

##### Response

###### Successful

Returns [\TwoFAS\Account\Integration](#integration) object.

###### Unsuccessful

Method can throw exceptions:

* `Exception` in case of unspecified type of exception

```php
Exception 'TwoFAS\Account\Exception\Exception'
with message 'Unsupported response'
```
* `NotFoundException` in case of resource is not found on the server

```php
Exception 'TwoFAS\Account\Exception\NotFoundException'
with message 'Resource not found'
```

#### createIntegration

Used for create integration in 2fas.

##### Parameters

Type | Name | Description
--- | --- | ---
string | $name | Integration name

##### Example

```php
$integration = $twoFAs->createIntegration('my-website');
```

##### Response

###### Successful

Returns [\TwoFAS\Account\Integration](#integration) object.

###### Unsuccessful

Method can throw exceptions:

* `Exception` in case of unspecified type of exception

```php
Exception 'TwoFAS\Account\Exception\Exception'
with message 'Unsupported response'
```
* `ValidationException` if you send invalid data in request [more](#more-validationexception)

```php
Exception 'TwoFAS\Account\Exception\ValidationException'
with message 'Validation exception'
```

#### updateIntegration

Used for update integration data.

##### Parameters

Type | Name | Description
--- | --- | ---
Integration | $integration | Integration object

##### Example

```php
$integration = $twoFAs->updateIntegration($integration);
```

##### Response

###### Successful

Returns [\TwoFAS\Account\Integration](#integration) object.

###### Unsuccessful

Method can throw exceptions:

* `Exception` in case of unspecified type of exception

```php
Exception 'TwoFAS\Account\Exception\Exception'
with message 'Unsupported response'
```
* `ValidationException` if you send invalid data in request [more](#more-validationexception)

```php
Exception 'TwoFAS\Account\Exception\ValidationException'
with message 'Validation exception'
```

#### deleteIntegration

Used for deleting integration.

##### Parameters

Type | Name | Description
--- | --- | ---
Integration | $integration | Integration object

##### Example

```php
$response = $twoFAs->deleteIntegration($integration);
```

##### Response

###### Successful

Returns [\TwoFAS\Account\NoContent](#noContent) object.

###### Unsuccessful

Method can throw exceptions:

* `Exception` in case of unspecified type of exception

```php
Exception 'TwoFAS\Account\Exception\Exception'
with message 'Unsupported response'
```

#### createKey

Used for create new integration key in 2fas. 

##### Parameters

Type | Name | Description
--- | --- | ---
int | $integrationId | ID of the integration
string | $name | Key's name

##### Example

```php
$key = $twoFAs->createKey($integration->getId(), 'Production key');
```

##### Response

###### Successful

Returns [\TwoFAS\Account\Key](#key) object.

###### Unsuccessful

Method can throw exceptions:

* `Exception` in case of unspecified type of exception

```php
Exception 'TwoFAS\Account\Exception\Exception'
with message 'Unsupported response'
```
* `ValidationException` if you send invalid data in request [more](#more-validationexception)

```php
Exception 'TwoFAS\Account\Exception\ValidationException'
with message 'Validation exception'
```

#### getPrimaryCard

Used for get primary card for specific client

##### Parameters

Type | Name | Description
--- | --- | ---
Client | $client | Client object

##### Example

```php
$card = $twoFAs->getPrimaryCard($client);
```

##### Response

###### Successful

Returns [\TwoFAS\Account\Card](#card) object.

###### Unsuccessful

Method can throw exceptions:

* `Exception` in case of unspecified type of exception

```php
Exception 'TwoFAS\Account\Exception\Exception'
with message 'Unsupported response'
```
* `NotFoundException` in case of resource is not found on the server

```php
Exception 'TwoFAS\Account\Exception\NotFoundException'
with message 'Resource not found'
```

#### resetPassword

Used for reset password in 2fas account - it sends email with link and instructions for password reset.

##### Parameters

Type | Name | Description
--- | --- | ---
string | $email | Client's e-mail

##### Example

```php
$twoFAs->resetPassword($email);
```

##### Response

###### Successful

Returns [\TwoFAS\Account\NoContent](#noContent) object.

###### Unsuccessful

Method can throw exceptions:

* `Exception` in case of unspecified type of exception

```php
Exception 'TwoFAS\Account\Exception\Exception'
with message 'Unsupported response'
```
* `PasswordResetAttemptsRemainingIsReachedException` in case of password reset attempts remaining is reached

```php
Exception 'TwoFAS\Account\Exception\PasswordResetAttemptsRemainingIsReachedException'
with message 'Limit of password reset attempts is already reached'
```

You can get additional information (for eg. minutes to next possible password reset) by calling:
```php
$exception->getMinutesToNextReset();
```
#### generateOAuthSetupToken

Used for generate OAuth Token with "Setup" scope. This kind of token is used for create Client and Integration.

##### Parameters

Type | Name | Description
--- | --- | ---
string | $email | Client's e-mail
string | $password | Client's password

##### Example

```php
$twoFAs->generateOAuthSetupToken($email, $password);
```

##### Response

###### Successful

Only store token in storage without any returned value.

###### Unsuccessful

Method can throw exceptions:

* `Exception` in case of unspecified type of exception

```php
Exception 'TwoFAS\Account\Exception\Exception'
with message 'Unsupported response'
```
* `AuthorizationException` in case of invalid credentials

```php
Exception 'TwoFAS\Account\Exception\AuthorizationException'
with message 'Invalid credentials'
```
* `ValidationException` if you send invalid data in request [more](#more-validationexception)

```php
Exception 'TwoFAS\Account\Exception\ValidationException'
with message 'Validation exception'
```

#### generateIntegrationSpecificToken

Used for generate OAuth Token with specific scope (which can be found in `\TwoFAS\Account\OAuth\TokenType`) for concrete integration.

##### Parameters

Type | Name | Description
--- | --- | ---
string | $email    | Client's e-mail
string | $password | Client's password
int    | $integrationId | Integration's ID

##### Example

```php
$twoFAs->generateIntegrationSpecificToken($email, $password, $integrationId);
```

##### Response

###### Successful

Only store token in storage without any returned value.

###### Unsuccessful

Method can throw exceptions:

* `Exception` in case of unspecified type of exception

```php
Exception 'TwoFAS\Account\Exception\Exception'
with message 'Unsupported response'
```
* `AuthorizationException` in case of invalid credentials

```php
Exception 'TwoFAS\Account\Exception\AuthorizationException'
with message 'Invalid credentials'
```
* `ValidationException` if you send invalid data in request [more](#more-validationexception)

```php
Exception 'TwoFAS\Account\Exception\ValidationException'
with message 'Validation exception'
```

#### getConfig

Used for get public configuration options from 2FAS.

##### Example

```php
$twoFAs->getConfig();
```

##### Response

###### Successful

Return array that contains configuration options.

###### Unsuccessful

Method can throw exceptions:

* `Exception` in case of unspecified type of exception

```php
Exception 'TwoFAS\Account\Exception\Exception'
with message 'Unsupported response'
```

#### setBaseUrl

Used to change the address of the server to which the SDK connects.

##### Parameters

Type | Name | Description
--- | --- | ---
string | $url | Account's API URL

##### Example

```php
$twoFAs = $twoFAs->setBaseUrl('http://account.api');
```

##### Response

Returns `\TwoFAS\Account\TwoFAs` instance allowing method chaining.

## Objects

### Card

Card object is returned by:

* [getPrimaryCard](#getprimarycard)

It is an [Entity](https://en.wikipedia.org/wiki/Entity) with methods:

#### Methods

Name | Type | Description
--- | --- | ---
getId() | int | Get card ID
setId($id) | Card | Set card ID
getLastFour() | string | Get last four numbers from Card
setLastFour($numbers) | Card | Set last four numbers in Card
getExpMonth() | int | Get month of Card expires
setExpMonth($month) | Card | Set month of Card expires
getExpYear() | int | Get year of Card expires
setExpYear($year) | Card | Set year of Card expires

#### Usage
```php
$card
    ->setId(123)
    ->setLastFour('1234')
    ->setExpMonth(4)
    ->setExpYear(2027);

$id = $card->getId();
$lastFour = $card->getLastFour();
```
### Client

Client object is returned by:

* [getClient](#getclient)
* [createClient](#createclient)

It is an [Entity](https://en.wikipedia.org/wiki/Entity) with methods:

#### Methods

Name | Type | Description
--- | --- | ---
getId() | int | Get client ID
setId($id) | Client | Set client ID
getEmail() | string | Get client e-mail
setEmail($mail) | Client | Set client e-mail
hasCard() | bool | Is client has primary card
setHasCard($hasCard) | Client | Sets primary card flag
hasGeneratedPassword() | bool | Is client has password generated automatically
setHasGeneratedPassword($hasPassword) | Client | Set password generated flag
getPrimaryCardId() | string | Get primary card ID
setPrimaryCardId($id) | Client | Set primary card ID

#### Usage
```php
$client
    ->setId(123)
    ->setEmail('foo@bar.com')
    ->setPrimaryCardId('jhfd73');

$id = $client->getId();
$email = $client->getEmail();
```
### Integration

Integration object is returned by:

* [getIntegration](#getintegration)
* [createIntegration](#createintegration)
* [updateIntegration](#updateintegration)

It is an [Entity](https://en.wikipedia.org/wiki/Entity) with methods:

#### Methods

Name | Type | Description
--- | --- | ---
getId() | int | Get integration ID
setId($id) | Client | Set integration ID
getLogin() | string | Get integration login
setLogin($login) | Integration | Set integration login
getName() | string | Get integration name
setName($name) | Integration | Set integration name
getPublicKey() | string | Get integration public key
setPublicKey($key) | Integration | Set integration public key
getPrivateKey() | string | Get integration private key
setPrivateKey($key) | Integration | Set integration private key
getChannels() | array | Get integration channels
setChannels($channels) | Integration | Set integration channels
getChannel($name) | bool | Get specific integration channel status
enableChannel($name) | void | Enable specific integration channel status
disableChannel($name) | void | Disable specific integration channel status (if any user use this channel, error is returned - use `forceDisableChannel` instead)
forceDisableChannel($name) | void | Force disable specific integration channel status
toArray() | array | Converts integration object to array

#### Usage
```php
$integration
    ->setId(123)
    ->setLogin('foo-bar integration')
    ->setChannels(array(...));

$id = $integration->getId();
$channels = $integration->getChannels();
```
### Key

Key object is returned by:

* [createKey](#createkey)

It is an [Entity](https://en.wikipedia.org/wiki/Entity) with methods:

#### Methods

Name | Type | Description
--- | --- | ---
getToken() | string | Get key token

#### Usage
```php
$key = new Key('57ff5cde6....');

$token = $key->getToken();
```

## More about exceptions

### ValidationException

Validation exceptions may contain multiple keys and rules.
For simplicity of integrating this exception has few methods:

#### Methods
Name | Type | Description
--- | --- | ---
getErrors() | `array` | Returns all errors as constants
getError($key) | `array or null` | Returns all failing rules for key (as constants), or null if key passes validation
getBareError($key) | `array or null` | Returns all failing rules for key (as bare strings), or null if key passes validation
hasKey($key) | `boolean` | Check if certain field failed validation
hasError($key, $rule) | `boolean` | Check if certain key failed specified rule
