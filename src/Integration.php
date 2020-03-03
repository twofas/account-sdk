<?php

namespace TwoFAS\Account;

/**
 * This is an Entity that stores information about integration.
 *
 * @package TwoFAS\Account
 */
final class Integration
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id'   => $this->getId(),
            'name' => $this->getName()
        ];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Integration
     */
    public function setId($id)
    {
        $this->id = (int) $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Integration
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * @param null|string $publicKey
     *
     * @return Integration
     */
    public function setPublicKey($publicKey)
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @param null|string $privateKey
     *
     * @return Integration
     */
    public function setPrivateKey($privateKey)
    {
        $this->privateKey = $privateKey;
        return $this;
    }
}