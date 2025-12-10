<?php

$page_title = "Registration";

include 'mysqli_connect.php';
include 'includes/csrf.php';

include 'includes/header.html';

include 'includes/navbar.html';

if (isset ($_SESSION['username'])){
	echo "Logged in with name '" . $_SESSION['username'] . "'. You can <a href='logout.php'>logout</a>";
}
else{
    // Generate CSRF token for the registration form
    $csrf_token = generate_csrf_token();
    include 'includes/registration.html';

?>


<?php

include 'includes/footer.html';

}

?>