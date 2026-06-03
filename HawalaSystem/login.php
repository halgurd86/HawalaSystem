<?php
session_start();
include 'config/db.php';

$error = "";

if(isset($_POST['login']))
{
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // پاراستنی کۆدەکە لە SQL Injection
    $stmt = mysqli_prepare($conn, "SELECT * FROM Admin WHERE UserName = ? AND Password = ?");
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0)
    {
        $_SESSION['user'] = $username;
        header("Location: dashboard.php");
        exit();
    }
    else
    {
        $error = "ناوی بەکارهێنەر یان پاسوۆردەکە ڕاست نییە!";
    }
}
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>چوونەژوورەوە - نوسینگەی محمد سەنگەسەری</title>
    
    <!-- Bootstrap 5.3 (RTL) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        @font-face {
            font-family: 'NRT';
            src: url('fonts/NRT-Regular.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            /* باکگراوندی مۆری کاڵ و زۆر شیک */
            background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
            min-height: 100vh;
            font-family: 'NRT', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 15px;
        }

        .login-card {
            background: #ffffff;
            border: none;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(124, 58, 237, 0.06);
            padding: 40px 35px;
        }

        .brand-logo {
            width: 65px;
            height: 65px;
            /* لۆگۆی مۆر */
            background: linear-gradient(135deg, #7c3aed, #a78bfa);
            color: white;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 20px auto;
            box-shadow: 0 8px 16px rgba(124, 58, 237, 0.2);
        }

        .kurdish-title {
            color: #6d28d9; /* ڕەنگی مۆری دەقەکە */
            font-size: 18px;
            font-weight: bold;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #4c1d95;
            margin-bottom: 8px;
        }

        .input-group-text {
            background-color: #fbfbfe;
            border-color: #ddd6fe;
            color: #a78bfa;
            border-radius: 0 12px 12px 0 !important;
        }

        .form-control {
            border-color: #ddd6fe;
            border-radius: 12px 0 0 12px !important;
            padding: 12px 15px;
            font-size: 15px;
            background-color: #fbfbfe;
            transition: all 0.2s ease;
            font-family: 'NRT', sans-serif;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: #7c3aed;
            box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.15);
        }

        /* دوگمەی مۆری نایاب */
        .btn-login {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.25);
            transition: all 0.3s ease;
            font-family: 'NRT', sans-serif;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(124, 58, 237, 0.35);
            background: linear-gradient(135deg, #6d28d9, #5b21b6);
        }

        .footer-text {
            color: #7c3aed;
            opacity: 0.8;
            font-size: 13px;
            margin-top: 25px;
            font-weight: 500;
        }
    </style>
</head>

<body>

<div class="login-container">

    <div class="card login-card text-center">
        
        <div class="brand-logo">
            <i class="bi bi-shield-lock"></i>
        </div>
        
        <h3 class="fw-bold mb-2 text-dark" style="font-family: 'NRT', sans-serif;">چوونەژوورەوە</h3>
        <p class="kurdish-title mb-4">- نوسینگەی محمد سەنگەسەری -</p>

        <?php if(!empty($error)) { ?>
            <div class="alert alert-danger d-flex align-items-center justify-content-center gap-2 border-0 rounded-3 mb-4 small py-2" style="background-color: #fdf2f8; color: #9d174d;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span><?php echo $error; ?></span>
            </div>
        <?php } ?>

        <form method="post" class="text-start" autocomplete="off">

            <div class="mb-3">
                <label class="form-label">ناوی بەکارهێنەر</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" id="username" name="username" class="form-control" placeholder="ناوی بەکارهێنەر بنووسە" required autocomplete="off">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">پاسوۆرد</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="new-password">
                </div>
            </div>

            <button type="submit" name="login" class="btn btn-login w-100 d-flex align-items-center justify-content-center gap-2">
                <span>بچۆ ژوورەوە</span>
                <i class="bi bi-box-arrow-in-right fs-5"></i>
            </button>

        </form>

    </div>

    <div class="text-center footer-text">
        نوسینگەی محمد سەنگەسەری © <?php echo date('Y'); ?>
    </div>

</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            document.getElementById('username').value = '';
            document.getElementById('password').value = '';
        }, 50);
    });
</script>
</body>
</html>