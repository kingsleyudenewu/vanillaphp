<?php

require 'config/Patricia.php';
require 'config/Request.php';

// Takes raw data from the request
$json = file_get_contents('php://input');
// Converts it into a PHP object
$_POST = json_decode($json, true);

if (isset($_POST['username']) && isset($_POST['password'])) {

    $name = $patricia->cleanInput($_POST['name']);
    $password = md5($patricia->cleanInput($_POST['password']));
    $username = $patricia->cleanInput($_POST['username']);

    if (empty($username)){
        echo $request->sendErrorResponse('Please enter a username');
    }
    elseif (empty($password)){
        echo $request->sendErrorResponse('Invalid enter a password');
    }
    elseif (empty($name)){
        echo $request->sendErrorResponse('Invalid enter a name');
    }
    else {

        $query = $patricia->dbcountchanges("INSERT INTO users (`name`, `password`, `username`, `priv`) VALUES ('{$name}', '{$password}', '{$username}', '0')");

        if (empty($query) || is_null($query)) {
            // Send error response
            echo $request->sendErrorResponse('Invalid email or password');
            die;
        }

        echo $request->sendResponse(null, 'success');
        die;
    }
}
