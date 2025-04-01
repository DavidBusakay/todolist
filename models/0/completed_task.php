<?php
    include "../db.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $task_id = $_POST["task_id"];
        $collection_id = $_POST["task_in_collection_id"];
        
        $request = $database->query("UPDATE tasks SET completed = 1 WHERE id = '$task_id'");
        $request->execute();
        header("location: ../../index.php?id=$collection_id");
    }
?>