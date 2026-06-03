<?php
session_start();
include 'config/db.php';

if(!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

/* =====================
   INSERT / UPDATE
===================== */
if(isset($_POST['save']))
{
    $id = intval($_POST['id'] ?? 0);
    $currencyName = trim($_POST['currency_name'] ?? '');

    if(!empty($currencyName)) {
        if($id == 0) {
            $stmt = mysqli_prepare($conn, "INSERT INTO Currency (CurrencyName) VALUES (?)");
            mysqli_stmt_bind_param($stmt, "s", $currencyName);
            mysqli_stmt_execute($stmt);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE Currency SET CurrencyName=? WHERE CurrencyID=?");
            mysqli_stmt_bind_param($stmt, "si", $currencyName, $id);
            mysqli_stmt_execute($stmt);
        }
    }
    header("Location: currency.php");
    exit();
}

/* =====================
   DELETE
===================== */
if(isset($_GET['delete']))
{
    $id = intval($_GET['delete']);
    $stmt = mysqli_prepare($conn, "DELETE FROM Currency WHERE CurrencyID=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    header("Location: currency.php");
    exit();
}

/* =====================
   EDIT LOAD
===================== */
$edit = null;
if(isset($_GET['edit']))
{
    $id = intval($_GET['edit']);
    $stmt = mysqli_prepare($conn, "SELECT * FROM Currency WHERE CurrencyID=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

$result = mysqli_query($conn, "SELECT * FROM Currency ORDER BY CurrencyID DESC");
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جۆری دراوەکان</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        @font-face { font-family: 'NRT'; src: url('fonts/NRT-Regular.ttf') format('truetype'); }
        body { background: #f4f6f9; font-family: 'NRT', sans-serif; color: #334155; font-size: 13px; padding-bottom: 60px; }
        .custom-container { max-width: 800px; margin: 0 auto; padding: 0 20px; }
        .main-header { background: linear-gradient(135deg, #d97706, #b45309); color: white; padding: 12px 20px; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; margin-top: 15px; margin-bottom: 15px; box-shadow: 0 4px 15px rgba(217, 119, 6, 0.15); }
        .card { background: #ffffff; border: none; border-radius: 12px; padding: 15px; margin-bottom: 15px; border: 1px solid #e2e8f0; }
        .section-title { font-size: 13.5px; font-weight: bold; color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin-bottom: 12px; }
        .form-control { border-color: #cbd5e1; border-radius: 6px; padding: 5px 10px; font-size: 13px; }
        .form-control:focus { border-color: #d97706; box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.12); }
        .btn-custom { border-radius: 6px; font-weight: bold; padding: 6px 16px; font-size: 13px; }
        .btn-save { background: linear-gradient(135deg, #d97706, #92400e); color: white; border: none; }
        .btn-save:hover { background: linear-gradient(135deg, #92400e, #78350f); color: white; }
        .table th, .table td { padding: 5px 8px !important; vertical-align: middle; height: 32px !important; }
        .back-btn { background: rgba(255, 255, 255, 0.12); color: white; text-decoration: none; padding: 5px 14px; border-radius: 8px; font-size: 12.5px; border: 1px solid rgba(255, 255, 255, 0.08); }
        .back-btn:hover { background: white; color: #b45309; }
        .btn-action { padding: 2px 8px; font-size: 12px; border-radius: 5px; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #d97706 !important; color: white !important; border-color: #d97706 !important; border-radius: 5px; }
    </style>
</head>
<body>

<div class="custom-container">
    <div class="main-header">
        <div>
            <h5 class="mb-0 fw-bold"><i class="bi bi-coin me-2"></i> جۆری دراوەکان</h5>
        </div>
        <a href="dashboard.php" class="back-btn"><i class="bi bi-arrow-right-circle"></i> گەڕانەوە</a>
    </div>

    <div class="card shadow-sm">
        <div class="section-title">
            <i class="bi bi-plus-square-fill text-warning me-1"></i> <?= $edit ? 'دەستکاریکردنی دراو' : 'زیادکردنی دراوی نوێ' ?>
        </div>
        <form method="post" autocomplete="off">
            <input type="hidden" name="id" value="<?= $edit['CurrencyID'] ?? '' ?>">
            <div class="row g-2 align-items-end">
                <div class="col-md-8">
                    <label class="form-label fw-bold mb-1">ناوی دراو</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted"><i class="bi bi-cash"></i></span>
                        <input type="text" name="currency_name" class="form-control" placeholder="نموونە: USD ($)، دینار، تمەن..." value="<?= htmlspecialchars($edit['CurrencyName'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="col-md-4 d-flex gap-1">
                    <button type="submit" name="save" class="btn btn-save btn-custom w-100">
                        <i class="bi bi-cloud-check-fill me-1"></i> پاشەکەوت
                    </button>
                    <?php if($edit){ ?>
                        <a href="currency.php" class="btn btn-secondary btn-custom"><i class="bi bi-x-circle"></i></a>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>

    <div class="card p-0 overflow-hidden shadow-sm">
        <div class="p-2 px-3 bg-dark text-white fw-bold" style="font-size: 13px;">
            <i class="bi bi-list-task text-warning me-1"></i> لیستی دراوە تۆمارکراوەکان
        </div>
        <div class="p-2">
            <table id="myTable" class="table table-sm table-striped table-bordered text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 15%;">ID</th>
                        <th>ناوی دراو</th>
                        <th style="width: 30%;">کردارەکان</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td class="fw-bold text-secondary"><?= $row['CurrencyID'] ?></td>
                            <td class="fw-bold text-start ps-3 text-primary"><i class="bi bi-currency-exchange text-muted me-2"></i><?= htmlspecialchars($row['CurrencyName']) ?></td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="?edit=<?= $row['CurrencyID'] ?>" class="btn btn-warning text-white btn-action"><i class="bi bi-pencil-square"></i> دەستکاری</a>
                                    <a href="?delete=<?= $row['CurrencyID'] ?>" class="btn btn-danger btn-action" onclick="return confirm('دڵنیایت لە سڕینەوە؟')"><i class="bi bi-trash"></i> سڕینەوە</a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function () {
    $('#myTable').DataTable({ pageLength: 10, ordering: true, searching: true, language: { search: "گەڕان:", lengthMenu: "_MENU_", info: "_TOTAL_ دراو", zeroRecords: "هیچ داتایەک نییە!", paginate: { previous: "پێشوو", next: "دواتر" } } });
});
</script>
</body>
</html>