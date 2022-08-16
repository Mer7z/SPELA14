<?php
  require 'conexion.php';
  session_start();
  $logged = false;
  $name = '';
  $lname = '';
  $email = '';
  $phone = '';
  $address = '';
  if(isset($_SESSION['useremail'])){
    $email = $_SESSION['useremail'];
    $logged = true;
    $sql = '';
    if($_SESSION['userType'] == 'client'){
      $sql = "SELECT nombre, apellido, direccion, telefono FROM clientes WHERE correo='$email'";
    } elseif($_SESSION['userType'] == 'employee'){
      $sql = "SELECT nombre FROM empleados WHERE correo='$email'";
    }
    $con = $mysqli->query($sql);
    $result = $con->fetch_assoc();
    if($result>0){
      $name = $result['nombre'];
      if(isset($result['apellido']) && isset($result['telefono']) && isset($result['direccion'])){
        $lname = $result['apellido'];
        $phone = $result['telefono'];
        $address = $result['direccion'];
      }
    }
  } else{
    header("location: signin.php");
  }

  if(isset($_POST['btncerrar'])){
    session_destroy();
    header("location: signin.php");
  }
  if(isset($_POST['btneditar'])){
    header("location: account-edit.php");
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
  <link rel="stylesheet" href="css/signin.css">
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
  <!-- content -->
  <div id="account-page">
    <h2 class="account-title">Cuenta</h2>
    <div class="account-data-container">
      <h3 class="account-name">
        <?php 
          echo $name . " " . $lname;
        ?>
      </h3>
      <div class="account-data">
        <p class="account-text">Correo:</p>
        <input class="account-info" type="text" value="<?php echo $email ?>" readonly>
      </div>
      <div class="account-data">
        <p class="account-text">Dirección:</p>
        <input class="account-info" type="text" value="<?php echo $address ?>" readonly>
      </div>
      <div class="account-data">
        <p class="account-text">Número de Teléfono:</p>
        <div class="d-flex">
        <span class="number-code">+57</span>  
        <input class="account-info" type="text" value="<?php echo $phone ?>" readonly>
        </div>
        <div class="log-out-container row">
          <div class="col">
            <form method="post">
              <input type="submit" value="Cerrar Sección" name="btncerrar" class="btn log-in-button">
            </form>
          </div>
          <div class="col">
            <form action="" method="post">
              <button type="submit" name="btneditar" class="btn edit-button">Editar</button>
            </form>
          </div>
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

<?php
  
?>