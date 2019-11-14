<?php

require 'config/Patricia.php';
require 'config/Request.php';

// Takes raw data from the request
$json = file_get_contents('php://input');
// Converts it into a PHP object
$_POST = json_decode($json, true);

if (isset($_POST['username']) && isset($_POST['password'])) {

    $username = $patricia->cleanInput($_POST['username']);
    $password = md5($patricia->cleanInput($_POST['password']));

    if (empty($username)){
        echo $request->sendErrorResponse('Please enter a username');
    }
    elseif (empty($password)){
        echo $request->sendErrorResponse('Invalid enter a password');
    }
    else{
        $query = $patricia->dbrow("SELECT * FROM users WHERE username = '$username' AND password = '{$password}'");

        if (empty($query) || is_null($query)) {
            // Send error response
            echo $request->sendErrorResponse('Invalid email or password');
            die;
        }

        echo $request->sendResponse($query, 'success');
        die;

    }
}