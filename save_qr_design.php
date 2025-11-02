<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!empty($data['image'])) {
        $image = $data['image'];
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $decoded = base64_decode($image);
        $path = 'images/qr1.png';
        if (file_put_contents($path, $decoded)) {
            echo json_encode(['success' => true]);
            exit;
        }
    }
}
echo json_encode(['success' => false]);
