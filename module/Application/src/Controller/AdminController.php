<?php

namespace Application\Controller;

use Application\Form\Admin as Form;
use Application\Model\Command\PositionCommandInterface;
use Application\Model\Command\UserCommandInterface;
use Application\Model\Entity\PositionList;
use Application\Model\Repository\PositionRepositoryInterface;
use Application\Model\Repository\UserRepositoryInterface;
use InvalidArgumentException;
use Laminas\Db\Sql\Predicate\Expression;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container as SessionContainer;
use Laminas\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
    private Form\PositionForm $positionForm;
    private Form\UserForm $userForm;
    private Form\AdminFilterForm $adminFilterForm;
    private UserRepositoryInterface $userRepository;
    private PositionRepositoryInterface $positionRepository;
    private UserCommandInterface $userCommand;
    private PositionCommandInterface $positionCommand;
    private SessionContainer $sessionContainer;

    public function __construct(
        Form\PositionForm           $positionForm,
        Form\UserForm               $userForm,
        Form\AdminFilterForm        $adminFilterForm,
        UserRepositoryInterface     $userRepository,
        PositionRepositoryInterface $positionRepository,
        UserCommandInterface        $userCommand,
        PositionCommandInterface    $positionCommand,
        SessionContainer            $sessionContainer
    ) {
        $this->positionForm = $positionForm;
        $this->userForm = $userForm;
        $this->adminFilterForm = $adminFilterForm;
        $this->userRepository = $userRepository;
        $this->positionRepository = $positionRepository;
        $this->userCommand = $userCommand;
        $this->positionCommand = $positionCommand;
        $this->sessionContainer = $sessionContainer;
    }

    public function viewUserListAction()
    {
        $userId = $this->getUserId();

        $viewModel = new ViewModel();

        $this->layout()->setVariable('headTitleName', 'Список пользователей (Администратор)');
        $this->layout()->setVariable('navbar', 'Laminas\Navigation\Admin');

        $viewModel->setVariables([
            'userInfo'           => $this->userRepository->findUsers(),
            'positionRepository' => $this->positionRepository,
            'page'               => 1,
            'adminFilterForm'    => $this->adminFilterForm,
        ]);

        return $viewModel;
    }

    public function getUsersAction()
    {
        $request = $this->getRequest();

        if (!$request->isXmlHttpRequest() || !$request->isPost()) {
            exit();
        }

        $data = $request->getPost()->toArray();
        parse_str($data['where'], $data['where']);
        $data['where'] = self::arrayFilterRecursive($data['where']);

        list($page, $order, $where) = self::extractOptions($data);

        if (isset($data['updatePage'])) {
            $count = count($this->userRepository->findUsers($where));
            echo json_encode([
                'count'        => $count,
                'maxPageCount' => UserController::MAX_USER_COUNT,
            ]);
            exit();
        }

        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('partial/user-list-admin.phtml');

        $viewModel->setVariables([
            'userList' => $this->userRepository->findUsers(
                $where,
                $order,
                true,
                $page
            ),
            'formName' => $data['formName'],
        ]);

        return $viewModel;
    }

    public static function arrayFilterRecursive($array, $callback = null)
    {
        foreach ($array as $key => & $value) {
            if (is_array($value)) {
                $value = AdminController::arrayFilterRecursive($value, $callback);
                if (!$value) {
                    unset($array[$key]);
                }
            } else {
                if (!is_null($callback)) {
                    if (!$callback($value)) {
                        unset($array[$key]);
                    }
                } else {
                    if (!$value) {
                        unset($array[$key]);
                    }
                }
            }
        }
        unset($value);

        return $array;
    }

    public static function extractOptions(array $data): array
    {
        $page = self::getPage($data['page']);

        $order = self::getOrder($data['order']);

        $where = self::getWhere($data['where']);
        return array($page, $order, $where);
    }

    public static function getPage(string $page): int
    {
        return (int)$page;
    }

    public static function getOrder(string $orderConfig): array
    {
        $order = [
            'u.surname',
            'u.name',
            'u.patronymic',
            'pos.name',
            'u.gender',
            'u.birthday DESC',
        ];
        if ($orderConfig == 'position') {
            array_unshift($order, 'pos.name');
        } elseif ($orderConfig == 'age') {
            array_unshift($order, 'u.birthday DESC');
        } elseif ($orderConfig == 'gender') {
            array_unshift($order, 'u.gender');
        }
        $order = array_unique($order);
        return $order;
    }

    public static function getWhere(array $whereConfig): array
    {
        $where = [];

        if (isset($whereConfig['positionId'])) {
            $where[] = new Expression(
                'u.position_id = ?',
                [$whereConfig['positionId']]
            );
        }

        if (isset($whereConfig['gender'])) {
            $where[] = new Expression(
                'u.gender = ?',
                [$whereConfig['gender']]
            );
        }

        if (isset($whereConfig['active'])) {
            $sign = $whereConfig['active'] == '1' ? 'IN' : 'NOT IN';
            $where[] = new Expression(
                'u.id ' . $sign . ' ( SELECT us.user_id FROM user_status us WHERE us.status_id = 2 )'
            );
        }

        if (isset($whereConfig['admin'])) {
            $sign = $whereConfig['admin'] == '1' ? 'IN' : 'NOT IN';
            $where[] = new Expression(
                'u.id ' . $sign . ' ( SELECT us.user_id FROM user_status us WHERE us.status_id = 1 )'
            );
        }

        if (isset($whereConfig['age'])) {
            if (isset($whereConfig['age']['min'])) {
                $where[] = new Expression('TIMESTAMPDIFF(YEAR, u.birthday, NOW()) >= ?', [$whereConfig['age']['min']]);
            }
            if (isset($whereConfig['age']['max'])) {
                $where[] = new Expression('TIMESTAMPDIFF(YEAR, u.birthday, NOW()) <= ?', [$whereConfig['age']['max']]);
            }
        }

        $empty = '-';

        if (isset($whereConfig['fullnamePhone'])) {
            $filterArr = array_map('trim', explode(',', $whereConfig['fullnamePhone']));
            if (count($filterArr) == 2) {
                list($fullname, $phone) = $filterArr;
                $where = self::getTextareaFilter($where, $fullname, $phone, $empty);
            }
        } elseif (isset($whereConfig['fullnamePhoneEmail'])) {
            $filterArr = array_map('trim', explode(',', $whereConfig['fullnamePhoneEmail']));
            if (count($filterArr) == 3) {
                list($fullname, $phone, $email) = $filterArr;
                $where = self::getTextareaFilter($where, $fullname, $phone, $empty);

                if ($email != $empty) {
                    $where[] = new Expression(
                        'u.id IN ( SELECT e.user_id FROM email AS e ' .
                        'WHERE e.address LIKE LOWER(CONCAT("%", ?, "%")))', [$email]
                    );
                }
            }
        }

        return $where;
    }

    public static function getTextareaFilter(
        array  $where,
        string $fullname,
        string $phone,
        string $empty
    ): array {
        $fullnameArr = explode(' ', $fullname);

        if (count($fullnameArr) == 3) {
            list($surname, $name, $patronymic) = $fullnameArr;

            if ($surname != $empty) {
                $where[] = new Expression('LOWER(u.surname) LIKE LOWER(CONCAT("%", ?, "%"))', [$surname]);
            }
            if ($name != $empty) {
                $where[] = new Expression('LOWER(u.name) LIKE LOWER(CONCAT("%", ?, "%"))', [$name]);
            }
            if ($patronymic != $empty) {
                $where[] = new Expression('LOWER(u.patronymic) LIKE LOWER(CONCAT("%", ?, "%"))', [$patronymic]);
            }
        }

        if ($phone != $empty) {
            $where[] = new Expression(
                'u.id IN ( SELECT ph.user_id FROM phone AS ph ' .
                'WHERE ph.number LIKE LOWER(CONCAT("%", ?, "%")))', [$phone]
            );
        }

        return $where;
    }

    public function editUserAction()
    {
        $userId = (int)$this->params()->fromRoute('id', 0);

        if ($userId === 0) {
            return $this->redirect()->toRoute('admin/view-user-list');
        }

        try {
            $user = $this->userRepository->findUser($userId);
        } catch (InvalidArgumentException $ex) {
            return $this->redirect()->toRoute('admin/view-user-list');
        }

        $this->layout()->setVariables([
            'headTitleName' => 'Редактирование пользователя (Администратор)',
            'navbar'        => 'Laminas\Navigation\Admin',
        ]);

        $this->userForm->bind($user);
        $viewModel = new ViewModel(['userForm' => $this->userForm]);

        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $viewModel;
        }

        $postData = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );

        $this->userForm->setData($postData);

        if (!$this->userForm->isValid()) {
            return $viewModel;
        }

        $this->userCommand->updateUser($user);
        return $this->redirect()->toRoute('admin/view-user-list');
    }

    public function editPositionsAction()
    {
        $userId = $this->getUserId();

        $this->layout()->setVariables([
            'headTitleName' => 'Управление должностями (Администратор)',
            'navbar'        => 'Laminas\Navigation\Admin',
        ]);

        $list = $this->positionRepository->findAllPositions();
        $positionList = new PositionList($list);
        $this->positionForm->bind($positionList);

        $viewModel = new ViewModel(['positionForm' => $this->positionForm]);

        $request = $this->getRequest();
        if (!$request->isPost()) {
            return $viewModel;
        }

        $this->positionForm->setData($request->getPost());

        if (!$this->positionForm->isValid()) {
            return $viewModel;
        }

        $this->positionCommand->updatePositions(
            $this->positionForm->getObject()
        );

        return $this->redirect()->toRoute('admin/edit-position');
    }

    private function getUserId(): ?int
    {
        return $this->sessionContainer->offsetGet(LoginController::USER_ID);
    }
}
