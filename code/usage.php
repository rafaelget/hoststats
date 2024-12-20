<?php
header('Access-Control-Allow-Headers: *');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$auth = getenv('AUTH_LOGIN') ?? false;

function authenticate($user, $pass) {
    $username = getenv('AUTH_LOGIN') ?? 'username';
    $password = getenv('AUTH_PASSWORD') ?? 'password';
    $users = [
        $username => $password
    ];
    if (isset($users[$user]) && ($users[$user] === $pass)) {
        return true;
    } else {
        return false;
    }
}

if ( $auth && (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']) || !authenticate($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) ) {
    header('WWW-Authenticate: Basic realm="Please Login"');

    $response = [
        'status'    => 0,
        'message'   => 'Invalid credentials',
    ];

    http_response_code(401);
    echo json_encode($response);
    exit;
}

$path = '../count/usage.json';
$error = null;
if ( file_exists($path) ){
    $json = file_get_contents($path);

    $lines = explode("\n", $json);

    $containers = [];

    foreach ($lines as $line) {
        $data = json_decode($line, true);
        if ( empty($data['MemUsage']) ) {
            continue;
        }

        $mem = $data['MemUsage'];

        $parts = explode(' ', $mem);
        $mem = $parts[0];
        $giga = strpos($mem, 'GiB') !== false;
        $mem = str_replace(['GiB','MiB'], '', $mem);
        if ( $giga ) {
            $mem = $mem * 1000;
        }

        $name = explode('.', $data['Name']);
        array_pop($name);

        $temp = [
            'name'  => join('.',$name),
            'cpu'   => floatval(str_replace('%','',$data['CPUPerc'])),
            'mem'   => floatval($mem),
        ];

        $containers[] = $temp;
    }

    $response = [
        'status'        => 1,
        'containers'    => $containers,
    ];

} else {
    $error = 'data file not found';
}

if ( ! empty($error) ){
    $response = [
        'status'    => 0,
        'message'   => $error,
    ];
}

echo json_encode($response);