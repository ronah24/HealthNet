// Function to toggle sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
}

// Event listeners for sidebar toggle
document.querySelector('.menu-toggle').addEventListener('click', toggleSidebar);
document.querySelector('.close-btn').addEventListener('click', toggleSidebar);