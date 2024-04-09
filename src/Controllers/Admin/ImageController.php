<?php

namespace src\Controllers\Admin;

class ImageController
{
    function getFolderImage($path = '')
    {
        $files = glob("uploads/images/" . ($path ? "$path/" : "") . "*");
        echo json_encode($files);
    }

    function newFolder($folderPath)
    {
        $folderPath = "uploads/images/$folderPath";
        $folderPath = preg_replace('/\.\.\//', '', $folderPath);
        createDirectory($folderPath);
    }

    function rename()
    {
        $_REQ = json_decode(file_get_contents('php://input', true));


        $current = "uploads/images/$_REQ->currentName";
        $new = "uploads/images/$_REQ->newName";


        rename_item(trim($current),trim($new));
    }

    function deleteFile($filePath)
    {
        $filePath = "uploads/images/$filePath";
        $filePath = preg_replace('/\.\.\//', '', $filePath);
        deleteFile($filePath);
    }

    function deleteFolder($folderPath)
    {
        $folderPath = "uploads/images/$folderPath";
        $folderPath = preg_replace('/\.\.\//', '', $folderPath);
        deleteFolder($folderPath);
    }

    function uploadImages()
    {
        $uploadPath = $_ENV['ROOT_PATH']."uploads/images/".$_GET['path'];
        echo json_encode(uploadImages($_FILES['images'],$uploadPath));
    }

}