<?php

namespace Application\Model\Command;

use Application\Model\Entity\Message;

interface MessageCommandInterface
{
    /**
     * @param Message $message
     *
     * @return void
     */
    public function sendMessage(Message $message);

    /**
     * @param int       $userId
     * @param Message[] $messageList
     *
     * @return Message[]
     */
    public function readBy(int $userId, array $messageList): array;
}