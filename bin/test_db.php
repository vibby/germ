<?php
/* Connect to an ODBC database using driver invocation */
$dsn = 'pgsql:dbname=germ;host=127.0.0.1';
$user = 'germ';
$password = 'germinati0n';

try {
    $dbh = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}


