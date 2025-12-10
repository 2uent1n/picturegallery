<?php

$page_title = "Delete pictures";

include 'mysqli_connect.php';

include 'includes/csrf.php';

include 'includes/header.html';

include 'includes/navbar.html';

if (!isset ($_SESSION ['username'])){
	header('location: index.php');
}
else{
    if (isset ($_POST['pictures_name'])){
        // Validate CSRF token
        if (!validate_csrf_token()) {
            die('CSRF token validation failed. Please try again.');
        }
        
        $pictures_name = $_POST['pictures_name'];
        
        // Validate filename to prevent path traversal attacks
        // Only allow alphanumeric, dash, underscore, and dot characters
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $pictures_name)) {
            die('Invalid filename. Only alphanumeric characters, dashes, underscores, and dots are allowed.');
        }
        
        // Prevent directory traversal
        if (strpos($pictures_name, '..') !== false || strpos($pictures_name, '/') !== false || strpos($pictures_name, '\\') !== false) {
            die('Invalid filename. Directory traversal not allowed.');
        }
        
        // SECURITY: Verify that the current user owns this picture before deletion
        $username = $_SESSION['username'];
        $stmt_check = mysqli_prepare($connection, 
            "SELECT p.pictures_name FROM pictures p 
             INNER JOIN users u ON p.id_users = u.users_id 
             WHERE p.pictures_name = ? AND u.users_username = ?");
        mysqli_stmt_bind_param($stmt_check, "ss", $pictures_name, $username);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        
        if (mysqli_num_rows($result_check) === 0) {
            mysqli_stmt_close($stmt_check);
            die('Access denied. You do not own this file.');
        }
        mysqli_stmt_close($stmt_check);
        
        // Use prepared statement to prevent SQL injection
        $stmt = mysqli_prepare($connection, "DELETE FROM pictures WHERE pictures_name = ?");
        mysqli_stmt_bind_param($stmt, "s", $pictures_name);
        
        if (mysqli_stmt_execute($stmt)){
            // Use realpath to ensure the file is within the uploads directory
            $upload_dir = realpath(__DIR__ . '/uploads');
            $file_path = $upload_dir . DIRECTORY_SEPARATOR . $pictures_name;
            $real_path = realpath($file_path);
            
            // Verify the file exists and is within the uploads directory
            // nosemgrep: php.lang.security.unlink-use.unlink-use
            // Justification: Filename is validated with regex, path traversal is blocked,
            // ownership is verified in database, and realpath ensures file is in uploads dir
            if ($real_path && strpos($real_path, $upload_dir) === 0 && file_exists($real_path)) {
                if (unlink($real_path)){
                    echo "Removed picture " . htmlspecialchars($pictures_name, ENT_QUOTES, 'UTF-8') . "<br>";
                    echo "Removed picture " . htmlspecialchars($pictures_name, ENT_QUOTES, 'UTF-8') . ", continue with  " . "<a href=''>" . "deleting pictures" . "</a>";
                } else {
                    echo "Error deleting file.";
                }
            } else {
                echo "File not found or access denied.";
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    $sql1 = "SELECT users.users_username, pictures.pictures_name FROM pictures INNER JOIN users ON pictures.id_users = users.users_id";
    $result = mysqli_query ($connection, $sql1) or die (mysqli_error ($connection));
    
    echo "<form action='' method='POST'>";
    echo csrf_token_field();
    echo "<select name='pictures_name'>";
    if (mysqli_num_rows ($result) > 0){
        while ($row = mysqli_fetch_assoc ($result)){
            if($row['users_username'] == $_SESSION['username']){
                $safe_name = htmlspecialchars($row['pictures_name'], ENT_QUOTES, 'UTF-8');
                echo "<option value='" . $safe_name . "'>" . $safe_name . "</option>";
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
