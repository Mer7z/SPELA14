<?php
require('conexion.php');
session_start();
$query = '';
if(isset($_SESSION['useremail']) && isset($_SESSION['verified'])){
  if($_SESSION['userType'] == 'employee'){
    if(isset($_POST['sendOrder'])){
      $IDs = $_POST['orderId'];
      foreach ($IDs as $id) {
        if(isset($_POST['clientId'])){
          $clientId = $_POST['clientId'];
          $sql = "UPDATE pedidos SET enviado = 1 WHERE id_pedidos='$id'";
          $query = $mysqli->query($sql);
          if($query){
            $sql = "UPDATE clientes SET ordenado = 0 WHERE id='$clientId'";
            $query = $mysqli->query($sql);
            if(!$query){
              echo "Error al actualizar cliente";
            }
          } else {
            echo "Error al actualizar pedido $id";
          }
        } elseif (isset($_POST['clientIdNoReg'])){
          $clientId = $_POST['clientIdNoReg'];
          $sql = "UPDATE pedidos SET enviado = 1 WHERE id_pedidos='$id'";
          $query = $mysqli->query($sql);
          if($query){
            $sql = "UPDATE clientes_no_registrados SET ordenado=0 WHERE id='$clientId'";
            $query = $mysqli->query($sql);
            if(!$query){
              echo "Error al actualizar cliente";
            }
          } else{
            echo "Error al actualizar pedido $id";
          }
        }
      }
      echo 'Done.';
    }
  } 
}

