<?php

$mysqli = mysqli_init();

mysqli_ssl_set(
    $mysqli,
    null,
    null,
    __DIR__ . '/ca.pem',
    null,
    null
);

mysqli_real_connect(
    $mysqli,
    $_ENV['DB_HOST'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASSWORD'],
    $_ENV['DB_NAME'],
    $_ENV['DB_PORT'],
    null,
    MYSQLI_CLIENT_SSL
);

$mysqli->set_charset("utf8mb4");

if (!$mysqli) {
    die("Error de conexión: " . mysqli_connect_error());
}