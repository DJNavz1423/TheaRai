<script>
  function fetchQrNotifications() {
      fetch('{{ route("qr.orders.notifications") }}')
          .then(response => response.json())
          .then(data => {
              // Use querySelectorAll to get a NodeList of all elements with this class
              const badges = document.querySelectorAll('.qr-notif-badge');
              
              // Loop through each badge and update it
              badges.forEach(badge => {
                  if (data.count > 0) {
                      badge.innerText = data.count;
                      badge.style.display = 'inline-block';
                  } else {
                      badge.style.display = 'none';
                  }
              });
          })
          .catch(error => console.error('Error fetching notifications:', error));
  }

  fetchQrNotifications();
  setInterval(fetchQrNotifications, 10000); 
</script>