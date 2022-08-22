<?php

namespace Home\Controller;

use Home\Form\LoginForm;
use Home\Form\RecoverForm;
use Home\Form\SignUpForm;
use Home\Model\EmailRepositoryInterface;
use Home\Model\PositionRepositoryInterface;
use Home\Model\UserRepositoryInterface;
use Laminas\Mvc\Controller\AbstractActionController;

class HomeController extends AbstractActionController
{
    /**
     * @var PositionRepositoryInterface
     */
    private $positionRepository;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var EmailRepositoryInterface
     */
    private $emailRepository;

    /**
     * @var LoginForm
     */
    private $loginForm;

    /**
     * @var SignUpForm
     */
    private $signUpForm;

    /**
     * @var RecoverForm
     */
    private $recoverForm;

    /**
     * @param PositionRepositoryInterface $positionRepository
     * @param UserRepositoryInterface $userRepository
     * @param EmailRepositoryInterface $emailRepository
     * @param LoginForm $loginForm
     * @param SignUpForm $signUpForm
     * @param RecoverForm $recoverForm
     */
    public function __construct(
        $positionRepository,
        $userRepository,
        $emailRepository,
        $loginForm,
        $signUpForm,
        $recoverForm
    )
    {
        $this->positionRepository = $positionRepository;
        $this->userRepository = $userRepository;
        $this->emailRepository = $emailRepository;
        $this->loginForm = $loginForm;
        $this->signUpForm = $signUpForm;
        $this->recoverForm = $recoverForm;
    }
}
