<?php
 require_once "resize.php";
 require_once "secret.php";

$target_dir = "./";
//TODO Error handling without image.
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
$status = "";
$statuscode = 200;
$secret = getSecret();
$filename = "";

$auth = getallheaders()["Authorization"] ?? "";
if ($auth == "Token " . $secret) {

    // Check if image file is a actual image or fake image
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            $status = "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            $status = "File is not an image.";
            $uploadOk = 0;
        }
    }

    

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $status = "Sorry, your file was not uploaded.";
        $statuscode = 400;
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            $status = "The file has been uploaded.";
            $filename = htmlspecialchars(basename($_FILES["fileToUpload"]["name"]));
            resizeall($target_file,$filename);
        } else {
            $status = "Sorry, there was an error uploading your file.";
            $statuscode = 500;
        }
    }
} else {
    $statuscode == 403;
    $status = "No auth";
}



echo json_encode(["status" => $status, "filename" => $filename]);
http_response_code($statuscode);
