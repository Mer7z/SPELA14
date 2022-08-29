<?php
require_once('common.php');
require 'conexion.php';
session_start();
$logged = false;
$name = '';
$lname = '';
$email = '';
$address = '';
$phone = '';
$id = '';
$resultOrders;
if(isset($_COOKIE['userEmail']) && isset($_COOKIE['token']) && isset($_COOKIE['id']) && isset($_COOKIE['type'])){
  $cookie = $_COOKIE['userEmail'];
  $hash = $_COOKIE['token'];
  $cookie_id = $_COOKIE['id'];
  $type = $_COOKIE['type'];
  
  
  $sql_client = "SELECT contraseña FROM clientes WHERE correo='$cookie' AND id='$cookie_id'";
  $sql_employee = "SELECT contraseña FROM empleados WHERE correo='$cookie' AND id='$cookie_id'";
  if(password_verify('client', $type)){
    $query = $mysqli->query($sql_client);
    $result = $query->fetch_assoc();
    if($result>0){
      if($result['contraseña']==$hash){
        $_SESSION['userType'] = 'client';
        $_SESSION['useremail'] = $_COOKIE['userEmail'];
        $_SESSION['verified'] = true;
      } else{
        session_destroy();
      }
    }
  } elseif(password_verify('employee', $type)){
    $query = $mysqli->query($sql_employee);
    $result = $query->fetch_assoc();
    if($result>0){
      if($result['contraseña']==$hash){
        $_SESSION['userType'] = 'employee';
        $_SESSION['useremail'] = $_COOKIE['userEmail'];
        $_SESSION['verified'] = true;
      } else{
        session_destroy();
      }
    } else{
      session_destroy();
    }
  }

  
}
if (isset($_SESSION['useremail']) && isset($_SESSION['verified'])) {
  #Iniciar Sección
  setcookie('userEmail', $cookie, time() + (86400 * 30), "/");
  setcookie('token', $hash, time() + (86400 * 30), "/");
  setcookie('id', $cookie_id, time() + (86400 * 30), "/");
  setcookie('type', $type, time() + (86400 * 30), "/");
  $email = $_SESSION['useremail'];
  $logged = true;
  if (isset($_COOKIE['noreg-id'])) {
    unset($_COOKIE['noreg-id']);
    setcookie('noreg-id', null, -1, '/');
  }
  $sql = '';
  if ($_SESSION['userType'] == 'client') {
    $sql = "SELECT id, nombre, apellido, direccion, telefono FROM clientes WHERE correo='$email'";
  } elseif ($_SESSION['userType'] == 'employee') {
    $sql = "SELECT nombre FROM empleados WHERE correo='$email'";
  }
  $col = $mysqli->query($sql);
  $resul = $col->fetch_assoc();
  if ($resul > 0) {
    $name = $resul['nombre'];
    if (isset($resul['apellido']) && isset($resul['direccion']) && isset($resul['telefono']) && isset($resul['id'])) {
      $lname = $resul['apellido'];
      $address = $resul['direccion'];
      $phone = $resul['telefono'];
      $id = $resul['id'];
    }
  }

  #Pedidos
  $sqlOrders = '';
  if ($_SESSION['userType'] == 'client') {
    $sqlOrders = "SELECT * FROM pedidos WHERE id_cliente='$id' AND enviado=0";
  } elseif ($_SESSION['userType'] == 'employee') {
    $sqlOrders = "SELECT * FROM pedidos";
  }
  $query = $mysqli->query($sqlOrders);
  $resultOrders = $query->fetch_all();
} elseif (isset($_COOKIE['noreg-id'])) {
  $cookieId = $_COOKIE['noreg-id'];
  $sqlOrders = "SELECT * FROM pedidos WHERE id_no_reg='$cookieId'";
  $query = $mysqli->query($sqlOrders);
  $resultOrders = $query->fetch_all();
}

