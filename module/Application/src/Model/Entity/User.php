<?php

namespace Application\Model\Entity;

class User extends Profile
{
    /**
     * @var int
     */
    protected $positionId;
    /**
     * @var bool[]
     */
    protected $status;

    /**
     * @param int         $positionId
     * @param bool[]      $status
     * @param string      $password
     * @param Email[]     $emails
     * @param Phone[]     $phones
     * @param string|null $tempPassword
     * @param string|null $tpCreatedAt
     * @param string|null $surname
     * @param string|null $name
     * @param string|null $patronymic
     * @param int|null    $gender
     * @param string|null $birthday
     * @param string|null $image
     * @param string|null $skype
     * @param int|null    $id
     */
    public function __construct(
        $positionId = 0,
        $status = [],
        $password = '',
        $emails = [],
        $phones = [],
        $tempPassword = null,
        $tpCreatedAt = null,
        $surname = null,
        $name = null,
        $patronymic = null,
        $gender = null,
        $birthday = null,
        $image = null,
        $skype = null,
        $id = null
    ) {
        parent::__construct(
            $password,
            $emails,
            $phones,
            $tempPassword,
            $tpCreatedAt,
            $surname,
            $name,
            $patronymic,
            $gender,
            $birthday,
            $image,
            $skype,
            $id
        );

        $this->positionId = $positionId;
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getPositionId()
    {
        return $this->positionId;
    }

    /**
     * @param int $positionId
     */
    public function setPositionId($positionId)
    {
        $this->positionId = $positionId;
    }

    /**
     * @return bool[]
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param bool[] $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
}