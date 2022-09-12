<?php

namespace Application\Model\Repository;

use Application\Model\Entity\Status;

interface StatusRepositoryInterface
{
    /**
     * @return Status[]
     */
    public function findAllStatuses();

    /**
     * @param int $userId
     *
     * @return Status[]
     */
    public function findStatusesOfUser($userId);
}