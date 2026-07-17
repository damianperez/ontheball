<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use OxMohsen\TgBot\Messages;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
     */
    protected $version = '1.0';

    /**
     * Main command execution.
     *
     * @return ServerResponse
     */
    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        // If a conversation is busy, execute the conversation command after handling the message.
        $conversation = new Conversation(
            $message->getFrom()->getId(),
            $message->getChat()->getId()
        );
        // Fetch conversation command if it exists and execute it.
        if ($conversation->exists() && $command = $conversation->getCommand()) {
            return $this->telegram->executeCommand($command);
        }
        $message_text = $message->getText(true);
        if ($message_text=='sarasa')            
            $this->debug_a_admins('Respuesta',json_encode($this->replyToChat('escribieron sarasa')));            

        $web_app_data = $this->getMessage()->getWebAppData();
        if ($web_app_data) {
            return $this->replyToChat(
                sprintf(Messages::WEBAPP_DATA_MESSAGE, $web_app_data->getData()),
                ['parse_mode' => 'Markdown']
            );
        }

        return $this->telegram->executeCommand('start');
    }
}
