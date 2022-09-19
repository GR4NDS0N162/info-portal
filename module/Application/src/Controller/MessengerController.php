<?php

namespace Application\Controller;

use Application\Form;
use Application\Model\Command\MessageCommandInterface;
use Application\Model\Repository\DialogRepositoryInterface;
use Application\Model\Repository\MessageRepositoryInterface;
use Application\Model\Repository\PositionRepositoryInterface;
use Application\Model\Repository\UserRepositoryInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class MessengerController extends AbstractActionController
{
    public const userId = 1;
    public const maxLoadCount = 5;

    private $dialogFilterForm;
    private $newMessageForm;
    /**
     * @var DialogRepositoryInterface
     */
    private $dialogRepository;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;
    /**
     * @var PositionRepositoryInterface
     */
    private $positionRepository;
    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;
    /**
     * @var MessageCommandInterface
     */
    private $messageCommand;

    public function __construct(
        $dialogFilterForm,
        $newMessageForm,
        $dialogRepository,
        $userRepository,
        $positionRepository,
        $messageRepository,
        $messageCommand
    ) {
        $this->dialogFilterForm = $dialogFilterForm;
        $this->newMessageForm = $newMessageForm;
        $this->dialogRepository = $dialogRepository;
        $this->userRepository = $userRepository;
        $this->positionRepository = $positionRepository;
        $this->messageRepository = $messageRepository;
        $this->messageCommand = $messageCommand;
    }

    public function viewDialogListAction()
    {
        $viewModel = new ViewModel();

        $this->layout()->setVariable('headTitleName', 'Диалоги');

        $dialogs = $this->dialogRepository->getDialogList(self::userId);

        $viewModel->setVariables([
            'dialogs'            => $dialogs,
            'dialogFilterForm'   => $this->dialogFilterForm,
            'userRepository'     => $this->userRepository,
            'positionRepository' => $this->positionRepository,
        ]);

        return $viewModel;
    }

    public function viewMessagesAction()
    {
        $buddyId = (int)$this->params()->fromRoute('id', 0);

        if ($buddyId === 0) {
            return $this->redirect()->toRoute('user/view-dialog-list');
        }

        $viewModel = new ViewModel();

        $this->layout()->setVariable('headTitleName', 'Сообщения');

        $userInfo = $this->userRepository->findUser(self::userId);
        $buddyInfo = $this->userRepository->findUser($buddyId);

        $viewModel->setVariables([
            'newMessageForm' => $this->newMessageForm,
            'userInfo'       => $userInfo,
            'buddyInfo'      => $buddyInfo,
        ]);

        return $viewModel;
    }

    public function getDialogsAction()
    {
        $request = $this->getRequest();

        if (!$request->isXmlHttpRequest() || !$request->isPost()) {
            exit();
        }

        $data = AdminController::array_filter_recursive($request->getPost()->toArray());

        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setTemplate('partial/dialog-list.phtml');

        $viewModel->setVariables([
            'dialogList'     => $this->dialogRepository->getDialogList(
                self::userId,
                AdminController::getWhere($data)
            ),
            'userRepository' => $this->userRepository,
        ]);

        return $viewModel;

    }

    public function sendMessageAction()
    {
    }

    public function loadMessagesAction()
    {
        $request = $this->getRequest();

        if (!$request->isXmlHttpRequest() || !$request->isPost()) {
            exit();
        }

        $post = $request->getPost();
        $lastMessageId = $post->get('lastMessageId', 0);
        $buddyId = $post->get('buddyId', 0);

        exit();
    }
}
