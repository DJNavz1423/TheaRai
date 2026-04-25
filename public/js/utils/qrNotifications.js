document.addEventListener('DOMContentLoaded', function() {
    const pollInterval = 15000; // Poll every 15 seconds

    function fetchPendingQrOrders() {
        // Hitting a secure web route so Laravel knows exactly which user/session is asking
        fetch('/api/internal/qr-orders/count')
            .then(response => response.json())
            .then(data => {
                updateBadges(data.count);
            })
            .catch(error => console.error('Error fetching QR order count:', error));
    }

    function updateBadges(count) {
        const badges = document.querySelectorAll('.qr-order-count');
        
        badges.forEach(badge => {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        });
    }

    fetchPendingQrOrders();
    setInterval(fetchPendingQrOrders, pollInterval);
});