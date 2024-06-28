<?php

// change the configuration to your database in db.php
require_once "db.php";

$tablename = "contacts";

// drop table 'contacts' if it exists
$drop = "DROP TABLE IF EXISTS " . $tablename;

if ($conn->query($drop) === TRUE) {
    echo "Table " . $tablename . " dropped successfully\n";
} else {
    echo "Error dropping table: " . $conn->error . "\n";
}

// create table
$create = "CREATE TABLE IF NOT EXISTS " . $tablename . " (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(45) NOT NULL,
    email VARCHAR(45) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($create) === TRUE) {
    echo "Table " . $tablename . " created successfully\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}

// insert data
$sql = "INSERT INTO " . $tablename . " (name, email, phone) VALUES ('John Doe', 'johndoe@gmail.com', '081234567890');";
$sql .= "INSERT INTO " . $tablename . " (name, email, phone) VALUES ('Jane Doe', 'janedoe@gmail.com', '081234567891');";
$sql .= "INSERT INTO " . $tablename . " (name, email, phone) VALUES ('John Smith', 'johnsmith@gmail.com', '081234567892');";

if ($conn->multi_query($sql) === TRUE) {
    echo "Data inserted successfully\n";
} else {
    echo "Error inserting data: " . $conn->error . "\n";
}

?>
