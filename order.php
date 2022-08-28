<?php
  require 'conexion.php';
  session_start();
  $logged = false;
  $name = '';
  $lname = '';
  $email = '';
  $address = '';
  $phone = '';
  $id = '';
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
    if(isset($_COOKIE['noreg-id'])){
      unset($_COOKIE['noreg-id']);
      setcookie('noreg-id', null, -1, '/');
    }
    $sql = '';
    if($_SESSION['userType'] == 'client'){
      $sql = "SELECT id, nombre, apellido, direccion, telefono FROM clientes WHERE correo='$email'";
    } elseif($_SESSION['userType'] == 'employee'){
      $sql = "SELECT nombre FROM empleados WHERE correo='$email'";
      header('location: index.php');
    }
    $col = $mysqli->query($sql);
    $resul = $col->fetch_assoc();
    if($resul>0){
      $name = $resul['nombre'];
      if(isset($resul['apellido']) && isset($resul['direccion']) && isset($resul['telefono']) && isset($resul['id'])){
        $lname = $resul['apellido'];
        $address = $resul['direccion'];
        $phone = $resul['telefono'];
        $id = $resul['id'];
      }
    }
  }

  if(isset($_POST['orderSubmit'])){
    $ordered = 0;
    if(isset($_COOKIE['noreg-id'])){
      $cookie = $_COOKIE['noreg-id'];
      $sql = "SELECT ordenado FROM clientes_no_registrados WHERE id='$cookie'";
      $query = $mysqli->query($sql);
      $resul = $query->fetch_assoc();
      if($resul>0){
        $ordered = $resul['ordenado'];
      } else{
        unset($_COOKIE['noreg-id']);
        setcookie('noreg-id', null, -1, '/');
      }
    } elseif($logged && $_SESSION['userType'] == 'client'){
      $sql = "SELECT ordenado FROM clientes WHERE id='$id'";
      $query = $mysqli->query($sql);
      $resul = $query->fetch_assoc();
      if($resul>0){
        $ordered = $resul['ordenado'];
      }
    }
    
    if($ordered != 1):
    $name_noreg = '';
    $lname_noreg  = '';
    $address_noreg = '';
    $phone_noreg = '';
    $email_noreg = '';

    if(isset($_POST['orderName']) && isset($_POST['orderLname']) && isset($_POST['orderAddress']) && isset($_POST['orderPhone']) && isset($_POST['orderEmail'])){
      $name_noreg = $mysqli->real_escape_string($_POST['orderName']);
      $lname_noreg  = $mysqli->real_escape_string($_POST['orderLname']);
      $address_noreg = $mysqli->real_escape_string($_POST['orderAddress']);
      $phone_noreg = $mysqli->real_escape_string($_POST['orderPhone']);
      $email_noreg = $mysqli->real_escape_string($_POST['orderEmail']);
    }

    if(!$logged){
      if(!isset($_COOKIE['noreg-id'])){
      $sql = "INSERT INTO clientes_no_registrados (nombre, apellido, direccion, telefono, correo) VALUES ('$name_noreg', '$lname_noreg', '$address_noreg', '$phone_noreg', '$email_noreg')";
      $query = $mysqli->query($sql);
      if($query){
        // echo 'Añadido Datos Cliente no registrado';
      } else{
        exit("No se pudo agregar los datos");
      }
    }
    }

    $products = $_POST['orderProduct'];
    for ($i=0; $i < count($products); $i++) { 
      $product_name = "";
      $info = [];
      $cant = '';
      switch ($products[$i]) {
        case '1':
          $product_name = "Arepa";
          $info = ['miel'=>isset($_POST['arepaMiel']), 'lecherita'=>isset($_POST['arepaLeche']), 'mantequilla'=>isset($_POST['arepaManteq'])];
          $cant = $_POST['arepaCant'];
          break;

        case '2':
          $product_name = "Mixta";
          $info = ['carne'=>isset($_POST['mixtaCarne']), 'pollo'=>isset($_POST['mixtaPollo']), 'jamon'=>isset($_POST['mixtaJamon']), 'salsa'=>isset($_POST['mixtaSalsa'])];
          $cant = $_POST['mixtaCant'];
          break;

        case '3':
          $product_name = "Arepa Hamburgesa";
          $info = ['especial'=>isset($_POST['aHamEspecial']), 'salsa'=>isset($_POST['aHamSalsa'])];
          $cant = $_POST['aHamCant'];
          break;

        case '4':
          $product_name = "Hamburgesa";
          $info = ['conCebolla'=>isset($_POST['hamCebolla']), 'salsa'=>isset($_POST['hamSalsa'])];
          $cant = $_POST['hamCant'];
          break;

        case '5':
          $product_name = "Patacón";
          $info = ['conQueso'=>isset($_POST['pataconQueso'])];
          $cant = $_POST['pataconCant'];
          break;

        default:
          header('location: order.php');
          break;
      }
      $info_sr = serialize($info);
      if($logged && $_SESSION['userType'] == 'client'){
        $sql = "INSERT INTO pedidos (producto, info, cantidad, nombre_pedido, apellido_pedido, direccion_pedido, telefono_pedido, correo_pedido, id_cliente) VALUES ('$product_name', '$info_sr', '$cant', '$name', '$lname', '$address', '$phone', '$email', '$id')";
        $query = $mysqli->query($sql);
        if($query){
          $orderSql = "UPDATE clientes SET ordenado=1 WHERE id='$id'";
          $updateQuery = $mysqli->query($orderSql);
          header('location: orders.php');
        } else{
          exit('No se pudo registrar el pedido');
        }
      } else{
        $sql = "SELECT id FROM clientes_no_registrados WHERE nombre='$name_noreg' AND apellido='$lname_noreg' AND direccion='$address_noreg' AND telefono='$phone_noreg' AND correo='$email_noreg'";
        $query = $mysqli->query($sql);
        $assoc = $query->fetch_all();
        if($assoc>0){
          if(isset($assoc[count($assoc)-1])){
            $last_id = $assoc[count($assoc)-1];
            $id_no_reg = $last_id[0];
          } else{
            header('location: order.php');
          }
          if(!isset($_COOKIE['noreg-id'])){
            setcookie('noreg-id', $id_no_reg, time() + (60 * 60), '/');
          } else{
            $id_no_reg = $_COOKIE['noreg-id'];
          }

          $sql = "INSERT INTO pedidos (producto, info, cantidad, nombre_pedido, apellido_pedido, direccion_pedido, telefono_pedido, correo_pedido, id_no_reg) VALUES ('$product_name', '$info_sr', '$cant', '$name_noreg', '$lname_noreg', '$address_noreg', '$phone_noreg', '$email_noreg', '$id_no_reg')";
          $orderQuery = $mysqli->query($sql);
          if($orderQuery){
            $orderSql = "UPDATE clientes_no_registrados SET ordenado=1 WHERE id='$id_no_reg'";
            $updateQuery = $mysqli->query($orderSql);
            header('location: orders.php');
          } else{
            exit('No se pudo registrar el pedido');
          }
        }
        
      }

    }
  else:
    echo '<script>alert("Ya ordenaste un pedido! Espera a que te lo envíen.")</script>';
  endif;
  }
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pedir | Arepahamburger La 14</title>
  <link rel="shortcut icon" href="images/arepa.png" type="image/x-icon">
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100;0,400;0,700;1,400;1,700&family=Jost:wght@300;400;700&family=Nunito:wght@400;700&display=swap" rel="stylesheet">

  <script src="https://kit.fontawesome.com/5bc1d976fa.js" crossorigin="anonymous"></script>

  <!-- CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
  <link rel="stylesheet" href="css/styles.css">
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
            <a href="index.php"><h1 class="title-text">Arepahamburger la 14</h1></a>
          </div>
          
          <div class="account-container">
            <a href="account.php" class="sign-in <?php if(!$logged){echo 'hidden';} ?>">
              <i class="fa-solid fa-circle-user"></i>
              <?php
                if($logged){
                  echo "<span>" . $name . "</span>";
                }
              ?>
            </a>
            <a href="signin.php" class="sign-in <?php if($logged){echo 'hidden';} ?>">
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
            <a href="order.php" class="nav-link selected">Pedir</a>
            <a href="orders.php" class="nav-link <?php if(!$logged && !isset($_COOKIE['noreg-id'])){ echo "hidden"; } ?>">
            <?php
                if($logged && $_SESSION['userType'] == 'client' || isset($_COOKIE['noreg-id'])){
                  echo "Tus Pedidos";
                } elseif($logged && $_SESSION['userType'] == 'employee'){
                  echo "Pedidos";
                }
              ?>
            </a>
            <a href="#" class="nav-link">Comentar</a>
            <a href="clients.php" class="nav-link <?php if(!$logged || $logged && $_SESSION['userType'] != 'employee'){ echo "hidden"; } ?>">Clientes</a>
          </div>
        </div>
      </div>
    </nav>
  </div>
  
  <!-- Order Page -->
  <div id="order-page">
    <div class="order-title-container">
      <h2>Realize su pedido en línea</h2>
    </div>
    <form method="post">
    <div class="select-menu-container">
      <p class="select-menu-text">Elija lo que desea pedir:</p>
      <div class="menu-container">
        <div class="row text-center">
          <div class="col-6 col-md food-container">
            <button type="button" class="select-button btn-arepa"></button>
            <p class="select-text">Arepa</p>
          </div>
          <div class="col-6 col-md food-container">
            <button type="button" class="select-button btn-mixta"></button>
            <p class="select-text">Arepa Mixta</p>
          </div>
          <div class="col-6 col-md food-container">
            <button type="button" class="select-button btn-arepahamburg"></button>
            <p class="select-text">Arepa Hamburgesa</p>
          </div>
          <div class="col-6 col-md food-container">
            <button type="button" class="select-button btn-hamburg"></button>
            <p class="select-text">Hamburgesa</p>
          </div>
          <div class="col-6 col-md food-container">
            <button type="button" class="select-button btn-patacon"></button>
            <p class="select-text">Patacón</p>
          </div>
        </div>
      </div>
      <div id="order-products-input">
        <input type="hidden" name="orderProduct[]" value="1" required>
        <input type="hidden" name="orderProduct[]" value="2" disabled="disabled" required>
        <input type="hidden" name="orderProduct[]" value="3" disabled="disabled" required>
        <input type="hidden" name="orderProduct[]" value="4" disabled="disabled" required>
        <input type="hidden" name="orderProduct[]" value="5" disabled="disabled" required>
      </div>
    </div>
    <div class="select-menu-container dark">
      <p class="select-menu-text">Cómo quiere su producto:</p>
      <div class="menu-container">
        <div class="product-info-warning hidden text-center"><h3>Seleccione un producto arriba.</h3></div>
        <div class="row">
          <div class="col-lg col-md-6 col-12 product-info-container">
            <div class="product-info">
              <p class="product-info-title">Arepa</p>
              <div class="input-container"><input type="checkbox" name="arepaMiel" checked> <span>Miel</span></div>
              <div class="input-container"><input type="checkbox" name="arepaLeche" checked> <span>Leche Condensada</span></div>
              <div class="input-container"><input type="checkbox" name="arepaManteq" checked> <span>Mantequilla</span></div>
              <div class="input-container"><span>Cantidad: </span> <input type="number" min="1" max="100" value="1" name="arepaCant" required></div>
            </div>
          </div>
          <div class="col-lg col-md-6 col-12 product-info-container">
            <div class="product-info">
              <p class="product-info-title">Arepa Mixta</p>
              <div class="input-container"><input type="checkbox" name="mixtaCarne" checked> <span>Carne</span></div>
              <div class="input-container"><input type="checkbox" name="mixtaPollo" checked> <span>Pollo</span></div>
              <div class="input-container"><input type="checkbox" name="mixtaJamon" checked> <span>Jamón</span></div>
              <div class="input-container"><input type="checkbox" name="mixtaSalsa" checked> <span>Salsas</span></div>
              <div class="input-container"><span>Cantidad: </span> <input type="number" min="1" max="100" value="1" name="mixtaCant" required></div>
            </div>
          </div>
          <div class="col-lg col-md-6 col-12 product-info-container">
            <div class="product-info">
              <p class="product-info-title">Arepa Hamburgesa</p>
              <div class="input-container"><input type="checkbox" name="aHamEspecial" > <span>Especial</span></div>
              <div class="input-container"><input type="checkbox" name="aHamSalsa" checked> <span>Salsas</span></div>
              <div class="input-container"><span>Cantidad: </span> <input type="number" min="1" max="100" value="1" name="aHamCant" required></div>
            </div>
          </div>
          <div class="col-lg col-md-6 col-12 product-info-container">
            <div class="product-info">
              <p class="product-info-title">Hamburgesa</p>
              <div class="input-container"><input type="checkbox" name="hamCebolla" checked> <span>Con cebolla</span></div>
              <div class="input-container"><input type="checkbox" name="hamSalsa" checked> <span>Salsas</span></div>
              <div class="input-container"><span>Cantidad: </span> <input type="number" min="1" max="100" value="1" name="hamCant" required></div>
            </div>
          </div>
          <div class="col-lg col-md-12 col-12 product-info-container">
            <div class="product-info">
              <p class="product-info-title">Patacón</p>
              <div class="input-container"><input type="checkbox" name="pataconQueso" checked> <span>Con Queso</span></div>
              <div class="input-container"><span>Cantidad: </span> <input type="number" min="1" max="100" value="1" name="pataconCant" required></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php
    if(!$logged):
    ?>
      <div class="select-menu-container">
      <p class="select-menu-text">Dónde quiere entregar su pedido:<br><span>(Solo pedidos a Caicedonia)</span></p>
      <div class="menu-container">
        <div class="person-info">
          <div class="input-container names-input row">
            <div class="input-flex name-input-container col-md-6">
              <span>Nombre:</span>
              <input type="text" name="orderName" required>
            </div>
            <div class="input-flex lname-input-container col-md-6">
              <span>Apellidos:</span>
              <input type="text" name="orderLname" required>
            </div>
          </div>
          <div class="input-container">
            <div class="input-flex">
              <span>Dirección:</span>
              <input type="text" name="orderAddress" placeholder="Carrera o Calle xy #12-34" required>
            </div>
          </div>
          <div class="input-container">
            <div class="input-flex">
              <span>Número de Teléfono:</span>
              <div class="telephone-container">
                <span>+57</span>
                <input type="tel" name="orderPhone" required>
              </div>
            </div>
          </div>
          <div class="input-container">
            <div class="input-flex">
              <span>Correo Electronico:</span>
              <input type="email" name="orderEmail" placeholder="usuario@ejemplo.com" required>
            </div>
          </div>
        </div>
        <div class="btn-container">
          <button type="submit" name="orderSubmit" class="order-button-product btn" id="order-page-submit">Solicitar Pedido</button>
        </div>
      </div>
    </div>
    <?php
    elseif ($logged && $_SESSION['userType'] == 'client'):
    ?>
      <div class="select-menu-container">
      <p class="select-menu-text">Tus Datos:<br><span>(Solo pedidos a Caicedonia)</span></p>
      <div class="menu-container">
        <div class="person-info">
          <div class="input-container names-input row">
            <div class="input-flex name-input-container col-md-6">
              <span>Nombre:</span>
              <h4><?php echo $name ?></h4>
            </div>
            <div class="input-flex lname-input-container col-md-6">
              <span>Apellidos:</span>
              <h4><?php echo $lname ?></h4>
            </div>
          </div>
          <div class="input-container">
            <div class="input-flex">
              <span>Dirección:</span>
              <h4><?php echo $address ?></h4>
            </div>
          </div>
          <div class="input-container">
            <div class="input-flex">
              <span>Número de Teléfono:</span>
              <div class="telephone-container">
                <h5>+57</h5>
                <h5><?php echo $phone ?></h5>
              </div>
            </div>
          </div>
          
        </div>
        <div class="btn-container">
          <button type="submit" name="orderSubmit" class="order-button-product btn" id="order-page-submit">Solicitar Pedido</button>
        </div>
      </div>
    </div>
    <?php
    endif;
    ?>
    </form>
  </div>
  <!-- Footer -->
  <div id="footer">
    <div class="footer text-center">
      <h3 class="contact-title">Contáctanos</h3>
      <div class="contact-section">
        <div>
          <i class="fa-solid fa-phone"></i>
          <i class="fa-brands fa-whatsapp"></i>
          <span>+57 318 000 0000</span>
        </div>
        <div>
          <i class="fa-solid fa-phone"></i>
          <span>321 715 6571</span>
        </div>
        <div>
          <i class="fa-solid fa-location-dot"></i>
          <span>Carrera 14 #5-54 Caicedonia, Valle del Cauca</span>
        </div>
      </div>
      <div class="credits-container d-flex">
        <div class="sena-logo">
          <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/83/Sena_Colombia_logo.svg/2090px-Sena_Colombia_logo.svg.png" alt="">
        </div>
        <div class="copyright">
          <i class="fa-solid fa-copyright"></i>
          <span>Manuel Esteban Ramírez Umaña - 2022<br>Logo by <a href="https://www.flaticon.com" target="_blank">Flaticon</a></span>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript Bundle with Popper -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
  <script src="js/index.js"></script>
</body>
  <script src="js/order.js"></script>
</html>