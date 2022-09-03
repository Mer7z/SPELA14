<?php
require('conexion.php');
session_start();
$query = '';
if(isset($_SESSION['useremail']) && isset($_SESSION['verified'])){
  if($_SESSION['userType'] == 'employee'){
    $query = "SELECT * FROM pedidos";
  } elseif($_SESSION['userType'] == 'client'){
    if(isset($_COOKIE['id'])){
      $id = $_COOKIE['id'];
      $query = "SELECT * FROM pedidos WHERE id_cliente=$id AND enviado=0";
    } else {
      exit('no id');
    }
  }
} else{
  if(isset($_COOKIE['noreg-id'])){
    $id = $_COOKIE['noreg-id'];
    $query = "SELECT * FROM pedidos WHERE id_no_reg=$id AND enviado=0";
  } else{
    exit('no id');
  }
}

$result = mysqli_query($mysqli, $query);
if(!$result){
  die('Query Error');
}

$json = [];
while($row = mysqli_fetch_array($result)){
  $json[] = [
    'id' => $row['id_pedidos'],
    'producto' => $row['producto'],
    'info' => unserialize($row['info']),
    'cantidad' => $row['cantidad'],
    'nombre' => $row['nombre_pedido'],
    'apellido' => $row['apellido_pedido'],
    'direccion' => $row['direccion_pedido'],
    'telefono' => $row['telefono_pedido'],
    'fecha' => $row['fecha_pedido'],
    'correo' => $row['correo_pedido'],
    'idNoReg' => $row['id_no_reg'],
    'idCliente' => $row['id_cliente'],
    'enviado' => $row['enviado']
  ];
}

$jsonString = json_encode($json);
echo $jsonString;