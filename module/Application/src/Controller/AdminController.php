<?php

namespace Application\Controller;

use Application\Form;
use Application\Model\Email;
use Application\Model\Phone;
use Application\Model\PhotoUrlGenerator;
use Application\Model\Profile;
use Application\Model\User;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
    public const maxPageCount = 20;

    /**
     * @var \Application\Form\Admin\PositionForm
     */
    private $positionForm;

    /**
     * @var \Application\Form\Admin\UserForm
     */
    private $userForm;

    /**
     * @var Profile
     */
    private $profilePrototype;

    /**
     * @var User
     */
    private $userPrototype;

    public function __construct(
        Form\Admin\PositionForm $positionForm,
        Form\Admin\UserForm     $userForm
    )
    {
        $this->positionForm = $positionForm;
        $this->userForm = $userForm;
        $this->profilePrototype = new Profile(
            'Anypassword1.',
            null,
            null,
            4,
            'Внуков',
            'Кирилл',
            'Денисович',
            1,
            '2003-05-19',
            '/img/favicon.ico',
            'gr4nds0n162',
            [
                new Email('1'),
                new Email('2'),
                new Email('3'),
                new Email('4'),
            ],
            [
                new Phone('1'),
                new Phone('2'),
                new Phone('3'),
            ],
        );
        $this->userPrototype = new User(
            $this->profilePrototype->getPassword(),
            $this->profilePrototype->getTempPassword(),
            $this->profilePrototype->getTpCreatedAt(),
            $this->profilePrototype->getPosition(),
            $this->profilePrototype->getSurname(),
            $this->profilePrototype->getName(),
            $this->profilePrototype->getPatronymic(),
            $this->profilePrototype->getGender(),
            $this->profilePrototype->getBirthday(),
            $this->profilePrototype->getImage(),
            $this->profilePrototype->getSkype(),
            $this->profilePrototype->getEmails(),
            $this->profilePrototype->getPhones(),
            [
                'admin'  => true,
                'active' => false,
            ]
        );
    }

    public function viewUserListAction(): ViewModel
    {
        $viewModel = new ViewModel();

        $headTitleName = 'Список пользователей (Администратор)';

        $this->layout()->setVariable('headTitleName', $headTitleName);
        $this->layout()->setVariable('navbar', 'Laminas\Navigation\Admin');

        $userInfo = [
            [
                'userId'   => 1,
                'isAdmin'  => true,
                'isActive' => true,
                'photo'    => PhotoUrlGenerator::generate(),
                'fullname' => 'Зубенко Михаил Петрович',
                'position' => 'Уборщик',
                'gender'   => 'Мужской',
                'age'      => 47,
            ],
            [
                'userId'   => 2,
                'isAdmin'  => false,
                'isActive' => true,
                'photo'    => PhotoUrlGenerator::generate(),
                'fullname' => 'Егоров Владимир Егорович',
                'position' => 'Бухгалтер',
                'gender'   => 'Мужской',
                'age'      => 31,
            ],
            [
                'userId'   => 3,
                'isAdmin'  => true,
                'isActive' => false,
                'photo'    => PhotoUrlGenerator::generate(),
                'fullname' => 'Мельникова Алёна Вадимовна',
                'position' => 'Юрист',
                'gender'   => 'Женский',
                'age'      => 23,
            ],
            [
                'userId'   => 4,
                'isAdmin'  => false,
                'isActive' => false,
                'photo'    => PhotoUrlGenerator::generate(),
                'fullname' => 'Тимофеева Вероника Денисовна',
                'position' => 'Менеджер',
                'gender'   => 'Женский',
                'age'      => 36,
            ],
        ];

        $viewModel->setVariables([
            'userInfo'        => $userInfo,
            'maxPageCount'    => self::maxPageCount,
            'page'            => 1,
            'adminFilterForm' => new Form\Admin\AdminFilterForm(),
        ]);

        return $viewModel;
    }

    public function editUserAction(): ViewModel
    {
        $viewModel = new ViewModel();

        $headTitleName = 'Редактирование пользователя (Администратор)';

        $this->layout()->setVariable('headTitleName', $headTitleName);
        $this->layout()->setVariable('navbar', 'Laminas\Navigation\Admin');

        $this->userForm->bind($this->userPrototype);

        $viewModel->setVariables([
            'userForm' => $this->userForm,
        ]);

        return $viewModel;
    }

    public function editPositionsAction(): ViewModel
    {
        $viewModel = new ViewModel();

        $headTitleName = 'Управление должностями (Администратор)';

        $this->layout()->setVariable('headTitleName', $headTitleName);
        $this->layout()->setVariable('navbar', 'Laminas\Navigation\Admin');

        $viewModel->setVariable('positionForm', $this->positionForm);

        return $viewModel;
    }
}
