<?php

date_default_timezone_set('America/Sao_Paulo');

error_reporting(E_ALL);

require_once 'vendor/autoload.php';

use Guzzle\Http\Client;
use Guzzle\Http\EntityBody;
use Guzzle\Http\Message\Request;
use Guzzle\Http\Message\Response;
# Logs

/** Setting logs **/
$logger = new Monolog\Logger('client');
$logger->pushHandler(new Monolog\Handler\StreamHandler('guzzle.log'));
$logAdapter = new Guzzle\Log\MonologLogAdapter($logger);
$logPlugin = new Guzzle\Plugin\Log\LogPlugin($logAdapter, Guzzle\Log\MessageFormatter::DEBUG_FORMAT);

/** @var $client Client */
$client = new Client("http://google.com.br", array('curl.options'=>array('CURLOPT_RETURNTRANSFER'=>TRUE)));

$client->addSubscriber($logPlugin);

/** @var $request Request */
$request = $client->get();

/** @var $response Response */
$response = $request->send();

/** @var $body EntityBody */
$body = $response->getBody(true);

/*
echo $response->getStatusCode();      // >>> 200
echo $response->getReasonPhrase();    // >>> OK
echo $response->getProtocol();        // >>> HTTP
echo $response->getProtocolVersion(); // >>> 1.1
*/

/** @var $responseInfo O retorno HTTP da requisição **/
$responseInfo = $response->getInfo();

$arrayInfoTransfer = array('http_code'=>$responseInfo['http_code'], 'total_time'=>$responseInfo['total_time'], 'redirect_count'=>$responseInfo['redirect_count'], 'content_type'=>$responseInfo['content_type'], 'content'=>base64_encode($body));

$returnString = json_encode($arrayInfoTransfer);
$logger->addInfo(base64_encode($body));
echo($returnString);
?>