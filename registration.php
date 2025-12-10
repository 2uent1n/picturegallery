<?php

$page_title = "Registration";

session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


include 'mysqli_connect.php';
include 'includes/csrf.php';

include 'includes/header.html';

include 'includes/navbar.html';

if (isset ($_SESSION['username'])){
	echo "Logged in with name '" . $_SESSION['username'] . "'. You can <a href='logout.php'>logout</a>";
}
else{
    include 'includes/registration.html';



?>


<?php

include 'includes/footer.html';

}

?>
