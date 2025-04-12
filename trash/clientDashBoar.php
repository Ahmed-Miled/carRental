<?php 
require __DIR__ . '/../includes/header.php';  
?>

<head>
    <link rel="stylesheet" href="/carRental/assets/css/clientDashBoard.css">
</head>

<p>
    this page is a user dash board that will be redirected to, after login. what will you suggest to display in it ? at first it must shows up the following options:
    the ability to discnnect (execution /carRental/views/auth/logout.php) and an option for changing info (username, email, phone number, etc)
    a btn for deleting this account, i will handel it's script.
    and i want it to shows up the historique if existe of the cars or bikes that the user have rented before (from the databsae),
    suggest for me the code nessecaire and the  /carRental/assets/css/clientDashBoard.css
</p>


<?php 
require __DIR__ . '/../includes/footer.php';  
?>