<?php
session_start();

// سڕینەوەی هەموو زانیارییەکانی سێشن
$_SESSION = array();

// سڕینەوەی کوکی سێشن ئەگەر هەبێت
if (ini_get("session_use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// لەناوبردنی تەواوی سێشنەکە
session_destroy();
?>
<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>چوونەدەرەوە - سیستەمی حەواڵە</title>
    <!-- بەکارهێنانی بۆتستراپ و ئایکۆنەکان -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        @font-face {
            font-family: 'NRT';
            src: url('fonts/NRT-Regular.ttf') format('truetype');
        }
        body {
            background: #f1f5f9;
            font-family: 'NRT', 'Segoe UI', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }
        .logout-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            text-align: center;
            max-width: 400px;
            width: 90%;
            border: 1px solid #e2e8f0;
            transform: translateY(0);
            animation: fadeInUp 0.6s ease-out;
        }
        .icon-box {
            width: 80px;
            height: 80px;
            background: #fee2e2;
            color: #ef4444;
            font-size: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 20px auto;
            animation: pulse 1.5s infinite;
        }
        .spinner-custom {
            width: 24px;
            height: 24px;
            border: 3px solid #cbd5e1;
            border-top: 3px solid #4f46e5;
            border-radius: 50%;
            display: inline-block;
            animation: spin 0.8s linear infinite;
            vertical-align: middle;
            margin-left: 10px;
        }
        .redirect-text {
            font-size: 13px;
            color: #64748b;
            margin-top: 25px;
            background: #f8fafc;
            padding: 10px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 15px rgba(239, 68, 68, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<div class="logout-card">
    <div class="icon-box">
        <i class="bi bi-box-arrow-right"></i>
    </div>
    <h4 class="fw-bold text-dark mb-2">چوونەدەرەوە سەرکەوتوو بوو</h4>
    <p class="text-muted small px-3">سوپاس بۆ بەکارهێنانی سیستەمەکە. هەژمارەکەت بە سەلامەتی داخرایۆ.</p>
    
    <div class="redirect-text">
        <span class="spinner-custom"></span>
        <span>گواستنەوە بۆ لاپەڕەی چوونەژوورەوە...</span>
    </div>
</div>

<script>
    // دوای 2 چرکە خۆکارانە دەچێتە سەر لاپەڕەی لۆگین
    setTimeout(function(){
        window.location.href = "login.php";
    }, 2000);
</script>

</body>
</html>