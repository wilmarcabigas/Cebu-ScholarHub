<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Billing — <?= esc($batch['semester']) ?> <?= esc($batch['school_year']) ?></title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: Calibri, Arial, sans-serif;
    font-size: 11pt;
    color: #000;
    background: #fff;
    padding: 20px 30px;
  }
  .header-top {
    text-align: center;
    margin-bottom: 4px;
    font-size: 11pt;
  }
  .header-title {
    text-align: center;
    font-size: 13pt;
    font-weight: bold;
    margin-bottom: 2px;
  }
  .header-subtitle {
    text-align: center;
    font-size: 11pt;
    font-weight: bold;
    margin-bottom: 14px;
  }
  .batch-label {
    text-align: center;
    font-weight: bold;
    font-size: 11pt;
    background: #1f4e79;
    color: #fff;
    padding: 5px 0;
    margin-bottom: 0;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 10pt;
  }
  thead th {
    border: 1px solid #000;
    padding: 4px 6px;
    text-align: left;
    background: #dce6f1;
    font-weight: bold;
    font-size: 9.5pt;
    white-space: nowrap;
  }
  tbody td {
    border: 1px solid #999;
    padding: 3px 6px;
    vertical-align: middle;
  }
  tbody tr:nth-child(even) { background: #f2f7ff; }
  tfoot td {
    border: 1px solid #000;
    padding: 5px 6px;
    font-weight: bold;
  }
  .text-right { text-align: right; }
  .text-center { text-align: center; }
  .footer-sig {
    margin-top: 40px;
    display: flex;
    justify-content: space-between;
  }
  .sig-block { text-align: center; width: 30%; }
  .sig-line {
    border-bottom: 1px solid #000;
    margin-bottom: 4px;
    height: 36px;
  }
  .sig-label { font-size: 9pt; }
  @media print {
    body { padding: 10px 15px; }
    .no-print { display: none; }
  }
</style>
</head>
<body>

<div class="no-print" style="margin-bottom:16px;">
  <button onclick="window.print()"
          style="background:#1f4e79;color:#fff;border:none;padding:8px 20px;border-radius:4px;cursor:pointer;font-size:13px;">
    Print / Save as PDF
  </button>
  <button onclick="window.close()"
          style="margin-left:8px;border:1px solid #ccc;padding:8px 20px;border-radius:4px;cursor:pointer;font-size:13px;">
    Close
  </button>
</div>

<div class="header-top">OFFICE OF ADMISSIONS AND SCHOLARSHIPS</div>
<div class="header-title">CEBU CITY GOVERNMENT SCHOLARSHIP PROGRAM</div>
<div class="header-subtitle">
  Billing for the <?= esc($batch['semester']) ?>, SY <?= esc($batch['school_year']) ?>
</div>

<div class="batch-label"><?= esc($batch['batch_label']) ?> — <?= esc($batch['school_name'] ?? '') ?></div>

<table>
  <thead>
    <tr>
      <th class="text-center" style="width:30px;">No.</th>
      <th>ID Num</th>
      <th>Family Name</th>
      <th>First Name</th>
      <th class="text-center" style="width:28px;">MI</th>
      <th>Course</th>
      <th class="text-center" style="width:28px;">Yr</th>
      <th>Control No.</th>
      <th>School</th>
      <th>Address / Barangay</th>
      <th class="text-right" style="width:90px;">Amount</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($items as $i => $item): ?>
      <tr>
        <td class="text-center"><?= $i + 1 ?></td>
        <td><?= esc($item['id_num'] ?? '—') ?></td>
        <td><?= esc($item['last_name'] ?? '—') ?></td>
        <td><?= esc($item['first_name'] ?? '—') ?></td>
        <td class="text-center"><?= esc($item['middle_name'][0] ?? '—') ?></td>
        <td><?= esc($item['course'] ?? '—') ?></td>
        <td class="text-center"><?= esc($item['year_level'] ?? '—') ?></td>
        <td><?= esc($item['control_no'] ?? $item['scholar_control_no'] ?? '—') ?></td>
        <td><?= esc($item['school_name'] ?? '—') ?></td>
        <td><?= esc($item['address'] ?? $item['barangay'] ?? '—') ?></td>
        <td class="text-right">PHP <?= number_format($item['amount'], 2) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot>
    <tr>
      <td colspan="10" class="text-right">TOTAL:</td>
      <td class="text-right">PHP <?= number_format($batch['total_amount'], 2) ?></td>
    </tr>
  </tfoot>
</table>

<div class="footer-sig">
  <div class="sig-block">
    <div class="sig-line"></div>
    <div class="sig-label">Prepared by</div>
  </div>
  <div class="sig-block">
    <div class="sig-line"></div>
    <div class="sig-label">Noted by</div>
  </div>
  <div class="sig-block">
    <div class="sig-line"></div>
    <div class="sig-label">Approved by</div>
  </div>
</div>

</body>
</html>
