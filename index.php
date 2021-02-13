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

$auth = $_POST["Authorization"] ?? "";
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
            $paths = resizeall($target_file,$filename);

            

            $photo = $target_file;
            $exif = exif_read_data($photo, 0, true);
            $height = $exif['COMPUTED']['Height'] ?? 300;
            $width = $exif['COMPUTED']['Width'] ?? 200;
            $date = strtotime($exif['EXIF']['DateTimeOriginal']) ?? null;

            $f = $exif['COMPUTED']['ApertureFNumber'] ?? 'none';
            $shutter = $exif['EXIF']['ExposureTime'] ?? 'none';
            $rawLensData = $exif['EXIF']['UndefinedTag:0xA434'] ?? 'none';
            if ($rawLensData === 'DT 0mm F0 SAM') {
                $lens = "Canon EF 70-200 f4L";
            } else if ($rawLensData === 'FE 16-35mm F4 ZA OSS') {
                $lens = "Sony FE 16-35 F4";
            } else if ($rawLensData === "20.7 mm") {
                $lens = "DJI Phantom 3";
            } else {
                $lens = "Minolta 45mm F2";
            }

            $exif = ["height"=>$height,"width"=>$width,"date"=>$date,"shutterspeed"=>$shutter, "lens"=>$lens, "aperture"=>$f];


        } else {
            $status = "Sorry, there was an error uploading your file.";
            $statuscode = 500;
        }
    }
} else {
    $statuscode == 403;
    $status = "No auth";
}

echo json_encode(["status" => $status, "filename" => $filename, "paths" => $paths, "exif"=>$exif]);
http_response_code($statuscode);