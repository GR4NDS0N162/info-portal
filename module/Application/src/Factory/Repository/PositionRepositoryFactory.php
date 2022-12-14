<?php

namespace Application\Factory\Repository;

use Application\Model\Repository\PositionRepository;
use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class PositionRepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new PositionRepository(
            $container->get(AdapterInterface::class),
        );
    }
}