<?php
    function count_all_collections($db) {
        $request = $db->query("SELECT * FROM collections");
        $request->execute();
        $number = $request->rowCount();

        return $number;
    }
?>