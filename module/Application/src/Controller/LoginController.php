<?php

namespace Application\Controller;

use Application\Form\Login;
use Application\Model\Command\UserCommandInterface;
use Application\Model\Entity\Email;
use Application\Model\Entity\User;
use Application\Model\Repository\UserRepositoryInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container as SessionContainer;
use Laminas\View\Model\ViewModel;

class LoginController extends AbstractActionController
{
    /**
     * @var Login\LoginForm
     */
    private Login\LoginForm $loginForm;
    /**
     * @var Login\SignUpForm
     */
    private Login\SignUpForm $signUpForm;
    /**
     * @var Login\RecoverForm
     */
    private Login\RecoverForm $recoverForm;
    /**
     * @var UserCommandInterface
     */
    private UserCommandInterface $userCommand;
    /**
     * @var SessionContainer
     */
    private SessionContainer $sessionContainer;
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $userRepository;

    /**
     * @param Login\LoginForm         $loginForm
     * @param Login\SignUpForm        $signUpForm
     * @param Login\RecoverForm       $recoverForm
     * @param UserRepositoryInterface $userRepository
     * @param UserCommandInterface    $userCommand
     * @param SessionContainer        $sessionContainer
     */
    public function __construct(
        Login\LoginForm         $loginForm,
        Login\SignUpForm        $signUpForm,
        Login\RecoverForm       $recoverForm,
        UserRepositoryInterface $userRepository,
        UserCommandInterface    $userCommand,
        SessionContainer        $sessionContainer
    ) {
        $this->loginForm = $loginForm;
        $this->signUpForm = $signUpForm;
        $this->recoverForm = $recoverForm;
        $this->userRepository = $userRepository;
        $this->userCommand = $userCommand;
        $this->sessionContainer = $sessionContainer;
    }

    public function indexAction()
    {
        $viewModel = new ViewModel([
            'loginForm'   => $this->loginForm,
            'signUpForm'  => $this->signUpForm,
            'recoverForm' => $this->recoverForm,
        ]);

        $this->layout('layout/home-layout');
        $this->layout()->setVariable('headTitleName', 'Вход | Регистрация');

        return $viewModel;
    }

    public function loginAction()
    {
    }

    public function signUpAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $this->redirect()->toRoute('home');
        }

        $this->signUpForm->setData($request->getPost());

        if (!$this->signUpForm->isValid()) {
            return $this->redirect()->toRoute('home');
        }

        $data = $this->signUpForm->getData();

        $email = new Email($data['email']);
        $user = new User(
            $data['newPassword'],
            $data['positionId'],
        );

        $this->userCommand->insertUser($user, $email);
        return $this->redirect()->toRoute('user/view-profile');
    }

    public function recoverAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $this->redirect()->toRoute('home');
        }

        $this->recoverForm->setData($request->getPost());

        if (!$this->recoverForm->isValid()) {
            return $this->redirect()->toRoute('home');
        }

        $data = $this->recoverForm->getData();
        $email = new Email($data['email']);

        $this->userCommand->setTempPassword($email);
        return $this->redirect()->toRoute('home');
    }
}
