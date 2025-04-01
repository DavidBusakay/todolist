<?php
    function get_time_for_message($time) {
        if ($time > "04:59:59" && $time < "06:00:00") {
            echo "C'est encore l'aube <span class='fs-4 emoji'>🌠</span>";
        } elseif ($time->format("Y-m-d") > "05:59:59" && $time->format("Y-m-d") < "08:30:00") {
            echo "C'est le matin <span class='fs-4 emoji'>😁</span>";
        } elseif ($time->format("Y-m-d") > "08:29:59" && $time->format("Y-m-d") < "12:00:00") {
            echo "Bonne avant-midi <span class='fs-4 emoji'>😋</span>";
        } elseif ($time->format("Y-m-d") > "11:59:59" && $time->format("Y-m-d") < "13:00:00") {
            echo "Il est midi <span class='fs-4 emoji'>🌞</span>";
        } elseif ($time->format("Y-m-d") > "12:59:59" && $time->format("Y-m-d") < "17:00:00") {
            echo "Bonne après-midi <span class='fs-4 emoji'>🤗</span>";
        } elseif ($time->format("Y-m-d") > "16:59:59" && $time->format("Y-m-d") < "17:51:00") {
            echo "Ne rate pas le coucher du soleil<span class='fs-4 emoji'>🌇</span>";
        } elseif ($time->format("Y-m-d") > "17:50:59" && $time->format("Y-m-d") < "21:30:00") {
            echo "Bonne soirée <span class='fs-4 emoji'>😊</span>";
        } else {
            echo "Il fait nuit <span class='fs-4 emoji'>😴</span>";
        }
    }
?>