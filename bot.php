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
    public function getUpdates($offset = null)
    {
        if (is_null($offset)){
            $data = file_get_contents($this->buildUrl('getUpdates'));
        } else {
            $data = file_get_contents($this->buildUrl('getUpdates').'?offset='.$offset);
        }

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
//создание и настройка объекта
$api = new Telegram();
$api->token = '333633425:AAGXKNTYXY6uY5Gd4NR4HBytJBzfYJWpDq8';



//получение данных из предыдущей сессии работы скрипта
$json = file_get_contents('php://input');
$data = json_decode($json, true);
if (is_null($data)) {
    $m = 'Post пустой, или первая работа скрипта.';
} else {
    $m = $data['text'];
    $data['envelope'] = (float)$data['envelope'];
//    $info = $obj['info'];
}


//получение данных из телеграма

$responce = $api->getUpdates();
$count = count($responce['result']);
$responce = $api->getUpdates($responce['result'][($count-1)]['update_id']);

$data['text'] = $responce['result'][0]['message']['text'];

//Принудительное прерывание скрипта
if ($data['text'] == 'exit') {
    $api->sendMessage(62611788, "Скрипт завершён.");
    exit;
}

//обработка комманд
if ($m !== $data['text']) {
    //##################### ОБРАБОТКА #####################
//    $api->sendMessage(62611788, "Ваше последнее сообщение: ". $data['text']);

    $messageArr = explode(' ', $data['text']);
    if (mb_strtolower($messageArr[0]) === 'конверт') {
        if (is_string($messageArr[1])) {
            $api->sendMessage(62611788, 'Новый конверт: ' . $messageArr[1] . ' рублей.');
            $data['envelope'] = (float)str_replace([',', '.'], '.', $messageArr[1]);
        } else {
            $api->sendMessage(62611788, "Вы не ввели сумму конверта.");
        }
    }

    if ((float)$messageArr[0]) {
        $messageArr[0] = (float)$messageArr[0];
    }

    if (is_float($data['envelope']) && (float)($messageArr[0])) {
        $data['spent'] = (float)str_replace([',', '.'], '.', $data['text']);
        $data['envelope'] = $data['envelope'] - $data['spent'];
        $data['message'] = 'Осталось: '. $data['envelope'];
        $api->sendMessage(62611788, $data['message']);
    }



    if ($data['text'] == 'info') {
        ob_start();
        var_dump($_SERVER);
        $api->sendMessage(62611788, ob_get_clean());
    }
    //##################### /ОБРАБОТКА #####################
}

//$api->sendMessage(62611788, "Памяти использовано: ". $info);
//задержка
sleep(3);

//$i = round(memory_get_usage()/1024/1024,2).' MB';

//передача данных в следующую сессию работы скрипта
//$data = ["text" => $message];
//$data = ["text" => $message, "info" => $i];
$data_string = json_encode($data);

$ch = curl_init('http://budgetbot.zapto.org/bot.php');
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
);
curl_exec($ch);
curl_close($ch);

//Чистим память - нужно ли?
unset($api, $json, $obj, $m, $responce, $count, $message, $data, $data_string);

//Принудительное прерывание скрипта - нужно ли?
exit;

?>