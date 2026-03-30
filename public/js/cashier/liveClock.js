function startClock() {
    const clockElement = document.getElementById('live-clock');
    
    function updateTime() {
        const now = new Date();
        
        // Options for Philippine Time (Asia/Manila)
        const options = { 
            timeZone: 'Asia/Manila',
            month: 'long',    // "March"
            day: 'numeric',   // "30"
            hour: '2-digit',  // "03"
            minute: '2-digit',// "57"
            second: '2-digit', // "12" (Optional, remove if you want cleaner look)
            hour12: true      // "PM"
        };
        
        // This creates a string like "March 30 at 03:57:12 PM"
        let timeString = now.toLocaleString('en-US', options);

        // Optional: Clean up the string to replace "at" with a separator like "|"
        timeString = timeString.replace(" at ", " | "); 
        
        clockElement.innerText = timeString;
    }

    updateTime(); // Run immediately
    setInterval(updateTime, 1000); // Update every second
}

document.addEventListener('DOMContentLoaded', startClock);
