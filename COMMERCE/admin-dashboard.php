<?php
session_start();
//check if user is logged in
if(!isset($_SESSION["email"])){
    header("Location: /COMMERCE/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">  
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-4xl font-bold mb-6 text-center">Admin Dashboard</h1>
        <header class="mb-10 flex justify-end">
            <div class="flex space-x-4">
                <a href="logout.php" 
       class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
        Logout
    </a>
        <a href="user-dashboard.php"
    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors">Users</a>
    
            </div>

             <label class="theme-switch">
            <input type="checkbox" id="theme-toggle">
            <span class="slider"></span>
        </label>
</header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
    <canvas id="usersChart" class="bg-white p-4 rounded-lg shadow"></canvas>
    <canvas id="salesChart" class="bg-white p-4 rounded-lg shadow"></canvas>
    <canvas id="productsChart" class="bg-white p-4 rounded-lg shadow"></canvas>
</div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- User Management Card -->
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-2xl font-semibold mb-4">User Management</h2>
                <p class="mb-4">Manage user accounts, roles, and permissions.</p>
                <a href="user-management.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">Go to User Management</a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-2xl font-semibold mb-4">Product Management</h2>
                <p class="mb-4">Add, edit, and remove products from the catalog.</p>
                <a href="product-management.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">Go to Product Management</a>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-2xl font-semibold mb-4">Order Management</h2>
                <p class="mb-4">View and manage customer orders.</p>
                <a href="order-management.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">Go to Order Management</a>
            </div>
            <!-- Site Settings Card -->
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-2xl font-semibold mb-4">Site Settings</h2>
                <p class="mb-4">Configure site-wide settings and preferences.</p>
                <a href="site-settings.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">Go to Site Settings</a>
            </div>
            
            <!-- Reports Card -->
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-2xl font-semibold mb-4">Reports</h2>
                <p class="mb-4">View and generate site reports.</p>
                <a href="reports.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">Go to Reports</a>
            </div>
            <!-- Content Moderation Card -->
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-2xl font-semibold mb-4">Content Moderation</h2>
                <p class="mb-4">Review and manage user-generated content.</p>
                <a href="content-moderation.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">Go to Content Moderation</a>
            </div>
            <!-- System Logs Card -->
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-2xl font-semibold mb-4">System Logs</h2>
                <p class="mb-4">View system activity and logs.</p>
                <a href="system-logs.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">Go to System Logs</a>
            </div>
            <!-- Notifications Card -->
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <h2 class="text-2xl font-semibold mb-4">Notifications</h2>
                <p class="mb-4">Manage site notifications and alerts.</p>
                <a href="notifications.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">Go to Notifications</a>  
            </div>
        </div>
    </div>
    <script>
        

        // Sample data for charts
        const usersData = {
            labels: ['January', 'February', 'March', 'April', 'May', 'June'],
            datasets: [{
                label: 'New Users',
                data: [50, 75, 150, 100, 200, 250],
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]
        };

        const salesData = {
            labels: ['January', 'February', 'March', 'April', 'May', 'June'],
            datasets: [{
                label: 'Sales',
                data: [3000, 4000, 3500, 5000, 6000, 7000],
                backgroundColor: 'rgba(16, 185, 129, 0.5)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 1
            }]
        };

        const productsData = {
            labels: ['Product A', 'Product B', 'Product C', 'Product D'],
            datasets: [{
                label: 'Products Sold',
                data: [120, 150, 180, 90],
                backgroundColor: [
                    'rgba(239, 68, 68, 0.5)',
                    'rgba(234, 179, 8, 0.5)',
                    'rgba(14, 165, 233, 0.5)',
                    'rgba(139, 92, 246, 0.5)'
                ],
                borderColor: [
                    'rgba(239, 68, 68, 1)',
                    'rgba(234, 179, 8, 1)',
                    'rgba(14, 165, 233, 1)',
                    'rgba(139, 92, 246, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Configurations for charts
        const usersConfig = {
            type: 'bar',
            data: usersData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };

        const salesConfig = {
            type: 'line',
            data: salesData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };
        const productsConfig = {
            type: 'doughnut',
            data: productsData,
            options: {
                responsive: true
            }
        };
        // Render charts
        new Chart(document.getElementById('usersChart'), usersConfig);
        new Chart(document.getElementById('salesChart'), salesConfig); 
        new Chart(document.getElementById('productsChart'), productsConfig);
        // Chart.js setup
async function renderAdminCharts(){
  // mock/fallback data
  const usersData = [12, 19, 3, 5, 2, 3];
  const salesData = [200, 450, 300, 500, 700, 400];
  const productsData = [50, 30, 20, 10, 5, 15];

  // fetch from API if available
  try{
    const u = await fetch('/api/admin/stats/users').then(r=>r.json());
    if(u.data) usersData.splice(0, usersData.length, ...u.data);
  }catch(e){}

  try{
    const s = await fetch('/api/admin/stats/sales').then(r=>r.json());
    if(s.data) salesData.splice(0, salesData.length, ...s.data);
  }catch(e){}

  try{
    const p = await fetch('/api/admin/stats/products').then(r=>r.json());
    if(p.data) productsData.splice(0, productsData.length, ...p.data);
  }catch(e){}

  // Users chart
  new Chart(document.getElementById('usersChart'), {
    type:'line',
    data:{ labels:['Jan','Feb','Mar','Apr','May','Jun'], datasets:[{label:'Users', data:usersData, borderColor:'rgba(79,70,229,1)', backgroundColor:'rgba(79,70,229,0.2)', tension:0.3}] },
    options:{ responsive:true, plugins:{ legend:{ display:true } } }
  });

  // Sales chart
  new Chart(document.getElementById('salesChart'), {
    type:'bar',
    data:{ labels:['Jan','Feb','Mar','Apr','May','Jun'], datasets:[{label:'Sales', data:salesData, backgroundColor:'rgba(16,185,129,0.7)'}] },
    options:{ responsive:true }
  });

  // Products chart
  new Chart(document.getElementById('productsChart'), {
    type:'doughnut',
    data:{ labels:['In Stock','Low Stock','Out of Stock'], datasets:[{label:'Products', data:productsData.slice(0,3), backgroundColor:['#4f46e5','#f59e0b','#ef4444']}] },
    options:{ responsive:true }
  });
}

// call charts render when admin opens panel
document.getElementById('open-admin').addEventListener('click', renderAdminCharts);



/* --------------------------------------------------------
   1. CHARTS (Version propre, sans doublons et sans erreurs)
--------------------------------------------------------- */
new Chart(document.getElementById('usersChart'), {
    type: 'bar',
    data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June'],
        datasets: [{
            label: 'New Users',
            data: [50, 75, 150, 100, 200, 250],
            backgroundColor: 'rgba(59, 130, 246, 0.5)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 1
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June'],
        datasets: [{
            label: 'Sales',
            data: [3000, 4000, 3500, 5000, 6000, 7000],
            backgroundColor: 'rgba(16, 185, 129, 0.5)',
            borderColor: 'rgba(16, 185, 129, 1)',
            borderWidth: 1,
            tension: 0.4
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('productsChart'), {
    type: 'doughnut',
    data: {
        labels: ['Product A', 'Product B', 'Product C', 'Product D'],
        datasets: [{
            label: 'Products Sold',
            data: [120, 150, 180, 90],
            backgroundColor: [
                'rgba(239, 68, 68, 0.5)',
                'rgba(234, 179, 8, 0.5)',
                'rgba(14, 165, 233, 0.5)',
                'rgba(139, 92, 246, 0.5)'
            ],
            borderColor: [
                'rgba(239, 68, 68, 1)',
                'rgba(234, 179, 8, 1)',
                'rgba(14, 165, 233, 1)',
                'rgba(139, 92, 246, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: { responsive: true }
});


/* --------------------------------------------------------
   2. DARK MODE + LOCALSTORAGE (persistant)
--------------------------------------------------------- */
const themeToggle = document.getElementById('theme-toggle');

// Charger le thème enregistré
if (localStorage.getItem("theme") === "dark") {
    document.documentElement.classList.add("dark");
    themeToggle.checked = true;
}

themeToggle.addEventListener("change", () => {
    if (themeToggle.checked) {
        document.documentElement.classList.add("dark");
        localStorage.setItem("theme", "dark");
    } else {
        document.documentElement.classList.remove("dark");
        localStorage.setItem("theme", "light");
    }
});
</script>

    
</body>
</html>

