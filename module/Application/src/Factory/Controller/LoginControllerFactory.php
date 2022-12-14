<?php

namespace Application\Factory\Controller;

use Application\Controller\LoginController;
use Application\Form\Login\LoginForm;
use Application\Form\Login\RecoverForm;
use Application\Form\Login\SignUpForm;
use Application\Model\Command\UserCommandInterface;
use Application\Model\Repository\UserRepositoryInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\Container as SessionContainer;

class LoginControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        /** @var ContainerInterface $formManager */
        $formManager = $container->get('FormElementManager');

        return new LoginController(
            $formManager->get(LoginForm::class),
            $formManager->get(SignUpForm::class),
            $formManager->get(RecoverForm::class),
            $container->get(UserCommandInterface::class),
            $container->get(UserRepositoryInterface::class),
            $container->get(SessionContainer::class),
        );
    }
}