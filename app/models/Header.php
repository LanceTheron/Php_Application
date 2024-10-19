<!-- Inside header.php -->
<nav class="navbar navbar-expand-lg bg-light w-100">
    <div class="container-fluid">
        <img src="../images/Recruitment-Logo-Lance.png" alt="Dashboard Image" class="logo" />
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" 
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="UserDashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="AssignedTasks.php">Assigned Tasks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="completed_tasks.php">Completed Tasks</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-logout ms-lg-2" href="../">Logout</a>
                </li>
            </ul>

            <div class="d-flex align-items-center ms-3" id="weather">
                <span class="nav-link" id="current-date"></span>
                <span class="nav-link" id="weather-info">Loading Weather...</span>
                <span id="weather-icon" class="ms-1"></span>
                <span class="nav-link ms-2" id="city-name">Cape Town</span>
            </div>
        </div>
    </div>
</nav>

<!-- Custom Styles -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap');

    .logo {
        max-width: 160px;
    }

    .navbar-light {
        background-color: #e5e5e5 !important;
    }

    /* Navigation Link Styling */
    .nav-link {
        font-family: 'Montserrat', sans-serif;
        text-transform: uppercase;
        font-weight: bold;
        color: #333;
        transition: color 0.3s ease-in-out;
        padding: 8px 12px;
    }

    .nav-link:hover {
        color: #007bff;
    }

    #weather {
        font-size: 14px;
    }

    #weather-icon i {
        font-size: 16px;
        color: #ffa500;
    }

    .navbar-collapse {
        flex-grow: 0 !important;
    }

    /* Logout Button Styling */
    .btn-logout {
        background-color: #dc3545;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-weight: bold;
        font-family: 'Montserrat', sans-serif;
        text-transform: uppercase;
    }

    .btn-logout:hover {
        background-color: #dc3545;
        color: white;
    }

    /* Additional Styling for Logout Button */
    a.btn.btn-logout.ms-lg-2 {
        background: #00367d;
        color: white;
    }
</style>

<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<!-- Script for Date and Weather -->
<script>
    function formatDate(date) {
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const year = date.getFullYear();
        return `${month}/${day}/${year}`;
    }

    document.getElementById('current-date').textContent = formatDate(new Date());

    async function fetchWeather() {
        const latitude = -33.918861;
        const longitude = 18.4233;

        try {
            const response = await fetch(
                `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&current_weather=true`
            );
            const data = await response.json();

            const { weathercode, temperature, is_day } = data.current_weather;
            const tempCelsius = Math.round(temperature);

            let weatherIcon;
            if (is_day) {
                weatherIcon = '<i class="fas fa-sun"></i>';
            } else if (weathercode === 3 || weathercode === 4) {
                weatherIcon = '<i class="fas fa-cloud"></i>';
            } else {
                weatherIcon = '<i class="fas fa-moon"></i>';
            }

            document.getElementById('weather-info').textContent = `${tempCelsius}°C`;
            document.getElementById('weather-icon').innerHTML = weatherIcon;
        } catch (error) {
            console.error(error);
            document.getElementById('weather-info').textContent = 'Weather unavailable';
            document.getElementById('weather-icon').innerHTML = '';
        }
    }

    fetchWeather();
</script>
