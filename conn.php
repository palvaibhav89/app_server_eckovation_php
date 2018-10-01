<?php
    
    $host = "localhost";
    $user = "u327340264_vaib";
    $pwd = "Hello14@";
    $db = "u327340264_ecko";
    
    $con = mysqli_connect($host, $user, $pwd, $db);
    
    if(!$con){
        die("Connection error !!!");
    }
?>