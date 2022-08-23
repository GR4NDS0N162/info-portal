<?php

namespace User\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Model\Phone;
use User\Model\PhoneRepository;

class PhoneRepositoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new PhoneRepository(
            $container->get(AdapterInterface::class),
            new ReflectionHydrator(),
            new Phone(''),
        );
    }
}
