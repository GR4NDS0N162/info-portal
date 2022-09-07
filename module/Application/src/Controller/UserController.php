<?php

namespace Application\Controller;

use Application\Form\User as Form;
use Application\Model\Command\UserCommandInterface;
use Application\Model\Entity\ChangePassword;
use Application\Model\Repository\UserInfoRepositoryInterface;
use Application\Model\Repository\UserRepositoryInterface;
use InvalidArgumentException;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    public const maxPageCount = 20;
    public const userId = 1;

    /**
     * @var Form\ProfileForm
     */
    private $profileForm;
    /**
     * @var Form\ViewProfileForm
     */
    private $viewProfileForm;
    /**
     * @var Form\UserFilterForm
     */
    private $userFilterForm;
    /**
     * @var Form\ChangePasswordForm
     */
    private $changePasswordForm;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var UserInfoRepositoryInterface
     */
    private $userInfoRepository;
    /**
     * @var UserCommandInterface
     */
    private $userCommand;

    /**
     * @param Form\ProfileForm        $profileForm
     * @param Form\ViewProfileForm    $viewProfileForm
     * @param Form\UserFilterForm     $userFilterForm
     * @param Form\ChangePasswordForm $changePasswordForm
     * @param UserRepositoryInterface $userRepository
     * @param UserCommandInterface    $userCommand
     */
    public function __construct(
        $profileForm,
        $viewProfileForm,
        $userFilterForm,
        $changePasswordForm,
        $userRepository,
        $userInfoRepository,
        $userCommand
    ) {
        $this->profileForm = $profileForm;
        $this->viewProfileForm = $viewProfileForm;
        $this->userFilterForm = $userFilterForm;
        $this->changePasswordForm = $changePasswordForm;
        $this->userRepository = $userRepository;
        $this->userInfoRepository = $userInfoRepository;
        $this->userCommand = $userCommand;
    }

    public function viewProfileAction()
    {
        $this->layout()->setVariables(['headTitleName' => 'Просмотр профиля']);
        $viewModel = new ViewModel(['viewProfileForm' => $this->viewProfileForm]);

        $profile = $this->userRepository->findProfile(self::userId);
        $this->viewProfileForm->bind($profile);
        $this->viewProfileForm->get('profile')->get('image')
            ->setAttribute('src', $profile->getImage() ?: '/img/headshot-1024x1024.jpg');

        return $viewModel;
    }

    public function editProfileAction()
    {
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
        $viewModel = new ViewModel();

        $headTitleName = 'Список пользователей';

        $this->layout()->setVariable('headTitleName', $headTitleName);

        $viewModel->setVariables([
            'userInfo'       => $this->userRepository->findUsers(),
            'maxPageCount'   => self::maxPageCount,
            'page'           => 1,
            'userFilterForm' => $this->userFilterForm,
        ]);

        return $viewModel;
    }
}