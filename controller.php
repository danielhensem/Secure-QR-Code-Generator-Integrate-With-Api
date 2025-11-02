



<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'save_qr') {
    // Clean output buffering to avoid HTML junk in response
    while (ob_get_level()) {
        ob_end_clean();
    }

    header('Content-Type: application/json');
    ini_set('display_errors', 0); // DO NOT show errors as HTML
    error_reporting(E_ALL);

    $response = ['success' => false, 'stage' => 'init'];

    try {
        // Load QR library
        $qrLibPath = 'phpqrcode/qrlib.php';
        if (!file_exists($qrLibPath)) {
            throw new RuntimeException("QR library not found at $qrLibPath", 500);
        }
        require_once $qrLibPath;

        // Validate input
        $qr_data = $_POST['item_id'] ?? '';
        $username = $_POST['username'] ?? 'anonymous';

        if (empty($qr_data)) {
            throw new RuntimeException("Text content is required", 400);
        }

        // QR generation parameters
        $ecc = 'H';
        $pixel_size = 10;
        $frame_size = 5;

        // Directory for QR code
        $qrDir = 'qr_temp';
        if (!file_exists($qrDir)) mkdir($qrDir, 0755, true);
        $qr_id = uniqid('qr_', true);
        $qr_path = "$qrDir/{$qr_id}.png";

        // Generate QR Code
        QRcode::png($qr_data, $qr_path, $ecc, $pixel_size, $frame_size);

        if (!file_exists($qr_path)) {
            throw new RuntimeException("QR code image was not generated", 500);
        }

        // Save to DB
        require_once 'componet/conn.php';
        if (!isset($con) || !($con instanceof mysqli)) {
            throw new RuntimeException("Database connection failed", 500);
        }

        $hash = hash('sha256', $qr_data);
        $qr_image = base64_encode(file_get_contents($qr_path));
        $filename = "text_input.txt";

        $stmt = $con->prepare("INSERT INTO qr_code (qr_code_image, hash_number, status, uploaded_filename, username)
                               VALUES (?, ?, 'generated', ?, ?)");
        if (!$stmt) throw new RuntimeException("Database error: " . $con->error);

        $stmt->bind_param("ssss", $qr_image, $hash, $filename, $username);
        $stmt->execute();

        $response = [
            'success' => true,
            'message' => 'QR code generated successfully',
            'qr_id' => $qr_id,
            'file_path' => $qr_path
        ];
    } catch (Throwable $e) {
        $response = [
            'success' => false,
            'message' => $e->getMessage(),
            'code' => $e->getCode() ?: 500,
            'stage' => $response['stage']
        ];
    }

    echo json_encode($response);
    exit;
}
