<?php

use TwoFAS\Account\Integration;

class IntegrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Integration
     */
    private $integration;

    protected function setUp()
    {
        parent::setUp();

        $this->integration = new Integration();
    }

    public function testInstance()
    {
        $this->assertNull($this->integration->getId());
        $this->assertNull($this->integration->getLogin());
        $this->assertNull($this->integration->getName());
        $this->assertEquals(array(
            'sms'   => null,
            'call'  => null,
            'email' => null,
            'totp'  => null
        ), $this->integration->getChannels());
    }

    public function testSettersAndGetters()
    {
        $id = 123;
        $login = uniqid();
        $name = 'DummyIntegration';
        $channels = array(
            'sms'   => false,
            'call'  => true,
            'email' => false,
            'totp'  => true
        );

        $this->integration
            ->setId($id)
            ->setLogin($login)
            ->setName($name)
            ->setChannels($channels);

        $this->assertEquals($id, $this->integration->getId());
        $this->assertEquals($login, $this->integration->getLogin());
        $this->assertEquals($name, $this->integration->getName());
        $this->assertEquals($channels, $this->integration->getChannels());
    }

    public function testGetChannel()
    {
        $this->integration->setChannels(array(
            'sms'   => false,
            'call'  => true,
            'email' => false,
            'totp'  => true
        ));

        $this->assertTrue($this->integration->getChannel('call'));
        $this->assertTrue($this->integration->getChannel('totp'));
        $this->assertFalse($this->integration->getChannel('sms'));
        $this->assertFalse($this->integration->getChannel('email'));
    }

    public function testCannotGetNotExistingChannel()
    {
        $this->setExpectedException('\InvalidArgumentException', 'Invalid channel name');
        $this->integration->getChannel('not_exists');
    }

    public function testEnableChannel()
    {
        $this->integration->setChannels(array(
            'sms'   => false,
            'call'  => false,
            'email' => false,
            'totp'  => false
        ));
        $this->integration->enableChannel('sms');
        $this->integration->enableChannel('email');
        $this->assertTrue($this->integration->getChannel('sms'));
        $this->assertTrue($this->integration->getChannel('email'));
        $this->assertFalse($this->integration->getChannel('call'));
        $this->assertFalse($this->integration->getChannel('totp'));
    }

    public function testDisableChannel()
    {
        $this->integration->setChannels(array(
            'sms'   => true,
            'call'  => true,
            'email' => true,
            'totp'  => true
        ));
        $this->integration->disableChannel('call');
        $this->integration->disableChannel('totp');
        $this->assertTrue($this->integration->getChannel('sms'));
        $this->assertTrue($this->integration->getChannel('email'));
        $this->assertFalse($this->integration->getChannel('call'));
        $this->assertFalse($this->integration->getChannel('totp'));
    }

    public function testForceDisableChannel()
    {
        $this->integration->setChannels(array(
            'sms'   => true,
            'call'  => true,
            'email' => true,
            'totp'  => true
        ));

        $this->integration->forceDisableChannel('sms');

        $this->assertFalse($this->integration->getChannel('sms'));
        $this->assertTrue($this->integration->getChannel('sms_force_disable'));
        $this->assertTrue($this->integration->getChannel('call'));
        $this->assertTrue($this->integration->getChannel('email'));
        $this->assertTrue($this->integration->getChannel('totp'));
    }

    public function testGetIntegrationAsArray()
    {
        $id = 123;
        $login = uniqid();
        $name = 'DummyIntegration';
        $channels = array(
            'sms'   => false,
            'call'  => true,
            'email' => false,
            'totp'  => true
        );

        $this->integration
            ->setId($id)
            ->setLogin($login)
            ->setName($name)
            ->setChannels($channels);

        $expectedArray = array(
            'id' => $id,
            'login' => $login,
            'name' => $name,
            'channel_sms' => false,
            'channel_call' => true,
            'channel_email' => false,
            'channel_totp' => true
        );

        $this->assertEquals($expectedArray, $this->integration->toArray());
    }
}
