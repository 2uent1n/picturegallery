<?php

$page_title = "Delete pictures";

include 'mysqli_connect.php';

include 'includes/header.html';

include 'includes/navbar.html';

if (!isset ($_SESSION ['username'])){
	header('location: index.php');
}
else{

if (isset($_POST['pictures_name'])) {

    // Empêche les chemins type ../../etc/passwd
    $pictures_name = basename($_POST['pictures_name']);

    // Chemin sécurisé vers le fichier
    $path = __DIR__ . "/uploads/" . $pictures_name;

    // Vérifie que le fichier existe ET qu’il est bien dans uploads/
    if (is_file($path) && strpos(realpath($path), realpath(__DIR__ . "/uploads/")) === 0) {

        if (unlink($path)) {
            echo "Removed picture: " . htmlspecialchars($pictures_name) . "<br>";
        } else {
            echo "Error deleting file.";
        }

    } else {
        echo "Invalid file.";
    }

    // Suppression dans la base de données via requête préparée
    $stmt = $connection->prepare("DELETE FROM pictures WHERE pictures_name = ?");
    $stmt->bind_param("s", $pictures_name);
    $stmt->execute();
    $stmt->close();
}

    $sql1 = "SELECT users.users_username, pictures.pictures_name FROM pictures INNER JOIN users ON pictures.id_users = users.users_id";
    $result = mysqli_query ($connection, $sql1) or die (mysqli_error ($connection));
    
    echo "<form action='' method='POST'>";
    echo "<select name='pictures_name'>";
    if (mysqli_num_rows ($result) > 0){
        while ($row = mysqli_fetch_assoc ($result)){
            if($row['users_username'] == $_SESSION['username']){
                echo "<option value='" . $row['pictures_name'] . "'>" . $row['pictures_name'] . "</option>";
            }
        }
    }
    else {
        echo "Error 2";
    }
    echo "</select>";
    echo "<input type='submit' value='Delete picture'>";
    echo "</form>";

    include 'includes/footer.html';

    mysqli_close ($connection);
    unset($connection);
}

?>

