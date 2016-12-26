<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
// https://api.telegram.org/bot320679954:AAE8u4TLYeJWwfH8hFY4uzKHkits3rlaP_c/setWebhook?url=https://fd.pochta911.ru/service/telegram_sync.php
include_once ('../config.php');

/**
 * Задаём основные переменные.
 */
$output = json_decode(file_get_contents('php://input'), TRUE);
file_put_contents('log.txt', "\n OK: " . date('d-m-Y H:i:s') ." ".json_encode($output) , FILE_APPEND);
$chat_id = $output['message']['chat']['id'];
$first_name = $output['message']['chat']['first_name'];
$message = $output['message']['text'];
$message_id = $output['message']['message_id'];

callApi( 'sendMessage', array(
    'chat_id'               => $chat_id,
    'text'                  => "Пришло сообщение: ".$message."",
    'reply_to_message_id'   => $message_id,
));

file_put_contents('log.txt', "\n Message: " . date('d-m-Y H:i:s') ." ".$message , FILE_APPEND);


function callApi( $method, $params) {
    $url = sprintf(
        "https://api.telegram.org/bot%s/%s",
        TLG_TOKEN,
        $method
    );

    $ch = curl_init();
    curl_setopt_array( $ch, array(
        CURLOPT_URL             => $url,
        CURLOPT_POST            => TRUE,
        CURLOPT_RETURNTRANSFER  => TRUE,
        CURLOPT_FOLLOWLOCATION  => FALSE,
        CURLOPT_HEADER          => FALSE,
        CURLOPT_TIMEOUT         => 10,
        CURLOPT_HTTPHEADER      => array( 'Accept-Language: ru,en-us'),
        CURLOPT_POSTFIELDS      => $params,

    ));

    $response = curl_exec($ch);
    return json_decode($response);
}