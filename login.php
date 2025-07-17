<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - ระบบจัดการงานไอที</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Sarabun', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            margin: 20px;
        }
        
        .login-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        
        .login-header h2 {
            margin: 0;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        
        .login-header .fa {
            font-size: 60px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid rgba(52, 73, 94, 0.1);
            padding: 15px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            background: rgba(255, 255, 255, 1);
        }
        
        .input-group-addon {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
            color: white;
            border-radius: 10px 0 0 10px;
            padding: 15px;
        }
        
        .input-group .form-control {
            border-radius: 0 10px 10px 0;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
            border-radius: 10px;
            padding: 15px 30px;
            font-weight: 700;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            width: 100%;
            color: white;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.4);
            color: white;
        }
        
        .login-footer {
            text-align: center;
            padding: 20px 30px;
            background: rgba(52, 73, 94, 0.05);
            color: #7f8c8d;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 20px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 20px 0;
        }
        
        .checkbox {
            margin: 0;
        }
        
        .forgot-password {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
        }
        
        .forgot-password:hover {
            color: #2980b9;
            text-decoration: underline;
        }
        
        @media (max-width: 480px) {
            .login-container {
                margin: 10px;
            }
            
            .login-header,
            .login-body {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fa fa-desktop"></i>
            <h2>ระบบจัดการงานไอที</h2>
            <p>แผนกเทคโนโลยีสารสนเทศ</p>
        </div>
        
        <div class="login-body">
            <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-triangle"></i> ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง
            </div>
            <?php endif; ?>
            
            <form action="auth.php" method="POST" id="loginForm">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        <input type="text" class="form-control" name="username" placeholder="ชื่อผู้ใช้" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" name="password" placeholder="รหัสผ่าน" required>
                    </div>
                </div>
                
                <div class="remember-me">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="remember"> จดจำการเข้าสู่ระบบ
                        </label>
                    </div>
                    <a href="#" class="forgot-password">ลืมรหัสผ่าน?</a>
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="fa fa-sign-in"></i> เข้าสู่ระบบ
                </button>
            </form>
        </div>
        
        <div class="login-footer">
            <p><i class="fa fa-info-circle"></i> สำหรับเจ้าหน้าที่แผนกไอทีเท่านั้น</p>
            <small>© 2025 ระบบจัดการงานไอที</small>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                var $btn = $('.btn-login');
                var originalText = $btn.html();
                $btn.html('<i class="fa fa-spinner fa-spin"></i> กำลังเข้าสู่ระบบ...').prop('disabled', true);
                
                setTimeout(function() {
                    $btn.html(originalText).prop('disabled', false);
                }, 2000);
            });
        });
    </script>
</body>
</html>