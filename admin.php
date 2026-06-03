<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

/* INSERT / UPDATE */
if(isset($_POST['save'])) {
    $id = intval($_POST['id'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $type = trim($_POST['type'] ?? 'Admin'); // وەرگرتنی دەق لە جیاتی ژمارە

    if(!empty($username) && !empty($password)) {
        if($id == 0) {
            // گۆڕینی ستوونەکە بۆ UserType و جۆری داتا بۆ sss
            $stmt = mysqli_prepare($conn, "INSERT INTO Admin (UserName, Password, UserType) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $username, $password, $type);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } else {
            // گۆڕینی ستوونەکە لە UPDATE و جۆری داتا بۆ sssi
            $stmt = mysqli_prepare($conn, "UPDATE Admin SET UserName=?, Password=?, UserType=? WHERE AdminID=?");
            mysqli_stmt_bind_param($stmt, "sssi", $username, $password, $type, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    header("Location: admin.php");
    exit();
}

/* DELETE */
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = mysqli_prepare($conn, "DELETE FROM Admin WHERE AdminID=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: admin.php");
    exit();
}

/* EDIT LOAD */
$editData = null;
if(isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = mysqli_prepare($conn, "SELECT * FROM Admin WHERE AdminID=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $editData = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
}

/* LIST ALL */
$result = mysqli_query($conn, "SELECT * FROM Admin ORDER BY AdminID DESC");
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی بەکارهێنەران</title>

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
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .main-header {
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.15);
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

        .form-control, .form-select {
            border-color: #cbd5e1;
            border-radius: 6px;
            padding: 5px 10px;
            font-family: 'NRT', sans-serif;
            font-size: 13px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
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
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            color: white;
            border: none;
            box-shadow: 0 3px 10px rgba(124, 58, 237, 0.2);
        }
        .btn-save:hover { background: linear-gradient(135deg, #6d28d9, #5b21b6); color: white; }

        .table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            font-size: 13px;
        }

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
        .back-btn:hover { background: white; color: #7c3aed; }

        .btn-action {
            padding: 2px 8px;
            font-size: 12px;
            border-radius: 5px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #7c3aed !important;
            color: white !important;
            border-color: #7c3aed !important;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="custom-container">

    <div class="main-header">
        <div>
            <h5 class="mb-0 fw-bold"><i class="bi bi-person-gear me-2"></i> بەڕێوەبردنی ئەندامان و دەسەڵاتەکان</h5>
        </div>
        <a href="dashboard.php" class="back-btn"><i class="bi bi-arrow-right-circle"></i> گەڕانەوە</a>
    </div>

    <div class="card shadow-sm">
        <div class="section-title">
            <i class="bi bi-person-plus-fill text-purple me-1"></i> 
            <?= $editData ? 'دەستکاریکردنی زانیاری بەکارهێنەر' : 'تۆمارکردنی بەکارهێنەری نوێ' ?>
        </div>

        <form method="post" autocomplete="off">
            <input type="hidden" name="id" value="<?= $editData['AdminID'] ?? '' ?>">

            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label">ناوی بەکارهێنەر (Username)</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="نموونە: muhammad" value="<?= htmlspecialchars($editData['UserName'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">وشەی نهێنی (Password)</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-key"></i></span>
                        <input type="text" name="password" class="form-control" placeholder="وشەی نهێنی" value="<?= htmlspecialchars($editData['Password'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label">جۆری دەسەڵات</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-shield-lock"></i></span>
                        <select name="type" class="form-select" required>
                            <?php $currentType = $editData['UserType'] ?? 'Admin'; ?>
                            <option value="SuperAdmin" <?= $currentType == 'SuperAdmin' ? 'selected' : '' ?>>SuperAdmin</option>
                            <option value="Admin" <?= $currentType == 'Admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="Editor" <?= $currentType == 'Editor' ? 'selected' : '' ?>>Editor</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" name="save" class="btn btn-save btn-custom w-100">
                        <i class="bi bi-cloud-check-fill me-1"></i> <?= $editData ? 'نوێکردنەوە' : 'پاشەکەوت' ?>
                    </button>
                </div>
            </div>

            <?php if($editData){ ?>
                <div class="mt-2 text-start">
                    <a href="admin.php" class="btn btn-sm btn-secondary rounded-2"><i class="bi bi-x-circle"></i> هەڵوەشاندنەوە</a>
                </div>
            <?php } ?>
        </form>
    </div>

    <div class="card p-0 overflow-hidden shadow-sm">
        <div class="p-2 px-3 bg-dark text-white fw-bold d-flex justify-content-between align-items-center" style="font-size: 13px;">
            <span><i class="bi bi-list-task text-warning me-1"></i> لیستی گشتی بەکارهێنەرانی سیستم</span>
        </div>
        <div class="p-2">
            <div class="table-responsive">
                <table id="adminTable" class="table table-sm table-striped table-bordered text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%;">ID</th>
                            <th>ناوی بەکارهێنەر</th>
                            <th>وشەی نهێنی</th>
                            <th style="width: 20%;">جۆری دەسەڵات</th>
                            <th style="width: 20%;">کردارەکان</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td class="fw-bold text-secondary"><?= $row['AdminID'] ?></td>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($row['UserName']) ?></td>
                                <td class="font-monospace text-muted small"><?= htmlspecialchars($row['Password']) ?></td>
                                <td>
                                    <?php 
                                        $badgeClass = 'bg-secondary';
                                        if($row['UserType'] == 'SuperAdmin') $badgeClass = 'bg-danger';
                                        if($row['UserType'] == 'Admin') $badgeClass = 'bg-purple bg-primary';
                                        if($row['UserType'] == 'Editor') $badgeClass = 'bg-info text-dark';
                                    ?>
                                    <span class="badge <?= $badgeClass ?> p-1 px-2 rounded-1 fw-bold"><?= htmlspecialchars($row['UserType']) ?></span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="?edit=<?= $row['AdminID'] ?>" class="btn btn-warning text-white btn-action" title="دەستکاری">
                                            <i class="bi bi-pencil-square"></i> دەستکاری
                                        </a>
                                        <a href="?delete=<?= $row['AdminID'] ?>" class="btn btn-danger btn-action" title="سڕینەوە" onclick="return confirm('ئایا دڵنیایت لە سڕینەوەی ئەم بەکارهێنەرە؟')">
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
    $('#adminTable').DataTable({
        pageLength: 10,
        ordering: true,
        searching: true,
        language: {
            search: "گەڕان:",
            lengthMenu: "نیشاندانی _MENU_ ڕیز",
            info: "تۆمارەکان: _START_ تا _END_ لە کۆی _TOTAL_ دانە",
            infoEmpty: "هیچ تۆمارێک نییە",
            zeroRecords: "هیچ داتایەک نەدۆزرایەوە!",
            paginate: { previous: "پێشوو", next: "دواتر" }
        }
    });
});
</script>

</body>
</html>