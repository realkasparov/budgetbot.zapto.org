<?php
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

$api = new Telegram() ;
$api->token = '367023794:AAGLifFIIA4avKeBfOla7lxB-N75inrOoyo';


$old_count = 0;
$on = true;
while ($on) {

    $responce = $api->getUpdates();
    $new_count = count($responce['result']);
    if (($new_count != $old_count) && ($old_count != 0)) {
//        ob_flush();
//        flush();
        $api->sendMessage(62611788, "Вы отправили сообщение: ". $responce['result'][($new_count-1)]['message']['text']);
        if ($responce['result'][($new_count-1)]['message']['text'] == 'exit') {
            $on = false;
        }
    }
    $old_count = $new_count;

//    $api->sendMessage(62611788, "Hello, world!");
    sleep(2);
//    $i++;
}
$api->sendMessage(62611788, "Скрипт остановлен");
?>