<?php

namespace TwoFAS\Account;

use InvalidArgumentException;

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
     * @var array
     */
    private $channels = array(
        'sms'   => null,
        'call'  => null,
        'email' => null,
        'totp'  => null
    );

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
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * @param array $channels
     *
     * @return Integration
     *
     * @throws InvalidArgumentException
     */
    public function setChannels(array $channels)
    {
        foreach ($channels as $name => $value) {
            if (!$this->hasChannel($name)) {
                throw new InvalidArgumentException('Invalid channel name');
            }

            $this->channels[$name] = (bool) $value;
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function getChannel($name)
    {
        if (!$this->hasChannel($name)) {
            throw new InvalidArgumentException('Invalid channel name');
        }

        return $this->channels[$name];
    }

    /**
     * @param string $name
     *
     * @throws InvalidArgumentException
     */
    public function enableChannel($name)
    {
        if (!$this->hasChannel($name)) {
            throw new InvalidArgumentException('Invalid channel name');
        }

        $this->channels[$name] = true;
    }

    /**
     * @param string $name
     *
     * @throws InvalidArgumentException
     */
    public function forceDisableChannel($name)
    {
        if (!$this->hasChannel($name)) {
            throw new InvalidArgumentException('Invalid channel name');
        }

        $this->disableChannel($name);
        $this->channels[$name . '_force_disable'] = true;
    }

    /**
     * @param string $name
     *
     * @throws InvalidArgumentException
     */
    public function disableChannel($name)
    {
        if (!$this->hasChannel($name)) {
            throw new InvalidArgumentException('Invalid channel name');
        }

        $this->channels[$name] = false;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(array(
            'id'    => $this->getId(),
            'login' => $this->getLogin(),
            'name'  => $this->getName()
        ),
            $this->getChannelsWithPrefix()
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

    /**
     * @param string $name
     *
     * @return bool
     */
    private function hasChannel($name)
    {
        return array_key_exists($name, $this->channels);
    }

    /**
     * @return array
     */
    private function getChannelsWithPrefix()
    {
        return array_combine(
            array_map(
                function($key) {
                    return 'channel_' . $key;
                },
                array_keys($this->channels)
            ),
            $this->channels
        );
    }
}