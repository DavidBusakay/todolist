<?php
    function get_diff_days($date_of_task) {
        $current_date = new DateTime();
        $target_date = new DateTime($date_of_task);
        $interval = $current_date->diff($target_date);
        $nb_days = $interval->days;

        if ($nb_days == 0) {
            echo "<p class='text-muted mb-2'><small>Aujourd'hui</small></p>";
        } elseif ($nb_days == 1) {
            echo "<p class='text-muted mb-2'><small>Hier</small></p>";
        } elseif ($nb_days == 2) {
            echo "<p class='text-muted mb-2'><small>Avant-hier</small></p>";
        } else {
            echo "<p class='text-muted mb-2'><small>" .$date_of_task. "</small></p>";
        }
    }
?>