
<?php

/**
 * @file
 * Receive Github webhook and printout the payload into a TXT file for analysis.
 *
 * I would suggest to use this service instead:
 * https://webhook.site/#!/9ca61d6d-7cc8-4341-b405-be5119caa5c4
 * https://github.com/webhooksite/webhook.site
 */

$log_message = '['.date("Y-m-d H:i:s").'] REQUEST:'.PHP_EOL;
$fs = fopen('./1.txt', 'a');
$client_ip = $_SERVER['REMOTE_ADDR'];
$log_message .= 'Request on ['.date("Y-m-d H:i:s").'] from ['.$client_ip.']';
fwrite($fs, $log_message.PHP_EOL);

fwrite($fs, ''.print_r($_GET, TRUE).''.PHP_EOL);

fwrite($fs, '----'.PHP_EOL);

fwrite($fs, ''.print_r($_POST, TRUE).''.PHP_EOL);

fwrite($fs, ''.print_r($data, TRUE).''.PHP_EOL);

fwrite($fs, '--==--'.PHP_EOL);

$parsed = json_decode($_POST['payload'], TRUE);

$slack_message = $parsed['pusher']['name'];

$commit_message = $parsed['head_commit']['message'];

$slack_message .= '(' . $commit_message . ')';

fwrite($fs, '--('.$slack_message.')--'.PHP_EOL);
