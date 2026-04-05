<?php
session_start();

// Check if there's a return URL and course
$return_url = isset($_SESSION['return_url']) ? $_SESSION['return_url'] : 'dashboard.php';
$course = isset($_SESSION['loading_course']) ? $_SESSION['loading_course'] : 'BSIT';

// Course-specific configurations for colors
$course_config = [
    'BSIT' => ['color' => '#2962ff', 'secondary' => '#00c853'],
    'CRIM' => ['color' => '#E74C3C', 'secondary' => '#C0392B'],
    'HM' => ['color' => '#E67E22', 'secondary' => '#D35400'],
    'OAD' => ['color' => '#9B59B6', 'secondary' => '#8E44AD'],
    'EDUC' => ['color' => '#27AE60', 'secondary' => '#229954'],
    'STEM' => ['color' => '#3498DB', 'secondary' => '#2980B9'],
    'HUMMS' => ['color' => '#9B59B6', 'secondary' => '#8E44AD'],
    'TECHVOC' => ['color' => '#E67E22', 'secondary' => '#D35400']
];

// Get current course config, fallback to BSIT
$config = isset($course_config[$course]) ? $course_config[$course] : $course_config['BSIT'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OLSHCO | Loading</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: <?php echo $config['color']; ?>;
            --secondary-color: <?php echo $config['secondary']; ?>;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        /* Online Background Image - Full Screen */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: brightness(0.4);
            z-index: -2;
        }

        /* Dark Overlay */
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7));
            z-index: -1;
        }

        /* Main Content - No Container */
        .content {
            position: relative;
            z-index: 10;
            text-align: center;
            width: 100%;
            max-width: 600px;
            padding: 20px;
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* OLSHCO Logo */
        .olshco-logo {
            margin-bottom: 40px;
        }

        .logo-icon {
            width: 150px;
            height: 150px;
            margin: 0 auto 25px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5rem;
            color: white;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            animation: logoGlow 2s infinite;
            border: 4px solid white;
        }

        @keyframes logoGlow {
            0%, 100% { 
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
                transform: scale(1);
            }
            50% { 
                box-shadow: 0 30px 60px rgba(255, 255, 255, 0.3);
                transform: scale(1.02);
            }
        }

        .school-name {
            color: white;
            font-size: 3.5rem;
            font-weight: 700;
            letter-spacing: 3px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .school-fullname {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.2rem;
            letter-spacing: 3px;
            font-weight: 300;
            margin-bottom: 40px;
            text-transform: uppercase;
        }

        /* Simple Loading Bar */
        .loading-bar-container {
            width: 80%;
            max-width: 400px;
            height: 8px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            overflow: hidden;
            margin: 30px auto 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .loading-bar-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 4px;
            transition: width 0.3s ease;
            position: relative;
        }

        .loading-bar-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* Simple Percentage */
        .percentage {
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 0 20px var(--primary-color);
            margin: 20px 0;
            animation: percentagePulse 2s infinite;
        }

        @keyframes percentagePulse {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 1; }
        }

        /* Loading Dots */
        .loading-dots {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 30px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: white;
            animation: dotPulse 1.5s infinite;
            box-shadow: 0 0 20px var(--primary-color);
        }

        .dot:nth-child(2) {
            animation-delay: 0.3s;
        }

        .dot:nth-child(3) {
            animation-delay: 0.6s;
        }

        @keyframes dotPulse {
            0%, 100% { 
                transform: scale(0.8); 
                opacity: 0.5;
            }
            50% { 
                transform: scale(1.2); 
                opacity: 1;
            }
        }

        /* Loading Text */
        .loading-text {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            letter-spacing: 3px;
            margin-top: 20px;
            text-transform: uppercase;
            font-weight: 300;
        }

        /* Fade out animation */
        .fade-out {
            animation: fadeOut 0.5s ease forwards;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                visibility: hidden;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .logo-icon {
                width: 120px;
                height: 120px;
                font-size: 4rem;
            }
            
            .school-name {
                font-size: 2.5rem;
                letter-spacing: 2px;
            }
            
            .school-fullname {
                font-size: 1rem;
                letter-spacing: 2px;
            }
            
            .percentage {
                font-size: 2rem;
            }
            
            .loading-bar-container {
                width: 90%;
            }
        }

        @media (max-width: 480px) {
            .logo-icon {
                width: 100px;
                height: 100px;
                font-size: 3.5rem;
            }
            
            .school-name {
                font-size: 2rem;
            }
            
            .school-fullname {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Main Content - No Container -->
    <div class="content" id="loadingContent">
        <!-- OLSHCO Logo and Name -->
        <div class="olshco-logo">
            <div class="logo-icon">
                <i class="fas fa-university"></i>
            </div>
            <h1 class="school-name">OLSHCO</h1>
            <div class="school-fullname">Our Lady of Sacred Heart College</div>
        </div>

        <!-- Simple Loading Bar -->
        <div class="loading-bar-container">
            <div class="loading-bar-fill" id="loadingBar"></div>
        </div>

        <!-- Percentage Counter -->
        <div class="percentage" id="percentageCounter">0%</div>

        <!-- Loading Dots -->
        <div class="loading-dots">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>

        <!-- Loading Text -->
        <div class="loading-text">Please wait</div>
    </div>

    <script>
        // Loading animation with progress simulation
        let progress = 0;
        const percentageCounter = document.getElementById('percentageCounter');
        const loadingBar = document.getElementById('loadingBar');
        const loadingContent = document.getElementById('loadingContent');
        
        // Return URL from PHP
        const returnUrl = '<?php echo $return_url; ?>';

        // Simulate loading progress
        const interval = setInterval(() => {
            progress += Math.random() * 2;
            
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
                
                // Add fade out effect
                loadingContent.classList.add('fade-out');
                
                // Redirect to dashboard after animation
                setTimeout(() => {
                    window.location.href = returnUrl;
                }, 500);
            }
            
            // Update progress displays
            percentageCounter.textContent = Math.round(progress) + '%';
            loadingBar.style.width = progress + '%';
        }, 100);
    </script>
</body>
</html>