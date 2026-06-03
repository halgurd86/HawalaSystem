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

/* ================= DELETE ================= */
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);

    $stmt1 = mysqli_prepare($conn, "DELETE FROM WargrtnDetails WHERE WargrtnID=?");
    mysqli_stmt_bind_param($stmt1, "i", $id);
    mysqli_stmt_execute($stmt1);

    $stmt2 = mysqli_prepare($conn, "DELETE FROM Wargrtn WHERE WargrtnID=?");
    mysqli_stmt_bind_param($stmt2, "i", $id);
    mysqli_stmt_execute($stmt2);

    header("Location: wargrtn.php");
    exit();
}

/* ================= LOAD EDIT ================= */
$edit = null;
$details = [];

if(isset($_GET['edit'])){
    $id = intval($_GET['edit']);

    $stmt = mysqli_prepare($conn, "SELECT * FROM Wargrtn WHERE WargrtnID=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $edit = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    $stmt_details = mysqli_prepare($conn, "SELECT * FROM WargrtnDetails WHERE WargrtnID=?");
    mysqli_stmt_bind_param($stmt_details, "i", $id);
    mysqli_stmt_execute($stmt_details);
    $q = mysqli_stmt_get_result($stmt_details);

    while($r = mysqli_fetch_assoc($q)){
        $details[] = $r;
    }
}

/* ================= SAVE ================= */
if(isset($_POST['save'])){
    $id = intval($_POST['id']);
    $nusenga = intval($_POST['nusenga'] ?? 0); 
    $wergr = $_POST['wergr'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $nerar = $_POST['nerar'] ?? '';
    $note  = $_POST['note'] ?? '';
    $is_received = intval($_POST['is_received'] ?? 0); 

    $barwarA = !empty($_POST['barwarA']) ? $_POST['barwarA'] : date('Y-m-d');
    $timeA   = !empty($_POST['timeA']) ? $_POST['timeA'] : date('H:i');
    $barwarB = !empty($_POST['barwarB']) ? $_POST['barwarB'] : date('Y-m-d');
    $timeB   = !empty($_POST['timeB']) ? $_POST['timeB'] : date('H:i');

    $shar = intval($_POST['shar'] ?? 0);

    if($id == 0){
        $stmt = mysqli_prepare($conn, "INSERT INTO Wargrtn (NusengaID, NawyWergr, PhoneNo, NawyNerar, Note, BarwarA, TimeA, BarwarB, TimeB, SharID, IsReceived) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "issssssssii", $nusenga, $wergr, $phone, $nerar, $note, $barwarA, $timeA, $barwarB, $timeB, $shar, $is_received);
        mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($conn);
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE Wargrtn SET NusengaID=?, NawyWergr=?, PhoneNo=?, NawyNerar=?, Note=?, BarwarA=?, TimeA=?, BarwarB=?, TimeB=?, SharID=?, IsReceived=? WHERE WargrtnID=?");
        mysqli_stmt_bind_param($stmt, "issssssssiii", $nusenga, $wergr, $phone, $nerar, $note, $barwarA, $timeA, $barwarB, $timeB, $shar, $is_received, $id);
        mysqli_stmt_execute($stmt);
    }

    /* DETAILS SAVE */
    $stmt_del = mysqli_prepare($conn, "DELETE FROM WargrtnDetails WHERE WargrtnID=?");
    mysqli_stmt_bind_param($stmt_del, "i", $id);
    mysqli_stmt_execute($stmt_del);

    if(isset($_POST['currency'])){
        for($i=0; $i<count($_POST['currency']); $i++){
            $cur = intval($_POST['currency'][$i]);
            $amount = floatval($_POST['amount'][$i] ?? 0);
            $comm = floatval($_POST['commission'][$i] ?? 0);

            if($cur > 0){
                $stmt_ins = mysqli_prepare($conn, "INSERT INTO WargrtnDetails (WargrtnID, CurrencyID, Amount, Commission) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt_ins, "iidd", $id, $cur, $amount, $comm);
                mysqli_stmt_execute($stmt_ins);
            }
        }
    }

    header("Location: wargrtn.php");
    exit();
}

/* FETCH ALL DATA FOR MAIN LIST */
$sql = "SELECT w.*, s.SharName, n.Naw AS NusengaName, n.Code AS NusengaCode 
        FROM Wargrtn w 
        LEFT JOIN Shar s ON w.SharID=s.SharID 
        LEFT JOIN Nusenga n ON w.NusengaID=n.NusengaID 
        ORDER BY w.IsReceived ASC, w.WargrtnID DESC";
$list = mysqli_query($conn, $sql);

$nusengas_for_code = mysqli_query($conn, "SELECT * FROM Nusenga");
$nusengas_for_name = mysqli_query($conn, "SELECT * FROM Nusenga");
$shars = mysqli_query($conn, "SELECT * FROM Shar");
$curr = mysqli_query($conn, "SELECT * FROM Currency");
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حەواڵە وەرگرتن - نوسینگەی محمد سەنگەسەری</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        @font-face {
            font-family: 'NRT';
            src: url('fonts/NRT-Regular.ttf') format('truetype');
        }

        body {
            background: #f4f6f9;
            min-height: 100vh;
            font-family: 'NRT', 'Segoe UI', Tahoma, sans-serif;
            color: #334155;
            font-size: 13.5px;
            padding-bottom: 50px;
        }

        /* زیادکردنی پانی گشتی بۆ شوێنکردنەوەی باشتر و ڕێگری لە قەرەباڵغی */
        .custom-container {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .main-header {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            padding: 12px 20px;
            border-radius: 12px;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            margin-top: 15px;
        }

        .card {
            background: #ffffff;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
            padding: 16px;
            margin-bottom: 15px;
            border: 1px solid #e2e8f0;
        }

        .form-label-inline {
            font-weight: 600;
            color: #334155;
            font-size: 13px;
            display: flex;
            align-items: center;
            margin-bottom: 0;
        }

        .form-control, .form-select {
            border-color: #cbd5e1;
            border-radius: 6px;
            padding: 6px 10px;
            font-family: 'NRT', sans-serif;
            font-size: 13.5px;
            background-color: #fff;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        .btn-custom {
            border-radius: 8px;
            font-weight: bold;
            padding: 8px 15px;
            font-family: 'NRT', sans-serif;
            font-size: 13.5px;
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

        .table th, .table td {
            padding: 10px;
            vertical-align: middle;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.18);
            color: white;
            text-decoration: none;
            padding: 5px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            transition: 0.2s;
        }
        .back-btn:hover { background: white; color: #4f46e5; }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
    </style>
</head>

<body>

<div class="custom-container">

    <div class="main-header">
        <div><i class="bi bi-wallet2 me-2"></i> بەڕێوەبردنی حەواڵەی وەرگیراو</div>
        <a href="dashboard.php" class="back-btn"><i class="bi bi-arrow-right-circle"></i> گەڕانەوە</a>
    </div>

    <div class="card p-2">
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
            <input type="text" id="liveSearch" class="form-control border-start-0" placeholder="گەڕانی خێرا لەناو خشتەکەدا... (ناو، کۆد، مۆبایل یان دۆخ)">
        </div>
    </div>

    <form method="post" autocomplete="off">
        <input type="hidden" name="id" value="<?= $edit['WargrtnID'] ?? 0 ?>">

        <div class="row g-3">
            
            <div class="col-md-6">
                <div class="card h-100">
                    
                    <div class="section-title text-indigo"><i class="bi bi-grid-1x2-fill text-primary me-1"></i> زانیاری گشتی حەواڵە</div>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <div class="row align-items-center">
                                <label class="col-4 form-label-inline text-danger">کۆدی نوسینگە</label>
                                <div class="col-8">
                                    <select id="codeSelect" name="nusenga" class="form-select form-select-sm fw-bold text-primary" required>
                                        <option value="">-- هەڵبژێرە --</option>
                                        <?php mysqli_data_seek($nusengas_for_code, 0); while($n = mysqli_fetch_assoc($nusengas_for_code)){ ?>
                                            <option value="<?= $n['NusengaID'] ?>" <?= (isset($edit['NusengaID']) && $edit['NusengaID'] == $n['NusengaID']) ? 'selected' : '' ?>><?= htmlspecialchars($n['Code']) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row align-items-center">
                                <label class="col-4 form-label-inline text-danger">ناوی نوسینگە</label>
                                <div class="col-8">
                                    <select id="nameSelect" class="form-select form-select-sm fw-bold">
                                        <option value="">-- هەڵبژێرە --</option>
                                        <?php mysqli_data_seek($nusengas_for_name, 0); while($n = mysqli_fetch_assoc($nusengas_for_name)){ ?>
                                            <option value="<?= $n['NusengaID'] ?>" <?= (isset($edit['NusengaID']) && $edit['NusengaID'] == $n['NusengaID']) ? 'selected' : '' ?>><?= htmlspecialchars($n['Naw']) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <div class="row align-items-center">
                                <label class="col-4 form-label-inline text-success">دۆخی وەرگرتن</label>
                                <div class="col-8">
                                    <select name="is_received" class="form-select form-select-sm fw-bold">
                                        <option value="0" <?= (isset($edit['IsReceived']) && $edit['IsReceived'] == 0) ? 'selected' : '' ?>>❌ وەرنەگیراوە</option>
                                        <option value="1" <?= (isset($edit['IsReceived']) && $edit['IsReceived'] == 1) ? 'selected' : '' ?>>✅ وەرگیراوە</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row align-items-center">
                                <label class="col-4 form-label-inline">شار / ناوچە</label>
                                <div class="col-8">
                                    <select name="shar" class="form-select form-select-sm">
                                        <?php mysqli_data_seek($shars, 0); while($s = mysqli_fetch_assoc($shars)){ ?>
                                            <option value="<?= $s['SharID'] ?>" <?= (isset($edit['SharID']) && $edit['SharID'] == $s['SharID']) ? 'selected' : '' ?>><?= htmlspecialchars($s['SharName']) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <div class="row align-items-center">
                                <label class="col-4 form-label-inline">بەرواری ناردن</label>
                                <div class="col-8">
                                    <input type="date" name="barwarA" class="form-control form-control-sm" value="<?= $edit['BarwarA'] ?? date('Y-m-d') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row align-items-center">
                                <label class="col-4 form-label-inline">کاتی ناردن</label>
                                <div class="col-8">
                                    <input type="time" name="timeA" class="form-control form-control-sm" value="<?= $edit['TimeA'] ?? date('H:i') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-title mt-2"><i class="bi bi-people-fill text-warning me-1"></i> زانیاری لایەنەکان (نێرەر و وەرگر)</div>

                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <div class="row align-items-center">
                                <label class="col-4 form-label-inline">ناوی وەرگر</label>
                                <div class="col-8">
                                    <input name="wergr" class="form-control form-control-sm fw-bold" value="<?= htmlspecialchars($edit['NawyWergr'] ?? '') ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row align-items-center">
                                <label class="col-4 form-label-inline">ژمارەی مۆبایل</label>
                                <div class="col-8">
                                    <input name="phone" class="form-control form-control-sm" value="<?= htmlspecialchars($edit['PhoneNo'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-sm-6">
                            <div class="row align-items-center">
                                <label class="col-4 form-label-inline">ناوی نێرەر</label>
                                <div class="col-8">
                                    <input name="nerar" class="form-control form-control-sm" value="<?= htmlspecialchars($edit['NawyNerar'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row align-items-center">
                                <label class="col-4 form-label-inline">تێبینی</label>
                                <div class="col-8">
                                    <input name="note" class="form-control form-control-sm" value="<?= htmlspecialchars($edit['Note'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-sm-6">
                            <div class="row align-items-center">
                                <label class="col-4 form-label-inline">بەرواری دانەوە</label>
                                <div class="col-8">
                                    <input type="date" name="barwarB" class="form-control form-control-sm" value="<?= $edit['BarwarB'] ?? date('Y-m-d') ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row align-items-center">
                                <label class="col-4 form-label-inline">کاتی دانەوە</label>
                                <div class="col-8">
                                    <input type="time" name="timeB" class="form-control form-control-sm" value="<?= $edit['TimeB'] ?? date('H:i') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="section-title"><i class="bi bi-cash-coin text-success me-1"></i> بڕی پارە و دراوەکان (فراوان و گەورە)</div>
                        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                            <table class="table table-sm table-bordered text-center mb-0" id="tbl">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 35%;">جۆری دراو</th>
                                        <th style="width: 35%;">بڕی حەواڵە</th>
                                        <th style="width: 20%;">کرێ</th>
                                        <th style="width: 10%;">X</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($details as $d){ ?>
                                    <tr>
                                        <td>
                                            <select name="currency[]" class="form-select form-select-sm fw-bold fs-6">
                                                <?php mysqli_data_seek($curr, 0); while($c = mysqli_fetch_assoc($curr)){ ?>
                                                    <option value="<?= $c['CurrencyID'] ?>" <?= $c['CurrencyID'] == $d['CurrencyID'] ? 'selected' : '' ?>><?= htmlspecialchars($c['CurrencyName']) ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td><input type="number" step="any" name="amount[]" class="form-control form-control-sm text-center fw-bold text-success fs-6" value="<?= htmlspecialchars($d['Amount']) ?>"></td>
                                        <td><input type="number" step="any" name="commission[]" class="form-control form-control-sm text-center text-secondary fw-bold" value="<?= htmlspecialchars($d['Commission']) ?>"></td>
                                        <td><button type="button" class="btn btn-outline-danger btn-sm p-1 py-0" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm w-100 mt-2" onclick="addRow()">
                            <i class="bi bi-plus-circle-fill"></i> زیادکردنی ڕیزی نوێی دراو
                        </button>
                    </div>

                    <button type="submit" name="save" class="btn btn-save btn-custom w-100 mt-3 py-2 fs-6">
                        <i class="bi bi-cloud-check-fill me-1"></i> پاشەکەوتکردنی زانیارییەکان (Save)
                    </button>
                </div>
            </div>

        </div>
    </form>

    <div class="my-3"></div>

    <div class="card p-0 overflow-hidden">
        <div class="p-2 bg-dark text-white fw-bold d-flex justify-content-between align-items-center" style="font-size: 13px;">
            <span><i class="bi bi-list-stars text-warning me-1"></i> لیستی گشتی حەواڵە تۆمارکراوەکان</span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0 text-center shadow-sm" id="mainTable">
                <thead class="table-light border-bottom">
                    <tr>
                        <th>ID</th>
                        <th>نوسینگە</th>
                        <th>کۆد</th>
                        <th>ناوی وەرگر</th>
                        <th>مۆبایل</th>
                        <th>شار</th>
                        <th>ناوی نێرەر</th>
                        <th>دۆخی حەواڵە</th>
                        <th>کردارەکان</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($list) > 0) { ?>
                        <?php while($r = mysqli_fetch_assoc($list)){ 
                            $row_class = ($r['IsReceived'] == 1) ? 'table-success text-dark' : ''; 
                        ?>
                        <tr class="<?= $row_class ?>">
                            <td class="fw-bold"><?= $r['WargrtnID'] ?></td>
                            <td><span class="badge bg-secondary p-1 px-2 rounded-2"><?= htmlspecialchars($r['NusengaName']) ?></span></td>
                            <td class="text-primary fw-bold"><?= htmlspecialchars($r['NusengaCode'] ?? '---') ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($r['NawyWergr']) ?></td>
                            <td dir="ltr" class="small"><?= htmlspecialchars($r['PhoneNo']) ?></td>
                            <td><?= htmlspecialchars($r['SharName']) ?></td>
                            <td><?= htmlspecialchars($r['NawyNerar']) ?></td>
                            <td>
                                <?php if($r['IsReceived'] == 1) { ?>
                                    <span class="badge bg-success p-1 px-2 rounded-2 small"><i class="bi bi-check-circle"></i> وەرگیراوە</span>
                                <?php } else { ?>
                                    <span class="badge bg-danger p-1 px-2 rounded-2 small"><i class="bi bi-x-circle"></i> وەرنەگیراوە</span>
                                <?php } ?>
                            </td>
                            <td>
                                <a href="invoice.php?id=<?= $r['WargrtnID'] ?>" class="btn btn-primary btn-sm p-1 py-0 text-white me-1" title="بینینی وەسڵ" target="_blank">
                                    <i class="bi bi-printer-fill"></i>
                                </a>
                                <a href="?edit=<?= $r['WargrtnID'] ?>" class="btn btn-warning btn-sm p-1 py-0 text-white me-1" title="دەستکاری"><i class="bi bi-pencil-square"></i></a>
                                <a href="?delete=<?= $r['WargrtnID'] ?>" class="btn btn-danger btn-sm p-1 py-0" title="سڕینەوە" onclick="return confirm('ئایا دڵنیای لە سڕینەوەی ئەم حەواڵەیە؟')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr class="no-data-row">
                            <td colspan="9" class="text-muted py-3">هیچ داتایەک نەدۆزرایەوە!</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
/* لۆجیکی بەستنەوەی دوولایەنەی (کۆدی نوسینگە) و (ناوی نوسینگە) */
const codeSelect = document.getElementById("codeSelect");
const nameSelect = document.getElementById("nameSelect");

codeSelect.addEventListener("change", function() { nameSelect.value = this.value; });
nameSelect.addEventListener("change", function() { codeSelect.value = this.value; });

/* لۆجیکی گەڕانی خێرا و ئۆتۆماتیکی بێ ڕیفرش (Live Search) */
document.getElementById("liveSearch").addEventListener("keyup", function() {
    let value = this.value.toLowerCase().trim();
    let rows = document.querySelectorAll("#mainTable tbody tr:not(.no-data-row)");
    let visibleRows = 0;

    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        if(text.includes(value)) {
            row.style.display = "";
            visibleRows++;
        } else {
            row.style.display = "none";
        }
    });

    let noDataRow = document.querySelector("#mainTable tbody .no-data-row");
    if(visibleRows === 0) {
        if(!noDataRow) {
            let tbody = document.querySelector("#mainTable tbody");
            let tr = document.createElement("tr");
            tr.className = "no-data-row";
            tr.innerHTML = `<td colspan="9" class="text-muted py-3">هیچ داتایەک هاوشێوەی گەڕانەکەت نەدۆزرایەوە!</td>`;
            tbody.appendChild(tr);
        }
    } else { if(noDataRow) noDataRow.remove(); }
});

/* زیادکردنی ڕیزی نوێ لە خشتەی دراوەکان */
function addRow(){
    let tbody = document.querySelector("#tbl tbody");
    let row = document.createElement("tr");

    row.innerHTML = `
        <td>
            <select name="currency[]" class="form-select form-select-sm fw-bold fs-6">
                <?php mysqli_data_seek($curr, 0); while($c = mysqli_fetch_assoc($curr)){ ?>
                    <option value="<?= $c['CurrencyID'] ?>"><?= htmlspecialchars($c['CurrencyName']) ?></option>
                <?php } ?>
            </select>
        </td>
        <td><input type="number" step="any" name="amount[]" class="form-control form-control-sm text-center fw-bold text-success fs-6" required></td>
        <td><input type="number" step="any" name="commission[]" class="form-control form-control-sm text-center text-secondary fw-bold" value="0"></td>
        <td><button type="button" class="btn btn-outline-danger btn-sm p-1 py-0" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(row);
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>