<?php

use src\Controllers\Admin\AuthController;
use src\Controllers\Admin\ImageController;
use src\Controllers\Admin\PostController as PostControllerAdmin;
use src\Controllers\Client\CategoryController;
use src\Controllers\Client\CommentController;
use src\Controllers\Client\PostController;
use src\Controllers\Admin\CategoryController as CategoryControllerAdmin;

$router = new Bramus\Router\Router();

$router->get('/', function () {
});

// PostRouter
$router->get('/getPostByKeyword/{keyword}/{page}', PostController::class . '@getPostByKeyword');
$router->get('/getPostByKeyword/{keyword}', PostController::class . '@getPostByKeyword');
$router->get('/getPostByCategory/{keyword}/{page}', PostController::class . '@getPostByCategory');
$router->get('/getPostByCategory/{keyword}', PostController::class . '@getPostByCategory');
$router->get('/trendingPosts/{limit}', PostController::class . '@getTrendingPosts');
$router->get('/trendingPosts', PostController::class . '@getTrendingPosts');
$router->get('/popularPosts/{limit}', PostController::class . '@getPopularPosts');
$router->get('/popularPosts', PostController::class . '@getPopularPosts');
$router->get('/posts/{limit}', PostController::class . '@getPosts');
$router->get('/posts', PostController::class . '@getPosts');
$router->get('/post/{slug}', PostController::class . '@getPost');

// CategoryRouter
$router->get('/categories', CategoryController::class . '@getCategories');
$router->get('/category/{slug}', CategoryController::class . '@getCategory');


// CommentRouter
$router->get('/getCommentsOfThePost/{slug}', CommentController::class . '@getCommentOfThePost');
$router->post('postComment', CommentController::class . '@postComment');

// Auth
$router->mount('/auth', function () use ($router) {
    $router->post('/login', AuthController::class . '@login');
});


//$router->before('GET|POST|PUT|DELETE','/admin/.*', AuthController::class . '@interceptor');
$router->mount('/admin', function () use ($router) {
    // Upload file

    $router->mount('/images', function () use ($router) {
        $router->get('/getFolderImage/{path}', ImageController::class . '@getFolderImage');
        $router->get('/getFolderImage', ImageController::class . '@getFolderImage');
        $router->get('/newFolder/{folderPath}', ImageController::class . '@newFolder');
        $router->get('/deleteFolder/{dirPath}', ImageController::class . '@deleteFolder');
        $router->get('/deleteFile/{filePath}', ImageController::class . '@deleteFile');
        $router->post('/rename', ImageController::class . '@rename');
        $router->all('/uploadImages', ImageController::class . '@uploadImages');
    });

    $router->mount('/post', function () use ($router) {
        $router->post('/addPost', PostControllerAdmin::class . '@addPost');
        $router->put('/editPost', PostControllerAdmin::class . '@editPost');
        $router->delete('/deletePosts/{id}', PostControllerAdmin::class . '@deletePosts');
        $router->get('/viewStatistics', PostControllerAdmin::class . '@viewStatistics');
    });

    $router->mount('/category', function () use ($router) {
        $router->post('/addCategory', CategoryControllerAdmin::class . '@addCategory');
        $router->put('/editCategory', CategoryControllerAdmin::class . '@editCategory');
        $router->delete('/deleteCategories/{id}', CategoryControllerAdmin::class . '@deleteCategories');
    });
});



$router->set404(function () {
    header('HTTP/1.1 404 Not Found');
    header('Content-Type: application/json');

    $jsonArray = array();
    $jsonArray['status'] = "404";
    $jsonArray['status_text'] = "route not defined";

    echo json_encode($jsonArray);
});


$router->run();