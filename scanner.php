<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/gh/cozmo/jsQR/dist/jsQR.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center text-gray-300">
    <div class="bg-gray-800 p-8 rounded-lg shadow-lg max-w-lg w-full text-center">
        <h1 class="text-3xl font-bold text-purple-400 mb-6">🎉 QR Code Scanner 🎉</h1>
        
        <video autoplay id="reader" class="border-2 border-gray-700 rounded-lg w-full h-64 mb-6"></video>
        <canvas id="canvas" hidden></canvas>

        <form id="qrForm" action="process_qr.php" method="POST" class="space-y-4">
            <input type="hidden" id="qrData" name="qrData">
            <p class="text-lg font-medium">Scanned QR Content:</p>
            <p id="qrContent" class="text-sm bg-gray-700 p-2 rounded-md"></p>
            
            <button type="submit" class="w-full bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                Submit Content
            </button>
        </form>
    </div>

    <script>
     const video = document.getElementById('reader');
const canvas = document.getElementById('canvas');
const context = canvas.getContext('2d');
const output = document.getElementById('qrContent');

navigator.mediaDevices
  .getUserMedia({ video: { facingMode: 'environment' } })
  .then((stream) => {
    video.srcObject = stream;
    video.setAttribute('playsinline', true);
    video.play();
    scanQRCode();
  })
  .catch((err) => console.error('Error accessing camera:', err));
async function fetchDataWithAlert(parameter) {

  Swal.fire({
    title: 'Processing...',
    text: 'Please wait while we fetch the data.',
    allowOutsideClick: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  try {
    const response = await fetch(`/check.php?param=${parameter}`);
    const data = await response.text();

    Swal.close();

    let alertOptions = {};

    if (data === "ok") {
      alertOptions = {
        icon: 'success',
        title: 'Success!',
        text: 'Ticket is Valid!'
      };
    } else if (data.startsWith('bad')) {
      const name = data.substr(3);
      alertOptions = {
        icon: 'error',
        title: 'Error!',
        text: `Ticket already submitted and assigned to ${name}`
      };
    } else {
      alertOptions = {
        icon: 'info',
        title: 'Info',
        text: 'Something unexpected happened.'
      };
    }

    const result = await Swal.fire(alertOptions);

    if (result.isDismissed || result.isConfirmed) {
      location.reload();
    }

  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error!',
      text: 'An error occurred while fetching the data.'
    });
  }
}


function scanQRCode() {
canvas.width = 250;
canvas.height = 250;

  context.drawImage(video, 0, 0, canvas.width, canvas.height);

  const imageData = context.getImageData(0, 0, canvas.width, canvas.height);

  const qrCode = jsQR(imageData.data, imageData.width, imageData.height);

  if (qrCode) {
    output.textContent = qrCode.data;
    var param = qrCode.data.substr(37, 12);
    fetchDataWithAlert(param);
    
  } else {
    requestAnimationFrame(scanQRCode);
  }
}
    </script>
</body>
</html>
