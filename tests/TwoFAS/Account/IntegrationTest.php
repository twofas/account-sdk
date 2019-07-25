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
    }

    public function testSettersAndGetters()
    {
        $id    = 123;
        $login = uniqid();
        $name  = 'DummyIntegration';

        $this->integration
            ->setId($id)
            ->setLogin($login)
            ->setName($name);

        $this->assertEquals($id, $this->integration->getId());
        $this->assertEquals($login, $this->integration->getLogin());
        $this->assertEquals($name, $this->integration->getName());
    }

    public function testGetIntegrationAsArray()
    {
        $id    = 123;
        $login = uniqid();
        $name  = 'DummyIntegration';

        $this->integration
            ->setId($id)
            ->setLogin($login)
            ->setName($name);

        $expectedArray = array(
            'id'    => $id,
            'login' => $login,
            'name'  => $name
        );

        $this->assertEquals($expectedArray, $this->integration->toArray());
    }
}
