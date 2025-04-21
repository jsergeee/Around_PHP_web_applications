   для немедленной отпавки в HandleEventsCommand
   
    // public function run(array $options = []): void
    // {
    //     echo "HandleEventsCommand выполняется...\n";
    //     echo "Запуск приложения...\n"; // Отладочное сообщение
    
    //     $eventModel = new Event(new SQLite($this->app));
    //     $events = $eventModel->select();
    //     echo "Получено событий: " . count($events) . "\n";
    
    //     // Получаем токен из конфигурации
    //     $token = $this->app->env('TELEGRAM_TOKEN'); // Убедитесь, что этот метод возвращает правильный токен
    //     $tgApi = new TelegramApiImpl($token); // Передаем токен в TelegramApiImpl
    //     $eventSender = new EventSender($tgApi); // Передаем tgApi в EventSender
    
    //     foreach ($events as $event) {
    //         // Отправляем сообщение немедленно, без проверки времени
    //         echo "Отправка сообщения на ID: " . $event['receiverId'] . "\n"; // Используем массив
    //         $eventSender->sendMessage($event['receiverId'], $event['text']); // Используем массив
    //     }
    // }
    

    Чтобы запустить бота Telegram в режиме демона, вам нужно реализовать бесконечный цикл, который будет периодически проверять новые сообщения и обрабатывать их. Это можно сделать с помощью while(true) в вашем скрипте. Вот как это можно реализовать:

▎Пример реализации демона

1. Создайте новый класс для демона (например, TelegramBotDaemon), который будет содержать логику для постоянной работы.

2. Используйте sleep для паузы между проверками, чтобы не перегружать API Telegram.

Вот пример кода:

namespace App\Telegram;

use App\Application;

class TelegramBotDaemon
{
    private TelegramApiImpl $tgApi;

    public function __construct(Application $app)
    {
        $this->tgApi = new TelegramApiImpl($app->env('TELEGRAM_TOKEN'));
    }

    public function run(): void
    {
        $offset = 0;

        while (true) {
            $messages = $this->tgApi->getMessage($offset);

            if (!empty($messages['result'])) {
                foreach ($messages['result'] as $chatId => $texts) {
                    foreach ($texts as $text) {
                        echo "Получено сообщение от ID: $chatId с текстом: $text\n";

                        // Пример обработки команды
                        if (trim($text) === '/start') {
                            $this->tgApi->sendMessage($chatId, "Добро пожаловать! Как я могу помочь?");
                        }

                        // Обновляем offset для следующего запроса
                        $offset = $messages['offset'];
                    }
                }
            }

            // Ждем 1 секунду перед следующей проверкой
            sleep(1);
        }
    }
}


▎Запуск демона

Чтобы запустить этот демон, вам нужно будет создать и запустить его в вашем основном скрипте. Например:

require 'path/to/your/autoload.php'; // Подключите ваш автозагрузчик

$app = new Application(); // Инициализируйте ваше приложение
$daemon = new TelegramBotDaemon($app);
$daemon->run();


▎Запуск в фоновом режиме

Чтобы запустить скрипт в фоновом режиме, вы можете использовать команду nohup в терминале:

nohup php path/to/your/daemon_script.php > bot.log 2>&1 &


• nohup позволяет вашему процессу продолжать работать даже после закрытия терминала.

• > bot.log перенаправляет стандартный вывод в файл bot.log, чтобы вы могли отслеживать логи.

• 2>&1 перенаправляет стандартный вывод ошибок в тот же файл.

• & запускает процесс в фоновом режиме.

▎Убедитесь, что

• Ваш бот зарегистрирован в Telegram и имеет доступ к API.

• Вы обрабатываете возможные ошибки, чтобы избежать остановки работы демона из-за непредвиденных ситуаций.

Теперь ваш бот будет работать в фоновом режиме, проверяя новые сообщения и обрабатывая их автоматически. Если у вас возникнут дополнительные вопросы или потребуется помощь, дайте знать!