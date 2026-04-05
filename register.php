<?php
require_once 'config.php';

// Get schools for dropdown
$colleges = getSchools($conn, 'college');
$highschools = getSchools($conn, 'highschool');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = sanitize($conn, $_POST['fullname']);
    $email = sanitize($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $course_type = $_POST['course_type'];
    $course = $_POST['course'];
    $previous_school_id = isset($_POST['previous_school_id']) ? (int)$_POST['previous_school_id'] : 0;
    $previous_school_other = isset($_POST['previous_school_other']) ? sanitize($conn, $_POST['previous_school_other']) : '';
    
    // Check if email exists
    $check_email = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check_email->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        // Generate OTP
        $otp = generateOTP();
        $otp_expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        // Insert user
        $sql = "INSERT INTO users (fullname, email, password, course_type, course, previous_school_id, previous_school_other, otp_code, otp_expires) 
                VALUES ('$fullname', '$email', '$password', '$course_type', '$course', $previous_school_id, '$previous_school_other', '$otp', '$otp_expires')";
        
        if ($conn->query($sql)) {
            // Send OTP
            if (sendOTP($email, $otp)) {
                $_SESSION['temp_email'] = $email;
                header("Location: verify_otp.php");
                exit();
            } else {
                // For development - show OTP
                $_SESSION['temp_email'] = $email;
                $_SESSION['debug_otp'] = $otp;
                header("Location: verify_otp.php?debug=1");
                exit();
            }
        } else {
            $error = "Registration failed: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guimba Marketing System • Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --maroon-deep: #4A0404;
            --maroon-rich: #6B0F1A;
            --maroon-burgundy: #800020;
            --maroon-crimson: #9B1D2C;
            --maroon-brick: #B22222;
            --maroon-rose: #C21E56;
            --maroon-blush: #D84B5E;
            --maroon-dust: #E6A4B4;
            --maroon-light: #FADADD;
            
            --gold-primary: #C5A028;
            --gold-light: #E5C87B;
            --gold-dark: #8B6F1A;
            
            --gray-dark: #1A0F0F;
            --gray-medium: #4A3A3A;
            --gray-light: #FAF3F3;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(145deg, #1A0404 0%, #2A0A0A 50%, #3B0F0F 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background Elements */
        .bg-ornament {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .ornament-1 {
            position: absolute;
            top: -100px;
            right: -100px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(139, 0, 32, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
        }

        .ornament-2 {
            position: absolute;
            bottom: -150px;
            left: -150px;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(197, 160, 40, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: floatReverse 25s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(50px, 50px) rotate(120deg); }
            66% { transform: translate(-20px, 30px) rotate(240deg); }
        }

        @keyframes floatReverse {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(-50px, -30px) rotate(-120deg); }
            66% { transform: translate(30px, -50px) rotate(-240deg); }
        }

        /* Main Container */
        .registration-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 900px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 48px;
            box-shadow: 
                0 50px 100px -20px rgba(0, 0, 0, 0.5),
                0 30px 60px -30px rgba(139, 0, 32, 0.5),
                inset 0 1px 1px rgba(255, 255, 255, 0.3);
            overflow: hidden;
            border: 1px solid rgba(197, 160, 40, 0.2);
        }

        /* Header Section */
        .register-header {
            background: linear-gradient(135deg, var(--maroon-deep), var(--maroon-burgundy));
            padding: 50px 40px 30px;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .register-header::before {
            content: '📚';
            position: absolute;
            top: -30px;
            right: -30px;
            font-size: 200px;
            opacity: 0.1;
            transform: rotate(15deg);
            color: var(--gold-light);
        }

        .register-header h1 {
            font-size: 3rem;
            font-weight: 800;
            color: white;
            margin-bottom: 10px;
            position: relative;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .register-header h1 span {
            color: var(--gold-light);
            font-weight: 800;
        }

        .register-header p {
            color: var(--maroon-light);
            font-size: 1.1rem;
            font-weight: 300;
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            line-height: 1.6;
        }

        .header-badge {
            display: inline-block;
            background: rgba(197, 160, 40, 0.2);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(197, 160, 40, 0.3);
            color: var(--gold-light);
            padding: 10px 30px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            margin-top: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.5px;
        }

        /* Progress Steps */
        .progress-steps {
            display: flex;
            justify-content: center;
            gap: 50px;
            padding: 30px 40px;
            background: white;
            border-bottom: 1px solid var(--maroon-light);
        }

        .step {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--gray-medium);
            position: relative;
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -30px;
            top: 50%;
            width: 20px;
            height: 2px;
            background: var(--maroon-dust);
            transform: translateY(-50%);
        }

        .step.active .step-number {
            background: linear-gradient(135deg, var(--maroon-burgundy), var(--maroon-crimson));
            color: white;
            border-color: var(--maroon-burgundy);
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(128, 0, 32, 0.3);
        }

        .step.active .step-text {
            color: var(--maroon-burgundy);
            font-weight: 700;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--gray-light);
            border: 2px solid var(--maroon-dust);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--gray-medium);
            transition: all 0.3s;
        }

        .step-text {
            font-weight: 600;
            font-size: 1rem;
        }

        /* Form Section */
        .form-section {
            padding: 40px;
            background: white;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--gray-dark);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .input-wrapper {
            position: relative;
        }

        input, select, textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid var(--maroon-light);
            border-radius: 16px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            background: var(--gray-light);
            transition: all 0.3s;
            color: var(--gray-dark);
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
        }

        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23800020' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }

        input:hover, select:hover, textarea:hover {
            border-color: var(--maroon-dust);
            background: white;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--maroon-burgundy);
            background: white;
            box-shadow: 0 10px 25px -10px var(--maroon-burgundy);
        }

        /* School Info Card */
        .school-info-card {
            background: linear-gradient(135deg, var(--maroon-light), white);
            border-radius: 20px;
            padding: 25px;
            margin: 0 40px 30px;
            border: 1px solid var(--gold-light);
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 10px 30px -10px rgba(128, 0, 32, 0.1);
        }

        .school-info-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--maroon-burgundy), var(--maroon-crimson));
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            box-shadow: 0 10px 20px -5px var(--maroon-burgundy);
        }

        .school-info-text h4 {
            color: var(--maroon-deep);
            margin-bottom: 5px;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .school-info-text p {
            color: var(--gray-medium);
            font-size: 0.95rem;
        }

        /* Other School Input */
        .other-school-input {
            display: none;
            margin-top: 15px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Option Groups Styling */
        optgroup {
            font-weight: 700;
            color: var(--maroon-burgundy);
            background: var(--maroon-light);
            padding: 10px;
            font-size: 1rem;
        }

        option {
            padding: 12px;
            font-weight: normal;
            color: var(--gray-dark);
            background: white;
        }

        option:hover {
            background: var(--maroon-light);
        }

        /* Register Button */
        .register-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--maroon-burgundy), var(--maroon-crimson));
            color: white;
            border: none;
            border-radius: 20px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s;
            margin-top: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 35px -10px var(--maroon-burgundy);
        }

        .register-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .register-btn:hover::before {
            width: 400px;
            height: 400px;
        }

        .register-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 45px -10px var(--maroon-burgundy);
        }

        .register-btn.loading {
            color: transparent;
            pointer-events: none;
        }

        .register-btn .loader {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 30px;
            height: 30px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .register-btn.loading .loader {
            display: block;
        }

        @keyframes spin {
            to { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Login Link */
        .login-link-section {
            text-align: center;
            padding: 25px 40px 35px;
            background: linear-gradient(to bottom, white, var(--gray-light));
            border-top: 1px solid var(--maroon-light);
        }

        .login-link {
            color: var(--gray-medium);
            font-size: 1rem;
        }

        .login-link a {
            color: var(--maroon-burgundy);
            text-decoration: none;
            font-weight: 700;
            margin-left: 8px;
            position: relative;
            transition: all 0.3s;
            padding-bottom: 2px;
        }

        .login-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, var(--gold-primary), var(--maroon-burgundy));
            transition: width 0.3s;
        }

        .login-link a:hover::after {
            width: 100%;
        }

        .login-link a:hover {
            color: var(--gold-dark);
        }

        /* Messages */
        .message {
            padding: 18px 25px;
            border-radius: 16px;
            margin: 20px 40px 0;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 500;
            animation: slideIn 0.3s ease;
            box-shadow: 0 10px 25px -10px rgba(0, 0, 0, 0.1);
        }

        .message.error {
            background: #FFE5E5;
            color: var(--maroon-burgundy);
            border: 1px solid #FFB3B3;
        }

        .message.success {
            background: #E8F5E9;
            color: #2E7D32;
            border: 1px solid #A5D6A7;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-group.full-width {
                grid-column: span 1;
            }
            
            .register-header h1 {
                font-size: 2.2rem;
            }
            
            .progress-steps {
                flex-direction: column;
                gap: 20px;
                align-items: flex-start;
                padding: 30px;
            }
            
            .step:not(:last-child)::after {
                display: none;
            }
            
            .registration-container {
                border-radius: 32px;
            }
            
            .school-info-card {
                flex-direction: column;
                text-align: center;
                margin: 20px;
            }
        }

        /* Password strength indicator */
        .strength-indicator {
            position: absolute;
            right: 15px;
            bottom: -22px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="bg-ornament">
        <div class="ornament-1"></div>
        <div class="ornament-2"></div>
    </div>

    <div class="registration-container">
        <!-- Header -->
        <div class="register-header">
            <h1>Guimba <span>Marketing</span></h1>
            <p>Join the premier educational marketing system in Guimba, Nueva Ecija. Track your interests and discover opportunities.</p>
            <div class="header-badge">
                Partnered with 15+ Local Schools
            </div>
        </div>

        <!-- Progress Steps -->
        <div class="progress-steps">
            <div class="step active">
                <div class="step-number">1</div>
                <div class="step-text">Registration</div>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-text">Verification</div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-text">Dashboard</div>
            </div>
        </div>

        <!-- Messages -->
        <?php if (isset($error)): ?>
            <div class="message error">
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="message success">
                <span><?php echo $success; ?></span>
            </div>
        <?php endif; ?>

        <!-- School Info Card -->
        <div class="school-info-card">
            <div class="school-info-icon">🏫</div>
            <div class="school-info-text">
                <h4>Schools in Guimba, Nueva Ecija</h4>
                <p>Select from <?php echo count($colleges) + count($highschools); ?> local educational institutions</p>
            </div>
        </div>

        <!-- Registration Form -->
        <form method="POST" action="" id="registrationForm">
            <div class="form-section">
                <div class="form-grid">
                    <!-- Full Name -->
                    <div class="form-group full-width">
                        <label>Full Name</label>
                        <div class="input-wrapper">
                            <input type="text" name="fullname" placeholder="Enter your complete name" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="form-group full-width">
                        <label>Email Address</label>
                        <div class="input-wrapper">
                            <input type="email" name="email" placeholder="your.name@email.com" required>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group full-width">
                        <label>Password</label>
                        <div class="input-wrapper">
                            <input type="password" name="password" id="password" placeholder="Create a strong password (min. 6 characters)" required>
                        </div>
                    </div>

                    <!-- Course Type -->
                    <div class="form-group">
                        <label>Course Type</label>
                        <div class="input-wrapper">
                            <select name="course_type" id="courseType" required>
                                <option value="">-- Select Course Type --</option>
                                <option value="college">College</option>
                                <option value="highschool">Senior High School</option>
                            </select>
                        </div>
                    </div>

                    <!-- Course/Strand -->
                    <div class="form-group">
                        <label>Course/Strand</label>
                        <div class="input-wrapper">
                            <select name="course" id="courseSelect" required>
                                <option value="">-- Select Type First --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Previous School -->
                    <div class="form-group full-width">
                        <label>Previous School</label>
                        <div class="input-wrapper">
                            <select name="previous_school_id" id="previousSchool" required>
                                <option value="">-- Select your previous school --</option>
                                <optgroup label="COLLEGES IN GUIMBA">
                                    <?php foreach ($colleges as $school): ?>
                                        <option value="<?php echo $school['id']; ?>">
                                            <?php echo htmlspecialchars($school['school_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="HIGH SCHOOLS IN GUIMBA">
                                    <?php foreach ($highschools as $school): ?>
                                        <option value="<?php echo $school['id']; ?>">
                                            <?php echo htmlspecialchars($school['school_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <option value="other">Other school not listed</option>
                            </select>
                        </div>
                        
                        <div class="other-school-input" id="otherSchoolDiv">
                            <div class="input-wrapper">
                                <input type="text" name="previous_school_other" id="otherSchool" 
                                       placeholder="Please specify your school name">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Register Button -->
                <button type="submit" class="register-btn" id="registerBtn">
                    Create Account
                    <span class="loader"></span>
                </button>
            </div>
        </form>

        <!-- Login Link -->
        <div class="login-link-section">
            <div class="login-link">
                Already have an account? <a href="login.php">Sign in here</a>
            </div>
        </div>
    </div>

    <script>
        // Course Type Change Handler - UPDATED WITH SPECIFIED COURSES ONLY
        const courseType = document.getElementById('courseType');
        const courseSelect = document.getElementById('courseSelect');
        const previousSchool = document.getElementById('previousSchool');
        const otherSchoolDiv = document.getElementById('otherSchoolDiv');
        const otherSchool = document.getElementById('otherSchool');
        const registerBtn = document.getElementById('registerBtn');
        const form = document.getElementById('registrationForm');
        
        // College Courses - SPECIFIED ONLY: CRIM, IT, EDUC, OAD, HM
        const collegeCourses = [
            { value: 'CRIM', text: 'Criminology' },
            { value: 'IT', text: 'Information Technology' },
            { value: 'EDUC', text: 'Education' },
            { value: 'OAD', text: 'Office Administration' },
            { value: 'HM', text: 'Hospitality Management' }
        ];

        // Senior High School Strands - SPECIFIED ONLY: TECHVOC, STEM, HUMSS
        const highschoolStrands = [
            { value: 'TECHVOC', text: 'TECHVOC - Technical-Vocational Livelihood' },
            { value: 'STEM', text: 'STEM - Science, Technology, Engineering, Mathematics' },
            { value: 'HUMSS', text: 'HUMSS - Humanities and Social Sciences' }
        ];
        
        courseType.addEventListener('change', function() {
            const type = this.value;
            
            // Clear current options
            courseSelect.innerHTML = '';
            
            if (type === 'college') {
                // Add default option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = '-- Select College Course --';
                courseSelect.appendChild(defaultOption);
                
                // Add college courses (CRIM, IT, EDUC, OAD, HM only)
                collegeCourses.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.value;
                    option.textContent = course.text;
                    courseSelect.appendChild(option);
                });
                
            } else if (type === 'highschool') {
                // Add default option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = '-- Select Senior High Strand --';
                courseSelect.appendChild(defaultOption);
                
                // Add high school strands (TECHVOC, STEM, HUMSS only)
                highschoolStrands.forEach(strand => {
                    const option = document.createElement('option');
                    option.value = strand.value;
                    option.textContent = strand.text;
                    courseSelect.appendChild(option);
                });
            } else {
                // No selection
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = '-- Select Type First --';
                courseSelect.appendChild(defaultOption);
            }
            
            // Trigger animation
            this.style.transform = 'scale(1.02)';
            setTimeout(() => this.style.transform = 'scale(1)', 200);
            
            // Log for debugging
            console.log('Course type selected:', type);
            console.log('Options added:', courseSelect.options.length);
        });
        
        previousSchool.addEventListener('change', function() {
            if (this.value === 'other') {
                otherSchoolDiv.style.display = 'block';
                otherSchool.required = true;
                otherSchool.focus();
            } else {
                otherSchoolDiv.style.display = 'none';
                otherSchool.required = false;
                otherSchool.value = '';
            }
        });

        // Form submission animation
        form.addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            
            // Validate password length
            if (password.length < 6) {
                e.preventDefault();
                showMessage('Password must be at least 6 characters long', 'error');
                return;
            }
            
            // Validate course selection
            const courseValue = courseSelect.value;
            if (!courseValue) {
                e.preventDefault();
                showMessage('Please select a course or strand', 'error');
                return;
            }
            
            // Show loading state
            registerBtn.classList.add('loading');
        });

        // Show floating message
        function showMessage(text, type) {
            // Remove any existing messages
            const existingMessages = document.querySelectorAll('.message');
            existingMessages.forEach(msg => msg.remove());
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            messageDiv.innerHTML = `<span>${text}</span>`;
            
            const schoolInfo = document.querySelector('.school-info-card');
            schoolInfo.parentNode.insertBefore(messageDiv, schoolInfo.nextSibling);
            
            setTimeout(() => {
                messageDiv.style.opacity = '0';
                setTimeout(() => messageDiv.remove(), 500);
            }, 5000);
        }

        // Input animations
        const inputs = document.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Live password strength indicator
        const passwordInput = document.getElementById('password');
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const strength = checkPasswordStrength(this.value);
                this.style.borderColor = strength.color;
                
                // Add strength indicator text
                let indicator = this.parentElement.querySelector('.strength-indicator');
                if (!indicator) {
                    indicator = document.createElement('small');
                    indicator.className = 'strength-indicator';
                    this.parentElement.appendChild(indicator);
                }
                indicator.textContent = strength.message;
                indicator.style.color = strength.color;
            });
        }

        function checkPasswordStrength(password) {
            if (password.length < 6) {
                return { color: 'var(--maroon-burgundy)', message: 'Too short' };
            }
            if (password.length >= 8 && /[A-Z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) {
                return { color: '#2E7D32', message: 'Strong password' };
            }
            if (password.length >= 6 && /[A-Z]/.test(password) && /[0-9]/.test(password)) {
                return { color: '#F4A261', message: 'Medium password' };
            }
            return { color: '#F4A261', message: 'Add numbers & uppercase for stronger password' };
        }

        // Debug function to test if select is working
        console.log('Registration page loaded');
        console.log('College courses loaded:', collegeCourses.length);
        console.log('High school strands loaded:', highschoolStrands.length);
    </script>
</body>
</html>