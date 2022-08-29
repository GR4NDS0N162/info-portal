<?php

namespace Application\Model;

use Application\Model\Entity\Email;
use Laminas\Db\ResultSet\HydratingResultSet;

interface EmailRepositoryInterface
{
    /**
     * @param integer $userId
     *
     * @return Email[]|HydratingResultSet
     */
    public function findEmailsOfUser(int $userId);

    /**
     * @param string $address
     *
     * @return Email
     */
    public function findEmail(string $address): Email;
}