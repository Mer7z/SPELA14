<?php
  require 'conexion.php';
  session_start();
  $logged = false;
  $name = '';
  $email = '';
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
      $sql = "SELECT nombre FROM clientes WHERE correo='$email'";
    } elseif($_SESSION['userType'] == 'employee'){
      $sql = "SELECT nombre FROM empleados WHERE correo='$email'";
    }
    $con = $mysqli->query($sql);
    $resul = $con->fetch_assoc();
    if($resul>0){
      $name = $resul['nombre'];
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
            <a href="orders.php" class="nav-link <?php if(!$logged && !isset($_COOKIE['noreg-id'])){ echo "hidden"; } ?>">
            <?php
                if($logged && $_SESSION['userType'] == 'client' || isset($_COOKIE['noreg-id'])){
                  echo "Tus Pedidos";
                } elseif($logged && $_SESSION['userType'] == 'employee'){
                  echo "Pedidos";
                }
              ?>
            </a>
            
          </div>
        </div>
      </div>
    </nav>
  </div>
  <!-- content -->
  <div id="home-page" class="">
    <div class="poster text-center">
      <h2>AREPAS DE QUESO Y MÁS</h2>
      <h3>¡Ven a disfrutar de las más ricas arepas de queso y mucho más en Caicedonia!</h3>
      <p>Pide ahora o llámanos al 318 000 0000</p>
      <div class="btn-title d-flex">
        <div>
          <a href="order.php" class="order-button-title btn btn-lg">Pedir</a>
        </div>
        <div>
          <a href="signin.php" class="log-in-title-btn btn btn-lg">Iniciar Sección</a>
        </div>
      </div>
      
    </div>
    <div class="products-about text-center">
      <h3>Antojate de la ricas arepas y demás productos de calidad <strong>¡Con 30 años de experiencia!</strong></h3>
      <div class="products-grid">
        <div class="row">
          <div class="col">
            <img class="product-img" src="https://i.pinimg.com/originals/e1/40/e3/e140e3595fe7ac017c145f481483ab73.png" alt="">
            <p class="product-text">Arepas de Queso</p>
          </div>
          <div class="col">
            <img class="product-img" src="data:image/jpeg;base64,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" alt="">
            <p class="product-text">Arepas Mixta</p>
          </div>
          <div class="col">
            <img class="product-img" src="https://fromtextstotable.files.wordpress.com/2016/02/hamburguesa-con-arepa-ns0-opt485x385o00s485x385.jpg?w=640" alt="">
            <p class="product-text">Arepas Hamburgesa</p>
          </div>
          <div class="col">
            <img class="product-img" src="https://lh3.googleusercontent.com/p/AF1QipM1eF_0ZPZFOsG7-sqs-MVxhdCxaBBjpxeEVhGa=w1080-h608-p-no-v0" alt="">
            <p class="product-text">Hamburgesas</p>
          </div>
          <div class="col">
            <img class="product-img" src="data:image/jpeg;base64,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" alt="">
            <p class="product-text">Patacones</p>
          </div>
        </div>
      </div>
      <p class="order-text-product">Pide domicilio YA:</p>
      <a href="order.php" class="order-button-product btn btn-lg">Pedir</a>
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
          <span>Manuel Esteban Ramírez Umaña - 2022<br>Logo by <a href="https://www.flaticon.com" target="_blank">Flaticon</a></span>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript Bundle with Popper -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script></body>
  <script src="js/index.js"></script>
</html>