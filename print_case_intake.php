<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: auth/login.php");
    exit();
}

include "config/database.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) { echo "Invalid ID."; exit(); }

$result = $conn->query("SELECT ci.*, u.name as submitted_by_name FROM case_intake ci LEFT JOIN users u ON ci.user_id = u.id WHERE ci.id = $id");
if (!$result || $result->num_rows === 0) { echo "Record not found."; exit(); }
$d = $result->fetch_assoc();

$nature = json_decode($d['nature_of_case'] ?? '[]', true) ?: [];

function val($v) { return htmlspecialchars($v ?? ''); }
function fdate($v) { return ($v && $v !== '0000-00-00') ? date('m/d/Y', strtotime($v)) : ''; }
function chk($nature, $label) { return in_array($label, $nature) ? '&#10003;' : '&nbsp;'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Case Intake Sheet - Print</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }

body {
    font-family: Arial, sans-serif;
    font-size: 9.5pt;
    color: #000;
    background: #e5e7eb;
}

/* Print button bar */
.no-print {
    background: #1e3a5f;
    padding: 12px 20px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
}
.btn-back {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 18px; background: #fff; color: #1e3a5f;
    font-weight: 700; font-size: 0.85rem; border: none;
    border-radius: 6px; cursor: pointer; text-decoration: none;
}
.btn-print {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 22px; background: #2563eb; color: #fff;
    font-weight: 700; font-size: 0.85rem; border: none;
    border-radius: 6px; cursor: pointer;
}

/* Page */
.page {
    width: 215mm;
    min-height: 279mm;
    margin: 12px auto;
    background: #fff;
    padding: 8mm 12mm 10mm;
    box-shadow: 0 2px 16px rgba(0,0,0,0.18);
}

