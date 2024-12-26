<?php
$filePath = 'data/bookings.json';

if (!file_exists($filePath)) {
    file_put_contents($filePath, json_encode([]));
}

$bookings = json_decode(file_get_contents($filePath), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Failed to read booking data";
    exit;
}

$ticket_number = isset($_GET['param']) ? $_GET['param'] : null;

if ($ticket_number) {
    foreach ($bookings as $index => $booking) {
        if ($booking['ticket_number'] === $ticket_number) {
            if (isset($booking['used']) && !$booking['used']) {
                $bookings[$index]['used'] = true;
                if (!file_put_contents($filePath, json_encode($bookings, JSON_PRETTY_PRINT))) {
                    echo "Failed to save booking";
                    exit;
                }
                echo "ok";
                exit;
            } elseif (isset($booking['used']) && $booking['used']) {
                echo "bad " . $booking['fullname'];
                exit;
            }
        }
    }

    echo "bad";
} else {
    echo "Ticket number is required";
}
?>
