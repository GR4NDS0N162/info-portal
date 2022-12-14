<?php

namespace Application\Factory\Repository;

use Application\Model\Repository\EmailRepositoryInterface;
use Application\Model\Repository\PhoneRepositoryInterface;
use Application\Model\Repository\StatusRepositoryInterface;
use Application\Model\Repository\UserRepository;
use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class UserRepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new UserRepository(
            $container->get(AdapterInterface::class),
            $container->get(EmailRepositoryInterface::class),
            $container->get(PhoneRepositoryInterface::class),
            $container->get(StatusRepositoryInterface::class),
        );
    }
}