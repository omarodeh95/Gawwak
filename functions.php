<?php
//include "connection.php";
function checklogin($conn){
if (!isset($_SESSION['email'])){
header("Location: login.php");
} else {
$emails=$_SESSION['email'];
$sql="update users set status = 'Online' where email = '$emails'";
mysqli_query($conn,$sql);

}
}

function logout($conn){
$emails = $_SESSION['email'];
$sql = "update users set status = 'Offline' where email = '$emails'";
mysqli_query($conn,$sql);
unset($_SESSION['email']);
unset($_SESSION['friendname']);
unset($_SESSION['friendemail']);
}
function basicvalidate($data){
    $data=htmlspecialchars($data);
    $data=stripslashes($data);
    $data=trim($data);
    return $data;
}
?>
