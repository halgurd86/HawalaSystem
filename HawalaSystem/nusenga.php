<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if(!$conn){
    die("پەیوەندی لەگەڵ بنکەی داتاکان سەرکەوتوو نەبوو");
}

/* =====================
   INSERT / UPDATE (Prepared Statements)
===================== */
if(isset($_POST['save']))
{
    $id = intval($_POST['id'] ?? 0);
    $code = trim($_POST['code'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if($id == 0)
    {
        $stmt = mysqli_prepare($conn, "INSERT INTO Nusenga (Code, Naw, PhoneNo, Address) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $code, $name, $phone, $address);
        mysqli_stmt_execute($stmt);
    }
    else
    {
        $stmt = mysqli_prepare($conn, "UPDATE Nusenga SET Code=?, Naw=?, PhoneNo=?, Address=? WHERE NusengaID=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $code, $name, $phone, $address, $id);
        mysqli_stmt_execute($stmt);
    }

    header("Location: nusenga.php");
    exit();
}

/* =====================
   DELETE (Prepared Statement)
===================== */
if(isset($_GET['delete']))
{
    $id = intval($_GET['delete']);

    $stmt = mysqli_prepare($conn, "DELETE FROM Nusenga WHERE NusengaID=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    header("Location: nusenga.php");
    exit();
}

/* =====================
   EDIT LOAD (Prepared Statement)
===================== */
$edit = null;

if(isset($_GET['edit']))
{
    $id = intval($_GET['edit']);

    $stmt = mysqli_prepare($conn, "SELECT * FROM Nusenga WHERE NusengaID=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

/* =====================
   LIST ALL DATA
===================== */
$result = mysqli_query($conn, "SELECT * FROM Nusenga ORDER BY NusengaID DESC");
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی نوسینگەکان</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        @font-face {
            font-family: 'NRT';
            src: url('fonts/NRT-Regular.ttf') format('truetype');
        }

        body {
            background: #f4f6f9;
            font-family: 'NRT', 'Segoe UI', Tahoma, sans-serif;
            color: #334155;
            font-size: 13px;
            padding-bottom: 60px;
        }

        .custom-container {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .main-header {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .card {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
        }

        .section-title {
            font-size: 13.5px;
            font-weight: bold;
            color: #1e293b;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 12.5px;
            margin-bottom: 4px;
        }

        .form-control {
            border-color: #cbd5e1;
            border-radius: 6px;
            padding: 5px 10px;
            font-family: 'NRT', sans-serif;
            font-size: 13px;
            background-color: #fff;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        .btn-custom {
            border-radius: 6px;
            font-weight: bold;
            padding: 6px 16px;
            font-family: 'NRT', sans-serif;
            font-size: 13px;
            transition: all 0.2s;
        }

        .btn-save {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            box-shadow: 0 3px 10px rgba(16, 185, 129, 0.2);
        }
        .btn-save:hover { background: linear-gradient(135deg, #059669, #047857); color: white; }

        .table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            font-size: 13px;
        }

        .table thead {
            background: #0f172a;
            color: white;
        }

        /* 🔽 کەمکردنەوەی بەرزی ڕیکۆردەکان بە تەواوی */
        .table th, .table td {
            padding: 5px 8px !important;
            vertical-align: middle;
            height: 32px !important;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.12);
            color: white;
            text-decoration: none;
            padding: 5px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            transition: 0.2s;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .back-btn:hover { background: white; color: #0f172a; }

        /* بچووککردنەوەی دوگمەکانی ناو خشتەکە */
        .btn-action {
            padding: 2px 8px;
            font-size: 12px;
            border-radius: 5px;
            font-family: 'NRT', sans-serif;
        }

        /* دیزاینی ناو داتاتەیبڵ بە کۆمپەکتی */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 3px 8px !important;
            font-size: 12px !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #4f46e5 !important;
            color: white !important;
            border-color: #4f46e5 !important;
            border-radius: 5px;
        }
        .dataTables_filter input {
            border: 1px solid #cbd5e1 !important;
            border-radius: 5px !important;
            padding: 3px 8px !important;
            font-size: 12.5px;
            margin-right: 5px;
        }
        .dataTables_length select {
            padding: 2px 20px 2px 10px !important;
            font-size: 12.5px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="custom-container">

    <div class="main-header">
        <div>
            <h5 class="mb-0 fw-bold"><i class="bi bi-building me-2"></i> بەڕێوەبردنی زانیاری نوسینگەکان</h5>
        </div>
        <a href="dashboard.php" class="back-btn"><i class="bi bi-arrow-right-circle"></i> گەڕانەوە</a>
    </div>

    <div class="card shadow-sm">
        <div class="section-title">
            <i class="bi bi-plus-square-fill text-primary me-1"></i> 
            <?= $edit ? 'دەستکاریکردنی زانیاری نوسینگە' : 'تۆمارکردنی نوسینگەی نوێ' ?>
        </div>

        <form method="post" autocomplete="off">
            <input type="hidden" name="id" value="<?= $edit['NusengaID'] ?? '' ?>">

            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label text-danger">کۆدی نوسینگە</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-qr-code"></i></span>
                        <input type="text" name="code" class="form-control" placeholder="نموونە: MS-01" value="<?= htmlspecialchars($edit['Code'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">ناوی نوسینگە</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-person-badge"></i></span>
                        <input type="text" name="name" class="form-control" placeholder="ناوی تەواوی نوسینگە" value="<?= htmlspecialchars($edit['Naw'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">ژمارەی تەلەفۆن</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-telephone"></i></span>
                        <input type="text" name="phone" class="form-control" placeholder="0770 000 0000" value="<?= htmlspecialchars($edit['PhoneNo'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">ناونیشان / شوێن</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" name="address" class="form-control" placeholder="شار - گەڕەک" value="<?= htmlspecialchars($edit['Address'] ?? '') ?>" required>
                    </div>
                </div>
            </div>

            <div class="mt-3 d-flex justify-content-start gap-2">
                <button type="submit" name="save" class="btn btn-save btn-custom">
                    <i class="bi bi-cloud-check-fill me-1"></i> <?= $edit ? 'نوێکردنەوە' : 'پاشەکەوتکردن' ?>
                </button>

                <?php if($edit){ ?>
                    <a href="nusenga.php" class="btn btn-secondary btn-custom">
                        <i class="bi bi-x-circle"></i> هەڵوەشاندنەوە
                    </a>
                <?php } ?>
            </div>
        </form>
    </div>

    <div class="card p-0 overflow-hidden shadow-sm">
        <div class="p-2 px-3 bg-dark text-white fw-bold d-flex justify-content-between align-items-center" style="font-size: 13px;">
            <span><i class="bi bi-list-task text-warning me-1"></i> لیستی تۆماری تەواوی نوسینگەکان</span>
        </div>
        <div class="p-2">
            <div class="table-responsive">
                <table id="myTable" class="table table-sm table-striped table-bordered text-center mb-0 shadow-sm">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 6%;">ID</th>
                            <th style="width: 12%;">کۆد</th>
                            <th>ناوی نوسینگە</th>
                            <th style="width: 18%;">ژمارەی تەلەفۆن</th>
                            <th style="width: 25%;">ناونیشان</th>
                            <th style="width: 18%;">کردارەکان</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td class="fw-bold text-secondary"><?= $row['NusengaID'] ?></td>
                                <td><span class="badge bg-primary-subtle text-primary p-1 px-2 rounded-1 fw-bold" style="font-size: 11.5px;"><?= htmlspecialchars($row['Code']) ?></span></td>
                                <td class="fw-bold"><?= htmlspecialchars($row['Naw']) ?></td>
                                <td dir="ltr" class="text-dark font-monospace small"><?= htmlspecialchars($row['PhoneNo']) ?></td>
                                <td class="small text-start"><i class="bi bi-geo-alt text-muted me-1"></i><?= htmlspecialchars($row['Address']) ?></td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="?edit=<?= $row['NusengaID'] ?>" class="btn btn-warning text-white btn-action" title="دەستکاری">
                                            <i class="bi bi-pencil-square"></i> دەستکاری
                                        </a>
                                        <a href="?delete=<?= $row['NusengaID'] ?>" class="btn btn-danger btn-action" title="سڕینەوە" onclick="return confirm('ئایا دڵنیای لە سڕینەوەی ئەم نوسینگەیە؟')">
                                            <i class="bi bi-trash"></i> سڕینەوە
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    $('#myTable').DataTable({
        pageLength: 10,
        ordering: true,
        searching: true,
        language: {
            search: "گەڕانی خێرا:",
            lengthMenu: "نیشاندانی _MENU_ ڕیز",
            info: "نیشاندانی _START_ تا _END_ لە کۆی _TOTAL_ تۆمار",
            infoEmpty: "هیچ تۆمارێک نییە",
            infoFiltered: "(پاڵێوراوە لە کۆی _MAX_ تۆمار)",
            zeroRecords: "هیچ داتایەک نەدۆزرایەوە!",
            paginate: {
                first: "یەکەم",
                previous: "پێشوو",
                next: "دواتر",
                last: "کۆتایی"
            }
        }
    });
});
</script>

</body>
</html>