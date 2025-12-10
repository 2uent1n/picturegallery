<?php

$page_title = 'Login';

session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include 'includes/header.html';

include 'includes/navbar.html';

if (isset ($_SESSION['username'])){
	echo "You are logged! You can " . "<a href='logout.php'>" . "logout" . "</a>";
}
else{
    include 'includes/login.html';
?>


<?php
}

include 'includes/footer.html';



?>

