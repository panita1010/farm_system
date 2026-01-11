<?php

if ($open_connect != 1) {
    die(header('Location: loginform.php'));
}

$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$port = $_ENV['DB_PORT'];

try {
    $conn = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}


function uploadToSupabase($file, $filename) {

    $url = $_ENV['SUPABASE_URL'] .
        "/storage/v1/object/" .
        $_ENV['SUPABASE_BUCKET'] . "/" . $filename;

    $data = file_get_contents($file['tmp_name']);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer " . $_ENV['SUPABASE_KEY'],
            "Content-Type: " . mime_content_type($file['tmp_name'])
        ],
        CURLOPT_POSTFIELDS => $data
    ]);

    curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status !== 200) return false;

    return $_ENV['SUPABASE_URL'] .
        "/storage/v1/object/public/" .
        $_ENV['SUPABASE_BUCKET'] . "/" . $filename;
}


?>


