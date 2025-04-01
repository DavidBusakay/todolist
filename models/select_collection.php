<?php
    include "db.php";

    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $collection_unique_id = $_POST["collection_unique_id"];
        $_SESSION["collection_unique_id"] = $collection_unique_id;
        header("location: ../index.php?id=$collection_unique_id");
    }
?>