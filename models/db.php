<?php
    try {
        $database = new PDO("mysql::host=localhost; dbname=todolist", "root", "");
        $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Une erreur s'est produite : " .$e->getMessage();
    }
?>