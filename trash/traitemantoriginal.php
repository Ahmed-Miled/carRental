<?php
require_once '../config/database.php';

function getUsername($pdo, $email){
    try{
        $stmt = $pdo->prepare("SELECT name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user['name'];
    }catch (Exception $e){
        "<script> console.log(" . json_encode($e) . ");</script>";
        return false;
    }
}
function getUserId($pdo, $email){
    try{
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user['id'];
    }catch (Exception $e){
        "<script> console.log(" . json_encode($e) . ");</script>";
        return false;
    }
}
function getPhoneNumber($pdo, $email){
    try{
        $stmt = $pdo->prepare("SELECT phoneNumber FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user['phoneNumber'];
    }catch (Exception $e){
        "<script> console.log(" . json_encode($e) . ");</script>";
        return false;
    }
}


if (!isset($pdo)){
    die("ERREUR: Connexion DB échouée. Vérifiez database.php");
}else{
    // connxion avec la base du donner est verifier
    $role = $_POST['role'];
    $action = $_POST['action'];
    
    //handeling client rediraction if he exists in the data base
    if ($action == 'login'){
        $email = $_POST['logInEmail'];
        $password = $_POST['logInPassword'];
        $user_id = getUserId($pdo, $email);


        $emailStatment = $pdo->prepare("SELECT * FROM users WHERE email = ? AND type = ?");
        $emailStatment->execute([$email, $role]);
        $uEmail = $emailStatment->fetch(PDO::FETCH_ASSOC);

        $passwordStatment = $pdo->prepare("SELECT * FROM users WHERE type = ? AND password = ?");
        $passwordStatment->execute([$role, $password]); 
        $uPassword = $passwordStatment->fetch(PDO::FETCH_ASSOC);

        if ($uEmail && $uPassword){
            //echo "<script>console.log('correct email correct password');</script>";
            $username = getUsername($pdo, $email);
            $phoneNumber = getPhoneNumber($pdo, $email);
            if (!$username){
                // redirect to an error page
                header('Location: /carRental/views/error.php');
                exit();

            }else{
                session_start();
                // getting user info from the data base (user name, ....)
                $_SESSION['logged_in'] = true;
                $_SESSION['user_name'] = $username; // From your DB
                $_SESSION['user_email'] = $email;
                $_SESSION['phoneNumber'] = $phoneNumber;
                $_SESSION['role'] = $role;
                $_SESSION['user_id'] = $user_id;
                echo "<script>console.log('connected');</script>";
                header('Location: /carRental/index.php');
                exit(); 
            }

        }else{
            session_start();
            $_SESSION['error_message'] = "Mot de passe ou email incorrect. Veuillez essayer une autre fois.";
            $_SESSION['error_type'] = 'auth';
            
            header('Location: /carRental/views/error.php');
            exit(); 
        }
    }else{
        if ($role == 'client'){
            
            // check if the email is already used by someone else
            $email = $_POST['SignUpEmail'];
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if($user){
                session_start();
                $_SESSION['error_message'] = "Cet email est déjà utilisé. Veuillez utiliser un autre email.";
                $_SESSION['error_type'] = 'email';
                header('Location: /carRental/views/error.php');
                exit();
                
            }else{
                // create an account for the useer and opening a session

                $name = $_POST['SignUpName'];
                $phoneNumber = $_POST['SignUpPhoneNumber'];
                $password = $_POST['SignUpPassword'];
                $type = 'client';

                $stmt = $pdo->prepare("INSERT INTO users (name, phoneNumber, email, password, type) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $phoneNumber, $email, $password, $type]);
                
                session_start();
                
                $_SESSION['logged_in'] = true;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['phoneNumber'] = $phoneNumber;
                $_SESSION['user_id'] = getUserId($pdo, $email);
                $_SESSION['role'] = $role;
                
                header('Location: /carRental/index.php');
                exit();
            }
        }else{
            // owner cant create an account
            header('Location: /carRental/views/error.php');
            exit(); 
        }
    }
}
?>