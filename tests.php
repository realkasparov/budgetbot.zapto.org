<?php
//$data = [
//    'text' => 'message',
//    'author' => 'Ivan',
//    'plot' => [
//        '2' => 'ssdfds',
//        '3' => 'ssdfds',
//    ],
//    'author2' => 'Ivanko',
//];
//$curl = curl_init();
//curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//curl_setopt($curl, CURLOPT_URL, 'http://budgetbot.zapto.org/tests.php');
//curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
//$result = curl_exec($curl);
//curl_close($curl);
//$inputJSON = file_get_contents('php://input');
//$input= json_decode( $inputJSON, true);
//print_r(json_encode($input));
$str = 'сообщение';
$arr = explode(' ', $str);

echo $str;
echo '<br>';
var_dump($arr);
echo '<br>';
$arr['test'] = '20';
$arr['test'] = str_replace([',', '.'], '.', $arr['test']);
//$arr['test'] = (float)$arr['test'];
if ((float)$arr['test']) {
    echo 'ок';
} else {
    echo 'no';
}
echo '<br>';
var_dump(is_float($arr['test']));
echo '<br>';
var_dump($arr['test']);
echo '<br>';

$s =  'Тест';
$s = mb_strtolower($s);
if ($s === 'тест') {
    echo 'ок';
} else {
    echo 'no';
    echo '<br>';
    var_dump($s);
}

?>