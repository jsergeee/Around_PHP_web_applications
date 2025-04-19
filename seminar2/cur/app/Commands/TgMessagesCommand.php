<?php
namespace App\Commands;

use App\Application;
use App\Telegram\TelegramApiImpl;

class TgMessagesCommand extends Command
{
    public function __construct(public Application $app)
    {

    }

    function run(array $options = []): void
    {
        $tgApi = new TelegramApiImpl($this->app->env('TELEGRAM_TOKEN'));
        echo json_encode($tgApi->getMessage(0));
    }
}