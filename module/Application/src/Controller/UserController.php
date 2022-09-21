<?php

namespace Application\Controller;

use Application\Form\User as Form;
use Application\Model\Command\UserCommandInterface;
use Application\Model\Entity\ChangePassword;
use Application\Model\Repository\PositionRepositoryInterface;
use Application\Model\Repository\UserRepositoryInterface;
use InvalidArgumentException;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container as SessionContainer;
use Laminas\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    /**
     * Maximum number of users displayed on a page.
     */
    public const maxPageCount = 20;
    /**
     * The ID of the user who is currently logged in to the system.
     */
    public const userId = 1;

    /**
     * @var Form\ProfileForm
     */
    private Form\ProfileForm $profileForm;
    /**
     * @var Form\ViewProfileForm
     */
    private Form\ViewProfileForm $viewProfileForm;
    /**
     * @var Form\UserFilterForm
     */
    private Form\UserFilterForm $userFilterForm;
    /**
     * @var Form\ChangePasswordForm
     */
    private Form\ChangePasswordForm $changePasswordForm;
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $userRepository;
    /**
     * @var PositionRepositoryInterface
     */
    private PositionRepositoryInterface $positionRepository;
    /**
     * @var UserCommandInterface
     */
    private UserCommandInterface $userCommand;
    /**
     * @var SessionContainer
     */
    private SessionContainer $sessionContainer;

    /**
     * @param Form\ProfileForm            $profileForm
     * @param Form\ViewProfileForm        $viewProfileForm
     * @param Form\UserFilterForm         $userFilterForm
     * @param Form\ChangePasswordForm     $changePasswordForm
     * @param UserRepositoryInterface     $userRepository
     * @param PositionRepositoryInterface $positionRepository
     * @param UserCommandInterface        $userCommand
     */
    public function __construct(
        Form\ProfileForm            $profileForm,
        Form\ViewProfileForm        $viewProfileForm,
        Form\UserFilterForm         $userFilterForm,
        Form\ChangePasswordForm     $changePasswordForm,
        UserRepositoryInterface     $userRepository,
        PositionRepositoryInterface $positionRepository,
        UserCommandInterface        $userCommand,
        SessionContainer            $sessionContainer
    ) {
        $this->profileForm = $profileForm;
        $this->viewProfileForm = $viewProfileForm;
        $this->userFilterForm = $userFilterForm;
        $this->changePasswordForm = $changePasswordForm;
        $this->userRepository = $userRepository;
        $this->positionRepository = $positionRepository;
        $this->userCommand = $userCommand;
        $this->sessionContainer = $sessionContainer;
    }

    public function viewProfileAction()
    {
        $userId = $this->sessionContainer->offsetGet('userId');

        UserController::setAdminNavbar($this->userRepository, $this, $userId);

        $this->layout()->setVariables(['headTitleName' => 'Просмотр профиля']);

        $viewModel = new ViewModel(['viewProfileForm' => $this->viewProfileForm]);

        $profile = $this->userRepository->findProfile($userId);

        $this->viewProfileForm->bind($profile);
        $this->viewProfileForm->get('profile')->get('image')
            ->setAttribute('src', $profile->getImagePath());

        return $viewModel;
    }

    /**
     * @param UserRepositoryInterface  $userRepository
     * @param AbstractActionController $controller
     * @param int                      $userId
     *
     * @return void
     */
    public static function setAdminNavbar(
        UserRepositoryInterface  $userRepository,
        AbstractActionController $controller,
        int                      $userId
    ) {
        if ($userRepository->findUser($userId)->getStatus()['admin']) {
            $controller->layout()->setVariables(['navbar' => 'Laminas\Navigation\Admin']);
        }
    }

    public function editProfileAction()
    {
        UserController::setAdminNavbar($this->userRepository, $this, self::userId);
        $this->layout()->setVariables(['headTitleName' => 'Редактирование профиля']);

        try {
            $foundProfile = $this->userRepository->findProfile(self::userId);
            $changePassword = new ChangePassword($foundProfile->getId());
        } catch (InvalidArgumentException $ex) {
            return $this->redirect()->toRoute('home');
        }

        $viewModel = new ViewModel([
            'profileForm'        => $this->profileForm,
            'changePasswordForm' => $this->changePasswordForm,
        ]);

        $this->profileForm->bind($foundProfile);
        $this->changePasswordForm->bind($changePassword);

        return $viewModel;
    }

    public function profileFormAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $this->redirect()->toRoute('user/edit-profile');
        }

        $postData = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );

        $this->profileForm->setData($postData);

        if (!$this->profileForm->isValid()) {
            return $this->redirect()->toRoute('user/edit-profile');
        }

        $this->userCommand->updateProfile($this->profileForm->getObject());

        return $this->redirect()->toRoute('user/view-profile');
    }

    public function changePasswordFormAction()
    {
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return $this->redirect()->toRoute('user/edit-profile');
        }

        $this->changePasswordForm->setData($request->getPost());

        if (!$this->changePasswordForm->isValid()) {
            return $this->redirect()->toRoute('user/edit-profile');
        }

        $this->userCommand->changePassword(
            $this->changePasswordForm->getObject()
        );

        return $this->redirect()->toRoute('user/edit-profile');
    }

    public function viewUserListAction()
    {
        UserController::setAdminNavbar($this->userRepository, $this, self::userId);
        $viewModel = new ViewModel();

        $headTitleName = 'Список пользователей';

        $this->layout()->setVariable('headTitleName', $headTitleName);

        $viewModel->setVariables([
            'userInfo'           => $this->userRepository->findUsers(),
            'positionRepository' => $this->positionRepository,
            'maxPageCount'       => self::maxPageCount,
            'page'               => 1,
            'userFilterForm'     => $this->userFilterForm,
        ]);

        return $viewModel;
    }
}