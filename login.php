<?php
require_once 'helper/connection.php';
session_start();

$error_message = "";
if (isset($_POST['submit'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $sql = "SELECT * FROM login WHERE username='$username' and password='$password' LIMIT 1";
  $result = mysqli_query($connection, $sql);

  $row = mysqli_fetch_assoc($result);
  if ($row) {
    $_SESSION['login'] = $row;
    header('Location: index.php');
    exit();
  } else {
    $error_message = "Username atau password salah!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login &mdash; Dapodik</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="assets/modules/bootstrap-social/bootstrap-social.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/components.css">
  
  <style>
    :root {
      --primary-color: #4361ee;
      --primary-light: #6a7ef0;
      --secondary-color: #3a0ca3;
      --accent-color: #4cc9f0;
      --light-color: #f8f9fa;
      --dark-color: #212529;
      --success-color: #4ade80;
      --warning-color: #f59e0b;
    }
    
    * {
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
      padding: 20px;
      position: relative;
      overflow-x: hidden;
    }
    
    /* Background Animation */
    .bg-animation {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      overflow: hidden;
    }
    
    .bg-bubble {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      animation: float 15s infinite linear;
    }
    
    @keyframes float {
      0% {
        transform: translateY(0) rotate(0deg);
        opacity: 1;
      }
      100% {
        transform: translateY(-1000px) rotate(720deg);
        opacity: 0;
      }
    }
    
    .login-container {
      max-width: 450px;
      width: 100%;
      margin: 0 auto;
      animation: fadeIn 0.8s ease-out;
    }
    
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .login-brand {
      text-align: center;
      margin-bottom: 2.5rem;
    }
    
    .logo-container {
      display: inline-block;
      position: relative;
      margin-bottom: 15px;
    }
    
    .logo-container::before {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
      z-index: -1;
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0% {
        transform: scale(1);
        opacity: 0.8;
      }
      50% {
        transform: scale(1.05);
        opacity: 1;
      }
      100% {
        transform: scale(1);
        opacity: 0.8;
      }
    }
    
    .login-brand img {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      padding: 12px;
      background: white;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      margin-bottom: 10px;
      transition: transform 0.3s;
    }
    
    .login-brand img:hover {
      transform: rotate(10deg);
    }
    
    .login-brand h1 {
      color: white;
      font-weight: 700;
      font-size: 2.2rem;
      margin: 0;
      text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }
    
    .login-brand p {
      color: rgba(255, 255, 255, 0.85);
      margin: 5px 0 0;
      font-size: 1rem;
    }
    
    .card {
      border: none;
      border-radius: 20px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.95);
      transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .card-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      text-align: center;
      padding: 1.8rem;
      border-bottom: none;
      position: relative;
      overflow: hidden;
    }
    
    .card-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
      transform: translateX(-100%);
      animation: shimmer 3s infinite;
    }
    
    @keyframes shimmer {
      100% {
        transform: translateX(100%);
      }
    }
    
    .card-header h4 {
      margin: 0;
      font-weight: 600;
      font-size: 1.5rem;
      position: relative;
      z-index: 1;
    }
    
    .card-body {
      padding: 2.5rem;
    }
    
    .form-group {
      margin-bottom: 1.8rem;
      position: relative;
    }
    
    .form-group label {
      font-weight: 500;
      margin-bottom: 8px;
      color: var(--dark-color);
      display: flex;
      align-items: center;
    }
    
    .form-group label i {
      margin-right: 8px;
      color: var(--primary-color);
    }
    
    .input-group {
      position: relative;
    }
    
    .input-group-prepend .input-group-text {
      background: white;
      border-right: none;
      border-top-left-radius: 10px;
      border-bottom-left-radius: 10px;
      color: var(--primary-color);
    }
    
    .form-control {
      border-radius: 10px;
      padding: 14px 15px;
      border: 2px solid #e1e5eb;
      transition: all 0.3s;
      font-size: 1rem;
    }
    
    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
    }
    
    .password-toggle {
      position: relative;
    }
    
    .password-toggle .toggle-password {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #6c757d;
      cursor: pointer;
      z-index: 5;
      transition: color 0.3s;
    }
    
    .password-toggle .toggle-password:hover {
      color: var(--primary-color);
    }
    
    .btn-primary {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
      border: none;
      border-radius: 10px;
      padding: 14px;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.3s;
      position: relative;
      overflow: hidden;
    }
    
    .btn-primary::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }
    
    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3);
    }
    
    .btn-primary:hover::before {
      left: 100%;
    }
    
    .btn-primary:active {
      transform: translateY(-1px);
    }
    
    .custom-checkbox .custom-control-input:checked~.custom-control-label::before {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }
    
    .custom-control-label {
      cursor: pointer;
    }
    
    .alert {
      border-radius: 10px;
      margin-bottom: 1.5rem;
      border: none;
      padding: 12px 15px;
      display: flex;
      align-items: center;
      animation: shake 0.5s;
    }
    
    @keyframes shake {
      0%, 100% {transform: translateX(0);}
      10%, 30%, 50%, 70%, 90% {transform: translateX(-5px);}
      20%, 40%, 60%, 80% {transform: translateX(5px);}
    }
    
    .alert i {
      margin-right: 10px;
      font-size: 1.2rem;
    }
    
    .login-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1.5rem;
      flex-wrap: wrap;
    }
    
    .forgot-password {
      color: var(--primary-color);
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s;
      position: relative;
    }
    
    .forgot-password::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -2px;
      left: 0;
      background: var(--primary-color);
      transition: width 0.3s;
    }
    
    .forgot-password:hover {
      color: var(--secondary-color);
      text-decoration: none;
    }
    
    .forgot-password:hover::after {
      width: 100%;
    }
    
    .simple-footer {
      text-align: center;
      color: rgba(255, 255, 255, 0.8);
      margin-top: 2.5rem;
      font-size: 0.9rem;
    }
    
    .feature-list {
      display: flex;
      justify-content: space-around;
      margin-top: 1.5rem;
      flex-wrap: wrap;
    }
    
    .feature-item {
      text-align: center;
      color: white;
      margin: 10px;
      flex: 1;
      min-width: 100px;
    }
    
    .feature-item i {
      font-size: 1.5rem;
      margin-bottom: 5px;
      background: rgba(255, 255, 255, 0.2);
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 10px;
    }
    
    .feature-item span {
      display: block;
      font-size: 0.85rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 575.98px) {
      .card-body {
        padding: 1.8rem;
      }
      
      .login-options {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .forgot-password {
        margin-top: 10px;
      }
      
      .login-brand h1 {
        font-size: 1.8rem;
      }
      
      .login-brand img {
        width: 80px;
        height: 80px;
      }
    }
    
    @media (max-width: 400px) {
      .card-body {
        padding: 1.5rem;
      }
    }
  </style>
</head>

<body>
  <!-- Background Animation -->
  <div class="bg-animation" id="bgAnimation"></div>
  
  <div class="container">
    <div class="login-container">
      <div class="login-brand">
        <div class="logo-container">
          <img src="assets/img/logo.png" alt="Dapodik">
        </div>
        <h1>BTIKP</h1>
        <p>Sistem Informasi Pendidikan Terpadu</p>
      </div>

      <div class="card">
        <div class="card-header" style="justify-content: center;">
          <h4><i class="fas fa-sign-in-alt mr-2"></i>Login ke Akun Anda</h4>
        </div>

        <div class="card-body">
          <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger">
              <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
          <?php endif; ?>
          
          <form method="POST" action="" class="needs-validation" novalidate="">
            <div class="form-group">
              <label for="username"><i class=""></i> Username</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fas fa-user"></i></span>
                </div>
                <input id="username" type="text" class="form-control" name="username" tabindex="1" required autofocus placeholder="Masukkan username">
              </div>
              <div class="invalid-feedback">
                Mohon isi username Anda
              </div>
            </div>

            <div class="form-group">
              <label for="password"><i class=""></i> Password</label>
              <div class="input-group password-toggle">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
                <input id="password" type="password" class="form-control" name="password" tabindex="2" required placeholder="Masukkan password">
                <button type="button" class="toggle-password" aria-label="Toggle password visibility">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
              <div class="invalid-feedback">
                Mohon isi kata sandi Anda
              </div>
            </div>

            <div class="form-group">
              <button name="submit" type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                <i class="fas fa-sign-in-alt mr-2"></i>Login
              </button>
            </div>
            
            <div class="login-options">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" name="remember" class="custom-control-input" id="remember-me-2">
                <label class="custom-control-label" for="remember-me-2">Ingat Saya</label>
              </div>
              <a href="#" class="forgot-password">Lupa Password?</a>
            </div>
          </form>
        </div>
      </div>
      
      <div class="simple-footer">
        Copyright &copy; Dapodik 2025. All rights reserved.
      </div>
    </div>
  </div>

  <!-- General JS Scripts -->
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
  <script src="assets/js/stisla.js"></script>

  <!-- Template JS File -->
  <script src="assets/js/scripts.js"></script>
  <script src="assets/js/custom.js"></script>
  
  <script>
    // Background animation
    document.addEventListener('DOMContentLoaded', function() {
      const bgAnimation = document.getElementById('bgAnimation');
      const bubblesCount = 15;
      
      for (let i = 0; i < bubblesCount; i++) {
        const bubble = document.createElement('div');
        bubble.classList.add('bg-bubble');
        
        const size = Math.random() * 60 + 20;
        bubble.style.width = `${size}px`;
        bubble.style.height = `${size}px`;
        bubble.style.left = `${Math.random() * 100}%`;
        bubble.style.animationDelay = `${Math.random() * 15}s`;
        bubble.style.animationDuration = `${Math.random() * 20 + 15}s`;
        
        bgAnimation.appendChild(bubble);
      }
      
      // Toggle password visibility
      $('.toggle-password').click(function() {
        const passwordInput = $(this).siblings('input');
        const icon = $(this).find('i');
        
        if (passwordInput.attr('type') === 'password') {
          passwordInput.attr('type', 'text');
          icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
          passwordInput.attr('type', 'password');
          icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
      });
      
      // Form validation
      $('form').on('submit', function(e) {
        const form = $(this);
        if (form[0].checkValidity() === false) {
          e.preventDefault();
          e.stopPropagation();
        }
        form.addClass('was-validated');
      });
      
      // Add focus effects to form inputs
      $('.form-control').focus(function() {
        $(this).parent().addClass('focused');
      }).blur(function() {
        if ($(this).val() === '') {
          $(this).parent().removeClass('focused');
        }
      });
    });
  </script>
</body>

</html>