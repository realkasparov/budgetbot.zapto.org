<?php
require_once 'vendor/autoload.php';
use TelegramBot\Api\Types\Update;

class Telegram
{
    public $token;

    const BASE_API_URL = 'https://api.telegram.org/bot';

    /**
     * @param String $hookUrl - адрес на нашем сервере, куда будут приходить обновления
     * @return mixed|null
     */
    public function setWebHook($hookUrl)
    {
        return $this->sendPost('setWebHook', ['url' => $hookUrl]);
    }

    /**
     * @return mixed
     */
    public function getUpdates()
    {
        $data = file_get_contents($this->buildUrl('getUpdates'));
        return json_decode($data, true);
    }

    /**
     * @param int $chatId - ID чата, в который отправляем сообщение
     * @param String $message - текст сообщения
     * @param array $params - дом.параметры (опционально)
     * @return mixed
     */
    public function sendMessage($chatId, $message, $params = [])
    {
        if (!is_array($params)) {
            $params = array();
        }

        $params['chat_id'] = $chatId;
        $params['text'] = strip_tags($message); // Telegram не понимает html-тегов

        $url = $this->buildUrl('sendMessage') . '?' . http_build_query($params);

        $data = file_get_contents($url);
        return json_decode($data, true);
    }

    /**
     * @param String $methodName - имя метода в API, который вызываем
     * @param array $data - параметры, которые передаем, необязательное поле
     * @return mixed|null
     */
    private function sendPost($methodName, $data = [])
    {
        $result = null;

        if (is_array($data)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->buildUrl($methodName));
            curl_setopt($ch, CURLOPT_POST, count($data));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            $result = curl_exec($ch);
            curl_close($ch);
        }

        return $result;

    }

    /**
     * @param String $methodName - имя метода в API, который вызываем
     * @return string - Софрмированный URL для отправки запроса
     */
    private function buildUrl($methodName)
    {
        return self::BASE_API_URL . $this->token . '/' . $methodName;
    }
}
/*
$time = time();
$api = new Telegram();
$api->token = '367023794:AAGLifFIIA4avKeBfOla7lxB-N75inrOoyo';
$api->sendMessage(62611788, "Запуск");
//$upd = new Update();
//var_dump($upd->getMessage());


//var_dump($api);
//exit;

$old_count = 0;
$on = 0;
while ($on < 118) { //49 //118
    $t = time();
//    if ($_POST) {
//        var_dump($_POST);
//
//        $api->sendMessage(62611788,  'есть контакт');
//    } else {
//        echo 'Пост не пришёл';
//        $api->sendMessage(62611788, 'нет контакта');
//
//    }
    $responce = $api->getUpdates();
    $new_count = count($responce['result']);
    if (($new_count != $old_count) && ($old_count != 0)) {
//        ob_flush();
//        flush();
        $api->sendMessage(62611788, "Вы отправили сообщение: ". $responce['result'][($new_count-1)]['message']['text']);
        if ($responce['result'][($new_count-1)]['message']['text'] == 'exit') {
            break;
        }
        unset($responce);
    }
    $old_count = $new_count;
    sleep(2);
    $on++;
    $t = time() - $t;
    if (floor($t)) {
        $on = $on + round($t);
    } else {
        $on++;
    }
}
$time = time() - $time;
echo 'time: '.$time . ' сек<br>';
$api->sendMessage(62611788, "Скрипт остановлен");

$api->sendMessage(62611788, date('i:s', $time).' сек');
*/


//echo '1';
//$api = new Telegram();
//$api->token = '367023794:AAGLifFIIA4avKeBfOla7lxB-N75inrOoyo';
//$api->sendMessage(62611788, "Запуск. Читаем сообщения.");
//
//$responce = $api->getUpdates();
//$count = count($responce['result']);
//var_dump($responce);
//
//$message = $responce['result'][($count-1)]['message']['text'];
//if ($message == 'exit') {
//    $api->sendMessage(62611788, "Скрипт завершён.");
//    exit;
//}
//sleep(5);
//echo '<br><br>';
//echo $responce['result'][($count-1)]['message']['text'];
//$message = 'Ваше последнее сообщение: ' . $responce['result'][($count-1)]['message']['text'];
//$api->sendMessage(62611788, $message);

//$data = array("name" => "Hagrid", "age" => "36");
//$data_string = json_encode($data);

//$ch = curl_init('http://budgetbot.zapto.org/');
////curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
////curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
//curl_setopt($ch, CURLOPT_TIMEOUT, 2);
////curl_setopt($ch, CURLOPT_HTTPHEADER, array(
////        'Content-Type: application/json',
////        'Content-Length: ' . strlen($data_string))
////);
//
//$result = curl_exec($ch);
//curl_close($ch);
//exit;




//require_once 'vendor/autoload.php';
////require_once 'stopwatch.php';
//
//// connect to database
////$mysqli = new mysqli('database_host', 'database_user', 'database_password', 'database_name');
////if (!empty($mysqli->connect_errno)) {
////    throw new \Exception($mysqli->connect_error, $mysqli->connect_errno);
////}
//
//// create a bot
//$bot = new \TelegramBot\Api\Client('367023794:AAGLifFIIA4avKeBfOla7lxB-N75inrOoyo');
//// run, bot, run!
//$bot->run();
//$bot->command('new', function ($message) use ($bot) {
//    $answer = 'Howdy! Welcome to the stopwatch. Use bot commands or keyboard to control your time.';
//    $bot->sendMessage($message->getChat()->getId(), $answer);
//});
?>