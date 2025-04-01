<?php
    include "db.php";

    function verification_input_task($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = strip_tags($data);
        
        return $data;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $task_content = verification_input_task($_POST["task_content"]);
        $get_collection_unique_id = $_POST["get_collection_unique_id"];

        if (isset($get_collection_unique_id)) {
            $request = $database->prepare("INSERT INTO tasks (id, content, collection_id, completed) VALUES (0, :content, :collection_id, :completed)");
            $request->execute(
                array(
                    "content" => ucfirst($task_content),
                    "collection_id" => $get_collection_unique_id,
                    "completed" => false
                )
            );

            if ($request->rowCount() > 0) {
                header("location: ../index.php?id=$get_collection_unique_id");
                exit();
            } else {
                echo "Erreur lors de l'ajout d'une tache";
            }
        } else {
            echo "ID de la collection non trouvé";
        }
    }
?>