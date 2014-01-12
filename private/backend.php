<?php
session_start();
error_reporting(~0); ini_set('display_errors', 1);

require('../_CONFIG.php');

try {
        $dbh = new PDO("mysql:host=" . DBHOST . ";dbname=" . DATABASE_NAME , DBUSERNAME, DBPASSWD);
    }
catch(PDOException $e)
    {
        echo $e->getMessage();
    }

/*
    Log the user
*/
if ($_POST['action'] === 'log'){
    $user_add = $dbh->prepare("INSERT INTO `users` (`id`, `user_id`, `user_ip`, `city`, `region`, `loc`, `zip`, `created`)
    VALUES (NULL, :user_id, :user_ip, :city, :region, :loc,  :zip, CURRENT_TIMESTAMP);");
    $user_id = uniqid();
    //$user_ip = $_SERVER['REMOTE_ADDR'];
    $user_ip = '24.252.88.20';

    //Query ipinfo for data
    if (!function_exists('curl_init')){
        //We need curl to query ipinfo
        $resp = array('status' => 'ERROR');
        die(json_encode($resp)) ;
    }
                
    $ch = curl_init('http://ipinfo.io/' . $user_ip);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  /* return the data */
    $result = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($result);

    $data = array('user_id' => $user_id, 'user_ip' => $user_ip, 'city' => $json->city, 'region' => $json->region, 'loc' => $json->loc,  'zip' => $json->postal);
    $user_add->execute($data);

    $error = $user_add->errorInfo();

    if (!$error[1]){
        $resp = array('status' => 'OK', 'userid' => $user_id);
        echo json_encode($resp);
    } else {
        $resp = array('status' => 'ERROR');
        echo json_encode($resp);
    }
}
/*
    Record User Progress
*/


if ($_POST['action'] === 'progress'){


}

/*
    Log in user to edit tree
*/
if ($_POST['action'] === 'login'){

    $user = $_POST['username'];
    $pass = sha1($_POST['password']);
    $login = $dbh->prepare('SELECT * from `admin` where username = :username AND password = :password');
    $data = array('username' => $user, 'password' => $pass);
    $login->execute($data);
    if ($login->rowCount() > 0){
        $_SESSION['isLoggedIn'] = '1';
        echo json_encode(array('status' => 'OK','message' => 'Logging you in...'));
    } else {
        echo json_encode(array('status' => 'ERROR','message' => 'Incorrect Username or Password'));
    }
}

