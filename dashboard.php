<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user']))
{
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پانێڵی سەرەکی - نوسینگەی محمد سەنگەسەری</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @font-face {
            font-family: 'NRT';
            src: url('fonts/NRT-Regular.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
            min-height: 100vh;
            font-family: 'NRT', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #334155;
        }

        /* HEADER SECTION */
        .header-box {
            text-align: center;
            padding: 40px 20px 20px 20px;
        }

        .top-title {
            font-size: 32px;
            font-weight: bold;
            color: #4c1d95;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 16px;
            color: #7c3aed;
            opacity: 0.85;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .welcome-badge {
            background-color: #ffffff;
            padding: 8px 20px;
            border-radius: 30px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: bold;
            color: #6d28d9;
            box-shadow: 0 4px 10px rgba(124, 58, 237, 0.05);
            border: 1px solid #e9e3ff;
        }

        /* MODERN COLORFUL BUTTONS */
        .dashboard-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            min-height: 140px;
            border-radius: 22px;
            color: #ffffff !important;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.06);
            padding: 20px;
            border: none;
        }

        .dashboard-btn i {
            font-size: 38px;
            color: #ffffff;
            margin-bottom: 12px;
            transition: all 0.3s ease;
        }

        .dashboard-btn:hover {
            transform: translateY(-7px);
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.15);
        }

        .dashboard-btn:hover i {
            transform: scale(1.15);
        }

        /* کلاسەکانی ڕەنگی مۆدێرن و جۆراوجۆر */
        .btn-purple { background: linear-gradient(135deg, #7c3aed, #9333ea); }
        .btn-blue   { background: linear-gradient(135deg, #2563eb, #3b82f6); }
        .btn-green  { background: linear-gradient(135deg, #059669, #10b981); }
        .btn-orange { background: linear-gradient(135deg, #ea580c, #f97316); }
        .btn-red    { background: linear-gradient(135deg, #dc2626, #ef4444); }
        .btn-cyan   { background: linear-gradient(135deg, #0891b2, #06b6d4); }
        .btn-dark   { background: linear-gradient(135deg, #334155, #475569); }
        .btn-pink   { background: linear-gradient(135deg, #db2777, #ec4899); }
        .btn-teal   { background: linear-gradient(135deg, #0d9488, #14b8a6); } /* ڕەنگی نوێ بۆ شار */
        .btn-amber  { background: linear-gradient(135deg, #d97706, #f59e0b); } /* ڕەنگی نوێ بۆ دراو */

        /* LOGOUT BUTTON */
        .logout-container {
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .logout-btn {
            border-radius: 14px;
            padding: 12px 40px;
            font-weight: bold;
            font-size: 15px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: none;
            color: white;
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.2);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: 'NRT', sans-serif;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.3);
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="header-box">
        <div class="top-title">نوسینگەی محمد سەنگەسەری</div>
        <div class="subtitle">سیستەمی پێشکەوتووی بەڕێوەبردنی حەواڵە و گۆڕینەوەی دراو</div>
        
        <div class="welcome-badge">
            <i class="bi bi-person-circle fs-5"></i>
            <span>بەخێربێیتەوە بەڕێز: <?php echo htmlspecialchars($_SESSION['user']); ?></span>
        </div>
    </div>

    <div class="mt-4">
        <div class="row g-4 justify-content-center">

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="admin.php" class="dashboard-btn btn-purple">
                    <i class="bi bi-person-gear"></i>
                    <span>بەڕێوەبەر</span>
                </a>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="nusenga.php" class="dashboard-btn btn-blue">
                    <i class="bi bi-buildings"></i>
                    <span>نوسینگەکان</span>
                </a>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="shar.php" class="dashboard-btn btn-teal">
                    <i class="bi bi-geo-alt"></i>
                    <span>زیادکردنی شار</span>
                </a>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="currency.php" class="dashboard-btn btn-amber">
                    <i class="bi bi-coin"></i>
                    <span>جۆری دراوەکان</span>
                </a>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="wargrtn.php" class="dashboard-btn btn-green">
                    <i class="bi bi-cash-stack"></i>
                    <span>حەواڵە وەرگرتن</span>
                </a>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="hawalaroishtoo.php" class="dashboard-btn btn-red">
                    <i class="bi bi-send"></i>
                    <span>حەواڵەی ڕۆیشتوو</span>
                </a>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="paranardn.php" class="dashboard-btn btn-cyan">
                    <i class="bi bi-arrow-up-circle"></i>
                    <span>پارە ناردن</span>
                </a>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="parawargrtn.php" class="dashboard-btn btn-pink">
                    <i class="bi bi-arrow-down-circle"></i>
                    <span>پارە وەرگرتن</span>
                </a>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="dollar.php" class="dashboard-btn btn-orange">
                    <i class="bi bi-currency-dollar"></i>
                    <span>نرخی دۆلار</span>
                </a>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="expense.php" class="dashboard-btn btn-red">
                    <i class="bi bi-receipt"></i>
                    <span>خەرجی</span>
                </a>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="debtor.php" class="dashboard-btn btn-purple">
                    <i class="bi bi-person-vcard"></i>
                    <span>خاوەن قەرز</span>
                </a>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="loan_receive.php" class="dashboard-btn btn-blue">
                    <i class="bi bi-wallet2"></i>
                    <span>وەرگرتنی قەرز</span>
                </a>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="loan_pay.php" class="dashboard-btn btn-dark">
                    <i class="bi bi-credit-card"></i>
                    <span>دانەوەی قەرز</span>
                </a>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="exchange_in.php" class="dashboard-btn btn-cyan">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>ئاڵوگۆڕی هاتوو</span>
                </a>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="exchange_out.php" class="dashboard-btn btn-orange">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>ئاڵوگۆڕی ڕۆیشتوو</span>
                </a>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="archive.php" class="dashboard-btn btn-dark">
                    <i class="bi bi-archive"></i>
                    <span>ئەرشیف</span>
                </a>
            </div>

            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                <a href="reports/daily.php" class="dashboard-btn btn-blue">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>ڕاپۆرتەکان</span>
                </a>
            </div>

        </div>
    </div>

    <div class="text-center logout-container">
        <a href="logout.php" class="btn logout-btn">
            <i class="bi bi-box-arrow-right"></i>
            <span>دەرچوون لە سیستم</span>
        </a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>