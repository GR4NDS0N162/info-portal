<?php

namespace Application\Model\Repository;

use Application\Model\Entity\Status;
use InvalidArgumentException;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Select;

class StatusRepository implements StatusRepositoryInterface
{
    private AdapterInterface $db;
    private Status $prototype;

    public function __construct(AdapterInterface $db)
    {
        $this->db = $db;
        $this->prototype = new Status();
    }

    public function findAllStatuses()
    {
        $select = new Select(['s' => 'status']);
        $select->columns([
            'id'    => 's.id',
            'name'  => 's.name',
            'label' => 's.label',
        ], false);

        /** @var Status[] $statuses */
        $statuses = Extracter::extractValues(
            $select,
            $this->db,
            $this->prototype->getHydrator(),
            $this->prototype
        );

        return $statuses;
    }

    public function findStatusesOfUser($userId)
    {
        $select = new Select(['us' => 'user_status']);
        $select->where(['us.user_id = ?' => $userId]);
        $select->join(
            ['s' => 'status'],
            'us.status_id = s.id',
            [],
        );
        $select->columns([
            'id'    => 's.id',
            'name'  => 's.name',
            'label' => 's.label',
        ], false);

        /** @var Status[] $statuses */
        $statuses = Extracter::extractValues(
            $select,
            $this->db,
            $this->prototype->getHydrator(),
            $this->prototype
        );

        return $statuses;
    }

    public function checkStatusOfUser(int $userId, $statusIdentifier): bool
    {
        $select = new Select(['us' => 'user_status']);
        $select->columns([
            'id'    => 's.id',
            'name'  => 's.name',
            'label' => 's.label',
        ], false);
        $select->join(
            ['s' => 'status'],
            'us.status_id = s.id',
            [],
        );

        if (is_integer($statusIdentifier)) {
            $statusColumn = 's.id';
        } elseif (is_string($statusIdentifier)) {
            $statusColumn = 's.name';
        } else {
            throw new InvalidArgumentException(
                sprintf(
                    'The argument $statusIdentifier must be of type integer or string. %s given',
                    is_object($statusIdentifier)
                        ? get_class($statusIdentifier)
                        : gettype($statusIdentifier)
                )
            );
        }

        $select->where([
            'us.user_id'  => $userId,
            $statusColumn => $statusIdentifier,
        ]);

        try {
            Extracter::extractValue(
                $select,
                $this->db,
                $this->prototype->getHydrator(),
                $this->prototype
            );
        } catch (InvalidArgumentException $ex) {
            return false;
        }

        return true;
    }
}