/* Letterhead */
.lh-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 2px;
}
.lh-center { text-align: center; }
.lh-center .city { font-size: 8pt; color: #333; }
.lh-center .office { font-size: 11pt; font-weight: bold; color: #1a3a6b; letter-spacing: 0.5px; }
.lh-divider { border: none; border-top: 2.5px solid #1a56a0; margin: 4px 0 6px; }

/* Form title box */
.title-box {
    border: 1.5px solid #555;
    padding: 5px 10px 6px;
    margin-bottom: 7px;
    text-align: center;
}
.title-box .t1 { font-size: 13pt; font-weight: bold; letter-spacing: 1px; }
.title-box .t2 { font-size: 8.5pt; color: #444; }

/* Generic field line */
.fl {
    display: inline-block;
    border-bottom: 1px solid #000;
    vertical-align: bottom;
    padding: 0 2px 1px;
    min-width: 80px;
}
.fl.xs  { min-width: 45px; }
.fl.sm  { min-width: 70px; }
.fl.md  { min-width: 120px; }
.fl.lg  { min-width: 180px; }
.fl.xl  { min-width: 240px; }
.fl.full{ min-width: 0; width: 100%; display: block; margin-top: 1px; }

/* Info rows */
.row { margin-bottom: 4px; line-height: 1.7; }
.row b { font-weight: bold; }

/* Two-column layout */
.two-col { display: flex; gap: 10px; margin-bottom: 4px; }
.two-col .col { flex: 1; }

/* Name row */
.name-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 6px; margin: 2px 0 4px; }
.name-cell { text-align: center; }
.name-cell .nline { border-bottom: 1px solid #000; min-height: 14px; padding: 0 3px; }
.name-cell .nhint { font-size: 7.5pt; color: #444; font-style: italic; margin-top: 1px; }

/* Inline fields */
.irow { display: flex; flex-wrap: wrap; gap: 6px 12px; margin-bottom: 4px; align-items: flex-end; line-height: 1.7; }
.irow .f { display: flex; align-items: flex-end; gap: 3px; }
.irow .f b { white-space: nowrap; font-weight: bold; }

/* Section label */
.sec { font-weight: bold; margin-bottom: 2px; }

/* Nature of case */
.nat-wrap { display: grid; grid-template-columns: 1fr 1fr; gap: 1px 16px; margin: 3px 0 5px; }
.nat-item { display: flex; align-items: center; gap: 4px; font-size: 9pt; line-height: 1.5; }
.cbox {
    width: 10px; height: 10px;
    border: 1.5px solid #000;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 8pt; flex-shrink: 0; line-height: 1;
}

/* Write lines */
.wline { border-bottom: 1px solid #000; height: 16px; margin-bottom: 3px; }

/* Signature block */
.sig-block { margin-top: 10px; border-top: 1.5px solid #000; padding-top: 8px; }
.sig-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 10px; }
.sig-line { border-bottom: 1.5px solid #000; min-height: 26px; margin-bottom: 2px; }
.sig-label { font-size: 8pt; font-weight: bold; }
.sig-dt { display: flex; gap: 20px; margin-top: 3px; font-size: 8pt; font-weight: bold; }
.sig-dt span { display: flex; align-items: flex-end; gap: 4px; }
.sig-dt .dtline { border-bottom: 1px solid #000; min-width: 65px; display: inline-block; }

@media print {
    .no-print { display: none !important; }
    body { background: #fff; }
    .page { margin: 0; box-shadow: none; width: 100%; padding: 8mm 12mm; }
}
</style>
</head>
<body>

<div class="no-print">
    <a href="manage_case_intake.php" class="btn-back">&#8592; Back</a>
    <button class="btn-print" onclick="window.print()">&#128438;&nbsp; Print / Save as PDF</button>
</div>

<div class="page">

    <!-- Letterhead -->
    <div class="lh-wrap">
        <div class="lh-center">
            <div class="city">OFFICE OF THE CITY MAYOR</div>
            <div class="office">OVERSEAS FILIPINO WORKERS OFFICE</div>
        </div>
    </div>
    <hr class="lh-divider">

    <!-- Title -->
    <div class="title-box">
        <div class="t1">CASE INTAKE SHEET</div>
        <div class="t2">(Please Print All Information)</div>
    </div>

    <!-- Top fields -->
    <div class="row">
        <b>Welfare Case No.:</b> <span class="fl xl"><?= val($d['welfare_case_no']) ?></span>
        &nbsp;&nbsp;<b>Date:</b> <span class="fl md"><?= fdate($d['date_filed']) ?></span>
    </div>
    <div class="row">
        <b>Requesting Party/NOK:</b> <span class="fl xl"><?= val($d['requesting_party']) ?></span>
        &nbsp;&nbsp;<b>Relationship to OFW:</b> <span class="fl md"><?= val($d['relationship_to_ofw']) ?></span>
    </div>
    <div class="row">
        <b>Address:</b> <span class="fl xl"><?= val($d['requesting_address']) ?></span>
        &nbsp;&nbsp;<b>Contact Nos.:</b> <span class="fl md"><?= val($d['requesting_contact']) ?></span>
    </div>

    <!-- Section 1 -->
    <div class="row" style="margin-top:5px;">
        <b>1.&nbsp; Name of OFW:</b>
    </div>
    <div class="name-row">
        <div class="name-cell">
            <div class="nline"><?= val($d['ofw_last_name']) ?></div>
            <div class="nhint">(Last Name)</div>
        </div>
        <div class="name-cell">
            <div class="nline"><?= val($d['ofw_first_name']) ?></div>
            <div class="nhint">(First Name)</div>
        </div>
        <div class="name-cell">
            <div class="nline"><?= val($d['ofw_middle_name']) ?></div>
            <div class="nhint">(Complete Middle Name)</div>
        </div>
    </div>

    <div class="row">
        <b>Contact Nos.:</b> <span class="fl lg"><?= val($d['ofw_contact']) ?></span>
        &nbsp;&nbsp;<b>FB Account Name:</b> <span class="fl lg"><?= val($d['ofw_fb_account']) ?></span>
    </div>
    <div class="row">
        <b>Address:</b> <span class="fl" style="min-width:380px;"><?= val($d['ofw_address']) ?></span>
    </div>
    <div class="irow">
        <div class="f"><b>Sex:</b> <span class="fl sm"><?= val($d['ofw_sex']) ?></span></div>
        <div class="f"><b>Civil Status:</b> <span class="fl sm"><?= val($d['ofw_civil_status']) ?></span></div>
        <div class="f"><b>Birthdate:</b> <span class="fl sm"><?= fdate($d['ofw_birthdate']) ?></span></div>
        <div class="f"><b>Age:</b> <span class="fl xs"><?= val($d['ofw_age']) ?></span></div>
        <div class="f"><b>Passport No.</b> <span class="fl md"><?= val($d['ofw_passport_no']) ?></span></div>
    </div>
    <div class="row">
        <b>Nature of Work Abroad/Position:</b> <span class="fl lg"><?= val($d['ofw_nature_of_work']) ?></span>
        &nbsp;&nbsp;<b>Length of Service</b> <span class="fl md"><?= val($d['ofw_length_of_service']) ?></span>
    </div>
    <div class="row">
        <b>Jobsite Abroad:</b> <span class="fl" style="min-width:340px;"><?= val($d['ofw_jobsite_abroad']) ?></span>
    </div>

    <!-- Section 2 -->
    <div class="row" style="margin-top:4px;">
        <b>2.&nbsp; Name of OFW's Company/ Foreign Employer:</b>
        <span class="fl" style="min-width:260px;"><?= val($d['employer_name']) ?></span>
    </div>
    <div class="row">
        <b>Address/Contact Nos.:</b>
        <span class="fl" style="min-width:340px;"><?= val($d['employer_address']) ?></span>
    </div>

    <!-- Section 3 -->
    <div class="row" style="margin-top:4px;">
        <b>3.&nbsp; Name of OFW's Local Recruitment Agency:</b>
        <span class="fl" style="min-width:240px;"><?= val($d['agency_name']) ?></span>
    </div>
    <div class="row">
        <b>Address/Contact Nos.:</b>
        <span class="fl" style="min-width:340px;"><?= val($d['agency_address']) ?></span>
    </div>

    <!-- Section 4 -->
    <div class="row" style="margin-top:4px;">
        <b>4.&nbsp; Date of Departure:</b>
        <span class="fl md"><?= fdate($d['date_of_departure']) ?></span>
        <span style="font-size:7.5pt;color:#555;">(from the Philippines)</span>
        &nbsp;&nbsp;<b>Date of Arrival:</b>
        <span class="fl md"><?= fdate($d['date_of_arrival']) ?></span>
        <span style="font-size:7.5pt;color:#555;">(in the Philippines)</span>
    </div>

    <!-- Section 5 -->
    <div class="row">
        <b>5.&nbsp; Latest OWWA Membership Payment:</b>
        <span class="fl" style="min-width:260px;"><?= val($d['owwa_membership_payment']) ?></span>
    </div>

    <!-- Nature of Case -->
    <div class="sec" style="margin-top:5px;">NATURE OF CASE/COMPLAINT:</div>
    <div class="nat-wrap">
        <?php
        $left = ['Death','Disability / Physical / Mental Illness','Maltreatment','Abuses (Physical and Verbal)','Sexual Abuse / Harassment / Rape','Unpaid / Delayed Salary','Contract Violations'];
        $right = ['Non-Financial Support','Non-Communication','Deportation / Detention','Legal Assistance','Homesickness','Family Problems/s'];
        $max = max(count($left), count($right));
        for ($i = 0; $i < $max; $i++):
            $lc = $left[$i] ?? null;
            $rc = $right[$i] ?? null;
        ?>
        <?php if ($lc): ?>
        <div class="nat-item"><span class="cbox"><?= chk($nature,$lc) ?></span><?= htmlspecialchars(strtoupper($lc)) ?></div>
        <?php else: ?><div></div><?php endif; ?>
        <?php if ($rc): ?>
        <div class="nat-item"><span class="cbox"><?= chk($nature,$rc) ?></span><?= htmlspecialchars(strtoupper($rc)) ?></div>
        <?php else: ?><div></div><?php endif; ?>
        <?php endfor; ?>
        <!-- Other concerns on right column -->
        <div></div>
        <div class="nat-item">
            <span class="cbox"><?= chk($nature,'Other Concerns') ?></span>
            <span>OTHER CONCERNS: <span style="border-bottom:1px solid #000;min-width:80px;display:inline-block;padding:0 2px;"><?= val($d['other_concerns']) ?></span></span>
        </div>
    </div>

    <!-- Intake Facts -->
    <div class="sec">INTAKE INFORMATION / FACTS OF THE CASE:</div>
    <?php
    $facts = trim($d['intake_facts'] ?? '');
    $lines = $facts ? explode("\n", wordwrap($facts, 115, "\n", true)) : [];
    $total = max(5, count($lines) + 1);
    for ($i = 0; $i < $total; $i++):
    ?>
    <div class="wline"><?= htmlspecialchars($lines[$i] ?? '') ?></div>
    <?php endfor; ?>

    <!-- Assistance Needed -->
    <div class="row" style="margin-top:8px; border-top:1px solid #000; padding-top:6px;">
        <b>ASSISTANCE NEEDED:</b>
        <span class="fl" style="min-width:340px;"><?= val($d['assistance_needed']) ?></span>
    </div>

    <!-- Signature Block -->
    <div class="sig-block">
        <!-- Row 1: Assisting Officer | Requesting Party -->
        <div class="sig-row">
            <div>
                <div class="sig-line"></div>
                <div class="sig-label">ASSISTING OFFICER (NAME &amp; SIGNATURE)</div>
            </div>
            <div>
                <div class="sig-line"></div>
                <div class="sig-label">SIGNATURE OF REQUESTING PARTY / NOK</div>
            </div>
        </div>

        <!-- OWWA label -->
        <div style="font-size:9pt;font-weight:bold;margin-bottom:6px;">TO BE ACCOMPLISHED BY OWWA RWD VI/NIR:</div>

        <!-- Row 2: Received | Endorsed -->
        <div class="sig-row">
            <div>
                <div style="font-size:9pt;margin-bottom:14px;">Received by OWWA RWD VI/NIR:</div>
                <div class="sig-line"></div>
                <div class="sig-label">NAME &amp; SIGNATURE OF RW06/NIR PERSONNEL</div>
                <div class="sig-dt">
                    <span>DATE: <span class="dtline">&nbsp;</span></span>
                    <span>TIME: <span class="dtline">&nbsp;</span></span>
                </div>
            </div>
            <div>
                <div style="font-size:9pt;margin-bottom:14px;">Endorsed to:</div>
                <div class="sig-line"></div>
                <div class="sig-label">NAME &amp; SIGNATURE OF RWO6/NIR CASE OFFICER</div>
                <div class="sig-dt">
                    <span>DATE: <span class="dtline">&nbsp;</span></span>
                    <span>TIME: <span class="dtline">&nbsp;</span></span>
                </div>
            </div>
        </div>
    </div>

</div><!-- end .page -->

<script>
if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
    window.onload = () => window.print();
}
</script>
</body>
</html>
