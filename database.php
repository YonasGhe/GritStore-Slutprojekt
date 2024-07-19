<?php

function getDatabase()
{
    $host = "localhost";
    $port = 3306;
    $database = "project";
    $username = "root";
    $password = "";

    $connection = new mysqli($host, $username, $password, $database, $port);

    if ($connection->connect_error != null) {
        die("Anslutning misslyckades: " . $connection->connect_error);
    } else {
        return $connection;
    }
}
