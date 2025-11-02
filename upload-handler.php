<?php
// Handle text
if (isset($_POST['submit_text'])) {
    $userText = $_POST['user_text'] ?? '';
    echo "You submitted the following text:<br><pre>$userText</pre>";
}

// Handle PDF upload
elseif (isset($_POST['submit_pdf'])) {
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $pdfName = $_FILES['pdf_file']['name'];
        $tmpName = $_FILES['pdf_file']['tmp_name'];
        move_uploaded_file($tmpName, "uploads/" . $pdfName);
        echo "PDF uploaded successfully: $pdfName";
    } else {
        echo "PDF upload failed.";
    }
}

// Handle image upload
elseif (isset($_POST['submit_image'])) {
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $imgName = $_FILES['image_file']['name'];
        $tmpName = $_FILES['image_file']['tmp_name'];
        move_uploaded_file($tmpName, "uploads/" . $imgName);
        echo "Image uploaded successfully: $imgName";
    } else {
        echo "Image upload failed.";
    }
}
?>
