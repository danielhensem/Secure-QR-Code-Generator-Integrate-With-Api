<?php
session_start();
include "../componet/conn.php";

header('Content-Type: application/json');

$range = $_POST['range'] ?? 'all';

/**
 * Returns array of rows: [ { period: "...", total: N, sort: "..." }, ... ]
 */
function countWithGrouping($con, $table, $dateColumn, $range) {
    $results = [];

    if ($range === 'weekly') {
        $query = "
            SELECT period, total, sort
            FROM (
                SELECT
                    YEARWEEK($dateColumn, 1) AS sort,
                    DATE_FORMAT(MIN($dateColumn), 'Week %u (%Y)') AS period,
                    COUNT(*) AS total
                FROM $table
                WHERE $dateColumn IS NOT NULL
                GROUP BY YEARWEEK($dateColumn, 1)
                ORDER BY sort DESC
                LIMIT 8
            ) AS recent
            ORDER BY sort ASC
        ";
    } elseif ($range === 'monthly') {
        $query = "
            SELECT period, total, sort
            FROM (
                SELECT
                    CONCAT(YEAR($dateColumn), '-', LPAD(MONTH($dateColumn),2,'0')) AS sort,
                    DATE_FORMAT(MIN($dateColumn), '%M %Y') AS period,
                    COUNT(*) AS total
                FROM $table
                WHERE $dateColumn IS NOT NULL
                GROUP BY YEAR($dateColumn), MONTH($dateColumn)
                ORDER BY sort DESC
                LIMIT 12
            ) AS recent
            ORDER BY sort ASC
        ";
    } else {
        $query = "
            SELECT DATE_FORMAT(d.day, '%Y-%m-%d') AS sort,
                   DATE_FORMAT(d.day, '%d %b') AS period,
                   IFNULL(t.total, 0) AS total
            FROM (
                SELECT DATE_ADD(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL (a.n + b.n*10) DAY) AS day
                FROM (
                    SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
                    UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9
                ) a
                CROSS JOIN (
                    SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3
                ) b
                WHERE (a.n + b.n*10) < DAY(LAST_DAY(CURDATE()))
            ) d
            LEFT JOIN (
                SELECT DATE($dateColumn) AS day, COUNT(*) AS total
                FROM $table
                WHERE $dateColumn IS NOT NULL
                  AND YEAR($dateColumn) = YEAR(CURDATE())
                  AND MONTH($dateColumn) = MONTH(CURDATE())
                GROUP BY DATE($dateColumn)
            ) t ON d.day = t.day
            ORDER BY d.day ASC
        ";
    }

    $res = mysqli_query($con, $query);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $results[] = [
                "period" => $row["period"],
                "total"  => (int)$row["total"],
                "sort"   => (string)$row["sort"],
            ];
        }
        mysqli_free_result($res);
    }

    return $results;
}

/**
 * Aggregate IP usage into country counts
 */
function getIPStats($con, $range) {
    $results = [];

    // Adjust query per range like above
    $where = "1";
    if ($range === 'weekly') {
        $where = "YEARWEEK(timestamp,1) = YEARWEEK(CURDATE(),1)";
    } elseif ($range === 'monthly') {
        $where = "YEAR(timestamp) = YEAR(CURDATE()) AND MONTH(timestamp) = MONTH(CURDATE())";
    }

    $query = "
        SELECT ip_address, COUNT(*) as total
        FROM accessrecord
        WHERE ip_address IS NOT NULL AND $where
        GROUP BY ip_address
        ORDER BY total DESC
        LIMIT 50
    ";

    $res = mysqli_query($con, $query);
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            // 👉 Optionally resolve IP → location using external API
            $results[] = [
                "ip"    => $row["ip_address"],
                "total" => (int)$row["total"],
                "country" => "Unknown" // replace with API lookup if available
            ];
        }
        mysqli_free_result($res);
    }

    return $results;
}

$data = [
    "Users"      => countWithGrouping($con, "users", "timestamp", $range),
    "Generated"  => countWithGrouping($con, "qr_security", "created_at", $range),
    "Encrypted"  => countWithGrouping($con, "qr_secondlayer", "created_at", $range),
    "Shared"     => countWithGrouping($con, "qr_shares", "shared_at", $range),
    "Accessed"   => countWithGrouping($con, "accessrecord", "timestamp", $range),
    "IPStats"    => getIPStats($con, $range)
];

echo json_encode($data);
