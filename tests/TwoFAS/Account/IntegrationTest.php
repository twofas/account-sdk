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
        $this->assertNull($this->integration->getName());
    }

    public function testSettersAndGetters()
    {
        $id   = 123;
        $name = 'DummyIntegration';

        $this->integration
            ->setId($id)
            ->setName($name);

        $this->assertEquals($id, $this->integration->getId());
        $this->assertEquals($name, $this->integration->getName());
    }

    public function testGetIntegrationAsArray()
    {
        $id   = 123;
        $name = 'DummyIntegration';

        $this->integration
            ->setId($id)
            ->setName($name);

        $expectedArray = [
            'id'   => $id,
            'name' => $name
        ];

        $this->assertEquals($expectedArray, $this->integration->toArray());
    }
}
