<?php

namespace Application\Model\Command;

use Laminas\Db\Adapter\AdapterInterface;

class PhoneCommand implements PhoneCommandInterface
{
    /**
     * @var AdapterInterface
     */
    private $db;

    /**
     * @param AdapterInterface $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function updatePhones($oldList, $newList)
    {
        // TODO: Implement updatePhones() method.
    }
}