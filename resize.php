<?php
require_once './vendor/autoload.php';

use Grafika\Grafika; // Import package

function resize($path, $filename, $destination, $size, $mode)
{
    $editor = Grafika::createEditor();
    $newPath = $destination . DIRECTORY_SEPARATOR . $filename;
    if ($editor->isAvailable()) { // Safety check
        $editor->open($newPhoto, $path);
        if ($mode === "w") $editor->resizeExactWidth($newPhoto, $size);
        if ($mode === "h") $editor->resizeExactHeight($newPhoto, $size);
        $editor->save($newPhoto, $newPath);
    }
    return "https://" . $_SERVER['SERVER_NAME'] ."/". $destination . "/". $filename; 
}

function resizeall($path, $filename)
{
    $paths = ["original" => "https://" . $_SERVER['SERVER_NAME'] ."/". $filename];
    $paths["preview"] = resize($path, $filename, 'preview', 40, 'h');
    $paths["h400"] = resize($path, $filename, '400h', 400, 'h');
    $paths["w400"] =  resize($path, $filename, '400w', 400, 'w');
    $paths["h1080"] =  resize($path, $filename, '1080h', 1080, 'h');
    $paths["w1080"] =  resize($path, $filename, '1080w', 1080, 'w');
    return $paths;

}
