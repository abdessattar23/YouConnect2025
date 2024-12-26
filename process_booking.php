<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

function small($string, $maxLength = 20, $trailingChars = 4) {
    if (strlen($string) > $maxLength) {
        $visibleStart = substr($string, 0, $maxLength - $trailingChars - 3);
        $visibleEnd = substr($string, -$trailingChars);
        return $visibleStart . '...' . $visibleEnd;
    }
    return $string;
}

$fullname = trim($_POST['fullname']);
$email = trim($_POST['email']);
$ticket_number = 'TKT-' . strtoupper(substr(md5(uniqid()), 0, 8));

$filePath = 'data/bookings.json';

if (!file_exists($filePath)) {
    file_put_contents($filePath, json_encode([]));
}

$bookings = json_decode(file_get_contents($filePath), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Failed to read booking data']);
    exit;
}

foreach ($bookings as $booking) {
    if ($booking['email'] === $email || $booking['fullname'] === $fullname) {
        echo json_encode(['success' => false, 'message' => 'You have already booked a ticket']);
        exit;
    }
}

$newBooking = [
    'fullname' => $fullname,
    'email' => $email,
    'used' => false,
    'ticket_number' => $ticket_number
];
$bookings[] = $newBooking;

if (!file_put_contents($filePath, json_encode($bookings, JSON_PRETTY_PRINT))) {
    echo json_encode(['success' => false, 'message' => 'Failed to save booking']);
    exit;
}

$ticketHtml = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Year Party 2025</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body>
    <div style="width: 60em; margin: auto; font-family: sans-serif;">
  <div style="background: linear-gradient(to bottom, #1e40af 0%, #1e40af 26%, #1e293b 26%, #1e293b 100%); height: 12em; float: left; position: relative; padding: 1em; margin-top: 50px; border-top-left-radius: 8px; border-bottom-left-radius: 8px; width: 30em;">
    <h1 style="font-size: 1.5em; margin-top: 0; color: #93c5fd; text-align:center">🎉 New Year Party 2025 🎉 </h1>
    <div style="display: flex; margin: 0">
        <div>
    <div style="text-transform: uppercase; font-weight: normal; margin: 1em 0 0 0;">
      <h2 style="font-size: 0.9em; color: #60a5fa; margin: 0;">Location</h2>
      <span style="font-size: 0.8em; color: #93c5fd;">Youcode nador</span>
    </div>
    <div style="text-transform: uppercase; font-weight: normal; margin: 1em 0 0 0;">
      <h2 style="font-size: 0.9em; color: #60a5fa; margin: 0;">Date</h2>
      <span style="font-size: 0.8em; color: #93c5fd;">31st Dec 2024</span>
    </div>
    <div style="text-transform: uppercase; font-weight: normal; margin: 1em 0 0 0;">
      <h2 style="font-size: 0.9em; color: #60a5fa; margin: 0;">Time</h2>
      <span style="font-size: 0.8em; color: #93c5fd;">5:30 PM - Midnight</span>
    </div>
    </div><div style="margin-left: 10em">
    <div style="text-transform: uppercase; font-weight: normal; margin: 1em 0 0 0;">
      <h2 style="font-size: 0.9em; color: #60a5fa; margin: 0;">Full Name</h2>
      <span style="font-size: 0.8em; color: #93c5fd;">' . htmlspecialchars($fullname) . '</span>
    </div>
    <div style="text-transform: uppercase; font-weight: normal; margin: 1em 0 0 0;">
      <h2 style="font-size: 0.9em; color: #60a5fa; margin: 0;">email</h2>
      <span style="font-size: 0.8em; color: #93c5fd;">' . htmlspecialchars($email) . '</span>
    </div>
    <div style="text-transform: uppercase; font-weight: normal; margin: 1em 0 0 0;">
      <h2 style="font-size: 0.9em; color: #60a5fa; margin: 0;">number</h2>
      <span style="font-size: 0.8em; color: #93c5fd;">' . $ticket_number . '</span>
    </div>
    </div>
  </div>
  </div>
  <div style="background: linear-gradient(to bottom, #1e40af 0%, #1e40af 26%, #1e293b 26%, #1e293b 100%); height: 12em; float: left; position: relative; padding: 1em; margin-top: 50px; width: 6.5em; border-left: 0.18em dashed #60a5fa; border-top-right-radius: 8px; border-bottom-right-radius: 8px;">
    <div style="text-align: center; margin: 1em 0;">
      <img src="https://api.qrserver.com/v1/create-qr-code/?size=115x115&data=http://youconnect.rf.gd/tickets/html/' . $ticket_number . '.html" 
           alt="Ticket QR Code" 
           style="width: 6em; height: 6em; background: #ffffff; padding: 0.5em; border-radius: 8px;">
    </div>
    <div style="text-align: center; color: #60a5fa; margin: 0;">
      <span style="font-size: 0.9em; text-transform: uppercase;">Scan for Details</span>
    </div>
  </div>
</div>
</body>
</html>';

file_put_contents('tickets/html/' . $ticket_number . '.html', $ticketHtml);
$baseUrl = "http://youconnect.rf.gd/tickets/html/" . $ticket_number . ".html";
$full_url = 'https://api.microlink.io/?url=' . urlencode($baseUrl) . '&screenshot=true';

$ch = curl_init($full_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
    curl_close($ch);
    exit;
}
curl_close($ch);

$data = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Failed to decode JSON']);
    exit;
}

$screenshotUrl = $data['data']['screenshot']['url'] ?? null;
if (!$screenshotUrl) {
    echo json_encode(['success' => false, 'message' => 'Screenshot URL not found']);
    exit;
}

$savePath = 'tickets/' . $ticket_number . '.png';
$ch = curl_init($screenshotUrl);
$file = fopen($savePath, 'w');
if (!$file) {
    echo json_encode(['success' => false, 'message' => 'Failed to open file for writing']);
    exit;
}

curl_setopt($ch, CURLOPT_FILE, $file);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

if (curl_exec($ch) === false) {
    echo 'cURL Error: ' . curl_error($ch);
    fclose($file);
    curl_close($ch);
    exit;
}

fclose($file);
curl_close($ch);

echo json_encode([
    'success' => true,
    'message' => 'Ticket booked successfully!',
    'ticketUrl' => 'http://youconnect.rf.gd/tickets/' . $ticket_number . '.png'
]);
?>
