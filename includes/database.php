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

$conexion = mysqli_real_connect(
    $mysqli,
    getenv('DB_HOST'),
    getenv('DB_USER'),
    getenv('DB_PASSWORD'),
    getenv('DB_NAME'),
    (int)getenv('DB_PORT'),
    null,
    MYSQLI_CLIENT_SSL
);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

$mysqli->set_charset("utf8mb4");

$db = $mysqli;