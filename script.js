document.getElementById('bookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('process_booking.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const messageDiv = document.getElementById('message');
        messageDiv.classList.remove('hidden');
        
        if (data.success) {
            messageDiv.className = 'mt-4 text-center text-green-600';
            messageDiv.textContent = data.message;
            // Open the ticket in a new window
            window.open(data.ticketUrl, '_blank');
        } else {
            messageDiv.className = 'mt-4 text-center text-red-600';
            messageDiv.textContent = data.message;
        }
    })
    .catch(error => {
        const messageDiv = document.getElementById('message');
        messageDiv.classList.remove('hidden');
        messageDiv.className = 'mt-4 text-center text-red-600';
        messageDiv.textContent = 'An error occurred. Please try again.';
    });
});
