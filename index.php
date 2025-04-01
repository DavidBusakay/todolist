<?php
    include "models/db.php";
    include "models/add_collection.php";
    include "models/add_task.php";
    include "models/0/get_num_collections.php";
    include "models/0/get_num_tasks.php";
    include "models/0/get_time_for_message.php";
    include "models/0/get_diff_days.php";

    date_default_timezone_set("Africa/Kinshasa");

    session_start();

    if (isset($_GET["id"])) {
        $get_collection_id = $_GET["id"];
    } else {
        // On récupère "unique_id" par rapport à l'ID précédent
        $request = $database->query("SELECT * FROM collections ORDER BY id DESC LIMIT 1");
        $request->execute();
        $response = $request->fetch();
        $get_collection_id = $response["unique_id"];
        header("location: index.php?id=$get_collection_id");
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todolist</title>
    <link rel="shortcut icon" href="img/liste-de-taches.png" type="image/x-icon">
    <link rel="stylesheet" href="css/fontawesome/all.min.css">
    <link rel="stylesheet" href="css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="nav mb-4 pt-4 pb-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="img/liste-de-taches.png" alt="Logo" class="logo">
                    <h1 class="display-4 text-bold ms-2 mb-0">Todolist</h1>
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-5">
                        <?php
                            $get_time = new DateTime();
                        ?>
                        <p class="text-bold mb-0 msg"><?php get_time_for_message($get_time); ?></p>
                        <p class="text-bold mb-0">
                            <i class="fa-solid fa-clock"></i> : <span id="currentTime"></span>
                        </p>
                    </div>
                    <button class="btn btn-light px-3" data-bs-toggle="modal" data-bs-target="#modalAbout">
                        <i class="fa-solid fa-info"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div>
                        <p class="mb-2"><span><?php echo count_all_collections($database); ?></span> collection(s)</p>
                    </div>
                    <!-- Liste des collections -->
                    <ul class="overflow-y-scroll ps-0 pe-2 py-2" id="collectionsList" style="max-height: 65vh;"> <!-- 470px -->
                        <?php
                            // On recupère toutes les collections
                            $req = $database->query("SELECT * FROM collections ORDER BY id");
                            $req->execute();
                            $collections = $req->fetchAll(PDO::FETCH_ASSOC);

                            if ($req->rowCount() == 0) { // On ajoute une collection par défaut s'il n'existe aucune collection
                                $r = $database->prepare("INSERT INTO collections (id, unique_id, name) VALUES (0, :unique_id, :name)");
                                $r->execute(
                                    array(
                                        "unique_id" => get_unique_id(),
                                        "name" => "Défaut"
                                    )
                                );
                                header("location: index.php");
                            } else { // On affiche toutes les collections
                                foreach ($collections as $collection) {
                                    $count_tasks_in_collection = count_all_tasks($database, $collection["unique_id"]);

                                    // On vérifie si la collection est sélectionnée en vérifiant le paramètre 'id' dans l'URL pour lui appliquer un style différent
                                    if ($collection["unique_id"] == $get_collection_id) {
                                        ?>
                                            <li class="mb-2">
                                                <form action="models/select_collection.php" method="post">
                                                    <input type="submit" class="btn btn-primary rounded-3 text-start w-100" value="<?php echo $collection["name"]; ?> (<?php echo $count_tasks_in_collection; ?>)">
                                                    <input type="text" name="collection_unique_id" class="form-control" value="<?php echo $collection["unique_id"]; ?>" hidden>
                                                </form>
                                            </li>
                                        <?php
                                    } else {
                                        ?>
                                            <li class="mb-2">
                                                <form action="models/select_collection.php" method="post">
                                                    <input type="submit" class="btn btn-light border-0 rounded-3 text-start text-muted w-100" value="<?php echo $collection["name"]; ?> (<?php echo $count_tasks_in_collection; ?>)">
                                                    <input type="text" name="collection_unique_id" class="form-control" value="<?php echo $collection["unique_id"]; ?>" hidden>
                                                </form>
                                            </li>
                                        <?php
                                    }
                                }
                            }
                        ?>
                    </ul>
                    <form action="models/add_collection.php" method="post" id="formAddCollection">
                        <div class="input-group flex-nowrap mt-3">
                            <input type="text" name="collection_name" class="form-control rounded-3" placeholder="Ajoute une collection" required>
                            <button type="submit" class="btn btn-success rounded-3 ms-2 px-3">
                                <i class="fa-solid fa-add"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-9">
                    <div class="mb-3">
                        <form action="models/add_task.php" method="post">
                            <div class="input-group">
                                <input type="text" name="task_content" class="form-control rounded-3" placeholder="Ex : Faire du sport..." required>
                                <input type="text" name="get_collection_unique_id" class="form-control" value="<?php echo $get_collection_id; ?>" hidden>
                                <input type="submit" name="btn_add_task" value="Ajouter" class="btn btn-success rounded-3 ms-2">
                            </div>
                        </form>
                    </div>
                    <!-- TabList -->
                    <div class="tab-list mb-2">
                        <ul class="nav nav-tabs nav-pills card-header-tabs">
                            <li class="nav-item">
                                <button
                                    class="btn me-2 active"
                                    id="btn-nav-tab"
                                    type="button"
                                    role="tab"
                                    data-bs-toggle="pill"
                                    data-bs-target="#allTasks"
                                    aria-controls="allTasks"
                                    aria-selected="true"
                                >Toutes (<?php echo count_all_tasks($database, $get_collection_id); ?>)</button>
                            </li>
                            <li class="nav-item">
                                <button
                                    class="btn me-2"
                                    id="btn-nav-tab"
                                    type="button"
                                    role="tab"
                                    data-bs-toggle="pill"
                                    data-bs-target="#laterTasks"
                                    aria-controls="laterTasks"
                                    aria-selected="false"
                                >A faire (<?php echo count_later_tasks($database, $get_collection_id); ?>)</button>
                            </li>
                            <li class="nav-item">
                                <button
                                    class="btn"
                                    id="btn-nav-tab"
                                    type="button"
                                    role="tab"
                                    data-bs-toggle="pill"
                                    data-bs-target="#completedTasks"
                                    aria-controls="completedTasks"
                                    aria-selected="false"
                                >Terminées (<?php echo count_completed_tasks($database, $get_collection_id); ?>)</button>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane active show fade" id="allTasks">
                            <!-- Liste de toutes les taches -->
                            <ul class="overflow-y-scroll ps-0 pe-2 pt-0 pb-2" id="tasksList" style="max-height: 58vh;">
                                <?php
                                    $req = $database->query("SELECT * FROM tasks WHERE collection_id = '$get_collection_id' ORDER BY id DESC");
                                    $req->execute();
                                    $tasks = $req->fetchAll(PDO::FETCH_ASSOC);
                                    $grouped_tasks = []; // Tableaux pour grouper les taches par date

                                    if ($req->rowCount() == 0) {
                                        echo "<p class='text-center text-muted mb-0'><small>Commence par ajouter une tâche 😴</small></p>";
                                    } else {
                                        foreach ($tasks as $task) {
                                            $get_date_of_task = explode(" ", $task["created_at"]);
                                            
                                            if (!isset($grouped_tasks[$get_date_of_task[0]])) {
                                                $grouped_tasks[$get_date_of_task[0]] = [];
                                            }
                                            
                                            $grouped_tasks[$get_date_of_task[0]][] = $task;
                                        }
                                        
                                        foreach ($grouped_tasks as $date => $tasks_for_date) {
                                            get_diff_days($date);
                                            foreach ($tasks_for_date as $task) {
                                                // On vérifie si la tache est terminée pour lui appliquer un style différent
                                                if ($task["completed"] == 1) {
                                                    ?>
                                                        <li>
                                                            <div class="d-flex align-items-center justify-content-between mb-3 task">
                                                                <div class="rounded-3 border border-light-subtle w-100 px-3 py-2 task-text-completed">
                                                                    <p class="text-decoration-line-through text-muted mb-0"><?php echo $task["content"]; ?></p>
                                                                </div>
                                                                <form action="models/delete_task.php" method="post">
                                                                    <input type="text" name="task_id" class="form-control" value="<?php echo $task["id"]; ?>" hidden>
                                                                    <input type="text" name="task_in_collection_id" class="form-control" value="<?php echo $task["collection_id"]; ?>" hidden>
                                                                    <button type="submit" class="btn btn-danger ms-2 px-3">
                                                                        <i class="fa-solid fa-trash-can"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </li>
                                                    <?php
                                                } else {
                                                    ?>
                                                        <li>
                                                            <div class="d-flex align-items-center justify-content-between mb-3 task">
                                                                <div class="rounded-3 border border-light-subtle w-100 px-3 py-2 task-text">
                                                                    <p class="mb-0"><?php echo $task["content"]; ?></p>
                                                                </div>
                                                                <form action="models/0/completed_task.php" method="post">
                                                                    <input type="text" name="task_id" class="form-control" value="<?php echo $task["id"]; ?>" hidden>
                                                                    <input type="text" name="task_in_collection_id" class="form-control" value="<?php echo $task["collection_id"]; ?>" hidden>
                                                                    <button type="submit" class="btn btn-success ms-2 px-3">
                                                                        <i class="fa-solid fa-check"></i>
                                                                    </button>
                                                                </form>
                                                                <form action="models/delete_task.php" method="post">
                                                                    <input type="text" name="task_id" class="form-control" value="<?php echo $task["id"]; ?>" hidden>
                                                                    <input type="text" name="task_in_collection_id" class="form-control" value="<?php echo $task["collection_id"]; ?>" hidden>
                                                                    <button type="submit" class="btn btn-danger ms-2 px-3">
                                                                        <i class="fa-solid fa-trash-can"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </li>
                                                    <?php
                                                }
                                            }
                                        }
                                    }
                                ?>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="laterTasks">
                            <!-- Liste des taches à faire -->
                            <ul class="overflow-y-scroll ps-0 pe-2 pt-0 pb-2" id="tasksList" style="max-height: 58vh;">
                                <?php
                                    $req = $database->query("SELECT * FROM tasks WHERE collection_id = '$get_collection_id' AND completed = 0 ORDER BY id DESC");
                                    $req->execute();
                                    $tasks = $req->fetchAll(PDO::FETCH_ASSOC);
                                    $grouped_tasks = []; // Tableaux pour grouper les taches par date

                                    if ($req->rowCount() == 0) {
                                        echo "<p class='text-center text-muted mb-0'><small>Commence par ajouter une tâche 😴</small></p>";
                                    } else {
                                        foreach ($tasks as $task) {
                                            $get_date_of_task = explode(" ", $task["created_at"]);
                                            
                                            if (!isset($grouped_tasks[$get_date_of_task[0]])) {
                                                $grouped_tasks[$get_date_of_task[0]] = [];
                                            }
                                            
                                            $grouped_tasks[$get_date_of_task[0]][] = $task;
                                        }

                                        foreach ($grouped_tasks as $date => $tasks_for_date) {
                                            get_diff_days($date);
                                            foreach ($tasks_for_date as $task) {
                                                ?>
                                                    <li>
                                                        <div class="d-flex align-items-center justify-content-between mb-3 task">
                                                            <div class="rounded-3 border border-light-subtle w-100 px-3 py-2 task-text">
                                                                <p class="mb-0"><?php echo $task["content"]; ?></p>
                                                            </div>
                                                            <form action="models/0/completed_task.php" method="post">
                                                                <input type="text" name="task_id" class="form-control" value="<?php echo $task["id"]; ?>" hidden>
                                                                <input type="text" name="task_in_collection_id" class="form-control" value="<?php echo $task["collection_id"]; ?>" hidden>
                                                                <button type="submit" class="btn btn-success ms-2 px-3">
                                                                    <i class="fa-solid fa-check"></i>
                                                                </button>
                                                            </form>
                                                            <form action="models/delete_task.php" method="post">
                                                                <input type="text" name="task_id" class="form-control" value="<?php echo $task["id"]; ?>" hidden>
                                                                <input type="text" name="task_in_collection_id" class="form-control" value="<?php echo $task["collection_id"]; ?>" hidden>
                                                                <button type="submit" class="btn btn-danger ms-2 px-3">
                                                                    <i class="fa-solid fa-trash-can"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </li>
                                                <?php
                                            }
                                        }
                                    }
                                ?>
                            </ul>
                        </div>
                        <div class="tab-pane fade" id="completedTasks">
                            <!-- Liste des taches terminées -->
                            <ul class="overflow-y-scroll ps-0 pe-2 pt-0 pb-2" id="tasksList" style="max-height: 58vh;">
                                <?php
                                    $req = $database->query("SELECT * FROM tasks WHERE collection_id = '$get_collection_id' AND completed = 1 ORDER BY id DESC");
                                    $req->execute();
                                    $tasks = $req->fetchAll(PDO::FETCH_ASSOC);
                                    $grouped_tasks = []; // Tableaux pour grouper les taches par date

                                    if ($req->rowCount() == 0) {
                                        echo "<p class='text-center text-muted mb-0'><small>Commence par ajouter une tâche 😴</small></p>";
                                    } else {
                                        foreach ($tasks as $task) {
                                            $get_date_of_task = explode(" ", $task["created_at"]);
                                            
                                            if (!isset($grouped_tasks[$get_date_of_task[0]])) {
                                                $grouped_tasks[$get_date_of_task[0]] = [];
                                            }
                                            
                                            $grouped_tasks[$get_date_of_task[0]][] = $task;
                                        }
                                        
                                        foreach ($grouped_tasks as $date => $tasks_for_date) {
                                            get_diff_days($date);
                                            foreach ($tasks_for_date as $task) {
                                                ?>
                                                    <li>
                                                        <div class="d-flex align-items-center justify-content-between mb-3 task">
                                                            <div class="rounded-3 border border-light-subtle w-100 px-3 py-2 task-text">
                                                                <p class="mb-0"><?php echo $task["content"]; ?></p>
                                                            </div>
                                                            <form action="models/delete_task.php" method="post">
                                                                <input type="text" name="task_id" class="form-control" value="<?php echo $task["id"]; ?>" hidden>
                                                                <input type="text" name="task_in_collection_id" class="form-control" value="<?php echo $task["collection_id"]; ?>" hidden>
                                                                <button type="submit" name="btn_delete_task" class="btn btn-danger ms-2 px-3">
                                                                    <i class="fa-solid fa-trash-can"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </li>
                                                <?php
                                            }
                                        }
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalDeleteCollection">Supprimer cette collection</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="modal bg-blur fade" tabindex="-1" id="modalDeleteCollection" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-body">
                <div class="modal-content border-white">
                    <div class="modal-body text-center">
                        <p class="mb-2">Veux-tu vraiment supprimer cette collection ? 😥</p>
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-success me-2" data-bs-dismiss="modal">Non</button>
                            <form action="models/delete_collection.php" method="post">
                                <?php
                                    $req = $database->query("SELECT * FROM collections WHERE unique_id = '$get_collection_id'");
                                    $req->execute();
                                    $res = $req->fetch();
                                ?>
                                <input type="submit" name="btn_delete_collection" value="Oui" class="btn btn-danger rounded-3">
                                <input type="text" name="get_collection_unique_id" class="form-control" value="<?php echo $get_collection_id; ?>" hidden>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal bg-blur fade" tabindex="-1" id="modalAbout" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-body">
                <div class="modal-content border-white">
                    <div class="modal-header justify-content-end border-0 text-end pb-0">
                        <button class="btn btn-light" data-bs-dismiss="modal">
                            <i class="fa-solid fa-close"></i>
                        </button>
                    </div>
                    <div class="modal-body text-center pt-0 pb-1">
                        <h5 class="text-bold">Todolist</h5>
                        <p class="mb-0">Développée par David Busakay 😎</p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <a href="" class="btn btn-success w-100">Voir le code source</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/fontawesome/all.min.js"></script>
    <script src="js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>