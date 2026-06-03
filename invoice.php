<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// دڵنیابوونەوە لە ناردنی ئایدی حەواڵەکە
if(!isset($_GET['id'])){
    die("تکایە ناسنامەی حەواڵە (ID) دیاری بکە.");
}

$id = intval($_GET['id']);

// ۱. هێنانی زانیارییە گشتییەکانی حەواڵە
$sql = "SELECT w.*, s.SharName, n.Naw AS NusengaName, n.Code AS NusengaCode 
        FROM Wargrtn w 
        LEFT JOIN Shar s ON w.SharID=s.SharID 
        LEFT JOIN Nusenga n ON w.NusengaID=n.NusengaID 
        WHERE w.WargrtnID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$invoice = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if(!$invoice){
    die("ئەم حەواڵەیە بوونی نییە!");
}

// ۲. هێنانی تێپەڕی دراوەکان (بڕ و کرێ)
$sql_details = "SELECT wd.*, c.CurrencyName 
                FROM WargrtnDetails wd
                LEFT JOIN Currency c ON wd.CurrencyID = c.CurrencyID 
                WHERE wd.WargrtnID = ?";
$stmt_details = mysqli_prepare($conn, $sql_details);
mysqli_stmt_bind_param($stmt_details, "i", $id);
mysqli_stmt_execute($stmt_details);
$details = mysqli_stmt_get_result($stmt_details);
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاکتۆری حەواڵە #<?= $invoice['WargrtnID'] ?></title>
    
    <!-- Bootstrap RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @font-face {
            font-family: 'NRT';
            src: url('fonts/NRT-Regular.ttf') format('truetype');
        }

        body {
            background-color: #f1f5f9;
            font-family: 'NRT', 'Segoe UI', Tahoma, sans-serif;
            color: #1e293b;
            font-size: 14px;
            padding: 40px 0;
        }

        .invoice-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            padding: 40px;
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
        }

        /* زانیاری سەرەوەی فاکتۆر */
        .invoice-header {
            border-bottom: 2px dashed #cbd5e1;
            padding-bottom: 25px;
            margin-bottom: 30px;
        }

        .company-logo {
            font-size: 26px;
            font-weight: 800;
            color: #6d28d9;
            letter-spacing: 0.5px;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
        }

        /* بەشی زانیاری باڵندەیی */
        .info-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 15px 20px;
            border-right: 4px solid #7c3aed;
            height: 100%;
        }

        .info-box h6 {
            color: #6d28d9;
            font-weight: 700;
            margin-bottom: 12px;
            font-size: 15px;
        }

        .info-item {
            margin-bottom: 6px;
            font-size: 13.5px;
        }
        .info-item strong {
            color: #475569;
        }

        /* مۆری دۆخی حەواڵە */
        .status-badge {
            font-size: 16px;
            font-weight: bold;
            padding: 6px 16px;
            border-radius: 50px;
            display: inline-block;
        }

        /* خشتەی حسابات */
        .table-invoice {
            margin-top: 30px;
        }
        .table-invoice thead {
            background-color: #7c3aed;
            color: white;
        }
        .table-invoice th {
            font-weight: 700;
            padding: 12px;
            font-size: 14px;
        }
        .table-invoice td {
            padding: 12px;
            vertical-align: middle;
            font-size: 14px;
        }

        /* بەشی واژۆ */
        .signature-section {
            margin-top: 60px;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }
        .signature-box {
            border-bottom: 1px dashed #94a3b8;
            width: 150px;
            margin: 10px auto;
            height: 40px;
        }

        /* دوگمەکان لە دەرەوەی پرینت */
        .no-print-zone {
            margin-bottom: 20px;
        }

        /* ڕێکخستنی تایبەت بۆ کاتی پرینت کردن (گرنگ) */
        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
            }
            .invoice-card {
                box-shadow: none;
                padding: 0;
                border: none;
            }
            .no-print-zone {
                display: none !important;
            }
            .info-box {
                background: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .table-invoice thead {
                background-color: #7c3aed !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="container">
    
    <!-- دوگمەکانی سەرەوە (لە کاتی پرینتدا دەشاردرێنەوە) -->
    <div class="row no-print-zone">
        <div class="col-12 d-flex justify-content-between">
            <a href="wargrtn.php" class="btn btn-secondary rounded-3"><i class="bi bi-arrow-right"></i> گەڕانەوە بۆ لیستی گشتی</a>
            <button onclick="window.print();" class="btn btn-primary rounded-3 px-4"><i class="bi bi-printer me-2"></i> پرینتکردنی فاکتۆر</button>
        </div>
    </div>

    <!-- فاکتۆری سەرەکی -->
    <div class="invoice-card">
        
        <!-- سەرپەڕی فاکتۆر -->
        <div class="invoice-header">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <div class="company-logo"><i class="bi bi-bank2 text-primary me-2"></i>نوسینگەی محمد سەنگەسەری</div>
                    <p class="text-muted small mb-0">بۆ ئاڵوگۆڕی دراو و حەواڵە داراییەکان</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <h1 class="invoice-title mb-1">فاکتۆری وەرگرتن</h1>
                    <div class="text-muted">ژمارەی فاکتۆر: <span class="fw-bold text-dark">#<?= $invoice['WargrtnID'] ?></span></div>
                </div>
            </div>
        </div>

        <!-- بەشی زانیاری لایەنەکان و بەروارەکان -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="info-box">
                    <h6><i class="bi bi-person-badge-fill me-1"></i> زانیاری وەرگر</h6>
                    <div class="info-item"><strong>ناوی وەرگر:</strong> <?= htmlspecialchars($invoice['NawyWergr']) ?></div>
                    <div class="info-item"><strong>ژمارەی مۆبایل:</strong> <span dir="ltr"><?= htmlspecialchars($invoice['PhoneNo'] ?: '---') ?></span></div>
                    <div class="info-item"><strong>شار/ناوچە:</strong> <?= htmlspecialchars($invoice['SharName'] ?: '---') ?></div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="info-box" style="border-right-color: #10b981;">
                    <h6><i class="bi bi-send-fill me-1"></i> زانیاری نێرەر و سەرچاوە</h6>
                    <div class="info-item"><strong>ناوی نێرەر:</strong> <?= htmlspecialchars($invoice['NawyNerar'] ?: '---') ?></div>
                    <div class="info-item"><strong>نوسینگەی نێرەر:</strong> <?= htmlspecialchars($invoice['NusengaName'] ?: '---') ?></div>
                    <div class="info-item"><strong>کۆدی نوسینگە:</strong> <span class="text-primary fw-bold"><?= htmlspecialchars($invoice['NusengaCode'] ?: '---') ?></span></div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box" style="border-right-color: #f59e0b;">
                    <h6><i class="bi bi-calendar3 me-1"></i> بەروار و کات</h6>
                    <div class="info-item"><strong>بەرواری تۆمار:</strong> <?= htmlspecialchars($invoice['BarwarA']) ?> | <?= htmlspecialchars($invoice['TimeA']) ?></div>
                    <div class="info-item"><strong>بەرواری نوێکردنەوە:</strong> <?= htmlspecialchars($invoice['BarwarB']) ?> | <?= htmlspecialchars($invoice['TimeB']) ?></div>
                    <div class="info-item mt-2">
                        <strong>دۆخی حەواڵە:</strong>
                        <?php if($invoice['IsReceived'] == 1) { ?>
                            <span class="badge bg-success-subtle text-success p-1 px-2 rounded-2 small"><i class="bi bi-check-circle"></i> وەرگیراوە</span>
                        <?php } else { ?>
                            <span class="badge bg-danger-subtle text-danger p-1 px-2 rounded-2 small"><i class="bi bi-x-circle"></i> وەرنەگیراوە</span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- تێبینی ئەگەر هەبێت -->
        <?php if(!empty($invoice['Note'])) { ?>
            <div class="alert alert-secondary mt-4 mb-0 rounded-3" role="alert" style="background-color: #f8fafc; border-color: #e2e8f0;">
                <h6 class="fw-bold text-dark mb-1" style="font-size: 13px;"><i class="bi bi-sticky me-1 text-warning"></i> تێبینی حەواڵە:</h6>
                <p class="mb-0 text-muted small"><?= htmlspecialchars($invoice['Note']) ?></p>
            </div>
        <?php } ?>

        <!-- خشتەی دراوەکان و بڕی پارە -->
        <div class="table-responsive table-invoice">
            <table class="table table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th style="width: 10%;">#</th>
                        <th style="width: 40%;">جۆری دراو</th>
                        <th style="width: 25%;">بڕی حەواڵە</th>
                        <th style="width: 25%;">کرێ / کۆمسیۆن</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $count = 1;
                    if(mysqli_num_rows($details) > 0) { 
                        while($d = mysqli_fetch_assoc($details)){
                    ?>
                        <tr>
                            <td class="fw-bold text-muted"><?= $count++ ?></td>
                            <td class="fw-bold text-primary fs-6"><?= htmlspecialchars($d['CurrencyName']) ?></td>
                            <td class="fw-bold text-success fs-6"><?= number_format($d['Amount'], 2) ?></td>
                            <td class="text-secondary"><?= number_format($d['Commission'], 2) ?></td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="4" class="text-muted py-3">هیچ زانیارییەکی پارە و دراو بۆ ئەم حەواڵەیە تۆمار نەکراوە.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- بەشی خوارەوە و واژۆکان -->
        <div class="signature-section">
            <div class="row text-center">
                <div class="col-6 col-md-4">
                    <p class="text-muted small mb-1">ئامادەکار یان ژمێریار</p>
                    <div class="signature-box"></div>
                </div>
                <div class="col-6 col-md-4">
                    <p class="text-muted small mb-1">مۆری نوسینگە</p>
                    <div style="height: 50px;"></div>
                </div>
                <div class="col-12 col-md-4 mt-3 mt-md-0">
                    <p class="text-muted small mb-1">واژۆی وەرگر</p>
                    <div class="signature-box"></div>
                </div>
            </div>
        </div>

        <!-- پەرەی کۆتایی فاکتۆر -->
        <div class="text-center text-muted mt-5 pt-3" style="border-top: 1px solid #f1f5f9; font-size: 11px;">
            سیستەمی ئەلیکترۆنی نوسینگەی محمد سەنگەسەری - دیزاین کراوە بە بەرزترین کوالێتی
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>