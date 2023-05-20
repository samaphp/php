<?php

/**
 * @file
 * One file to receive Github hooks and send it to Slack as a customized request.
 *
 * If your use is a simple notifications, I would strongly recommend to use IFTTT or Zapier.
 * Add new webhook from your Github repository to this file.
 * https://github.com/YOUR/REPO/settings/hooks
 */

if (!isset($_GET['token']) || ($_GET['token'] != 'HARD_CODED_TOKEN')) {
  exit;
}

// Validate the Github payload
if (!isset($_POST['payload']) || empty(json_decode($_POST['payload'], TRUE))) {
  exit;
}

$parsed = json_decode($_POST['payload'], TRUE);

// Validate the pusher details.
if (!isset($parsed['pusher']) || !isset($parsed['pusher']['name'])) {
  exit;
}

if (!isset($parsed['head_commit']) || !isset($parsed['head_commit']['message'])) {
  exit;
}

$username = $parsed['pusher']['name'];
$commit_message = $parsed['head_commit']['message'];

$message = '@' . htmlspecialchars($username) . ': pushed new code (`' . htmlspecialchars($commit_message) . '`)';
slack_message($message);

function slack_message($message) {
  $ch = curl_init();
  // Get your Incoming hook URL from this:
  // https://api.slack.com/apps/YOUR_APP/incoming-webhooks
  curl_setopt($ch, CURLOPT_URL, 'https://hooks.slack.com/services/PART1/PART2/PART3');
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-type: application/json',
  ]);
  $json_array = [
    'text' => $message,
  ];
  $body = json_encode($json_array);

  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
  $response = curl_exec($ch);
  if (!$response) {
    die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
  }
  echo 'HTTP Status Code: ' . curl_getinfo($ch, CURLINFO_HTTP_CODE) . PHP_EOL;
  echo 'Response Body: ' . $response . PHP_EOL;
  curl_close($ch);
}
