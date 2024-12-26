<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YouConnect 2025 Party</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg max-w-md w-full">
        <h1 class="text-3xl font-bold text-center mb-6 text-purple-400">🎉 YouConnect 2025 Party🎉</h1>
        <form id="bookingForm" action="process_booking.php" method="POST" class="space-y-6">
            <?php if (isset($_GET['error'])): ?>
                <div>
                    <p class="text-red-400 text-center font-semibold"><?php echo $_GET['error']; ?></p>
                </div>
            <?php endif; ?>
            <div>
                <label for="fullname" class="block text-sm font-medium text-gray-300">Full Name</label>
                <input type="text" id="fullname" name="fullname" required
                    class="mt-1 block w-full rounded-md border-gray-700 bg-gray-700 text-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                <input type="email" id="email" name="email" required
                    class="mt-1 block w-full rounded-md border-gray-700 bg-gray-700 text-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
            </div>
            <button type="submit"
                class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                Book Ticket
            </button>
        </form>
        <div id="message" class="mt-4 text-center hidden"></div>
    </div>
    <script src="script.js"></script>
</body>
</html>
