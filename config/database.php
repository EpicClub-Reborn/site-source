<?php
// Create MySQLi connection
if (!function_exists('getDatabaseConnection')) {
    function getDatabaseConnection(): mysqli {
        $config = [
            'host' => 'localhost',
            'database' => 'u571375065_epicclubdb',
            'username' => 'u571375065_epicclubusr',
            'password' => 'v@BX~r1I7', // This password is changed anyways, goodluck.
        ];
        
        $conn = new mysqli(
            $config['host'],
            $config['username'],
            $config['password'],
            $config['database']
        );

        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }

        return $conn;
    }
}
?>
