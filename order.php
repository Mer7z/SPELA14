<?php
  require 'conexion.php';
  session_start();
  $logged = false;
  $name = '';
  $lname = '';
  $email = '';
  $address = '';
  $phone = '';
  if(isset($_SESSION['useremail'])){
    $email = $_SESSION['useremail'];
    $logged = true;
    $sql = '';
    if($_SESSION['userType'] == 'client'){
      $sql = "SELECT nombre FROM clientes WHERE correo='$email'";
    } elseif($_SESSION['userType'] == 'employee'){
      $sql = "SELECT nombre FROM empleados WHERE correo='$email'";
    }
    $resul = $mysqli->query($sql);
    if($resul>0){
      $row = $resul->fetch_assoc();
      $name = $row['nombre'];
      if(isset($row['apellido']) && isset($row['direccion']) && isset($row['telefono'])){
        $lname = $row['apellido'];
        $address = $row['direccion'];
        $phone = $row['telefono'];
      }
    }
  }
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Arepahamburger La 14</title>
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
            <a href="#">
              <img class="logo-image" src="images/arepa.png" alt="">
            </a>
          </div>
          <div class="title">
            <a href="#"><h1 class="title-text">Arepahamburger la 14</h1></a>
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
            <a href="index.php" class="nav-link selected">Inicio</a>
            <a href="order.php" class="nav-link">Pedir</a>
            <a href="orders.php" class="nav-link <?php if(!$logged){ echo "hidden"; } ?>">
            <?php
                if($logged && $_SESSION['userType'] == 'client'){
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
    <div class="select-menu-container">
      <p class="select-menu-text">Elija lo que desea pedir:</p>
      <div class="menu-container">
        <div class="row text-center">
          <div class="col-6 col-md food-container">
            <button class="select-button btn-arepa button-selected"></button>
            <p class="select-text">Arepa</p>
          </div>
          <div class="col-6 col-md food-container">
            <button class="select-button btn-mixta"></button>
            <p class="select-text">Arepa Mixta</p>
          </div>
          <div class="col-6 col-md food-container">
            <button class="select-button btn-arepahamburg"></button>
            <p class="select-text">Arepa Hamburgesa</p>
          </div>
          <div class="col-6 col-md food-container">
            <button class="select-button btn-hamburg"></button>
            <p class="select-text">Hamburgesa</p>
          </div>
          <div class="col-6 col-md food-container">
            <button class="select-button btn-patacon"></button>
            <p class="select-text">Patacón</p>
          </div>
        </div>
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
              <div class="input-container"><input type="checkbox" checked> <span>Miel</span></div>
              <div class="input-container"><input type="checkbox" checked> <span>Leche Condensada</span></div>
              <div class="input-container"><input type="checkbox" checked> <span>Mantequilla</span></div>
              <div class="input-container"><span>Cantidad: </span> <input type="number" min="1" max="100" value="1"></div>
            </div>
          </div>
          <div class="col-lg col-md-6 col-12 product-info-container">
            <div class="product-info">
              <p class="product-info-title">Arepa Mixta</p>
              <div class="input-container"><input type="checkbox" checked> <span>Carne</span></div>
              <div class="input-container"><input type="checkbox" checked> <span>Pollo</span></div>
              <div class="input-container"><input type="checkbox" checked> <span>Jamón</span></div>
              <div class="input-container"><input type="checkbox" checked> <span>Salsas</span></div>
              <div class="input-container"><span>Cantidad: </span> <input type="number" min="1" max="100" value="1"></div>
            </div>
          </div>
          <div class="col-lg col-md-6 col-12 product-info-container">
            <div class="product-info">
              <p class="product-info-title">Arepa Hamburgesa</p>
              <div class="input-container"><input type="checkbox" > <span>Especial</span></div>
              <div class="input-container"><input type="checkbox" checked> <span>Salsas</span></div>
              <div class="input-container"><span>Cantidad: </span> <input type="number" min="1" max="100" value="1"></div>
            </div>
          </div>
          <div class="col-lg col-md-6 col-12 product-info-container">
            <div class="product-info">
              <p class="product-info-title">Hamburgesa</p>
              <div class="input-container"><input type="checkbox" checked> <span>Con cebolla</span></div>
              <div class="input-container"><input type="checkbox" checked> <span>Salsas</span></div>
              <div class="input-container"><span>Cantidad: </span> <input type="number" min="1" max="100" value="1"></div>
            </div>
          </div>
          <div class="col-lg col-md-12 col-12 product-info-container">
            <div class="product-info">
              <p class="product-info-title">Patacón</p>
              <div class="input-container"><input type="checkbox" checked> <span>Con Queso</span></div>
              <div class="input-container"><span>Cantidad: </span> <input type="number" min="1" max="100" value="1"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="select-menu-container">
      <p class="select-menu-text">Dónde quiere entregar su pedido:<br><span>(Solo pedidos a Caicedonia)</span></p>
      <div class="menu-container">
        <div class="person-info">
          <div class="input-container names-input row">
            <div class="input-flex name-input-container col-md-6">
              <span>Nombre:</span>
              <input type="text">
            </div>
            <div class="input-flex lname-input-container col-md-6">
              <span>Apellidos:</span>
              <input type="text">
            </div>
          </div>
          <div class="input-container">
            <div class="input-flex">
              <span>Dirección:</span>
              <input type="text" placeholder="Carrera o Calle xy #12-34">
            </div>
          </div>
          <div class="input-container">
            <div class="input-flex">
              <span>Número de Teléfono:</span>
              <div class="telephone-container">
                <span>+57</span>
                <input type="tel">
              </div>
            </div>
          </div>
          <div class="input-container">
            <div class="input-flex">
              <span>Correo Electronico:</span>
              <input type="email" placeholder="usuario@ejemplo.com">
            </div>
          </div>
        </div>
        <div class="btn-container">
          <button class="order-button-product btn" id="order-page-submit">Solicitar Pedido</button>
        </div>
      </div>
    </div>
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
          <span>Manuel Esteban Ramírez Umaña - 2022</span>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript Bundle with Popper -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script></body>
  <script src="js/index.js"></script>
  <script src="js/order.js"></script>
</html>