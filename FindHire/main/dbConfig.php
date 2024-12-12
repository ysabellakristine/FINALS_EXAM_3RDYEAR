<?php  
session_start();
$host = "localhost";
$user = "root";
$password = "";
$dbname = "FindHire_db";
$dsn = "mysql:host={$host};dbname={$dbname}";

$options = [ 
    // ATTR_ERRMODE = error reporting mode, ERRMODE_EXCEPTION = Throws PDO exceptions.
    // If an error occurs, you get detailed error descriptions. 
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,    
    // ATTR_DEFAULT_FETCH_MODE = fetch mode, FETCH_ASSOC = returns results as an associative array.
    // Associative array returns column names as array keys. Removes the need to specify fetch mode with every query.
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // ATTR_EMULATE_PREPARES = option that checks whether PDO uses emulated prepared statements or native prepared statements.
    // false = disables emulation; native prepared statements = handled directly by the database, safer than emulated ones.
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try { 

    $pdo = new PDO("mysql:host=$host", $user, $password, $options); // Connect to the server, not the database yet
    $pdo->exec("SET time_zone = '+08:00';");                                               // Sets timezone for database

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");   // Create the database if it doesn't exist
    $pdo->exec("USE `$dbname`");                             // Select the database
  
    if (file_exists('schema.sql')) {
        $schema = file_get_contents('schema.sql');  // Reads content of the schema.sql file
        $pdo->exec($schema);                      // Executes SQL statements to create tables
        $dbCreation_Message = "Ayos na tables natin.<br>";                     // Prints statement once completed
    } else {
        $dbCreation_Message = "Schema file not found.<br>";
    }

} catch (PDOException $e) {
    $dbconnection_Message = "Data insertion failed: " . $e->getMessage();          // Error handling
}
