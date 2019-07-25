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
    private $login;

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
        return array(
            'id'    => $this->getId(),
            'login' => $this->getLogin(),
            'name'  => $this->getName()
        );
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
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     *
     * @return Integration
     */
    public function setLogin($login)
    {
        $this->login = (string) $login;
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