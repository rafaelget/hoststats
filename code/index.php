<?php
$auth = false;

function authenticate($user, $pass) {
    $users = [
        'username' => 'password'
    ];
    if (isset($users[$user]) && ($users[$user] === $pass)) {
        return true;
    } else {
        return false;
    }
}

if ( $auth && (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']) || !authenticate($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) ) {
    header('WWW-Authenticate: Basic realm="Please Login"');
    header('Content-Type: application/json; charset=utf-8');
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    $response = [
        'status'    => 0,
        'message'   => 'Invalid credentials',
    ];

    http_response_code(401);
    echo json_encode($response);
    exit;
}

$path = '../count/containers.csv';
$error = null;
if ( file_exists($path) ){
    $lines = explode(PHP_EOL,file_get_contents($path));

    $first = array_shift($lines);
    array_pop($lines);

    $data = explode(',',$first);
    if ( count($data) === 3 ){
        $response = [
            'status'    => 1,
            'cpu'       => intval($data[0]),
            'memory'    => intval($data[1]),
            'disk'      => intval($data[2]),
            'containers_count' => count($lines),
            'containers'=> $lines,
        ];
    } else {
        $error = 'data file is invalid';
    }
} else {
    $error = 'data file not found';
}

if ( ! empty($error) ){
    $response = [
        'status'    => 0,
        'message'   => $error,
    ];
}

header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

echo json_encode($response);