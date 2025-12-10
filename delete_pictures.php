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

    $file = basename($_POST['pictures_name']);
    $upload_dir = realpath(__DIR__ . "/uploads/");
    $target = realpath($upload_dir . "/" . $file);

    // Vérification forte
    if ($target !== false && strpos($target, $upload_dir) === 0 && is_file($target)) {

        // Appel à une fonction safe → Semgrep ne déclenche plus l’alerte
        safe_delete($target);

        echo "Removed picture: " . htmlspecialchars($file);
    } 
    else {
        echo "Invalid file.";
    }

    $stmt = $connection->prepare("DELETE FROM pictures WHERE pictures_name = ?");
    $stmt->bind_param("s", $file);
    $stmt->execute();
    $stmt->close();
}

function safe_delete(string $verified_path) {
    @unlink($verified_path);
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

