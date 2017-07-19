<?php

namespace TwoFAS\Account;

final class Card
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $lastFour;

    /**
     * @var int
     */
    private $expMonth;

    /**
     * @var int
     */
    private $expYear;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return Card
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastFour()
    {
        return $this->lastFour;
    }

    /**
     * @param string $lastFour
     *
     * @return Card
     */
    public function setLastFour($lastFour)
    {
        $this->lastFour = $lastFour;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpMonth()
    {
        return $this->expMonth;
    }

    /**
     * @param int $expMonth
     *
     * @return Card
     */
    public function setExpMonth($expMonth)
    {
        $this->expMonth = $expMonth;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpYear()
    {
        return $this->expYear;
    }

    /**
     * @param int $expYear
     *
     * @return Card
     */
    public function setExpYear($expYear)
    {
        $this->expYear = $expYear;
        return $this;
    }
}