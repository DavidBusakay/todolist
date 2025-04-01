<?php
    function count_all_tasks($db, $collection_unique_id) {
        $request = $db->query("SELECT * FROM tasks WHERE collection_id = '$collection_unique_id'");
        $request->execute();
        $number = $request->rowCount();

        return $number;
    }

    function count_later_tasks($db, $collection_unique_id) {
        $request = $db->query("SELECT * FROM tasks WHERE collection_id = '$collection_unique_id' AND completed = 0");
        $request->execute();
        $number = $request->rowCount();

        return $number;
    }

    function count_completed_tasks($db, $collection_unique_id) {
        $request = $db->query("SELECT * FROM tasks WHERE collection_id = '$collection_unique_id' AND completed = 1");
        $request->execute();
        $number = $request->rowCount();

        return $number;
    }
?>