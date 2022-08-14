<?php
require 'conexion.php';
session_start();
if (isset($_SESSION['useremail'])) {
  #Iniciar Sección
  header("location: index.php");
}
$email = $mysqli->real_escape_string($_POST['email']);
$password = $mysqli->real_escape_string($_POST['password']);
$userType = $mysqli->real_escape_string($_POST['user-type']);

$sql_client = "SELECT id, correo, contraseña FROM clientes WHERE correo='$email'";
$sql_employee = "SELECT id, correo, contraseña FROM empleados WHERE correo='$email'";

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Arepahamburger La 14</title>
  <link rel="shortcut icon" href="images/arepa.png" type="image/x-icon" />
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100;0,400;0,700;1,400;1,700&family=Jost:wght@300;400;700&family=Nunito:wght@400;700&display=swap" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/5bc1d976fa.js" crossorigin="anonymous"></script>

  <!-- CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous" />
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/signin.css" />
</head>

<body>
  <div class="" id="nav">
    <nav class="navbar navbar-expand-md bg-light">
      <div class="nav-container">
        <div class="nav-top d-flex">
          <div class="logo-container">
            <a href="index.php">
              <img class="logo-image" src="images/arepa.png" alt="" />
            </a>
          </div>
          <div class="title">
            <a href="index.php">
              <h1 class="title-text">Arepahamburger la 14</h1>
            </a>
          </div>

          <div class="account-container">
            <!-- <a href="#" class="sign-in">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Iniciar Sección</span>
              </a> -->
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
            <a href="#" class="nav-link">Pedir</a>
            <a href="#" class="nav-link">Comentar</a>
          </div>
        </div>
      </div>
    </nav>
  </div>
  <!-- content -->
  <div id="sign-in">
    <div class="row sign-in-pages">
      <div class="col page">
        <div class="log-in-page">
          <h2>Iniciar Sección</h2>
          <div class="sign-in-form">
            <form action="signin.php" method="POST">
              <div>
                <label for="user-type">Tipo de Usuario (Cliente o Empleado):</label>
                <select name="user-type" id="user-type">
                  <option value="client">Cliente</option>
                  <option value="employee">Empleado</option>
                </select>
              </div>
              <div class="input-flex">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" placeholder="nombre@ejemplo.com" required />
              </div>
              <div class="input-flex">
                <label for="password">Contraseña:</label>
                <input type="password" name="password" placeholder="Introduce tu contraseña" required />
              </div>
              <div>
                <?php
                if (!isset($_SESSION['useremail'])) {
                  if ($userType == 'client') {
                    $result = $mysqli->query($sql_client);
                    if ($result > 0) {
                      $row = $result->fetch_assoc();
                      if ($row['contraseña'] == $password) {
                        $_SESSION['useremail']= $email;
                        $_SESSION['userType'] = $userType;
                        header("location: index.php");
                      } else {
                        echo "<p class='warning-text'>Contraseña o correo incorrectos</p>";
                      }
                    } else {
                      echo "<p class='warning-text'>El Usuario no está registrado</p>";
                    }
                  } elseif ($userType == 'employee') {
                    $result = $mysqli->query($sql_employee);
                    if ($result > 0) {
                      $row = $result->fetch_assoc();
                      if ($row['contraseña'] == $password) {
                        #Iniciar Sección
                        $_SESSION['useremail']= $email;
                        $_SESSION['userType'] = $userType;
                        header("location: index.php");
                      } else {
                        echo "<p class='warning-text'>Contraseña o correo incorrectos</p>";
                      }
                    } else {
                      echo "<p class='warning-text'>El Usuario no está registrado</p>";
                    }
                  }
                }
                ?>
              </div>
              <input type="submit" value="Iniciar Sección" name="btnsubmit" class="log-in-button btn" />
            </form>
          </div>
        </div>
      </div>
      <div class="col-sm-12 col-md-6 page">
        <div class="sign-up-page">
          <h2>Registrarse</h2>
          <p class="sign-up-text">
            Registrate para no tener que escribir tu dirección cada vez y
            comentar que te a parecido tu experiencia.
          </p>
          <div class="sign-up-form">
            <form action="sign.php" method="post">
              <div class="row">
                <div class="input-flex col">
                  <label for="name">Nombre:</label>
                  <input type="text" name="name" placeholder="Ingrese su nombre"  required/>
                </div>
                <div class="input-flex col">
                  <label for="lname">Apellido:</label>
                  <input type="text" name="lname" placeholder="Ingrese su Apellido" required/>
                </div>
              </div>
              <div class="input-flex">
                <label for="email">Correo Electrónico</label>
                <input type="email" name="email" placeholder="nombre@ejemplo.com" required>
              </div>
              <div class="row">
                <div class="input-flex col">
                  <label for="password1">Contraseña:</label>
                  <input type="password" id="password1" name="password1" onblur="savePass()" oninput="checkPassLength()" required>
                </div>
                <div class="input-flex col">
                  <label for="password2">Confirmar contraseña:</label>
                  <input type="password" name="password2" id="password2" oninput="checkPassword()" required>
                </div>
              </div>
              <div class="password-error">
                <p class="warning-text" id="password-warning"></p>
              </div>
              <div class="input-flex">
                <label for="address">Dirección:</label>
                <input type="text" name="address" id="address" placeholder="Igrese su dirección de domicilio" required>
              </div>
              <div class="input-flex">
                <label for="phone">Número de teléfono:</label>
                <div>
                  <span>+57</span>
                  <input type="tel" name="phone" id="phone" required>
                </div>
              </div>
              <input type="submit" value="Registrarse" class="log-in-button btn" id="reg-submit-btn">
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript Bundle with Popper -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
</body>
<?php if(isset($_GET['email-taken'])){
    if($_GET['email-taken'] == true){
      echo "<script>alert('Este usuario ya existe')</script>";
    }
  } 
  ?>
<script src="js/index.js"></script>
<script src="js/order.js"></script>
<script src="js/log-in.js"></script>

</html>