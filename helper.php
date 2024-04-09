<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function logger($var)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}

function timeDiffNow($date)
{
    $now = new DateTime();
    $targetDate = new DateTime($date);
    $interval = $now->diff($targetDate);

    if ($interval->y > 0) {
        return $interval->y . ' năm trước';
    } else if ($interval->m > 0) {
        return $interval->m . ' tháng trước';
    } else if ($interval->d > 0) {
        return $interval->d . ' ngày trước';
    } else if ($interval->h > 0) {
        return $interval->h . ' giờ trước';
    } else if ($interval->i > 0) {
        return $interval->i . ' phút trước';
    } else {
        return $interval->s . ' giây trước';
    }
}

function convert_date_format($date)
{
    $date = DateTime::createFromFormat('Y-m-d', explode(' ', $date)[0]);

    // Chuyển đổi ngày từ cơ sở dữ liệu thành định dạng mới
    $new_format = $date->format('M j');

    // Thêm hậu tố cho ngày
    $day = $date->format('j');
    if ($day >= 4 && $day <= 20 || $day >= 24 && $day <= 30) {
        $suffix = "TH";
    } else {
        $suffix = ["ST", "ND", "RD"][$day % 10 - 1];
    }

    // Thêm năm
    $year = $date->format("'y");

    return strtoupper($new_format) . $suffix . $year;
}


function uploadImages($files, $uploadPath)
{
    $errors = [];
    $allowedExtensions = ["jpeg", "jpg", "png", "gif", "webp", "jfif"];
    $maxFileSize = 30 * 1024 * 1024; // 2MB
    $uploadedFiles = [];

    foreach ($files['tmp_name'] as $key => $tmpName) {
        $fileExtension = strtolower(pathinfo($files['name'][$key], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors[] = "Chỉ hỗ trợ upload file JPEG hoặc PNG.";
        }

        if ($files['size'][$key] > $maxFileSize) {
            $errors[] = 'Kích thước file không được lớn hơn 2MB.';
        }

        if (empty($errors)) {
            $newFileName = uniqid() . '.' . $fileExtension;
            if (move_uploaded_file($tmpName,  $uploadPath . '/' . $newFileName)) {
                $uploadedFiles[] = $newFileName;
            } else {
                $errors[] = "Có lỗi xảy ra khi upload file " . $uploadPath;
            }
        }
    }

    if (!empty($errors)) {
        return $errors;
    }

    return $uploadedFiles;
}

function deleteFolder($dirPath)
{
    if (!is_dir($dirPath)) {
        echo json_encode([
            'message' => "$dirPath is not a folder"
        ]);
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteFolder($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
    echo json_encode([
        'message' => 'Thao tác thành công',
        'dirPath' => $dirPath,
        'files' => $files
    ]);
}

function deleteFile($filePath)
{
    if (file_exists($filePath)) {
        unlink($filePath);
        echo json_encode([
            'message' => 'Thao tác Thành công'
        ]);
        return true;
    } else {
        echo json_encode([
            'message' => "$filePath không tồn tại"
        ]);
        return false;
    }
}

function createDirectory($dirPath, $permissions = 0755)
{
    // Kiểm tra xem thư mục đã tồn tại hay chưa
    if (!file_exists($dirPath)) {
        // Tạo thư mục với quyền hạn được chỉ định
        mkdir($dirPath, $permissions);
        echo json_encode([
            'message' => "Thư mục '$dirPath' đã được tạo."
        ]);
        http_response_code(200);
    } else {
        echo json_encode([
            'message' => "Thư mục '$dirPath' Đã tồn tại."
        ]);
    }
}

function rename_item($current, $new)
{
    if (rename($current, $new)) {
        echo json_encode([
            'message' => "Thao tác thành công."
        ]);
    } else {
        echo json_encode([
            'message' => "Thao tác thất bại."
        ]);
    }
}

function cors()
{

    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
        // you want to allow, and if so:
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            // may also be using PUT, PATCH, HEAD etc
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }

}

function veryfyToken($token)
{
    try {
        $decoded = JWT::decode($token, new Key($_ENV['JWT_KEY'], 'HS256'));
        http_response_code(200);
    } catch (\Exception $e) {
        http_response_code(401);
        echo json_encode([
            'message' => 'Unauthorized',
            'error' => $e->getMessage(),
        ]);
        exit(0);
    }
}

