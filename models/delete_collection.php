<?php
    include "db.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $collection_id = $_POST["get_collection_unique_id"];

        $request = $database->query("DELETE collections, tasks FROM collections LEFT JOIN tasks ON collections.unique_id = tasks.collection_id WHERE collections.unique_id = '$collection_id'");
        $request->execute();
        header("location: ../index.php");
    }
?>