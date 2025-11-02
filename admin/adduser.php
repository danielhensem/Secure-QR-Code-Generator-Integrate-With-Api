<?php
include "../componet/conn.php";
date_default_timezone_set('Asia/Kuala_Lumpur');

// Function to generate random phrase
function generateRandomPhrase($length = 40){
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+[]{}<>?';
    $phrase = '';
    for($i=0;$i<$length;$i++){
        $phrase .= $chars[random_int(0, strlen($chars)-1)];
    }
    return $phrase;
}

header('Content-Type: application/json');

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$status = $_POST['status'] ?? 0;

if($name && $email && $password){
    // check email exists
    $stmt = $con->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){
        echo json_encode(['success'=>false,'error'=>'Email already exists']);
        exit();
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $phrase = generateRandomPhrase();
    $timestamp1 = date("Y-m-d H:i:s");
    $insert = $con->prepare("INSERT INTO users (name,password,email,phrase,timestamp,status) VALUES (?,?,?,?,?,?)");
    $insert->bind_param("sssssi",$name,$hashed,$email,$phrase,$timestamp1,$status);

    if($insert->execute()){
        $last_id = $con->insert_id;

        // Optionally insert default friend
        $request_id = 61;
        $status_friend = 1;
        $timestamp = date("Y-m-d H:i:s");
        $friendInsert = $con->prepare("INSERT INTO friends (user_id, request_id, status, timestamp) VALUES (?,?,?,?)");
        $friendInsert->bind_param("iiis",$last_id,$request_id,$status_friend,$timestamp);
        $friendInsert->execute();

        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['success'=>false,'error'=>$insert->error]);
    }
} else {
    echo json_encode(['success'=>false,'error'=>'All fields are required']);
}
?>
