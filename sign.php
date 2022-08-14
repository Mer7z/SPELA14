<?php
require 'conexion.php';
session_start();
$nombre = $mysqli->real_escape_string($_POST['name']);
$lnombre = $mysqli->real_escape_string($_POST['lname']);
$email = $mysqli->real_escape_string($_POST['email']);
$password = $mysqli->real_escape_string($_POST['password1']);
$direccion = $mysqli->real_escape_string($_POST['address']);
$telefono = $mysqli->real_escape_string($_POST['phone']);

$sql_email_verify = "SELECT * FROM clientes WHERE correo='$email'";
$result = $mysqli->query($sql_email_verify);
if ($result > 0) {
  header("location: signin.php?email-taken=true");
} else {
  $sql = "INSERT INTO clientes (nombre, apellido, correo, contraseña, direccion, telefono) VALUES ('$nombre', '$lnombre', '$email', '$password', '$direccion', '$telefono')";
  $resultado = $mysqli->query($sql);

  if ($resultado > 0) {
    $_SESSION['useremail'] = $email;
    $_SESSION['userType'] = 'client';
    header("location: account.php");
  } else {
    echo 'Ops! Hubo Un problema';
  }
}
