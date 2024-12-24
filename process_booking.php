<?php
require 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$fullname = trim($_POST['fullname']);
$email = trim($_POST['email']);

// Check if user already exists
$stmt = $conn->prepare("SELECT id FROM bookings WHERE email = ? AND full_name = ?");
$stmt->bind_param("ss", $email, $fullname);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already booked a ticket']);
    exit;
}

// Generate unique ticket number
$ticket_number = 'TKT-' . strtoupper(substr(md5(uniqid()), 0, 8));

// Insert new booking
$stmt = $conn->prepare("INSERT INTO bookings (full_name, email, ticket_number) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $fullname, $email, $ticket_number);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to book ticket']);
    exit;
}

// Generate ticket HTML
$ticketHtml = '

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
      <span style="font-size: 0.8em; color: #93c5fd;">5:00 PM - Midnight</span>
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
      <img src="https://api.qrserver.com/v1/create-qr-code/?size=115x115&data=https://abdessattar.is-a.dev/YouConnect2025/tickets/html/' . $ticket_number . '.html" 
           alt="Ticket QR Code" 
           style="width: 6em; height: 6em; background: #ffffff; padding: 0.5em; border-radius: 8px;">
    </div>
    <div style="text-align: center; color: #60a5fa; margin: 0;">
      <span style="font-size: 0.9em; text-transform: uppercase;">Scan for Details</span>
    </div>
  </div>
</div>
';
file_put_contents('tickets/html/' . $ticket_number . '.html', $ticketHtml);

$full_url = urlencode("https://abdessattar.is-a.dev/YouConnect2025/tickets/html/" . $ticket_number . ".html");

$pdf = file_get_contents('https://api.microlink.io/?url=' . $full_url . '&pdf=true&embed=pdf.url');

file_put_contents('tickets/' . $ticket_number . '.pdf', $pdf);


echo json_encode([
    'success' => true,
    'message' => 'Ticket booked successfully!',
    'ticketUrl' => 'https://abdessattar.is-a.dev/YouConnect2025/tickets/' . $ticket_number . '.pdf'
]);
?>
