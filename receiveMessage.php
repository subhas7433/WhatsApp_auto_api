<?php
include 'Database.php';

// Verify the webhook subscription by returning the challenge parameter
$challenge = $_REQUEST['hub_challenge'];
if ($challenge) {
    echo $challenge;
    exit;
}

// Get the webhook payload from the HTTP POST request
$webhook_payload = file_get_contents('php://input');

// Decode the JSON payload into a PHP array
$data = json_decode($webhook_payload, true);

// Extracting the message text and phone number from the incoming message
$sender_id = $data['entry'][0]['changes'][0]['value']['messages'][0]['from'];
$message_text = $data['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];

// Insert the received message into the database
$stmt = $conn->prepare('INSERT INTO recieveMessagesTEST (receivedObject) VALUES (?)');
$stmt->bind_param('s', $webhook_payload);
$stmt->execute();

// Set the $_GET parameters for the second file
$_GET['message'] = $message_text;
$_GET['whatsapp'] = $sender_id;

// Start output buffering to capture the echo from the second file
ob_start();

// Include the second PHP file
include 'path/to/second_file.php';

// Get the contents of the output buffer (the echoed response)
$response = ob_get_clean();



// Construct the reply message
$reply_message = array(
    'messaging_product' => 'whatsapp',
    'to' => $sender_id,
    'type' => 'text',
    'text' => array(
        'preview_url' => false,
        'body' =>  $response
    )
);

// Sending the reply message using cURL
$curl = curl_init();

$access_token = 'acc_token';
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://graph.facebook.com/v16.0/117652341285539/messages',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($reply_message),
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '.$access_token,
        'Content-Type: application/json'
    ),
));

$response = curl_exec($curl);
if ($response === false) {
    echo 'cURL Error: '.curl_error($curl);
} else {
    echo $response;
}

curl_close($curl);
?>