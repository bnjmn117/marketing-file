<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OLSHCO Guimba | Enrollment System</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --olshco-red: #8B0000;
            --olshco-red-dark: #660000;
            --olshco-red-light: #A52A2A;
            --olshco-gold: #FFD700;
            --olshco-cream: #FFF5E6;
            --text-dark: #2C3E50;
            --text-light: #7F8C8D;
            --chat-bg: #ffffff;
            --chat-user: #8B0000;
            --chat-bot: #f0f2f5;
            --chat-text: #333333;
            --shadow-color: rgba(139, 0, 0, 0.2);
        }

        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            color: var(--text-dark);
            background: url('images/back.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        /* Dark overlay para sa background image para readable ang text */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--olshco-red);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--olshco-red-dark);
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            background: rgba(139, 0, 0, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 3px solid var(--olshco-gold);
            transition: all 0.3s ease;
        }

        .logo {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-img {
            height: 55px;
            width: 55px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--olshco-gold);
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            background-color: white;
            padding: 2px;
        }

        .school-name-nav {
            font-size: 20px;
            font-weight: 700;
            white-space: nowrap;
        }

        .est-year {
            background: var(--olshco-gold);
            color: var(--olshco-red);
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 14px;
        }

        .nav-links a:hover {
            background: var(--olshco-gold);
            color: var(--olshco-red);
            transform: translateY(-2px);
        }

        .nav-links .register-btn {
            background: var(--olshco-gold);
            color: var(--olshco-red);
            font-weight: 600;
        }

        .nav-links .register-btn:hover {
            background: white;
            color: var(--olshco-red);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding: 120px 40px 80px;
        }

        .hero::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: rgba(255, 215, 0, 0.05);
            transform: rotate(45deg);
            animation: moveBackground 20s linear infinite;
        }

        @keyframes moveBackground {
            0% { transform: rotate(45deg) translate(-10%, -10%); }
            100% { transform: rotate(45deg) translate(10%, 10%); }
        }

        .hero::after {
            content: 'OLSHCO';
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 150px;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.05);
            pointer-events: none;
            transform: rotate(-15deg);
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
            color: white;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .hero-text h1 {
            font-size: 52px;
            line-height: 1.2;
            margin-bottom: 20px;
            font-weight: 800;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .hero-text .highlight {
            color: var(--olshco-gold);
        }

        .hero-text p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.95;
            line-height: 1.8;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
        }

        .btn {
            padding: 15px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
            font-size: 16px;
        }

        .btn-primary {
            background: var(--olshco-gold);
            color: var(--olshco-red);
            border: 2px solid var(--olshco-gold);
        }

        .btn-primary:hover {
            background: transparent;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.3);
        }

        .btn-outline {
            border: 2px solid var(--olshco-gold);
            color: white;
        }

        .btn-outline:hover {
            background: var(--olshco-gold);
            color: var(--olshco-red);
            transform: translateY(-3px);
        }

        .hero-image {
            animation: float 6s ease-in-out infinite;
            text-align: center;
        }

        .hero-image img {
            width: 100%;
            max-width: 450px;
            height: auto;
            border-radius: 50%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            border: 5px solid var(--olshco-gold);
            object-fit: cover;
            aspect-ratio: 1/1;
            background-color: white;
            padding: 5px;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        /* Mission Vision Section */
        .mission-vision {
            padding: 100px 40px;
            background: rgba(255, 245, 230, 0.9);
            backdrop-filter: blur(5px);
            position: relative;
            overflow: hidden;
        }

        .section-title {
            text-align: center;
            font-size: 42px;
            color: var(--olshco-red);
            margin-bottom: 20px;
            font-weight: 700;
            position: relative;
            display: inline-block;
            left: 50%;
            transform: translateX(-50%);
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: var(--olshco-gold);
            border-radius: 2px;
        }

        .mv-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 60px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .mv-card {
            background: white;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(139, 0, 0, 0.2);
            transition: all 0.3s;
            text-align: center;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .mv-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--olshco-red), var(--olshco-gold));
        }

        .mv-card:hover {
            transform: translateY(-10px);
            border-color: var(--olshco-gold);
            box-shadow: 0 30px 60px rgba(139, 0, 0, 0.3);
        }

        .mv-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--olshco-red), var(--olshco-red-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: var(--olshco-gold);
            font-size: 36px;
            border: 3px solid var(--olshco-gold);
        }

        .mv-card h3 {
            font-size: 28px;
            color: var(--olshco-red);
            margin-bottom: 20px;
            font-weight: 700;
        }

        .mv-card p {
            color: #666;
            line-height: 1.8;
            font-size: 16px;
        }

        /* Philosophy Section */
        .philosophy {
            padding: 80px 40px;
            background: rgba(139, 0, 0, 0.9);
            backdrop-filter: blur(5px);
            color: white;
            text-align: center;
        }

        .philosophy h2 {
            font-size: 42px;
            margin-bottom: 30px;
            color: var(--olshco-gold);
            font-weight: 700;
        }

        .philosophy p {
            max-width: 900px;
            margin: 0 auto 30px;
            font-size: 18px;
            line-height: 1.9;
            opacity: 0.95;
        }

        .philosophy-quote {
            font-size: 24px;
            font-style: italic;
            color: var(--olshco-gold);
            margin-top: 40px;
            padding: 30px;
            border-top: 2px solid rgba(255, 215, 0, 0.3);
            border-bottom: 2px solid rgba(255, 215, 0, 0.3);
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Courses Section */
        .courses-section {
            padding: 100px 40px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
        }

        .section-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 50px;
            font-size: 18px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Carousel Styles */
        .programs-wrapper {
            margin-top: 40px;
            position: relative;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .row-title {
            font-size: 28px;
            color: var(--olshco-red);
            margin-bottom: 20px;
            padding-left: 20px;
            border-left: 5px solid var(--olshco-gold);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .row-title i {
            color: var(--olshco-gold);
            font-size: 32px;
        }

        .carousel-container {
            width: 100%;
            overflow: hidden;
            position: relative;
            padding: 20px 0;
        }

        .carousel-track {
            display: flex;
            gap: 30px;
            transition: transform 0.5s ease-in-out;
            will-change: transform;
        }

        .carousel-slide {
            flex: 0 0 calc(33.333% - 20px);
            min-width: 300px;
        }

        /* Course Card */
        .course-card {
            background: linear-gradient(135deg, #fff, #fff5e6);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(139, 0, 0, 0.2);
            transition: all 0.3s;
            text-align: center;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .course-card:hover {
            transform: translateY(-10px);
            border-color: var(--olshco-gold);
            box-shadow: 0 25px 50px rgba(139, 0, 0, 0.3);
        }

        .course-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--olshco-red), var(--olshco-red-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: var(--olshco-gold);
            font-size: 40px;
            border: 3px solid var(--olshco-gold);
            transition: all 0.3s;
        }

        .course-card:hover .course-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .course-card h3 {
            font-size: 26px;
            color: var(--olshco-red);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .course-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
            line-height: 1.4;
        }

        .course-details {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 15px 0;
            font-size: 13px;
            color: var(--olshco-red);
        }

        .course-details span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .course-details i {
            color: var(--olshco-gold);
        }

        .course-full-description {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
            margin: 15px 0;
            flex-grow: 1;
        }

        .course-tag {
            display: inline-block;
            padding: 8px 20px;
            background: linear-gradient(135deg, var(--olshco-red), var(--olshco-red-dark));
            border-radius: 30px;
            font-size: 13px;
            color: white;
            font-weight: 600;
            letter-spacing: 1px;
            margin-top: auto;
        }

        /* Carousel Controls */
        .carousel-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            margin-top: 30px;
        }

        .carousel-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--olshco-red), var(--olshco-red-dark));
            border: 2px solid var(--olshco-gold);
            color: var(--olshco-gold);
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .carousel-btn:hover {
            background: var(--olshco-gold);
            color: var(--olshco-red);
            transform: scale(1.1);
        }

        .carousel-dots {
            display: flex;
            gap: 10px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #ccc;
            cursor: pointer;
            transition: all 0.3s;
        }

        .dot.active {
            background: var(--olshco-gold);
            transform: scale(1.2);
            box-shadow: 0 0 10px var(--olshco-gold);
        }

        /* Features Section */
        .features-section {
            padding: 100px 40px;
            background: rgba(248, 249, 250, 0.9);
            backdrop-filter: blur(5px);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-top: 60px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .feature {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(139, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .feature:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px rgba(139, 0, 0, 0.2);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--olshco-red), var(--olshco-red-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            color: var(--olshco-gold);
            font-size: 32px;
            border: 3px solid var(--olshco-gold);
        }

        .feature h3 {
            font-size: 22px;
            color: var(--olshco-red);
            margin-bottom: 15px;
            font-weight: 600;
        }

        .feature p {
            color: #666;
            line-height: 1.8;
        }

        /* CTA Section - Simplified Red Design */
        .cta-section {
            background: var(--olshco-red);
            padding: 80px 20px;
            text-align: center;
            color: white;
            position: relative;
        }

        .cta-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .cta-section h2 {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 20px;
            color: white;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .cta-section p {
            font-size: 18px;
            margin-bottom: 40px;
            opacity: 0.95;
            line-height: 1.6;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-btn {
            background: white;
            color: var(--olshco-red);
            padding: 15px 50px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 18px;
            transition: all 0.3s ease;
            display: inline-block;
            border: 2px solid white;
        }

        .cta-btn:hover {
            background: transparent;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.2);
        }

        /* Footer */
        .footer {
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(5px);
            color: white;
            padding: 60px 40px 20px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .footer-section h3 {
            color: var(--olshco-gold);
            margin-bottom: 25px;
            font-size: 20px;
            font-weight: 600;
        }

        .footer-section p, .footer-section a {
            color: #999;
            text-decoration: none;
            line-height: 1.9;
            transition: all 0.3s;
            display: block;
            margin-bottom: 10px;
        }

        .footer-section a:hover {
            color: var(--olshco-gold);
            transform: translateX(5px);
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            color: white;
            font-size: 20px;
        }

        .social-links a:hover {
            background: var(--olshco-gold);
            color: var(--olshco-red);
            transform: translateY(-5px);
        }

        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #333;
            color: #999;
            font-size: 14px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Chatbot Styles */
        .chatbot-container {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
            font-family: 'Poppins', sans-serif;
        }

        .chatbot-toggle {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--olshco-red), var(--olshco-red-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(139, 0, 0, 0.4);
            border: 3px solid var(--olshco-gold);
            transition: all 0.3s ease;
            animation: pulse 2s infinite;
        }

        .chatbot-toggle:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 15px 40px rgba(255, 215, 0, 0.3);
        }

        .chatbot-toggle i {
            color: var(--olshco-gold);
            font-size: 30px;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(139, 0, 0, 0.7);
            }
            70% {
                box-shadow: 0 0 0 15px rgba(139, 0, 0, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(139, 0, 0, 0);
            }
        }

        .chatbot-panel {
            position: absolute;
            bottom: 90px;
            right: 0;
            width: 380px;
            height: 550px;
            background: white;
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(139, 0, 0, 0.3);
            display: none;
            flex-direction: column;
            overflow: hidden;
            border: 3px solid var(--olshco-gold);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chatbot-panel.active {
            display: flex;
        }

        .chatbot-header {
            background: linear-gradient(135deg, var(--olshco-red), var(--olshco-red-dark));
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-bottom: 3px solid var(--olshco-gold);
        }

        .chatbot-avatar {
            width: 50px;
            height: 50px;
            background: var(--olshco-gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--olshco-red);
            font-size: 24px;
            font-weight: bold;
            border: 2px solid white;
        }

        .chatbot-title h4 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--olshco-gold);
        }

        .chatbot-title p {
            font-size: 12px;
            opacity: 0.9;
        }

        .chatbot-close {
            margin-left: auto;
            cursor: pointer;
            width: 35px;
            height: 35px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .chatbot-close:hover {
            background: var(--olshco-gold);
            color: var(--olshco-red);
        }

        .chatbot-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            max-width: 85%;
        }

        .message.bot {
            align-self: flex-start;
        }

        .message.user {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--olshco-red);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--olshco-gold);
            font-size: 16px;
            flex-shrink: 0;
        }

        .message.user .message-avatar {
            background: var(--olshco-gold);
            color: var(--olshco-red);
        }

        .message-content {
            background: white;
            padding: 12px 16px;
            border-radius: 18px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            font-size: 13px;
            line-height: 1.5;
            color: #333;
        }

        .message.bot .message-content {
            background: white;
            border-bottom-left-radius: 5px;
        }

        .message.user .message-content {
            background: var(--olshco-red);
            color: white;
            border-bottom-right-radius: 5px;
        }

        .quick-replies {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
            padding: 10px 20px;
            background: #f8f9fa;
        }

        .quick-reply-btn {
            padding: 8px 15px;
            background: white;
            border: 2px solid var(--olshco-red);
            border-radius: 25px;
            font-size: 12px;
            font-weight: 500;
            color: var(--olshco-red);
            cursor: pointer;
            transition: all 0.3s;
        }

        .quick-reply-btn:hover {
            background: var(--olshco-red);
            color: white;
            transform: translateY(-2px);
        }

        .chatbot-input-area {
            padding: 20px;
            background: white;
            border-top: 2px solid #eee;
            display: flex;
            gap: 10px;
        }

        .chatbot-input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #eee;
            border-radius: 30px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            transition: all 0.3s;
        }

        .chatbot-input:focus {
            outline: none;
            border-color: var(--olshco-gold);
        }

        .chatbot-send {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--olshco-red), var(--olshco-red-dark));
            border: none;
            border-radius: 50%;
            color: var(--olshco-gold);
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--olshco-gold);
        }

        .chatbot-send:hover {
            background: var(--olshco-gold);
            color: var(--olshco-red);
            transform: scale(1.1);
        }

        .typing-indicator {
            display: flex;
            gap: 5px;
            padding: 12px 16px;
            background: white;
            border-radius: 18px;
            width: fit-content;
        }

        .typing-indicator span {
            width: 8px;
            height: 8px;
            background: var(--olshco-red);
            border-radius: 50%;
            animation: typing 1s infinite ease-in-out;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .carousel-slide {
                flex: 0 0 calc(50% - 15px);
            }
            
            .mv-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .chatbot-panel {
                width: 350px;
                height: 500px;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 15px;
                padding: 15px 20px;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }

            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
                padding: 20px 0;
            }

            .hero-text h1 {
                font-size: 32px;
            }

            .hero-buttons {
                justify-content: center;
            }

            .section-title {
                font-size: 32px;
            }

            .mv-grid {
                grid-template-columns: 1fr;
            }

            .carousel-slide {
                flex: 0 0 calc(100% - 0px);
            }

            .course-details {
                flex-direction: column;
                gap: 10px;
            }

            .philosophy h2 {
                font-size: 32px;
            }

            .philosophy p {
                font-size: 16px;
            }

            .philosophy-quote {
                font-size: 20px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .cta-section {
                padding: 60px 20px;
            }
            
            .cta-section h2 {
                font-size: 32px;
            }
            
            .cta-section p {
                font-size: 16px;
            }
            
            .cta-btn {
                padding: 12px 40px;
                font-size: 16px;
            }

            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .social-links {
                justify-content: center;
            }
            
            .logo-img {
                height: 45px;
                width: 45px;
            }
            
            .school-name-nav {
                font-size: 16px;
            }

            .chatbot-container {
                bottom: 20px;
                right: 20px;
            }

            .chatbot-toggle {
                width: 60px;
                height: 60px;
            }

            .chatbot-panel {
                width: 320px;
                height: 480px;
                bottom: 80px;
            }
        }

        /* Pause on Hover */
        .carousel-container:hover .carousel-track {
            animation-play-state: paused;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <a href="index.php" class="logo">
            <img src="images/Logo.png" alt="OLSHCO Logo" class="logo-img" onerror="this.src='https://via.placeholder.com/55x55/8B0000/FFD700?text=OLSHCO'">
            <span class="school-name-nav">OLSHCO Guimba</span>
            <span class="est-year">Est. 1947</span>
        </a>
        <div class="nav-links">
            <a href="#home">Home</a>
            <a href="#mission-vision">Mission & Vision</a>
            <a href="#courses">Programs</a>
            <a href="#about">About</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="register-btn">Dashboard</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php" class="register-btn">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Welcome to <span class="highlight">OLSHCO Guimba</span></h1>
                <p>Your journey to excellence begins here. Register now for our quality education programs and become part of our rich tradition of academic excellence and holistic formation.</p>
                <div class="hero-buttons">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="register.php" class="btn btn-primary">Register</a>
                        <a href="#courses" class="btn btn-outline">View Programs</a>
                    <?php else: ?>
                        <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="hero-image">
                <img src="images/Logo.png" alt="OLSHCO Logo" onerror="this.src='https://via.placeholder.com/450x450/8B0000/FFD700?text=OLSHCO'">
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="mission-vision" id="mission-vision">
        <h2 class="section-title" data-aos="fade-up">Our Identity</h2>
        
        <div class="mv-grid">
            <!-- Mission -->
            <div class="mv-card" data-aos="fade-up" data-aos-delay="100">
                <div class="mv-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h3>Mission</h3>
                <p>OLSHCO is committed to provide quality and relevant education to the youth of Guimba and nearby towns, forming them into competent, compassionate, and committed individuals who are ready to serve God and country.</p>
            </div>

            <!-- Vision -->
            <div class="mv-card" data-aos="fade-up" data-aos-delay="200">
                <div class="mv-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <h3>Vision</h3>
                <p>A center of excellence in education that produces morally upright, socially responsible, and globally competitive graduates imbued with the values of truth, justice, and peace.</p>
            </div>

            <!-- Philosophy -->
            <div class="mv-card" data-aos="fade-up" data-aos-delay="300">
                <div class="mv-icon">
                    <i class="fas fa-scroll"></i>
                </div>
                <h3>Philosophy</h3>
                <p>Education is a lifelong process of human development anchored on the principles of love for God, respect for others, and care for the environment, leading to the total formation of the person.</p>
            </div>
        </div>
    </section>

    <!-- Philosophy Section - Expanded -->
    <section class="philosophy">
        <h2 data-aos="fade-up">Our Educational Philosophy</h2>
        <p data-aos="fade-up" data-aos-delay="100">At OLSHCO Guimba, we believe that education is not merely the acquisition of knowledge but the holistic formation of the person. We strive to develop students who are intellectually competent, spiritually mature, morally upright, and socially responsible.</p>
        <p data-aos="fade-up" data-aos-delay="200">We adhere to the principles of inclusive education, recognizing that every student is unique and deserves equal opportunities to learn and grow. Our approach combines academic excellence with values formation, preparing students not just for careers, but for life.</p>
        <div class="philosophy-quote" data-aos="zoom-in" data-aos-delay="300">
            "Pro Deo et Patria" — For God and Country
        </div>
    </section>

    <!-- Academic Programs Section with Auto-Sliding Carousel -->
    <section class="courses-section" id="courses">
        <h2 class="section-title" data-aos="fade-up">Academic Programs</h2>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Discover our quality programs designed for your future success</p>
        
        <!-- College Programs Carousel -->
        <div class="programs-wrapper">
            <h3 class="row-title" data-aos="fade-right">
                <i class="fas fa-graduation-cap"></i> College Programs
            </h3>
            
            <div class="carousel-container">
                <div class="carousel-track" id="collegeCarousel">
                    <!-- BSIT -->
                    <div class="carousel-slide">
                        <div class="course-card">
                            <div class="course-icon">💻</div>
                            <h3>BSIT</h3>
                            <p class="course-description">Bachelor of Science in Information Technology</p>
                            <div class="course-details">
                                <span><i class="fas fa-clock"></i> 4 Years</span>
                                <span><i class="fas fa-users"></i> 120 Units</span>
                            </div>
                            <p class="course-full-description">Prepare for a career in software development, network administration, and IT consulting with our comprehensive IT program.</p>
                            <span class="course-tag">College</span>
                        </div>
                    </div>
                    
                    <!-- HM -->
                    <div class="carousel-slide">
                        <div class="course-card">
                            <div class="course-icon">🍽️</div>
                            <h3>HM</h3>
                            <p class="course-description">Hospitality Management</p>
                            <div class="course-details">
                                <span><i class="fas fa-clock"></i> 4 Years</span>
                                <span><i class="fas fa-users"></i> 130 Units</span>
                            </div>
                            <p class="course-full-description">Learn the art of hotel and restaurant management, event planning, and culinary arts with industry-standard training.</p>
                            <span class="course-tag">College</span>
                        </div>
                    </div>
                    
                    <!-- OAD -->
                    <div class="carousel-slide">
                        <div class="course-card">
                            <div class="course-icon">📋</div>
                            <h3>OAD</h3>
                            <p class="course-description">Office Administration</p>
                            <div class="course-details">
                                <span><i class="fas fa-clock"></i> 4 Years</span>
                                <span><i class="fas fa-users"></i> 125 Units</span>
                            </div>
                            <p class="course-full-description">Master the skills needed for efficient office management, business communication, and administrative support.</p>
                            <span class="course-tag">College</span>
                        </div>
                    </div>
                    
                    <!-- CRIM -->
                    <div class="carousel-slide">
                        <div class="course-card">
                            <div class="course-icon">🔍</div>
                            <h3>CRIM</h3>
                            <p class="course-description">Criminology</p>
                            <div class="course-details">
                                <span><i class="fas fa-clock"></i> 4 Years</span>
                                <span><i class="fas fa-users"></i> 135 Units</span>
                            </div>
                            <p class="course-full-description">Study criminal justice system, law enforcement, forensic science, and correctional administration.</p>
                            <span class="course-tag">College</span>
                        </div>
                    </div>
                    
                    <!-- EDUC -->
                    <div class="carousel-slide">
                        <div class="course-card">
                            <div class="course-icon">📚</div>
                            <h3>EDUC</h3>
                            <p class="course-description">Education</p>
                            <div class="course-details">
                                <span><i class="fas fa-clock"></i> 4 Years</span>
                                <span><i class="fas fa-users"></i> 140 Units</span>
                            </div>
                            <p class="course-full-description">Become a licensed professional teacher with specializations in elementary or secondary education.</p>
                            <span class="course-tag">College</span>
                        </div>
                    </div>

                    <!-- Duplicate BSIT for seamless loop -->
                    <div class="carousel-slide">
                        <div class="course-card">
                            <div class="course-icon">💻</div>
                            <h3>BSIT</h3>
                            <p class="course-description">Bachelor of Science in Information Technology</p>
                            <div class="course-details">
                                <span><i class="fas fa-clock"></i> 4 Years</span>
                                <span><i class="fas fa-users"></i> 120 Units</span>
                            </div>
                            <p class="course-full-description">Prepare for a career in software development, network administration, and IT consulting with our comprehensive IT program.</p>
                            <span class="course-tag">College</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Carousel Controls -->
            <div class="carousel-controls">
                <button class="carousel-btn prev-btn" onclick="slideCollege('prev')">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="carousel-dots" id="collegeDots">
                    <span class="dot active" onclick="goToCollegeSlide(0)"></span>
                    <span class="dot" onclick="goToCollegeSlide(1)"></span>
                    <span class="dot" onclick="goToCollegeSlide(2)"></span>
                    <span class="dot" onclick="goToCollegeSlide(3)"></span>
                    <span class="dot" onclick="goToCollegeSlide(4)"></span>
                </div>
                <button class="carousel-btn next-btn" onclick="slideCollege('next')">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <!-- Senior High School Carousel -->
        <div class="programs-wrapper" style="margin-top: 60px;">
            <h3 class="row-title" data-aos="fade-right">
                <i class="fas fa-school"></i> Senior High School Strands
            </h3>
            
            <div class="carousel-container">
                <div class="carousel-track" id="shsCarousel">
                    <!-- STEM -->
                    <div class="carousel-slide">
                        <div class="course-card">
                            <div class="course-icon">🔬</div>
                            <h3>STEM</h3>
                            <p class="course-description">Science, Technology, Engineering, Mathematics</p>
                            <div class="course-details">
                                <span><i class="fas fa-clock"></i> 2 Years</span>
                                <span><i class="fas fa-users"></i> 15 Subjects</span>
                            </div>
                            <p class="course-full-description">Prepare for college courses in engineering, medical fields, and other science-related programs.</p>
                            <span class="course-tag">Senior High</span>
                        </div>
                    </div>
                    
                    <!-- HUMMS -->
                    <div class="carousel-slide">
                        <div class="course-card">
                            <div class="course-icon">📖</div>
                            <h3>HUMMS</h3>
                            <p class="course-description">Humanities and Social Sciences</p>
                            <div class="course-details">
                                <span><i class="fas fa-clock"></i> 2 Years</span>
                                <span><i class="fas fa-users"></i> 15 Subjects</span>
                            </div>
                            <p class="course-full-description">Ideal for students planning to take up education, political science, psychology, and other social science courses.</p>
                            <span class="course-tag">Senior High</span>
                        </div>
                    </div>
                    
                    <!-- TECHVOC -->
                    <div class="carousel-slide">
                        <div class="course-card">
                            <div class="course-icon">🛠️</div>
                            <h3>TECHVOC</h3>
                            <p class="course-description">Technical-Vocational Livelihood</p>
                            <div class="course-details">
                                <span><i class="fas fa-clock"></i> 2 Years</span>
                                <span><i class="fas fa-users"></i> TESDA Accredited</span>
                            </div>
                            <p class="course-full-description">Gain practical skills in automotive, electronics, cookery, and other technical fields with TESDA certification.</p>
                            <span class="course-tag">Senior High</span>
                        </div>
                    </div>

                    <!-- Duplicate STEM for seamless loop -->
                    <div class="carousel-slide">
                        <div class="course-card">
                            <div class="course-icon">🔬</div>
                            <h3>STEM</h3>
                            <p class="course-description">Science, Technology, Engineering, Mathematics</p>
                            <div class="course-details">
                                <span><i class="fas fa-clock"></i> 2 Years</span>
                                <span><i class="fas fa-users"></i> 15 Subjects</span>
                            </div>
                            <p class="course-full-description">Prepare for college courses in engineering, medical fields, and other science-related programs.</p>
                            <span class="course-tag">Senior High</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Carousel Controls -->
            <div class="carousel-controls">
                <button class="carousel-btn prev-btn" onclick="slideSHS('prev')">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="carousel-dots" id="shsDots">
                    <span class="dot active" onclick="goToSHSSlide(0)"></span>
                    <span class="dot" onclick="goToSHSSlide(1)"></span>
                    <span class="dot" onclick="goToSHSSlide(2)"></span>
                </div>
                <button class="carousel-btn next-btn" onclick="slideSHS('next')">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <h2 class="section-title" data-aos="fade-up">Why Choose OLSHCO?</h2>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Excellence • Formation • Service</p>
        
        <div class="features-grid">
            <div class="feature" data-aos="zoom-in" data-aos-delay="100">
                <div class="feature-icon">📝</div>
                <h3>Easy Enrollment</h3>
                <p>Simple and convenient online registration process with OTP verification for your security.</p>
            </div>
            
            <div class="feature" data-aos="zoom-in" data-aos-delay="150">
                <div class="feature-icon">🎯</div>
                <h3>Quality Programs</h3>
                <p>CHED-recognized programs designed to meet industry standards and global demands.</p>
            </div>
            
            <div class="feature" data-aos="zoom-in" data-aos-delay="200">
                <div class="feature-icon">📊</div>
                <h3>Student Dashboard</h3>
                <p>Personalized dashboard for each program with relevant academic information.</p>
            </div>
            
            <div class="feature" data-aos="zoom-in" data-aos-delay="250">
                <div class="feature-icon">🔐</div>
                <h3>Secure System</h3>
                <p>Your data is protected with our secure registration and login system.</p>
            </div>
            
            <div class="feature" data-aos="zoom-in" data-aos-delay="300">
                <div class="feature-icon">📧</div>
                <h3>Email Verification</h3>
                <p>OTP sent to your email for secure account verification and activation.</p>
            </div>
            
            <div class="feature" data-aos="zoom-in" data-aos-delay="350">
                <div class="feature-icon">⏰</div>
                <h3>24/7 Access</h3>
                <p>Access your student dashboard anytime, anywhere, any device.</p>
            </div>
        </div>
    </section>

    <!-- Call to Action Section - Simplified Red Design -->
    <section class="cta-section">
        <div class="cta-content">
            <h2 data-aos="fade-up">Start Your Journey Today</h2>
            <p data-aos="fade-up" data-aos-delay="100">Be part of the OLSHCO community and shape your future with us.</p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="cta-btn" data-aos="zoom-in" data-aos-delay="200">Enroll Now</a>
            <?php else: ?>
                <a href="dashboard.php" class="cta-btn" data-aos="zoom-in" data-aos-delay="200">Go to Dashboard</a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="about">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About OLSHCO Guimba</h3>
                <p>Our Lady of Sacred Heart College (OLSHCO) has been providing quality education to the youth of Guimba and nearby towns since 1947. We are committed to academic excellence and holistic formation.</p>
            </div>
            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <p><a href="#home">Home</a></p>
                <p><a href="#mission-vision">Mission & Vision</a></p>
                <p><a href="#courses">Programs Offered</a></p>
                <p><a href="register.php">Enroll Now</a></p>
                <p><a href="login.php">Student Login</a></p>
            </div>
            
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p><i class="fas fa-map-marker-alt"></i> Poblacion, Guimba, Nueva Ecija</p>
                <p><i class="fas fa-phone"></i> (044) 943-1234</p>
                <p><i class="fas fa-envelope"></i> admissions@olshco.edu.ph</p>
            </div>
            
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
                <p style="margin-top: 20px;">#OLSHCOGuimba</p>
            </div>
        </div>
        
        <div class="copyright">
            &copy; 2024 Our Lady of Sacred Heart College - Guimba. All rights reserved.
        </div>
    </footer>

    <!-- Chatbot Container -->
    <div class="chatbot-container">
        <div class="chatbot-toggle" onclick="toggleChatbot()">
            <i class="fas fa-comment-dots"></i>
        </div>
        <div class="chatbot-panel" id="chatbotPanel">
            <div class="chatbot-header">
                <div class="chatbot-avatar">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="chatbot-title">
                    <h4>OLSHCO Assistant</h4>
                    <p>Online • Ready to help</p>
                </div>
                <div class="chatbot-close" onclick="toggleChatbot()">
                    <i class="fas fa-times"></i>
                </div>
            </div>
            
            <div class="chatbot-messages" id="chatbotMessages">
                <!-- Messages will be added here dynamically -->
            </div>
            
            <div class="quick-replies" id="quickReplies">
                <button class="quick-reply-btn" onclick="sendQuickReply('admission')">📝 Admission</button>
                <button class="quick-reply-btn" onclick="sendQuickReply('courses')">📚 Courses</button>
                <button class="quick-reply-btn" onclick="sendQuickReply('requirements')">📋 Requirements</button>
                <button class="quick-reply-btn" onclick="sendQuickReply('contact')">📞 Contact</button>
                <button class="quick-reply-btn" onclick="sendQuickReply('tuition')">💰 Tuition</button>
                <button class="quick-reply-btn" onclick="sendQuickReply('schedule')">📅 Schedule</button>
            </div>
            
            <div class="chatbot-input-area">
                <input type="text" class="chatbot-input" id="chatbotInput" placeholder="Type your message..." onkeypress="handleKeyPress(event)">
                <button class="chatbot-send" onclick="sendMessage()">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Data Analytics API Integration - OLSHCO Analytics -->
    <script>
        // OLSHCO Data Analytics System
        // This tracks page views, user engagement, and sends data to analytics endpoint
        
        (function() {
            // Configuration
            const ANALYTICS_ENDPOINT = 'https://api.olshco-analytics.com/v1/track'; // Replace with actual API endpoint in production
            const SITE_ID = 'OLSHCO_GUIMBA_001';
            const SESSION_KEY = 'olshco_analytics_session';
            const VISITOR_KEY = 'olshco_visitor_id';
            
            // Helper: Generate unique ID
            function generateUUID() {
                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                    const r = Math.random() * 16 | 0;
                    const v = c === 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                });
            }
            
            // Get or create visitor ID
            function getVisitorId() {
                let visitorId = localStorage.getItem(VISITOR_KEY);
                if (!visitorId) {
                    visitorId = generateUUID();
                    localStorage.setItem(VISITOR_KEY, visitorId);
                }
                return visitorId;
            }
            
            // Get or create session ID
            function getSessionId() {
                let sessionId = sessionStorage.getItem(SESSION_KEY);
                if (!sessionId) {
                    sessionId = generateUUID();
                    sessionStorage.setItem(SESSION_KEY, sessionId);
                }
                return sessionId;
            }
            
            // Get page information
            function getPageInfo() {
                return {
                    url: window.location.href,
                    path: window.location.pathname,
                    title: document.title,
                    referrer: document.referrer || null,
                    screenWidth: window.screen.width,
                    screenHeight: window.screen.height,
                    viewportWidth: window.innerWidth,
                    viewportHeight: window.innerHeight
                };
            }
            
            // Get device info
            function getDeviceInfo() {
                const ua = navigator.userAgent;
                const isMobile = /Mobile|Android|iPhone|iPad|iPod/i.test(ua);
                const isTablet = /iPad|Android(?!.*Mobile)/i.test(ua);
                const os = (() => {
                    if (/Windows/i.test(ua)) return 'Windows';
                    if (/Mac/i.test(ua)) return 'macOS';
                    if (/Linux/i.test(ua)) return 'Linux';
                    if (/Android/i.test(ua)) return 'Android';
                    if (/iPhone|iPad|iPod/i.test(ua)) return 'iOS';
                    return 'Unknown';
                })();
                const browser = (() => {
                    if (/Chrome/i.test(ua) && !/Edg/i.test(ua)) return 'Chrome';
                    if (/Firefox/i.test(ua)) return 'Firefox';
                    if (/Safari/i.test(ua) && !/Chrome/i.test(ua)) return 'Safari';
                    if (/Edg/i.test(ua)) return 'Edge';
                    if (/Opera|OPR/i.test(ua)) return 'Opera';
                    return 'Other';
                })();
                
                return {
                    userAgent: ua,
                    isMobile: isMobile,
                    isTablet: isTablet,
                    os: os,
                    browser: browser,
                    language: navigator.language,
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone
                };
            }
            
            // Track user engagement (scroll depth, time on page)
            let maxScrollDepth = 0;
            let pageStartTime = Date.now();
            let scrollTimeout;
            
            function trackScrollDepth() {
                const scrollPercent = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
                if (scrollPercent > maxScrollDepth) {
                    maxScrollDepth = Math.min(100, Math.floor(scrollPercent));
                }
            }
            
            function sendPageExitAnalytics() {
                const timeOnPage = Math.floor((Date.now() - pageStartTime) / 1000);
                const exitData = {
                    event_type: 'page_exit',
                    time_on_page: timeOnPage,
                    scroll_depth: maxScrollDepth,
                    timestamp: new Date().toISOString()
                };
                sendAnalyticsData(exitData, true); // Fire and forget, don't block
            }
            
            // Send analytics data to API
            function sendAnalyticsData(data, isExit = false) {
                const payload = {
                    site_id: SITE_ID,
                    visitor_id: getVisitorId(),
                    session_id: getSessionId(),
                    page_info: getPageInfo(),
                    device_info: getDeviceInfo(),
                    user_data: {
                        is_logged_in: <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>,
                        user_id: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>
                    },
                    event_data: data,
                    timestamp: new Date().toISOString()
                };
                
                // Use sendBeacon for exit events (more reliable), fetch for others
                if (isExit && navigator.sendBeacon) {
                    navigator.sendBeacon(ANALYTICS_ENDPOINT, JSON.stringify(payload));
                } else {
                    fetch(ANALYTICS_ENDPOINT, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(payload),
                        keepalive: true
                    }).catch(err => {
                        // Silently fail - analytics shouldn't break the site
                        console.debug('Analytics error:', err);
                    });
                }
            }
            
            // Track page view
            function trackPageView() {
                const pageViewData = {
                    event_type: 'page_view',
                    event_category: 'navigation',
                    event_label: document.title,
                    timestamp: new Date().toISOString()
                };
                sendAnalyticsData(pageViewData);
            }
            
            // Track element clicks
            function trackClick(element, elementType, elementText, elementId, elementClass) {
                const clickData = {
                    event_type: 'click',
                    event_category: 'engagement',
                    event_label: elementText || elementType,
                    element_type: elementType,
                    element_id: elementId,
                    element_class: elementClass,
                    element_text: elementText ? elementText.substring(0, 100) : null,
                    timestamp: new Date().toISOString()
                };
                sendAnalyticsData(clickData);
            }
            
            // Track section view (when section becomes visible)
            function trackSectionView(sectionId, sectionName) {
                const sectionData = {
                    event_type: 'section_view',
                    event_category: 'engagement',
                    event_label: sectionName,
                    section_id: sectionId,
                    timestamp: new Date().toISOString()
                };
                sendAnalyticsData(sectionData);
            }
            
            // Track chat interactions
            function trackChatInteraction(action, message = null) {
                const chatData = {
                    event_type: 'chat_interaction',
                    event_category: 'chatbot',
                    event_label: action,
                    chat_action: action,
                    chat_message: message ? message.substring(0, 200) : null,
                    timestamp: new Date().toISOString()
                };
                sendAnalyticsData(chatData);
            }
            
            // Track carousel interactions
            function trackCarouselInteraction(carouselName, action, slideIndex) {
                const carouselData = {
                    event_type: 'carousel_interaction',
                    event_category: 'carousel',
                    event_label: `${carouselName}_${action}`,
                    carousel_name: carouselName,
                    carousel_action: action,
                    slide_index: slideIndex,
                    timestamp: new Date().toISOString()
                };
                sendAnalyticsData(carouselData);
            }
            
            // Track program card click
            function trackProgramClick(programName, programLevel) {
                const programData = {
                    event_type: 'program_click',
                    event_category: 'programs',
                    event_label: programName,
                    program_name: programName,
                    program_level: programLevel,
                    timestamp: new Date().toISOString()
                };
                sendAnalyticsData(programData);
            }
            
            // Track CTA button click
            function trackCTAClick(buttonName, buttonDestination) {
                const ctaData = {
                    event_type: 'cta_click',
                    event_category: 'conversion',
                    event_label: buttonName,
                    button_name: buttonName,
                    button_destination: buttonDestination,
                    timestamp: new Date().toISOString()
                };
                sendAnalyticsData(ctaData);
            }
            
            // Setup Intersection Observer for section views
            function setupSectionTracking() {
                const sections = [
                    { id: 'home', name: 'Hero Section' },
                    { id: 'mission-vision', name: 'Mission Vision Section' },
                    { id: 'courses', name: 'Courses Section' },
                    { id: 'features', name: 'Features Section' },
                    { id: 'about', name: 'About/Footer Section' }
                ];
                
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const section = sections.find(s => s.id === entry.target.id);
                            if (section) {
                                trackSectionView(section.id, section.name);
                                // Unobserve after first view to avoid multiple triggers
                                observer.unobserve(entry.target);
                            }
                        }
                    });
                }, { threshold: 0.5 });
                
                sections.forEach(section => {
                    const element = document.getElementById(section.id);
                    if (element) {
                        observer.observe(element);
                    }
                });
            }
            
            // Setup click tracking
            function setupClickTracking() {
                document.addEventListener('click', function(event) {
                    const target = event.target;
                    let element = target;
                    
                    // Find the most relevant clickable element
                    while (element && element !== document.body) {
                        if (element.tagName === 'A' || element.tagName === 'BUTTON' || 
                            element.classList?.contains('course-card') || 
                            element.classList?.contains('feature') ||
                            element.classList?.contains('quick-reply-btn')) {
                            break;
                        }
                        element = element.parentElement;
                    }
                    
                    if (!element || element === document.body) return;
                    
                    const tagName = element.tagName;
                    const text = element.innerText?.trim() || '';
                    const id = element.id || null;
                    const className = element.className || null;
                    
                    // Track CTA buttons specifically
                    if (element.classList?.contains('cta-btn') || 
                        element.classList?.contains('btn-primary') ||
                        (tagName === 'A' && (text.includes('Register') || text.includes('Enroll') || text.includes('Login')))) {
                        trackCTAClick(text, element.href || '#');
                    }
                    
                    // Track program cards
                    if (element.classList?.contains('course-card') || element.closest('.course-card')) {
                        const card = element.classList?.contains('course-card') ? element : element.closest('.course-card');
                        const programName = card?.querySelector('h3')?.innerText || '';
                        const programTag = card?.querySelector('.course-tag')?.innerText || '';
                        trackProgramClick(programName, programTag);
                    }
                    
                    // Track carousel buttons
                    if (element.classList?.contains('carousel-btn')) {
                        const isPrev = element.classList?.contains('prev-btn');
                        const carouselName = element.closest('.programs-wrapper')?.querySelector('.row-title')?.innerText || 'Unknown';
                        trackCarouselInteraction(carouselName, isPrev ? 'prev' : 'next', null);
                    }
                    
                    // Track navigation links
                    if (tagName === 'A' && element.getAttribute('href')?.startsWith('#')) {
                        trackClick(element, 'anchor_link', text, id, className);
                    } else {
                        trackClick(element, tagName.toLowerCase(), text, id, className);
                    }
                });
            }
            
            // Expose tracking functions globally for chatbot and carousel integration
            window.analytics = {
                trackChatInteraction,
                trackCarouselInteraction,
                trackProgramClick,
                trackCTAClick
            };
            
            // Override carousel functions to include tracking
            const originalSlideCollege = window.slideCollege;
            const originalSlideSHS = window.slideSHS;
            const originalGoToCollegeSlide = window.goToCollegeSlide;
            const originalGoToSHSSlide = window.goToSHSSlide;
            
            window.slideCollege = function(direction) {
                if (originalSlideCollege) originalSlideCollege(direction);
                const carouselName = 'College Programs';
                trackCarouselInteraction(carouselName, direction, null);
            };
            
            window.slideSHS = function(direction) {
                if (originalSlideSHS) originalSlideSHS(direction);
                const carouselName = 'Senior High School Strands';
                trackCarouselInteraction(carouselName, direction, null);
            };
            
            window.goToCollegeSlide = function(index) {
                if (originalGoToCollegeSlide) originalGoToCollegeSlide(index);
                const carouselName = 'College Programs';
                trackCarouselInteraction(carouselName, 'dot_click', index);
            };
            
            window.goToSHSSlide = function(index) {
                if (originalGoToSHSSlide) originalGoToSHSSlide(index);
                const carouselName = 'Senior High School Strands';
                trackCarouselInteraction(carouselName, 'dot_click', index);
            };
            
            // Override chat functions to include tracking
            const originalSendMessage = window.sendMessage;
            const originalSendQuickReply = window.sendQuickReply;
            const originalToggleChatbot = window.toggleChatbot;
            
            window.sendMessage = function() {
                const input = document.getElementById('chatbotInput');
                const message = input?.value.trim();
                if (message && originalSendMessage) {
                    trackChatInteraction('send_message', message);
                    originalSendMessage();
                } else if (originalSendMessage) {
                    originalSendMessage();
                }
            };
            
            window.sendQuickReply = function(type) {
                trackChatInteraction('quick_reply', type);
                if (originalSendQuickReply) originalSendQuickReply(type);
            };
            
            window.toggleChatbot = function() {
                const panel = document.getElementById('chatbotPanel');
                const isOpening = panel && !panel.classList.contains('active');
                if (isOpening) {
                    trackChatInteraction('open_chatbot', null);
                }
                if (originalToggleChatbot) originalToggleChatbot();
            };
            
            // Initialize analytics
            function initAnalytics() {
                trackPageView();
                setupSectionTracking();
                setupClickTracking();
                
                // Track scroll depth
                window.addEventListener('scroll', function() {
                    if (scrollTimeout) clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(trackScrollDepth, 100);
                });
                
                // Track page exit
                window.addEventListener('beforeunload', sendPageExitAnalytics);
                
                // Track visibility change (tab switching)
                document.addEventListener('visibilitychange', function() {
                    if (document.hidden) {
                        const timeOnPage = Math.floor((Date.now() - pageStartTime) / 1000);
                        const visibilityData = {
                            event_type: 'tab_hide',
                            time_on_page: timeOnPage,
                            scroll_depth: maxScrollDepth,
                            timestamp: new Date().toISOString()
                        };
                        sendAnalyticsData(visibilityData, true);
                    } else {
                        pageStartTime = Date.now(); // Reset timer when returning
                        const visibilityData = {
                            event_type: 'tab_show',
                            timestamp: new Date().toISOString()
                        };
                        sendAnalyticsData(visibilityData);
                    }
                });
            }
            
            // Start analytics when page is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initAnalytics);
            } else {
                initAnalytics();
            }
        })();
    </script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 100,
            easing: 'ease-in-out'
        });

        // Carousel Variables
        let collegeCurrentIndex = 0;
        let shsCurrentIndex = 0;
        let collegeSlideWidth = 330;
        let shsSlideWidth = 330;
        let collegeInterval, shsInterval;

        // Get elements
        const collegeTrack = document.getElementById('collegeCarousel');
        const shsTrack = document.getElementById('shsCarousel');
        const collegeSlides = document.querySelectorAll('#collegeCarousel .carousel-slide');
        const shsSlides = document.querySelectorAll('#shsCarousel .carousel-slide');
        const collegeDots = document.querySelectorAll('#collegeDots .dot');
        const shsDots = document.querySelectorAll('#shsDots .dot');
        const totalCollegeSlides = 5;
        const totalSHSlides = 3;

        // Auto-slide functions
        function startCollegeAutoSlide() {
            collegeInterval = setInterval(() => {
                slideCollege('next');
            }, 3000);
        }

        function startSHSAutoSlide() {
            shsInterval = setInterval(() => {
                slideSHS('next');
            }, 3000);
        }

        // College Carousel Functions
        function slideCollege(direction) {
            if (direction === 'next') {
                collegeCurrentIndex++;
                if (collegeCurrentIndex >= totalCollegeSlides) {
                    collegeCurrentIndex = 0;
                    collegeTrack.style.transition = 'none';
                    collegeTrack.style.transform = `translateX(0)`;
                    setTimeout(() => {
                        collegeTrack.style.transition = 'transform 0.5s ease-in-out';
                        collegeCurrentIndex = 1;
                        updateCollegeSlide();
                    }, 50);
                } else {
                    updateCollegeSlide();
                }
            } else if (direction === 'prev') {
                collegeCurrentIndex--;
                if (collegeCurrentIndex < 0) {
                    collegeCurrentIndex = totalCollegeSlides - 1;
                    collegeTrack.style.transition = 'none';
                    collegeTrack.style.transform = `translateX(-${collegeCurrentIndex * collegeSlideWidth}px)`;
                    setTimeout(() => {
                        collegeTrack.style.transition = 'transform 0.5s ease-in-out';
                    }, 50);
                } else {
                    updateCollegeSlide();
                }
            }
            updateCollegeDots();
        }

        function updateCollegeSlide() {
            collegeTrack.style.transform = `translateX(-${collegeCurrentIndex * collegeSlideWidth}px)`;
        }

        function updateCollegeDots() {
            collegeDots.forEach((dot, index) => {
                if (index === collegeCurrentIndex % totalCollegeSlides) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }

        function goToCollegeSlide(index) {
            collegeCurrentIndex = index;
            updateCollegeSlide();
            updateCollegeDots();
            clearInterval(collegeInterval);
            startCollegeAutoSlide();
        }

        // SHS Carousel Functions
        function slideSHS(direction) {
            if (direction === 'next') {
                shsCurrentIndex++;
                if (shsCurrentIndex >= totalSHSlides) {
                    shsCurrentIndex = 0;
                    shsTrack.style.transition = 'none';
                    shsTrack.style.transform = `translateX(0)`;
                    setTimeout(() => {
                        shsTrack.style.transition = 'transform 0.5s ease-in-out';
                        shsCurrentIndex = 1;
                        updateSHSSlide();
                    }, 50);
                } else {
                    updateSHSSlide();
                }
            } else if (direction === 'prev') {
                shsCurrentIndex--;
                if (shsCurrentIndex < 0) {
                    shsCurrentIndex = totalSHSlides - 1;
                    shsTrack.style.transition = 'none';
                    shsTrack.style.transform = `translateX(-${shsCurrentIndex * shsSlideWidth}px)`;
                    setTimeout(() => {
                        shsTrack.style.transition = 'transform 0.5s ease-in-out';
                    }, 50);
                } else {
                    updateSHSSlide();
                }
            }
            updateSHSDots();
        }

        function updateSHSSlide() {
            shsTrack.style.transform = `translateX(-${shsCurrentIndex * shsSlideWidth}px)`;
        }

        function updateSHSDots() {
            shsDots.forEach((dot, index) => {
                if (index === shsCurrentIndex % totalSHSlides) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }

        function goToSHSSlide(index) {
            shsCurrentIndex = index;
            updateSHSSlide();
            updateSHSDots();
            clearInterval(shsInterval);
            startSHSAutoSlide();
        }

        // Pause on hover
        document.querySelectorAll('.carousel-container').forEach(container => {
            container.addEventListener('mouseenter', () => {
                clearInterval(collegeInterval);
                clearInterval(shsInterval);
            });

            container.addEventListener('mouseleave', () => {
                startCollegeAutoSlide();
                startSHSAutoSlide();
            });
        });

        // Initialize
        window.addEventListener('load', () => {
            setTimeout(() => {
                if (collegeSlides.length > 0) {
                    collegeSlideWidth = collegeSlides[0].offsetWidth + 30;
                    updateCollegeSlide();
                }
                if (shsSlides.length > 0) {
                    shsSlideWidth = shsSlides[0].offsetWidth + 30;
                    updateSHSSlide();
                }
            }, 100);
            
            startCollegeAutoSlide();
            startSHSAutoSlide();
        });

        window.addEventListener('resize', () => {
            if (collegeSlides.length > 0) {
                collegeSlideWidth = collegeSlides[0].offsetWidth + 30;
                updateCollegeSlide();
            }
            if (shsSlides.length > 0) {
                shsSlideWidth = shsSlides[0].offsetWidth + 30;
                updateSHSSlide();
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar background change on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 100) {
                navbar.style.background = 'rgba(139, 0, 0, 0.98)';
                navbar.style.backdropFilter = 'blur(10px)';
                navbar.style.padding = '15px 40px';
            } else {
                navbar.style.background = 'rgba(139, 0, 0, 0.95)';
                navbar.style.padding = '20px 40px';
            }
        });

        // Parallax effect on scroll
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero');
            if (hero) {
                hero.style.backgroundPositionY = scrolled * 0.5 + 'px';
            }
        });

        // Chatbot Functions
        const chatbotMessages = document.getElementById('chatbotMessages');
        const chatbotInput = document.getElementById('chatbotInput');
        const chatbotPanel = document.getElementById('chatbotPanel');

        // Hardcoded knowledge base
        const knowledgeBase = {
            // General
            'hello': 'Hello! 👋 Welcome to OLSHCO Guimba. How can I help you today?',
            'hi': 'Hi there! 😊 How can I assist you with your enrollment concerns?',
            'hey': 'Hey! 👋 What can I do for you today?',
            'good morning': 'Good morning! ☀️ How may I help you with your OLSHCO journey?',
            'good afternoon': 'Good afternoon! 🌞 How can I assist you today?',
            'good evening': 'Good evening! 🌙 How may I help you with your enrollment?',
            
            // Admission
            'admission': '📝 To enroll at OLSHCO Guimba, please follow these steps:\n\n1️⃣ Go to the registration page\n2️⃣ Fill out the enrollment form\n3️⃣ Verify your email with OTP\n4️⃣ Wait for approval\n5️⃣ Proceed with payment\n\nWould you like to know the requirements?',
            'enroll': 'To enroll at OLSHCO, you need to:\n1. Create an account\n2. Fill out the enrollment form\n3. Submit requirements\n4. Pay the enrollment fee\n\nClick the "Register" button to start!',
            'how to enroll': 'Enrollment process:\n\n📌 Step 1: Register online\n📌 Step 2: Email verification\n📌 Step 3: Fill out forms\n📌 Step 4: Submit requirements\n📌 Step 5: Pay enrollment fee\n\nNeed help with a specific step?',
            
            // Courses
            'courses': '🎓 We offer the following programs:\n\n📚 COLLEGE:\n• BSIT - Information Technology\n• HM - Hospitality Management\n• OAD - Office Administration\n• CRIM - Criminology\n• EDUC - Education\n\n🏫 SENIOR HIGH:\n• STEM\n• HUMMS\n• TECHVOC\n\nWhich program interests you?',
            'programs': 'Our academic programs:\n\nCOLLEGE (4 years):\n💻 BSIT - Information Technology\n🍽️ HM - Hospitality Management\n📋 OAD - Office Administration\n🔍 CRIM - Criminology\n📚 EDUC - Education\n\nSHS (2 years):\n🔬 STEM\n📖 HUMMS\n🛠️ TECHVOC\n\nType the program name for more details!',
            'bsit': '💻 Bachelor of Science in Information Technology\n\nDuration: 4 years\nUnits: 120\n\nCareer opportunities:\n• Software Developer\n• Network Administrator\n• IT Consultant\n• Web Developer\n• Database Administrator',
            'hm': '🍽️ Hospitality Management\n\nDuration: 4 years\nUnits: 130\n\nCareer opportunities:\n• Hotel Manager\n• Restaurant Manager\n• Event Planner\n• Culinary Arts\n• Tourism Officer',
            'oad': '📋 Office Administration\n\nDuration: 4 years\nUnits: 125\n\nCareer opportunities:\n• Administrative Assistant\n• Executive Secretary\n• Office Manager\n• Clerk\n• HR Assistant',
            'crim': '🔍 Criminology\n\nDuration: 4 years\nUnits: 135\n\nCareer opportunities:\n• Police Officer\n• Criminologist\n• Forensic Scientist\n• Security Consultant\n• Investigator',
            'educ': '📚 Education\n\nDuration: 4 years\nUnits: 140\n\nCareer opportunities:\n• Elementary Teacher\n• High School Teacher\n• School Administrator\n• Guidance Counselor\n• Curriculum Developer',
            'stem': '🔬 Science, Technology, Engineering, Mathematics\n\nDuration: 2 years\n\nCollege courses you can take:\n• Engineering\n• Medicine\n• Computer Science\n• Architecture\n• Mathematics',
            'humms': '📖 Humanities and Social Sciences\n\nDuration: 2 years\n\nCollege courses you can take:\n• Education\n• Psychology\n• Political Science\n• Sociology\n• Law',
            'techvoc': '🛠️ Technical-Vocational Livelihood\n\nDuration: 2 years\nTESDA Accredited\n\nSpecializations:\n• Automotive\n• Electronics\n• Cookery\n• Bread and Pastry\n• Dressmaking',
            
            // Requirements
            'requirements': '📋 Enrollment Requirements:\n\nFor Freshmen:\n• PSA Birth Certificate\n• Report Card (Form 138)\n• Good Moral Certificate\n• 2x2 ID Pictures (4 pcs)\n• Long Brown Envelope\n\nFor Transferees:\n• All freshmen requirements\n• Transfer Credentials\n• Honorable Dismissal\n\nFor SHS:\n• JHS Completion Certificate\n• Report Card (Grade 10)',
            'requirements for enrollment': 'Here are the requirements:\n\n✅ PSA Birth Certificate\n✅ Report Card (Form 138)\n✅ Good Moral Certificate\n✅ 2x2 ID Pictures (4 pcs)\n✅ Long Brown Envelope\n\nAdditional for transferees:\n✅ Transfer Credentials\n✅ Honorable Dismissal',
            
            // Contact
            'contact': '📞 Contact Information:\n\n🏫 Address: Poblacion, Guimba, Nueva Ecija\n📞 Phone: (044) 943-1234\n📧 Email: admissions@olshco.edu.ph\n\nOffice Hours:\nMonday-Friday: 8:00 AM - 5:00 PM\nSaturday: 8:00 AM - 12:00 PM',
            'location': '📍 We are located at Poblacion, Guimba, Nueva Ecija. Near the town plaza and church.',
            'email': '📧 You can email us at:\n• admissions@olshco.edu.ph\n• registrar@olshco.edu.ph\n• info@olshco.edu.ph',
            'phone': '📞 Call us at:\n(044) 943-1234\nor\n(044) 943-5678',
            
            // Tuition
            'tuition': '💰 Tuition Fees (Approximate per semester):\n\nCollege:\n• BSIT: ₱15,000 - ₱18,000\n• HM: ₱14,000 - ₱17,000\n• OAD: ₱13,000 - ₱16,000\n• CRIM: ₱16,000 - ₱19,000\n• EDUC: ₱14,000 - ₱17,000\n\nSHS:\n• STEM: ₱12,000 - ₱15,000\n• HUMMS: ₱11,000 - ₱14,000\n• TECHVOC: ₱10,000 - ₱13,000\n\nMiscellaneous fees: ₱2,000 - ₱3,000',
            'payment': 'Payment options:\n\n🏦 Bank Transfer\n💵 Over-the-counter\n💳 Credit/Debit Card\n📱 GCash / PayMaya\n\nInstallment plans available per semester.',
            'scholarship': '🎓 Scholarship Programs:\n\n• Academic Scholar (with high grades)\n• Athletic Scholar\n• Financial Aid\n• Working Student\n• Government Scholarships (CHED, DOST)\n\nVisit our registrar\'s office for more details!',
            
            // Schedule
            'schedule': '📅 School Schedule:\n\nClass hours:\nCollege: 7:30 AM - 5:00 PM\nSHS: 7:30 AM - 4:00 PM\n\nEnrollment period:\n1st Sem: May - June\n2nd Sem: October - November\nSummer: April',
            'calendar': 'Academic Calendar:\n\n• 1st Semester: August - December\n• 2nd Semester: January - May\n• Summer: June - July\n\nEnrollment starts 2 months before each semester.',
            
            // About
            'about': '🏛️ About OLSHCO Guimba:\n\nFounded: 1947\nMotto: "Pro Deo et Patria" (For God and Country)\n\nWe provide quality education to the youth of Guimba and nearby towns, forming competent, compassionate, and committed individuals.',
            'history': '📜 OLSHCO was established in 1947. For over 75 years, we have been providing quality education and holistic formation to students in Guimba and neighboring areas.',
            
            // Mission Vision
            'mission': 'Our Mission:\n\n"OLSHCO is committed to provide quality and relevant education to the youth of Guimba and nearby towns, forming them into competent, compassionate, and committed individuals who are ready to serve God and country."',
            'vision': 'Our Vision:\n\n"A center of excellence in education that produces morally upright, socially responsible, and globally competitive graduates imbued with the values of truth, justice, and peace."',
            'philosophy': 'Our Philosophy:\n\n"Education is a lifelong process of human development anchored on the principles of love for God, respect for others, and care for the environment, leading to the total formation of the person."',
            
            // Login/Register
            'login': '🔐 To login, click the "Login" button in the navigation bar. Use your email and password to access your dashboard.',
            'register': '📝 New student? Click the "Register" button to create your account. You\'ll need to verify your email with an OTP.',
            'forgot password': '🔑 Forgot your password? Click "Forgot Password" on the login page to reset it via email.',
            
            // Features
            'dashboard': '📊 Your student dashboard lets you:\n• View your enrollment status\n• Check your schedule\n• See payment history\n• Update information\n• Access grades',
            'otp': '🔐 OTP (One-Time Password) is sent to your email for verification. It expires in 5 minutes for security.',
            
            // Default
            'default': 'I\'m not sure about that. 🤔 You can ask me about:\n\n• Admission & Enrollment\n• Courses & Programs\n• Requirements\n• Tuition & Payment\n• Contact Information\n• Schedule\n• Mission & Vision\n\nOr use the quick reply buttons below!',
            'help': 'I can help you with:\n\n📝 Admission process\n📚 Courses and programs\n📋 Requirements\n💰 Tuition and payment\n📞 Contact information\n📅 Schedule\n🏛️ About OLSHCO\n\nWhat would you like to know?'
        };

        function toggleChatbot() {
            chatbotPanel.classList.toggle('active');
            if (chatbotPanel.classList.contains('active') && chatbotMessages.children.length === 0) {
                addBotMessage("👋 Hello! I'm your OLSHCO Assistant. How can I help you today?");
                addBotMessage("You can ask me about admission, courses, requirements, tuition, or click the quick reply buttons below!");
            }
        }

        function addUserMessage(message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message user';
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="message-content">${message}</div>
            `;
            chatbotMessages.appendChild(messageDiv);
            scrollToBottom();
        }

        function addBotMessage(message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message bot';
            messageDiv.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="message-content">${message.replace(/\n/g, '<br>')}</div>
            `;
            chatbotMessages.appendChild(messageDiv);
            scrollToBottom();
        }

        function addTypingIndicator() {
            const indicator = document.createElement('div');
            indicator.className = 'message bot typing';
            indicator.id = 'typingIndicator';
            indicator.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="typing-indicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            `;
            chatbotMessages.appendChild(indicator);
            scrollToBottom();
        }

        function removeTypingIndicator() {
            const indicator = document.getElementById('typingIndicator');
            if (indicator) {
                indicator.remove();
            }
        }

        function scrollToBottom() {
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        }

        function getBotResponse(userMessage) {
            userMessage = userMessage.toLowerCase().trim();
            
            // Check for exact matches
            if (knowledgeBase[userMessage]) {
                return knowledgeBase[userMessage];
            }
            
            // Check for keywords
            if (userMessage.includes('hello') || userMessage.includes('hi') || userMessage.includes('hey')) {
                return knowledgeBase['hello'];
            }
            if (userMessage.includes('admission') || userMessage.includes('enroll')) {
                return knowledgeBase['admission'];
            }
            if (userMessage.includes('course') || userMessage.includes('program')) {
                return knowledgeBase['courses'];
            }
            if (userMessage.includes('requirement')) {
                return knowledgeBase['requirements'];
            }
            if (userMessage.includes('contact') || userMessage.includes('locat') || userMessage.includes('address')) {
                return knowledgeBase['contact'];
            }
            if (userMessage.includes('tuition') || userMessage.includes('fee') || userMessage.includes('payment') || userMessage.includes('cost')) {
                return knowledgeBase['tuition'];
            }
            if (userMessage.includes('schedule') || userMessage.includes('calendar')) {
                return knowledgeBase['schedule'];
            }
            if (userMessage.includes('mission')) {
                return knowledgeBase['mission'];
            }
            if (userMessage.includes('vision')) {
                return knowledgeBase['vision'];
            }
            if (userMessage.includes('philosophy')) {
                return knowledgeBase['philosophy'];
            }
            if (userMessage.includes('login') || userMessage.includes('sign in')) {
                return knowledgeBase['login'];
            }
            if (userMessage.includes('register') || userMessage.includes('sign up')) {
                return knowledgeBase['register'];
            }
            if (userMessage.includes('dashboard')) {
                return knowledgeBase['dashboard'];
            }
            if (userMessage.includes('otp') || userMessage.includes('verify')) {
                return knowledgeBase['otp'];
            }
            if (userMessage.includes('scholarship') || userMessage.includes('financial')) {
                return knowledgeBase['scholarship'];
            }
            if (userMessage.includes('about') || userMessage.includes('history')) {
                return knowledgeBase['about'];
            }
            if (userMessage.includes('help')) {
                return knowledgeBase['help'];
            }
            
            // Check for specific course codes
            if (userMessage.includes('bsit')) {
                return knowledgeBase['bsit'];
            }
            if (userMessage.includes('hm') && !userMessage.includes('techvoc')) {
                return knowledgeBase['hm'];
            }
            if (userMessage.includes('oad')) {
                return knowledgeBase['oad'];
            }
            if (userMessage.includes('crim')) {
                return knowledgeBase['crim'];
            }
            if (userMessage.includes('educ')) {
                return knowledgeBase['educ'];
            }
            if (userMessage.includes('stem')) {
                return knowledgeBase['stem'];
            }
            if (userMessage.includes('humms')) {
                return knowledgeBase['humms'];
            }
            if (userMessage.includes('techvoc')) {
                return knowledgeBase['techvoc'];
            }
            
            return knowledgeBase['default'];
        }

        function sendMessage() {
            const message = chatbotInput.value.trim();
            if (message === '') return;
            
            addUserMessage(message);
            chatbotInput.value = '';
            
            addTypingIndicator();
            
            setTimeout(() => {
                removeTypingIndicator();
                const response = getBotResponse(message);
                addBotMessage(response);
            }, 1000);
        }

        function sendQuickReply(type) {
            let message = '';
            switch(type) {
                case 'admission':
                    message = 'How to enroll?';
                    break;
                case 'courses':
                    message = 'What courses do you offer?';
                    break;
                case 'requirements':
                    message = 'What are the requirements?';
                    break;
                case 'contact':
                    message = 'Contact information';
                    break;
                case 'tuition':
                    message = 'Tuition fees';
                    break;
                case 'schedule':
                    message = 'School schedule';
                    break;
                default:
                    message = 'Help';
            }
            
            addUserMessage(message);
            
            addTypingIndicator();
            
            setTimeout(() => {
                removeTypingIndicator();
                const response = getBotResponse(message);
                addBotMessage(response);
            }, 1000);
        }

        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                sendMessage();
            }
        }

        // Close chatbot when clicking outside (optional)
        document.addEventListener('click', function(event) {
            const isClickInside = chatbotPanel.contains(event.target) || event.target.classList.contains('chatbot-toggle') || event.target.closest('.chatbot-toggle');
            if (!isClickInside && chatbotPanel.classList.contains('active')) {
                // Optional: uncomment to close when clicking outside
                // toggleChatbot();
            }
        });

        // Cookie Consent Popup
        document.addEventListener('DOMContentLoaded', function() {
            // Check if user has already made a choice
            const cookieConsent = getCookie('cookie_consent');
            if (!cookieConsent) {
                showCookieConsent();
            }
        });

        function showCookieConsent() {
            const popup = document.createElement('div');
            popup.id = 'cookie-consent-popup';
            popup.innerHTML = `
                <div class="cookie-consent-overlay">
                    <div class="cookie-consent-modal">
                        <div class="cookie-consent-header">
                            <i class="fas fa-cookie-bite"></i>
                            <h3>Cookie Preferences</h3>
                        </div>
                        <div class="cookie-consent-body">
                            <p>We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.</p>
                            <div class="cookie-consent-buttons">
                                <button class="cookie-btn cookie-decline" onclick="declineCookies()">Decline</button>
                                <button class="cookie-btn cookie-accept" onclick="acceptCookies()">Accept All</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(popup);

            // Add styles
            const style = document.createElement('style');
            style.textContent = `
                .cookie-consent-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.7);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 10000;
                    animation: fadeIn 0.3s ease-out;
                }

                .cookie-consent-modal {
                    background: white;
                    border-radius: 15px;
                    padding: 30px;
                    max-width: 500px;
                    width: 90%;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
                    animation: slideUp 0.3s ease-out;
                    text-align: center;
                }

                .cookie-consent-header {
                    margin-bottom: 20px;
                }

                .cookie-consent-header i {
                    font-size: 48px;
                    color: #8B0000;
                    margin-bottom: 15px;
                }

                .cookie-consent-header h3 {
                    color: #2C3E50;
                    font-size: 24px;
                    margin: 0;
                }

                .cookie-consent-body p {
                    color: #666;
                    line-height: 1.6;
                    margin-bottom: 25px;
                    font-size: 16px;
                }

                .cookie-consent-buttons {
                    display: flex;
                    gap: 15px;
                    justify-content: center;
                }

                .cookie-btn {
                    padding: 12px 30px;
                    border: none;
                    border-radius: 8px;
                    font-size: 16px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    font-family: 'Poppins', sans-serif;
                }

                .cookie-accept {
                    background: #8B0000;
                    color: white;
                }

                .cookie-accept:hover {
                    background: #660000;
                    transform: translateY(-2px);
                }

                .cookie-decline {
                    background: #f8f9fa;
                    color: #666;
                    border: 2px solid #ddd;
                }

                .cookie-decline:hover {
                    background: #e9ecef;
                    border-color: #bbb;
                }

                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }

                @keyframes slideUp {
                    from { transform: translateY(30px); opacity: 0; }
                    to { transform: translateY(0); opacity: 1; }
                }

                @media (max-width: 768px) {
                    .cookie-consent-modal {
                        padding: 20px;
                        margin: 20px;
                    }

                    .cookie-consent-buttons {
                        flex-direction: column;
                    }

                    .cookie-btn {
                        width: 100%;
                    }
                }
            `;
            document.head.appendChild(style);
        }

        function acceptCookies() {
            setCookie('cookie_consent', 'accepted', 365);
            <?php
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['cookie_consent'] = 'accepted';
            ?>
            hideCookieConsent();
        }

        function declineCookies() {
            setCookie('cookie_consent', 'declined', 365);
            <?php
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['cookie_consent'] = 'declined';
            ?>
            hideCookieConsent();
        }

        function hideCookieConsent() {
            const popup = document.getElementById('cookie-consent-popup');
            if (popup) {
                popup.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => {
                    popup.remove();
                }, 300);
            }
        }

        function setCookie(name, value, days) {
            const expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
        }

        function getCookie(name) {
            const nameEQ = name + '=';
            const ca = document.cookie.split(';');
            for(let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }
    </script>
</body>
</html>