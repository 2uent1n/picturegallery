<?php

$page_title = 'Check login';

include 'includes/header.html';

include 'mysqli_connect.php';

if (isset ($_SESSION['username'])){
	header ('location: index.php');
}
else{
    $username = $_POST['username'];
    $password = $_POST['pass'];

    $sql = "SELECT users_username, users_password 
        FROM users 
        WHERE users_username = ? AND users_password = ?";

    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);


    if (mysqli_num_rows ($result) > 0){
        while ($row = mysqli_fetch_assoc ($result)){
            $_SESSION['username'] = $row['users_username'];
            include 'includes/navbar.html';
            include 'includes/logged.php';
            
	 
        }
    }
    else{
        include 'includes/navbar.html';
        include 'includes/notlogged.php';
        
    }
}

mysqli_close($connection);

include 'includes/footer.html';
?>