<?php
session_start();
require 'componet/conn.php'; // your DB connection

// TCPDF path (adjust if necessary)
require_once __DIR__ . '/TCPDF-main/TCPDF-main/tcpdf.php';

$qr_id = intval($_GET['qr_id'] ?? 0);
$range = $_GET['range'] ?? 'all';
$user_id = $_SESSION['id'] ?? 0;

if (!$qr_id || !$user_id) {
    http_response_code(400);
    echo "Missing QR ID or user not authenticated.";
    exit;
}

// Validate range
$allowed_ranges = ['7', '30', 'all'];
if (!in_array($range, $allowed_ranges)) {
    http_response_code(400);
    echo "Invalid range.";
    exit;
}

// Build SQL condition
$date_condition = "";
if ($range !== 'all') {
    $days = intval($range);
    $date_condition = "AND DATE(ar.timestamp) >= DATE_SUB(CURDATE(), INTERVAL $days DAY)";
}

$sql = "
    SELECT DATE(ar.timestamp) AS access_date, COUNT(*) AS access_count
    FROM accessrecord ar
    INNER JOIN qr_security qs ON qs.id = ar.qr_id
    WHERE ar.qr_id = ? AND qs.user_id = ?
    $date_condition
    GROUP BY DATE(ar.timestamp)
    ORDER BY access_date ASC
";

$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $qr_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Prepare data
$rows = [];
$total = 0;
$max = 0;
$min = PHP_INT_MAX;

while ($row = $result->fetch_assoc()) {
    $count = (int)$row['access_count'];
    $rows[] = $row;
    $total += $count;
    if ($count > $max) $max = $count;
    if ($count < $min) $min = $count;
}

$avg = count($rows) > 0 ? round($total / count($rows), 2) : 0;
if (empty($rows)) $min = 0;

$rangeLabel = ($range === 'all') ? 'All Time' : "Last $range Days";
date_default_timezone_set('Asia/Kuala_Lumpur');
$reportDate = date('Y-m-d H:i:s');

// Start building HTML
$html = "
<h2 style='text-align:center;'>QR Access Report</h2>
<p><strong>QR ID:</strong> $qr_id</p>
<p><strong>Range:</strong> $rangeLabel</p>
<p><strong>Generated On:</strong> $reportDate</p>
<hr>
<h4>📊 Statistics</h4>
<ul>
    <li><strong>Total Accesses:</strong> $total</li>
    <li><strong>Maximum per Day:</strong> $max</li>
    <li><strong>Minimum per Day:</strong> $min</li>
    <li><strong>Average per Day:</strong> $avg</li>
</ul>
<hr>
<h4>📅 Daily Access Count</h4>
<table border='1' cellspacing='0' cellpadding='4' width='100%'>
    <thead>
        <tr style='background-color:#f1f1f1;'>
            <th><strong>Date</strong></th>
            <th><strong>Access Count</strong></th>
        </tr>
    </thead>
    <tbody>";

foreach ($rows as $r) {
    $html .= "<tr><td>{$r['access_date']}</td><td>{$r['access_count']}</td></tr>";
}

$html .= "</tbody></table>
<p style='text-align:center; margin-top:40px;'>© " . date('Y') . " QR System Report Produced By SQ Tech Solver</p>
";

// ==== TCPDF Setup ====
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Meta info
$pdf->SetCreator('QR System');
$pdf->SetAuthor('QR System Report');
$pdf->SetTitle('QR Access Report');
$pdf->SetSubject('QR Access Stats');
$pdf->SetKeywords('QR, access, report');

// Layout
$pdf->SetMargins(15, 15, 15);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 15);

// Font and page
$pdf->SetFont('helvetica', '', 11);
$pdf->AddPage();

// Write content
$pdf->writeHTML($html, true, false, true, false, '');

// Output inline (I = inline, D = download)
$pdf->Output("QR_Access_Report_{$qr_id}.pdf", 'I');
exit;