if (isset($_POST['sendOrder'])) {
  $orderId = $_POST['orderId'];
  $sqlGetClient = "SELECT id_no_reg, id_cliente FROM pedidos WHERE id_pedidos='$orderId[0]'";
  $query = $mysqli->query($sqlGetClient);
  $result = $query->fetch_assoc();
  $sqlClientNoReg;
  $sqlClientReg;
  if($result>0){
    if(isset($result['id_no_reg'])){
      $clientIdNoReg = $result['id_no_reg'];
      $sqlClientNoReg = "UPDATE clientes_no_registrados SET ordenado=0 WHERE id=$clientIdNoReg";
    } elseif(isset($result['id_cliente'])){
      $clientIdReg = $result['id_cliente'];
      $sqlClientReg = "UPDATE clientes SET ordenado=0 WHERE id=$clientIdReg";
    }
  }
  

  $queryClient;
  if (isset($clientIdNoReg)) {
    $queryClient = $mysqli->query($sqlClientNoReg);
  } elseif (isset($clientIdReg)) {
    $queryClient = $mysqli->query($sqlClientReg);
  } else {
    header('location: order.php');
  }

  
    $updated = false;
    foreach($orderId as $id){
      $sql = "UPDATE pedidos SET enviado=1 WHERE id_pedidos=$id";
      $query = $mysqli->query($sql);
      if($query){
        $updated = true;
      }
    }
    if ($updated) {
      header('location: orders.php');
    }
  
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pedidos | Arepahamburger La 14</title>
  <link rel="shortcut icon" href="images/arepa.png" type="image/x-icon">
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100;0,400;0,700;1,400;1,700&family=Jost:wght@300;400;700&family=Nunito:wght@400;700&display=swap" rel="stylesheet">

  <script src="https://kit.fontawesome.com/5bc1d976fa.js" crossorigin="anonymous"></script>

  <!-- CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.dataTables.min.css">
</head>

<body>
  <div class="" id="nav">
    <nav class="navbar navbar-expand-md bg-light">
      <div class="nav-container">
        <div class="nav-top d-flex">
          <div class="logo-container">
            <a href="index.php">
              <img class="logo-image" src="images/arepa.png" alt="">
            </a>
          </div>
          <div class="title">
            <a href="index.php">
              <h1 class="title-text">Arepahamburger la 14</h1>
            </a>
          </div>

          <div class="account-container">
            <a href="account.php" class="sign-in <?php if (!$logged) {
                                                    echo 'hidden';
                                                  } ?>">
              <i class="fa-solid fa-circle-user"></i>
              <?php
              if ($logged) {
                echo "<span>" . $name . "</span>";
              }
              ?>
            </a>
            <a href="signin.php" class="sign-in <?php if ($logged) {
                                                  echo 'hidden';
                                                } ?>">
              <i class="fa-solid fa-right-to-bracket"></i>
              <span>Iniciar Sección</span>
            </a>
            <a href="#" class="toggle-button">
              <span class="bar"></span>
              <span class="bar"></span>
              <span class="bar"></span>
            </a>
          </div>
        </div>
        <div class="nav-bottom">
          <div class="d-flex flex-row nav-links-container">
            <a href="index.php" class="nav-link">Inicio</a>
            <a href="order.php" class="nav-link">Pedir</a>
            <a href="orders.php" class="nav-link selected <?php if (!$logged && !isset($_COOKIE['noreg-id'])) {
                                                            echo "hidden";
                                                          } ?>">
              <?php
              if ($logged && $_SESSION['userType'] == 'client' || isset($_COOKIE['noreg-id'])) {
                echo "Tus Pedidos";
              } elseif ($logged && $_SESSION['userType'] == 'employee') {
                echo "Pedidos";
              }
              ?>
            </a>
            <a href="#" class="nav-link">Comentar</a>
            <a href="clients.php" class="nav-link <?php if (!$logged || $logged && $_SESSION['userType'] != 'employee') {
                                                    echo "hidden";
                                                  } ?>">Clientes</a>
          </div>
        </div>
      </div>
    </nav>
  </div>
  <div style="padding: 3% 5px" id="orders-list">
    <h2>Pedidos</h2>
    <div>
      <table id=orders-table class="display nowrap" style="width: 100%;">
        <thead>
          <tr>
            <th>Dirección</th>
            <th>Nombre</th>
            <th>Producto</th>
            <th>Cant.</th>
            <th>Info</th>
            <th>Telefono</th>
            <th>Fecha</th>
            <?php
            if ($logged && $_SESSION['userType'] == 'employee' ) {
            ?>
              <th>Enviado</th>
            <?php } ?>
          </tr>
        </thead>
        <tbody>
          <?php if (isset($resultOrders)) :
            $lastId;
            $lastFecha;
            $endTr = false;
            $newTr = true;
            $group = true;
            $lastRow = false;
            $productsId = [];
            $productsCant = [];
            $productsNames = [];
            $productsInfo = [];
            $clientAddress;
            $clientName;
            $clientPhone;
            $date;
            $clientid;
            $sent;
            foreach($resultOrders as $rKey => $orders){
                $pedidoId;
                $productoNombre;
                $productoInfo;
                $cant;
                $direccion;
                $clienteNombre;
                $clienteTel;
                $fecha;
                $enviado;
                $idCliente;
                foreach($orders as $key => $data){
                  switch($key){
                    case 0:
                      $pedidoId = $data;
                    case 1:
                      $productoNombre = $data;
                      break;
                    case 2:
                      $productoInfo = unserialize($data);
                      break;
                    case 3:
                      $cant = $data;
                      break;
                    case 4:
                      $clienteNombre = $data;
                      break;
                    case 5:
                      $clienteNombre = $clienteNombre . ' ' . $data;
                      break;
                    case 6:
                      $direccion = $data;
                      break;
                    case 7:
                      $clienteTel = $data;
                      break;
                    case 8:
                      $fecha = $data;
                      
                      break;
                    case 10:
                      if($data){
                        $idCliente = $data;
                      }
                      break;
                    case 11:
                      if($data){
                        $idCliente = $data;
                      }
                      break;
                    case 12:
                      $enviado = $data;
                      break;
                    default:
                      break;
                  }
                }
              
                if(!isset($lastId) && !isset($lastFecha)){
                  $lastId = $idCliente;
                  $lastFecha = $fecha;

                  array_push($productsId, $pedidoId);
                  array_push($productsNames, $productoNombre);
                  array_push($productsCant, $cant);
                  array_push($productsInfo, $productoInfo);
                  $clientAddress = $direccion;
                  $clientName = $clienteNombre;
                  $clientPhone = $clienteTel;
                  $date = $fecha;
                  $clientid = $idCliente;
                  $sent = $enviado;
                  $newTr = true;
                  $group = true;
                } else {
                  if($lastId == $idCliente && $lastFecha == $fecha){
                    $productsId[] = $pedidoId;
                    $productsCant[] = $cant;
                    $productsNames[] = $productoNombre;
                    $productsInfo[] = $productoInfo;
                    $clientAddress = $direccion;
                    $clientName = $clienteNombre;
                    $clientPhone = $clienteTel;
                    $date = $fecha;
                    $clientid = $idCliente;
                    $sent = $enviado;
                    $newTr = false;
                    $group = true;
                  } else {
                    $endTr = true;
                    $group = false;
                  }
                }
                if($rKey == count($resultOrders)-1){
                  $lastRow = true;
                }
              if($newTr):
                $newTr = false;

          ?>
          <tr>
          <?php
          endif;

          if(!$group || $lastRow):
          ?>
            <td>
              <?php echo $clientAddress ?>
            </td>
            <td>
              <?php echo $clientName ?>
            </td>
            <td>
              <table>
              <?php
                foreach($productsNames as $pNames):
                ?>
                <tr>
                  <td>
                  <?php echo $pNames ?> 
                  </td>
                </tr>
                <?php endforeach; ?>
              </table>
            </td>
            <td>
              <table>
                <?php
                foreach($productsCant as $pCant):
                ?>
                <tr>
                  <td>
                  <?php echo $pCant ?> 
                  </td>
                </tr>
                <?php endforeach; ?>
              </table>
            </td>
            <td>
              <table>
              <?php
                foreach($productsInfo as $pInfo):
                ?>
                <tr>
                  <td>
                  <?php
                  foreach($pInfo as $infoKey => $info){
                    
                    if($info){
                      echo $infoKey . ' '; 
                    }
                  }
                  ?> 
                  </td>
                </tr>
                <?php endforeach; ?>
              </table>
            </td>
            
            <td>
              <?php echo $clientPhone ?>
            </td>
            <td>
              <?php echo $date ?>
            </td>
            <?php
            if ($logged && $_SESSION['userType'] == 'employee' ) {
            ?>
            <td>
              <form id="form-<?php echo $productsId[0] ?>" method="POST">
                <?php
                foreach($productsId as $id){
                ?>
                <input type="hidden" name="orderId[]" value="<?php echo $id ?>">
                <?php
                }

                $sqlSent = "SELECT enviado FROM pedidos WHERE id_pedidos='$productsId[0]'";
                $query = $mysqli->query($sqlSent);
                $result = $query->fetch_assoc();
                if($result>0){
                ?>
                <input type="hidden" name="sendOrder">
                <input type="checkbox" onclick="$('#form-<?php echo $productsId[0] ?>').submit()" <?php if($result['enviado'] == 1){ echo 'checked'; } ?>>
                <?php
                }
                ?>
              </form>
            </td>
            <?php } ?>
          <?php
            if($endTr):
              $newTr = true;
              $productsId = [$pedidoId];
              $productsNames = [$productoNombre];
              $productsCant = [$cant];
              $productsInfo = [$productoInfo];
              $clientAddress = $direccion;
              $clientName = $clienteNombre;
              $clientPhone = $clienteTel;
              $date = $fecha;
              $clientid = $idCliente;
              $sent = $enviado;
                  
              $lastFecha = $fecha;
              $lastId = $idCliente;
          ?>
          </tr>
          <?php
            if($lastRow):
          ?>
          <tr>
             <td>
              <?php echo $clientAddress ?>
            </td>
            <td>
              <?php echo $clientName ?>
            </td>
            <td>
              <table>
              <?php
                foreach($productsNames as $pNames):
                ?>
                <tr>
                  <td>
                  <?php echo $pNames ?> 
                  </td>
                </tr>
                <?php endforeach; ?>
              </table>
            </td>
            <td>
              <table>
                <?php
                foreach($productsCant as $pCant):
                ?>
                <tr>
                  <td>
                  <?php echo $pCant ?> 
                  </td>
                </tr>
                <?php endforeach; ?>
              </table>
            </td>
            <td>
              <table>
              <?php
                foreach($productsInfo as $pInfo):
                ?>
                <tr>
                  <td>
                  <?php
                  foreach($pInfo as $infoKey => $info){
                    
                    if($info){
                      echo $infoKey . ' '; 
                    }
                  }
                  ?> 
                  </td>
                </tr>
                <?php endforeach; ?>
              </table>
            </td>
            
            <td>
              <?php echo $clientPhone ?>
            </td>
            <td>
              <?php echo $date ?>
            </td>
            <?php
            if ($logged && $_SESSION['userType'] == 'employee' ) {
            ?>
            <td>
              <form id="form-<?php echo $productsId[0] ?>" method="POST">
                <?php
                foreach($productsId as $id){
                ?>
                <input type="hidden" name="orderId[]" value="<?php echo $id ?>">
                <?php
                }

                $sqlSent = "SELECT enviado FROM pedidos WHERE id_pedidos='$productsId[0]'";
                $query = $mysqli->query($sqlSent);
                $result = $query->fetch_assoc();
                if($result>0){
                ?>
                <input type="checkbox" name="sendOrder" onclick="$('#form-<?php echo $productsId[0] ?>').submit()" <?php if($result['enviado'] == 1){ echo 'checked'; } ?>>
                <?php
                }
                ?>
              </form>
            </td>
            <?php } ?>
          </tr>
          <?php
            endif;
          endif;
          ?>
          
          <?php
          endif;
          } 
          endif;
          ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php
  footer();
  ?>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
  <script src="js/orders.js"></script>

</html>