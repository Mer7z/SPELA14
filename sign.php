<?php
require 'conexion.php';
session_start();
if(isset($_POST['btnSubmit'])):
$nombre = $mysqli->real_escape_string($_POST['name']);
$lnombre = $mysqli->real_escape_string($_POST['lname']);
$email = $mysqli->real_escape_string($_POST['email']);
$password = $mysqli->real_escape_string($_POST['password1']);
$direccion = $mysqli->real_escape_string($_POST['address']);
$telefono = $mysqli->real_escape_string($_POST['phone']);

$sql_email_verify = "SELECT * FROM clientes WHERE correo='$email'";
$con = $mysqli->query($sql_email_verify);
$result = $con->fetch_assoc();
if ($result > 0) {
  header("location: signin.php?email-taken=true");
} else {
  $sql = "INSERT INTO clientes (nombre, apellido, correo, contraseña, direccion, telefono) VALUES ('$nombre', '$lnombre', '$email', '$password', '$direccion', '$telefono')";
  $con = $mysqli->query($sql);
  if ($mysqli) {
    $_SESSION['useremail'] = $email;
    $_SESSION['userType'] = 'client';
    header("location: account.php");
  } else {
    echo '<script>alert("Ocurrió un error")</script>';
    header("location: signin.php");
  }
}

else:
  header('location: signin.php');
endif;