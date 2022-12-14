<?php

namespace Application\Factory\Controller;

use Application\Controller\UserController;
use Application\Form\User\ChangePasswordForm;
use Application\Form\User\ProfileForm;
use Application\Form\User\UserFilterForm;
use Application\Form\User\ViewProfileForm;
use Application\Model\Command\UserCommandInterface;
use Application\Model\Repository\StatusRepositoryInterface;
use Application\Model\Repository\UserRepositoryInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Container as SessionContainer;

class UserControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        /** @var ContainerInterface $formManager */
        $formManager = $container->get('FormElementManager');

        return new UserController(
            $formManager->get(ProfileForm::class),
            $formManager->get(ViewProfileForm::class),
            $formManager->get(UserFilterForm::class),
            $formManager->get(ChangePasswordForm::class),
            $container->get(UserRepositoryInterface::class),
            $container->get(StatusRepositoryInterface::class),
            $container->get(UserCommandInterface::class),
            $container->get(SessionContainer::class),
        );
    }
}