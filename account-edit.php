<?php
  require 'conexion.php';
  session_start();
  $logged = false;
  $name = '';
  $lname = '';
  $email = '';
  $phone = '';
  $address = '';
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
      $sql = "SELECT nombre, apellido, direccion, telefono FROM clientes WHERE correo='$email'";
    } elseif($_SESSION['userType'] == 'employee'){
      $sql = "SELECT nombre FROM empleados WHERE correo='$email'";
    }
    $col = $mysqli->query($sql);
    $resul = $col->fetch_assoc();
    if($resul>0){
      $name = $resul['nombre'];
      if(isset($resul['apellido']) && isset($resul['telefono']) && isset($resul['direccion'])){
        $lname = $resul['apellido'];
        $phone = $resul['telefono'];
        $address = $resul['direccion'];
      }
    }
  } else{
    header("location: signin.php");
  }

  if(isset($_POST['cancel-edit'])){
    header("location: account.php");
  }

  if(isset($_POST['edit-account'])){
    $sql_get_id = '';
    if($_SESSION['userType'] == 'client'){
      $sql_get_id = "SELECT id FROM clientes WHERE correo='" . $_SESSION['useremail'] . "'";
    } elseif($_SESSION['userType'] == 'employee'){
      $sql_get_id = "SELECT id FROM empleados WHERE correo='" . $_SESSION['useremail'] . "'";
    }
    $result = $mysqli->query($sql_get_id);
    $row = $result->fetch_assoc();
    if(isset($row['id']))
    {
    $id = $row['id'];
    $name = $mysqli->real_escape_string($_POST['name']);
    $lname = $mysqli->real_escape_string($_POST['lname']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $password = $mysqli->real_escape_string($_POST['password1']);
    $address = $mysqli->real_escape_string($_POST['address']);
    $phone = $mysqli->real_escape_string($_POST['phone']);
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql_update = '';
    if($_SESSION['userType'] == 'client'){
      $sql_update = "UPDATE clientes SET nombre='$name', apellido='$lname', correo='$email', contraseña='$hash', direccion='$address', telefono='$phone' WHERE id='$id'";
    } elseif($_SESSION['userType'] == 'employee'){
      $sql_update = "UPDATE empleados SET nombre='$name', correo='$email', contraseña='$hash' WHERE id='$id'";
    }
    $con_edit = $mysqli->query($sql_update);
    if($con_edit){
      $_SESSION['useremail']=$email;
      setcookie('userEmail', $email, time() + (86400 * 30), "/");
      setcookie('token', $hash, time() + (86400 * 30), "/");
      setcookie('id', $_COOKIE['id'] , time() + (86400 * 30), "/");
      header("location: account.php");
    } else{
      echo "<script>Ocurrió un Error</script>";
    }
    } else{
      echo "<script>Ocurrió un Error</script>";
    }
  }
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cuenta | Arepahamburger La 14</title>
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
          </div>
        </div>
      </div>
    </nav>
  </div>
  <!-- content -->
  <div id="account-page">
    <h2 class="account-title">Editar Cuenta</h2>
    <div class="account-data-container">
      <form method="post">
      <div class="account-data row">
        <div class="col">
          <p class="account-text">Nombre:</p>
          <input type="text" name="name" class="account-info" value="<?php echo $name ?>" required>
        </div>
        <div class="col">
          <p class="account-text">Apellido:</p>
          <input type="text" name="lname" class="account-info" value="<?php echo $lname ?>" <?php if($_SESSION['userType'] == 'client'){echo "required";} ?> >
        </div>
      </div>
      <div class="account-data">
        <p class="account-text">Correo:</p>
        <input class="account-info" type="text" value="<?php echo $email ?>" name="email" required>
      </div>
      <div class="account-data">
        <div class="row">
          <div class="col-md col-sm-12">
            <p class="account-text">Nueva contraseña:</p>
            <input class="account-info" type="password" name="password1" id="password1" onblur="savePass()" oninput="checkPassLength()" required>
          </div>
          <div class="col-md col-sm-12">
            <p class="account-text">Confirmar contraseña:</p>
            <input class="account-info" type="password" name="password2" id="password2" oninput="checkPassword()" required>
          </div>
        </div>
        <div>
          <p id="password-warning" class="warning-text"></p>
        </div>
      </div>
      <div class="account-data">
        <p class="account-text">Dirección:</p>
        <input class="account-info" type="text" value="<?php echo $address ?>" name="address" <?php if($_SESSION['userType'] == 'client'){echo "required";} ?>>
      </div>
      <div class="account-data">
        <p class="account-text">Número de Teléfono:</p>
        <div class="d-flex">
          <span class="number-code">+57</span>  
          <input class="account-info" type="text" value="<?php echo $phone ?>" name="phone" <?php if($_SESSION['userType'] == 'client'){echo "required";} ?>>
        </div>
        <div class="log-out-container row">
          <div class="col">
            <button type="submit" name="edit-account" class="btn edit-button" id="reg-submit-btn">Editar</button>
          </div>
        </form>
          <div class="col">
          <form method="post">
            <button type="submit" name="cancel-edit" class="btn cancel-button">Cancelar</button>
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
  <script src="js/log-in.js"></script>
</html>

<?php
  
?>