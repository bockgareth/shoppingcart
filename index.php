<?php
    session_start();
    $_SESSION = [];
    session_destroy();

    $invLocation = "C:/xampp/htdocs/oshop/text/inventory.txt";
    $infoLocation = "C:/xampp/htdocs/oshop/text/store_info.txt";

    $conn = new mysqli('localhost', 'root', '');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 

    $sql = "CREATE DATABASE IF NOT EXISTS store_db";
    if ($conn->query($sql)) {
        echo "Database created successfully";
    } else {
        echo "Error creating database: " . $conn->error;
    }

    $conn->query("USE store_db");

    $sql = "CREATE TABLE IF NOT EXISTS `store_info` (
        `storeID` varchar(10),
        `name` varchar(50),
        `description` varchar(200),
        `welcome` TEXT,
        `css_file` varchar(250),
        `email` varchar(100),
        PRIMARY KEY  (`storeID`)
      )";
    
    $conn->query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `inventory` (
        `storeID` varchar(10),
        `productID` varchar(10),
        `name` varchar(100),
        `description` varchar(200),
        `price` FLOAT,
        PRIMARY KEY  (`productID`)
    )";
    $conn->query($sql);

    $sql = "LOAD DATA INFILE '".$infoLocation."' INTO TABLE store_info";
    $conn->query($sql);

    $sql = "LOAD DATA INFILE '".$invLocation."' INTO TABLE inventory";
    $conn->query($sql);

    

    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="php_styles.css" type="text/css">
    <title>Gosselin Gourmet Goods</title>
</head>
<body>
<h1>Gosselin Gourmet Goods</h1>
<h2>Shop by Category</h2>
<p><a href="gosselin-gourmet-coffee.php">Gourmet Coffees</a><br>
<a href="electronics-boutique.php">Electronics Boutique</a><br>
<a href="old-tyme-antiques.php">Old Tyme Antiques</a></p>
</body>
</html>