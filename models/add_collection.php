<?php
    include "db.php";

    function verification_input_collection($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = strip_tags($data);
        
        return $data;
    }

    function get_unique_id() {
        $min = pow(10, 7); // 10000000
        $max = pow(10, 8) - 1; // 99999999
        $random_int = random_int($min, $max);
        
        return $random_int;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $collection_name = verification_input_collection($_POST["collection_name"]);
        $collection_id = get_unique_id();

        // Requete pour vérifier si le nom est déjà dans la table "collections"
        $request = $database->prepare("SELECT * FROM collections WHERE name = :name");
        $request->execute(
            array(
                "name" => $collection_name
            )
        );

        if ($request->rowCount() > 0) {
            header("location: ../index.php?id=$collection_id");
            exit();
        } else {
            $req = $database->prepare("INSERT INTO collections (id, unique_id, name) VALUES (0, :unique_id, :name)");
            $req->execute(
                array(
                    "unique_id" => $collection_id,
                    "name" => ucfirst($collection_name)
                )
            );

            if ($req->rowCount() > 0) {
                header("location: ../index.php?id=$collection_id");
                exit();
            } else {
                echo "Erreur lors de l'ajout de la collection";
            }
        }
    }
?>