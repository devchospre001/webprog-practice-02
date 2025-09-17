<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, Authentication");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require "../vendor/autoload.php";
require "./services/ExamService.php";

Flight::register('examService', 'ExamService');

require 'routes/ExamRoutes.php';

Flight::start();
?>