<?php
require_once '../config/database.php';
require_once '../models/client.php';

if (!isset($pdo)){
    die("ERREUR: Connexion DB échouée. Vérifiez database.php");
}else{
    session_start();
    // connxion avec la base du donner est verifier
    $role = $_POST['role'];
    $action = $_POST['action'];
    if ($action == 'login' && $role !== 'admin') {
        
        $ad = "admin@gmail.com";
        
        $email = $_POST['logInEmail'];
        $password = $_POST['logInPassword'];
        echo "<script> console.log(" . json_encode($_POST['logInEmail']) . ");</script>";
        
        
        $client = getClient($pdo, $email, $role);
        "<script> console.log(" . json_encode($client) . ");</script>";
        
        if (!$client){
            // error
            
            $_SESSION['error_message'] = "email incorrect. Veuillez essayer une autre fois.";
            $_SESSION['error_type'] = 'authentification';
            header('Location: /carRental/views/feedback.php');
            exit();
        }elseif (password_verify($password, $client['password']) && $client['email'] == $ad && $client['email'] == $email) {
            // handeling admin
            echo "<script>console.log('QDSGQ');</script>";
            $_SESSION['admin_logged_in'] = true;
            header('Location: /carRental/views/dash-board/admin.php');
            exit();
        }elseif ($role == 'client' && password_verify($password, $client['password']) && $client['email'] == $email) {
            //handeling client
            echo "<script>console.log('connected');</script>";
            
            $_SESSION['logged_in'] = true;
            $_SESSION['role'] = 'client';
            $_SESSION['user_id'] = $client['id'];
            $_SESSION['user_name'] = $client['fullName'];
            $_SESSION['user_email'] = $client['email'];
            $_SESSION['phoneNumber'] = $client['phoneNumber'];
            $_SESSION['created_at'] = $client['created_at'];

            header('Location: /carRental/views/dash-board/clientDashBoard.php');
            exit();

        }elseif ($role == 'agency' && password_verify($password, $client['password']) && $client['email'] == $email) {
            echo "<script>console.log('connected');</script>";
            
            
            //handeling agency
            $_SESSION['logged_in'] = true;
            $_SESSION['role'] = 'agency';
            $_SESSION['user_id'] = $client['id'];
            $_SESSION['user_name'] = $client['fullName'];
            $_SESSION['user_email'] = $client['email'];
            $_SESSION['phoneNumber'] = $client['phoneNumber'];
            $_SESSION['address'] = $client['address'];
            $_SESSION['created_at'] = $client['created_at'];
            header('Location: /carRental/views/dash-board/agencyDashBoard.php');
            exit();
        }else{
            
        $_SESSION['error_message'] = "Mots de passe. Veuillez essayer une autre fois.";
        $_SESSION['error_type'] = 'authentification';
        header('Location: /carRental/views/feedback.php');
        exit();

        }
    }elseif($action == 'signup' && $role == 'client'){

        
        $email = $_POST['SignUpEmail'];
       
        if (emailExists($pdo, $email, $role) != null){
            $_SESSION['error_message'] = "Email deja existant";
            $_SESSION['error_type'] = 'signup';
            header('Location: /carRental/views/feedback.php');
            exit();
        }else{
            echo "<script>console.log('email mch mawjoud t3adena lel else');</script>";
            $password = $_POST['SignUpPassword'];
            $fullName = $_POST['SignUpName'];
            $phoneNumber = $_POST['SignUpPhoneNumber'];
            
            if(createClientAccount($pdo, $fullName, $email, $password, $phoneNumber, null,$role)){
                $client = getClient($pdo, $email, $role);

                $_SESSION['logged_in'] = true;
                $_SESSION['role'] = 'client';
                $_SESSION['user_id'] = $client['id'];
                $_SESSION['user_name'] = $client['fullName'];
                $_SESSION['user_email'] = $client['email'];
                $_SESSION['phoneNumber'] = $client['phoneNumber'];
                $_SESSION['created_at'] = $client['created_at'];

                header('Location: /carRental/views/dash-board/clientDashBoard.php');
                exit();
            }else{
            
                $_SESSION['error_message'] = "inscription echoué pour un error inconnu";
                $_SESSION['error_type'] = 'signup';
                header('Location: /carRental/views/feedback.php');
                exit();
                 
            }
        }
    }elseif($action == 'signup' && $role == 'admin'){
        // zid agence
        echo "<script>console.log('zid agenc');</script>";
        $email = $_POST['user_email'];
        
        if (emailExists($pdo, $email, "agency") != null){
        echo "<script>console.log('email mawjoud deja');</script>";

            $_SESSION['error_message'] = "Email deja existant";
            $_SESSION['error_type'] = 'signup';
            header('Location: /carRental/views/feedback.php');
            exit();
        } else{
            $password = $_POST['user_password'];
            $fullName = $_POST['user_name'];
            $phoneNumber = $_POST['phoneNumber'];
            $address = $_POST['user_address'];
            if(createClientAccount($pdo, $fullName, $email, $password, $phoneNumber, $address, "agency")){
               
                // affichi messgae ennou cbn
                
                header('Location: /carRental/views/dash-board/admin.php');
                exit();
            }else{
            
                $_SESSION['error_message'] = "inscription du agency echoué pour une error inconnu";
                $_SESSION['error_type'] = 'signup';
                header('Location: /carRental/views/feedback.php');
                exit();
                 
            }
        }

    }else{
        
        $_SESSION['error_message'] = "Action non autoriser";
        $_SESSION['error_type'] = 'authentification';
        header('Location: /carRental/views/feedback.php');
        exit();
    }
}

?>