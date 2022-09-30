<?php
require 'conexion.php';
require_once 'common.php';
session_start();
$logged = false;
$name = '';
$email = '';

if (isset($_SESSION['adminEmail']) && isset($_SESSION['verified']) && isset($_SESSION['admin'])) {
  #Iniciar Sección
  $email = $_SESSION['adminEmail'];
  $logged = true;
  if (isset($_COOKIE['noreg-id'])) {
    unset($_COOKIE['noreg-id']);
    setcookie('noreg-id', null, -1, '/');
  }
  $sql = "SELECT nombre FROM administradores WHERE correo='$email'";
  $con = $mysqli->query($sql);
  $resul = $con->fetch_assoc();
  if ($resul > 0) {
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
  <title>Admin | Arepahamburger La 14</title>
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
  <link rel="stylesheet" href="css/panel.css">
</head>

<body>
  <?php
  if (!$logged) :
  ?>
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
              <a href="#">
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
              <a href="orders.php" class="nav-link <?php if (!$logged && !isset($_COOKIE['noreg-id'])) {
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
              <a href="clients.php" class="nav-link <?php if (!$logged || $logged && $_SESSION['userType'] != 'employee') {
                                                      echo "hidden";
                                                    } ?>">Clientes</a>
            </div>
          </div>
        </div>
      </nav>
    </div>
    <!-- content -->

    <div id="login" class="container-fluid admin-poster" style="height: 100vh;">
      <div id="admin-login">
        <h4>Iniciar Sección</h4>
        <form method="POST">
          <div class="input-flex">
            <label class="account-text" for="email">Correo:</label>
            <input type="text" name="email" placeholder="Ingrese el correo" required>
          </div>
          <div class="input-flex">
            <label class="account-text" for="password">Contraseña:</label>
            <input type="password" name="password" placeholder="Ingrese la contraseña" required>
          </div>
          <?php
          if (isset($_POST['adminLogin'])) {
            $adminEmail = $mysqli->escape_string($_POST['email']);
            $adminPw = $mysqli->escape_string($_POST['password']);

            $sql = "SELECT contraseña FROM administradores WHERE correo='$adminEmail'";
            $query = $mysqli->query($sql);
            $result = $query->fetch_assoc();
            if ($result > 0) {
              if ($adminPw == $result['contraseña']) {
                $_SESSION['admin'] = true;
                $_SESSION['verified'] = true;
                $_SESSION['adminEmail'] = $adminEmail;
                header('location: admin.php');
              } else {
          ?>
                <p class="warning-text">Contraseña Incorrecta</p>
              <?php
              }
            } else {
              ?>
              <p class="warning-text">Correo Incorrecto</p>
          <?php
            }
          }
          ?>
          <input type="submit" name="adminLogin" class="btn log-in-button">
        </form>
      </div>
    </div>

  <?php
  else :
  ?>
    <?php
    if (!isset($_GET['panel'])) :
      if(isset($_POST['exitAdmin'])){
        session_destroy();
        header('location: index.php');
      }
    ?>
      <div class="container-fluid admin-poster">
        <div class="close-admin">
          <form method="POST">
            <button title="Cerrar Sección Admin" id="exit-admin-btn" type="submit" name="exitAdmin">
              <i class="fa-solid fa-right-from-bracket"></i>
            </button>
          </form>
        </div>
        <div id="admin-panel" class="row">
          <div class="col-md-6">
            <a href="?panel=clients" class="panel-button">
              <div class="panel-icon">
                <i class="fa-solid fa-users"></i>
              </div>
              <span class="panel-text">Clientes</span>
            </a>
          </div>
          <div class="col-md-6">
            <a href="?panel=employees" class="panel-button">
              <div class="panel-icon">
                <i class="fa-solid fa-user"></i>
              </div>
              <span class="panel-text">Empleados</span>
            </a>
          </div>
          <div class="col-md-6">
            <a href="?panel=orders" class="panel-button">
              <div class="panel-icon">
                <i class="fa-solid fa-cart-shopping"></i>
              </div>
              <span class="panel-text">Pedidos</span>
            </a>
          </div>
          <div class="col-md-6">
            <a href="?panel=admins" class="panel-button">
              <div class="panel-icon">
                <i class="fa-solid fa-user-gear"></i>
              </div>
              <span class="panel-text">Administradores</span>
            </a>
          </div>
        </div>
      </div>

      <?php
    #Panel Clientes
    else:
    ?>
      <div class="back-button">
        <a href="admin.php">
          <i class="fa-solid fa-arrow-left-long"></i>
        </a>
      </div>
    <?php
      if ($_GET['panel'] == 'clients') :
        $sql = "SELECT * FROM clientes WHERE activo=1";
        $query = $mysqli->query($sql);
        $results = $query->fetch_all();
      ?>
        <div class="admin-table">
          <?php
          if (isset($_GET['edit'])) :
            if (isset($_POST['editClient'])) {
              $id = $mysqli->escape_string($_POST['id']);
              $nombre = $mysqli->escape_string($_POST['name']);
              $apellido = $mysqli->escape_string($_POST['lname']);
              $correo = $mysqli->escape_string($_POST['email']);
              $pw = $mysqli->escape_string($_POST['password1']);
              $direccion = $mysqli->escape_string($_POST['address']);
              $telefono = $mysqli->escape_string($_POST['phone']);

              $hash = password_hash($pw, PASSWORD_DEFAULT);

              $sql = "UPDATE clientes SET nombre='$nombre', apellido='$apellido', correo='$correo', contraseña='$hash', direccion='$direccion', telefono='$telefono' WHERE id='$id'";
              $query = $mysqli->query($sql);
              if ($query) {
                header('location: admin.php?panel=clients');
              } else {
          ?>
                <p class="warning-text">Ocurrió un Error al editar.</p>
              <?php
              }
            } elseif (isset($_POST['deleteClient'])) {
              $id = $mysqli->escape_string($_POST['id']);
              $sql = "UPDATE clientes SET activo=0 WHERE id='$id'";
              $query = $mysqli->query($sql);
              if ($query) {
                header('location: admin.php?panel=clients');
              } else {
              ?>
                <p class="warning-text">Ocurrió un Error al borrar.</p>
            <?php
              }
            }


            $id = $_GET['edit'];
            $sql = "SELECT * FROM clientes WHERE id='$id'";
            $query = $mysqli->query($sql);
            $result = $query->fetch_assoc();
            $nombre;
            $apellido;
            $correo;
            $direccion;
            $telefono;
            $ordenado;
            if ($result > 0) {
              $nombre = $result['nombre'];
              $apellido = $result['apellido'];
              $correo = $result['correo'];
              $direccion = $result['direccion'];
              $telefono = $result['telefono'];
              $ordenado = $result['ordenado'];
            }
            ?>
            <div>
              <h2>Editar Cliente</h2>
              <div class="account-data-container">
                <form method="POST">
                  <div class="account-data row">
                    <div class="col">
                      <p class="account-text">Nombre:</p>
                      <input type="text" name="name" class="account-info" value="<?php echo $nombre ?>" required>
                    </div>
                    <div class="col">
                      <p class="account-text">Apellido:</p>
                      <input type="text" name="lname" class="account-info" value="<?php echo $apellido ?>" required>
                    </div>
                  </div>
                  <div class="account-data">
                    <p class="account-text">Correo:</p>
                    <input class="account-info" type="text" value="<?php echo $correo ?>" name="email" required>
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
                    <input class="account-info" type="text" value="<?php echo $direccion ?>" name="address" required>
                  </div>
                  <div class="account-data">
                    <p class="account-text">Número de Teléfono:</p>
                    <div class="d-flex">
                      <span class="number-code">+57</span>
                      <input class="account-info" type="text" value="<?php echo $telefono ?>" name="phone" required>
                    </div>
                  </div>
                  <input type="hidden" name="id" value="<?php echo $id ?>">
                  <div class="log-out-container row">
                    <div class="col">
                      <button type="submit" name="editClient" class="btn edit-button" id="reg-submit-btn">Editar</button>
                    </div>
                    <div class="col">
                      <a href="?panel=clients" class="btn cancel-button">Cancelar</a>
                    </div>
                  </div>
                </form>
                <form method="POST">
                  <input type="hidden" name="id" value="<?php echo $id ?>">
                  <button type="submit" name="deleteClient" class="btn btn-danger">Eliminar</button>
                </form>
              </div>
              <?php
            elseif (isset($_GET['addNew'])) :
              if ($_GET['addNew'] == 'true') {
                if (isset($_POST['addClient'])) {
                  $nombre = $mysqli->escape_string($_POST['name']);
                  $apellido = $mysqli->escape_string($_POST['lname']);
                  $correo = $mysqli->escape_string($_POST['email']);
                  $pw = $mysqli->escape_string($_POST['password1']);
                  $direccion = $mysqli->escape_string($_POST['address']);
                  $telefono = $mysqli->escape_string($_POST['phone']);

                  $hash = password_hash($pw, PASSWORD_DEFAULT);

                  $sql = "INSERT INTO clientes (nombre, apellido, correo, contraseña, direccion, telefono) VALUES ('$nombre', '$apellido', '$correo', '$hash', '$direccion', '$telefono')";
                  $query = $mysqli->query($sql);
                  if ($query) {
                    header('location: admin.php?panel=clients');
                  } else {
              ?>
                    <p class="warning-text">Ocurrió un error al añadir.</p>
                <?php
                  }
                }
                ?>
                <div>
                  <h2>Añadir Cliente</h2>
                  <div class="account-data-container">
                    <form method="POST">
                      <div class="account-data row">
                        <div class="col">
                          <p class="account-text">Nombre:</p>
                          <input type="text" name="name" class="account-info" required>
                        </div>
                        <div class="col">
                          <p class="account-text">Apellido:</p>
                          <input type="text" name="lname" class="account-info" required>
                        </div>
                      </div>
                      <div class="account-data">
                        <p class="account-text">Correo:</p>
                        <input class="account-info" type="text" name="email" required>
                      </div>
                      <div class="account-data">
                        <div class="row">
                          <div class="col-md col-sm-12">
                            <p class="account-text">Contraseña:</p>
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
                        <input class="account-info" type="text" name="address" required>
                      </div>
                      <div class="account-data">
                        <p class="account-text">Número de Teléfono:</p>
                        <div class="d-flex">
                          <span class="number-code">+57</span>
                          <input class="account-info" type="text" name="phone" required>
                        </div>
                      </div>
                      <div class="log-out-container row">
                        <div class="col">
                          <button type="submit" name="addClient" class="btn btn-success" style="margin: 2rem 0">Añadir</button>
                        </div>
                        <div class="col">
                          <a href="?panel=clients" class="btn cancel-button">Cancelar</a>
                        </div>
                      </div>
                    </form>
                  </div>
                <?php
              }
                ?>
              <?php
            else :
              ?>
                <h2>Clientes</h2>
                <div>
                  <div class="table-button-container">
                    <a href="?panel=clients&addNew=true" class="btn btn-success">Añadir</a>
                  </div>
                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Apellidos</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Dirección</th>
                        <th scope="col">Telefono</th>
                        <th scope="col">Editar</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if ($results > 0) :
                        foreach ($results as $clientRow) :
                          $clienteId;
                          $clienteNombre;
                          $clienteApellido;
                          $clienteCorreo;
                          $clienteDir;
                          $clienteTel;
                          foreach ($clientRow as $key => $data) {
                            switch ($key) {
                              case 0:
                                $clienteId = $data;
                                break;
                              case 1:
                                $clienteNombre = $data;
                                break;
                              case 2:
                                $clienteApellido = $data;
                                break;
                              case 3:
                                $clienteCorreo = $data;
                                break;
                              case 5:
                                $clienteDir = $data;
                                break;
                              case 6:
                                $clienteTel = $data;
                            }
                          }
                      ?>
                          <tr>
                            <td scope="row">
                              <?php
                              echo $clienteId
                              ?>
                            </td>
                            <td>
                              <?php
                              echo $clienteNombre
                              ?>
                            </td>
                            <td>
                              <?php
                              echo $clienteApellido
                              ?>
                            </td>
                            <td>
                              <?php
                              echo $clienteCorreo
                              ?>
                            </td>
                            <td>
                              <?php
                              echo $clienteDir
                              ?>
                            </td>
                            <td>
                              <?php
                              echo $clienteTel
                              ?>
                            </td>
                            <td>
                              <a href="?panel=clients&edit=<?php echo $clienteId; ?>" class="btn btn-warning">Editar</a>
                            </td>
                          </tr>
                      <?php
                        endforeach;
                      endif;
                      ?>
                    </tbody>
                  </table>
                </div>
              <?php
            endif;
              ?>
                </div>
              <?php
            elseif ($_GET['panel'] == 'employees') :
              $sql = "SELECT * FROM empleados";
              $query = $mysqli->query($sql);
              $results = $query->fetch_all();
              ?>
                <div class="admin-table">
                  <?php
                  if (isset($_GET['edit'])) :
                    if (isset($_POST['editEmployee'])) {
                      $id = $mysqli->escape_string($_POST['id']);
                      $nombre = $mysqli->escape_string($_POST['name']);
                      $correo = $mysqli->escape_string($_POST['email']);
                      $pw = $mysqli->escape_string($_POST['password1']);

                      $hash = password_hash($pw, PASSWORD_DEFAULT);

                      $sql = "UPDATE empleados SET nombre='$nombre', correo='$correo', contraseña='$hash' WHERE id='$id'";
                      $query = $mysqli->query($sql);
                      if ($query) {
                        header('location: admin.php?panel=employees');
                      } else {
                  ?>
                        <p class="warning-text">Ocurrió un error al editar</p>
                      <?php
                      }
                    }

                    if (isset($_POST['deleteEmployee'])) {
                      $id = $_POST['id'];

                      $sql = "DELETE FROM empleados WHERE id='$id'";
                      $query = $mysqli->query($sql);
                      if ($query) {
                        header('location: admin.php?panel=employees');
                      } else {
                      ?>
                        <p class="warning-text">Ocurrió un error al borrar</p>
                    <?php
                      }
                    }

                    $id = $_GET['edit'];
                    $nombre;
                    $correo;
                    $sql = "SELECT * FROM empleados WHERE id='$id'";
                    $query = $mysqli->query($sql);
                    $result = $query->fetch_assoc();
                    if ($result > 0) {
                      $nombre = $result['nombre'];
                      $correo = $result['correo'];
                    }
                    ?>
                    <h2>Editar Empleado</h2>
                    <div class="account-data-container">
                      <form method="POST">
                        <div class="account-data">
                          <div class="row">
                            <div class="col-md col-sm-12">
                              <p class="account-text">Nombre:</p>
                              <input class="account-info" type="text" name="name" value="<?php echo $nombre ?>" required>
                            </div>
                            <div class="col-md col-sm-12">
                              <p class="account-text">Correo:</p>
                              <input class="account-info" type="email" name="email" value="<?php echo $correo ?>" required>
                            </div>
                          </div>
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
                        <input type="hidden" name="id" value="<?php echo $id ?>">
                        <div class="log-out-container row">
                          <div class="col">
                            <button type="submit" name="editEmployee" class="btn edit-button" id="reg-submit-btn">Editar</button>
                          </div>
                          <div class="col">
                            <a href="?panel=employees" class="btn cancel-button">Cancelar</a>
                          </div>
                        </div>
                      </form>
                      <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $id ?>">
                        <button type="submit" name="deleteEmployee" class="btn btn-danger">Eliminar</button>
                      </form>
                    </div>

                    <?php
                  elseif (isset($_GET['addNew'])) :
                    if ($_GET['addNew'] == 'true') :
                      if (isset($_POST['addEmployee'])) {
                        $nombre = $mysqli->escape_string($_POST['name']);
                        $correo = $mysqli->escape_string($_POST['email']);
                        $pw = $mysqli->escape_string($_POST['password1']);
                        $hash = password_hash($pw, PASSWORD_DEFAULT);

                        $sql = "INSERT INTO empleados (nombre, correo, contraseña) VALUES ('$nombre', '$correo', '$hash')";
                        $query = $mysqli->query($sql);
                        if ($query) {
                          header('location: admin.php?panel=employees');
                        } else {
                    ?>
                          <p class="warning-text">Ocurrió un error al añadir</p>
                      <?php
                        }
                      }
                      ?>
                      <h2>Añadir Empleado</h2>
                      <div class="account-data-container">
                        <form method="POST">
                          <div class="account-data">
                            <div class="row">
                              <div class="col-md col-sm-12">
                                <p class="account-text">Nombre:</p>
                                <input class="account-info" type="text" name="name" required>
                              </div>
                              <div class="col-md col-sm-12">
                                <p class="account-text">Correo:</p>
                                <input class="account-info" type="email" name="email" required>
                              </div>
                            </div>
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
                          <div class="log-out-container row">
                            <div class="col">
                              <button type="submit" name="addEmployee" class="btn btn-success" id="reg-submit-btn" style="margin: 2rem 0">Añadir</button>
                            </div>
                            <div class="col">
                              <a href="?panel=employees" class="btn cancel-button">Cancelar</a>
                            </div>
                          </div>
                        </form>
                      </div>
                    <?php
                    endif;
                    ?>
                  <?php
                  else :
                  ?>
                    <h2>Empleados</h2>
                    <div>
                      <div class="table-button-container">
                        <a href="?panel=employees&addNew=true" class="btn btn-success">Añadir</a>
                      </div>
                      <table class="table">
                        <thead>
                          <tr>
                            <th scope="col">id</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Correo</th>
                            <th scope="col">Editar</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          if ($results > 0) :
                            foreach ($results as $empleado) :
                              $empleadoId;
                              $empleadoNombre;
                              $empleadoCorreo;
                              foreach ($empleado as $key => $data) {
                                switch ($key) {
                                  case 0:
                                    $empleadoId = $data;
                                    break;
                                  case 1:
                                    $empleadoNombre = $data;
                                    break;
                                  case 2:
                                    $empleadoCorreo = $data;
                                }
                              }
                          ?>
                              <tr>
                                <td scope="row"><?php echo $empleadoId ?></td>
                                <td><?php echo $empleadoNombre ?></td>
                                <td><?php echo $empleadoCorreo ?></td>
                                <td>
                                  <a href="?panel=employees&edit=<?php echo $empleadoId; ?>" class="btn btn-warning">Editar</a>
                                </td>
                              </tr>
                          <?php
                            endforeach;
                          endif;
                          ?>
                        </tbody>
                      </table>
                    </div>
                  <?php
                  endif;
                  ?>
                </div>

              <?php
            elseif ($_GET['panel'] == 'orders') :
              if(isset($_POST['delete-order'])){
                $orderId = $_POST['orderID'];
                $sql = "DELETE FROM pedidos WHERE id_pedidos='$orderId'";
                $query = $mysqli->query($sql);
              }


              $sql = "SELECT * FROM pedidos";
              $query = $mysqli->query($sql);
              $results = $query->fetch_all();
              ?>
                <div class="admin-table">
                  <h2>Pedidos</h2>
                  <div>
                    <table class="table">
                      <thead>
                        <tr>
                          <th scope="col">Id</th>
                          <th scope="col">Producto</th>
                          <th scope="col">Info</th>
                          <th scope="col">Cantidad</th>
                          <th scope="col">Nombre</th>
                          <th scope="col">Apellidos</th>
                          <th scope="col">Dirección</th>
                          <th scope="col">Telefono</th>
                          <th scope="col">Enviado</th>
                          <th scope="col">Fecha</th>
                          <th scope="col">Eliminar</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        if ($results > 0) :
                          foreach ($results as $pedido) :
                            $pedidoId;
                            $pedidoProducto;
                            $pedidoInfo;
                            $pedidoCant;
                            $pedidoNombre;
                            $pedidoApellido;
                            $pedidoDir;
                            $pedidoTel;
                            $pedidoEnviado;
                            $pedidoFecha;

                            foreach ($pedido as $key => $data) {
                              switch ($key) {
                                case 0:
                                  $pedidoId = $data;
                                  break;
                                case 1:
                                  $pedidoProducto = $data;
                                  break;
                                case 2:
                                  $pedidoInfo = unserialize($data);
                                  break;
                                case 3:
                                  $pedidoCant = $data;
                                  break;
                                case 4:
                                  $pedidoNombre = $data;
                                  break;
                                case 5:
                                  $pedidoApellido = $data;
                                  break;
                                case 6:
                                  $pedidoDir = $data;
                                  break;
                                case 7:
                                  $pedidoTel = $data;
                                  break;
                                case 8:
                                  $pedidoFecha = $data;
                                case 12:
                                  $pedidoEnviado = $data;
                                  break;
                              }
                            }
                        ?>
                            <tr>
                              <td scope="row"><?php echo $pedidoId ?></td>
                              <td><?php echo $pedidoProducto ?></td>
                              <td>
                                <?php
                                foreach ($pedidoInfo as $key => $info) {
                                  if ($info) {
                                    echo $key . ' ';
                                  }
                                }
                                ?>
                              </td>
                              <td><?php echo $pedidoCant ?></td>
                              <td><?php echo $pedidoNombre ?></td>
                              <td><?php echo $pedidoApellido ?></td>
                              <td><?php echo $pedidoDir ?></td>
                              <td><?php echo $pedidoTel ?></td>
                              <td>
                                <?php
                                if ($pedidoEnviado) {
                                  echo 'SI';
                                } else {
                                  echo 'NO';
                                }
                                ?>
                              </td>
                              <td><?php echo $pedidoFecha ?></td>
                              <td>
                                <form method="post">
                                  <input type="hidden" value="<?php echo $pedidoId ?>" name="orderID" >
                                  <input type="submit" name="delete-order" value="Borrar" class="btn btn-danger">
                                </form>
                              </td>
                            </tr>
                        <?php
                          endforeach;
                        endif;
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              <?php
            elseif ($_GET['panel'] == 'admins') :
              $sql = "SELECT * FROM administradores";
              $query = $mysqli->query($sql);
              $results = $query->fetch_all();
              ?>
                <div class="admin-table">
                  <?php
                  if(isset($_GET['edit'])):
                    if (isset($_POST['editAdmin'])) {
                      $id = $mysqli->escape_string($_POST['id']);
                      $nombre = $mysqli->escape_string($_POST['name']);
                      $correo = $mysqli->escape_string($_POST['email']);
                      $pw = $mysqli->escape_string($_POST['password1']);

                      // $hash = password_hash($pw, PASSWORD_DEFAULT);

                      $sql = "UPDATE administradores SET nombre='$nombre', correo='$correo', contraseña='$pw' WHERE id='$id'";
                      $query = $mysqli->query($sql);
                      if ($query) {
                        header('location: admin.php?panel=admins');
                      } else {
                  ?>
                        <p class="warning-text">Ocurrió un error al editar</p>
                      <?php
                      }
                    }

                    if (isset($_POST['deleteAdmin'])) {
                      $id = $_POST['id'];

                      $sql = "DELETE FROM administradores WHERE id='$id'";
                      $query = $mysqli->query($sql);
                      if ($query) {
                        header('location: admin.php?panel=admins');
                      } else {
                      ?>
                        <p class="warning-text">Ocurrió un error al borrar</p>
                    <?php
                      }
                    }
                    $id = $_GET['edit'];
                    $nombre;
                    $correo;
                    $sql = "SELECT * FROM administradores WHERE id='$id'";
                    $query = $mysqli->query($sql);
                    $result = $query->fetch_assoc();
                    if($result>0){
                      $nombre = $result['nombre'];
                      $correo = $result['correo'];
                    }
                  ?>
                  <h2>Editar Admin.</h2>
                  <div class="account-data-container">
                      <form method="POST">
                        <div class="account-data">
                          <div class="row">
                            <div class="col-md col-sm-12">
                              <p class="account-text">Nombre:</p>
                              <input class="account-info" type="text" name="name" value="<?php echo $nombre ?>" required>
                            </div>
                            <div class="col-md col-sm-12">
                              <p class="account-text">Correo:</p>
                              <input class="account-info" type="email" name="email" value="<?php echo $correo ?>" required>
                            </div>
                          </div>
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
                        <input type="hidden" name="id" value="<?php echo $id ?>">
                        <div class="log-out-container row">
                          <div class="col">
                            <button type="submit" name="editAdmin" class="btn edit-button" id="reg-submit-btn">Editar</button>
                          </div>
                          <div class="col">
                            <a href="?panel=admins" class="btn cancel-button">Cancelar</a>
                          </div>
                        </div>
                      </form>
                      <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $id ?>">
                        <button type="submit" name="deleteAdmin" class="btn btn-danger">Eliminar</button>
                      </form>
                    </div>

                    <?php
                  elseif (isset($_GET['addNew'])) :
                    if ($_GET['addNew'] == 'true') :
                      if (isset($_POST['addAdmin'])) {
                        $nombre = $mysqli->escape_string($_POST['name']);
                        $correo = $mysqli->escape_string($_POST['email']);
                        $pw = $mysqli->escape_string($_POST['password1']);
                        // $hash = password_hash($pw, PASSWORD_DEFAULT);

                        $sql = "INSERT INTO administradores (nombre, correo, contraseña) VALUES ('$nombre', '$correo', '$pw')";
                        $query = $mysqli->query($sql);
                        if ($query) {
                          header('location: admin.php?panel=admins');
                        } else {
                    ?>
                          <p class="warning-text">Ocurrió un error al añadir</p>
                      <?php
                        }
                      }
                      ?>
                      <h2>Añadir Admin.</h2>
                      <div class="account-data-container">
                        <form method="POST">
                          <div class="account-data">
                            <div class="row">
                              <div class="col-md col-sm-12">
                                <p class="account-text">Nombre:</p>
                                <input class="account-info" type="text" name="name" required>
                              </div>
                              <div class="col-md col-sm-12">
                                <p class="account-text">Correo:</p>
                                <input class="account-info" type="email" name="email" required>
                              </div>
                            </div>
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
                          <div class="log-out-container row">
                            <div class="col">
                              <button type="submit" name="addAdmin" class="btn btn-success" id="reg-submit-btn" style="margin: 2rem 0">Añadir</button>
                            </div>
                            <div class="col">
                              <a href="?panel=admins" class="btn cancel-button">Cancelar</a>
                            </div>
                          </div>
                        </form>
                      </div>
                    <?php
                    endif;
                    ?>
                  <?php
                  else:
                  ?>
                  <h2>Adminitradores</h2>
                  <div>
                    <div class="table-button-container">
                      <a href="?panel=admins&addNew=true" class="btn btn-success">Añadir</a>
                    </div>
                    <table class="table">
                      <thead>
                        <tr>
                          <th scope="col">id</th>
                          <th scope="col">Nombre</th>
                          <th scope="col">Correo</th>
                          <th scope="col">Editar</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        if ($results > 0) :
                          foreach ($results as $admin) :
                            $adminId;
                            $adminNombre;
                            $adminCorreo;
                            foreach ($admin as $key => $data) {
                              switch ($key) {
                                case 0:
                                  $adminNombre = $data;
                                  break;
                                case 1:
                                  $adminCorreo = $data;
                                  break;
                                case 3:
                                  $adminId = $data;
                                  break;
                              }
                            }
                        ?>
                            <tr>
                              <td scope="row"><?php echo $adminId ?></td>
                              <td><?php echo $adminNombre ?></td>
                              <td><?php echo $adminCorreo ?></td>
                              <td>
                                <a href="?panel=admins&edit=<?php echo $adminId ?>" class="btn btn-warning">Editar</a>
                              </td>
                            </tr>
                        <?php
                          endforeach;
                        endif;
                        ?>
                      </tbody>
                    </table>
                  </div>
                <?php
                endif;
                ?>
                </div>
          <?php
            endif;
          endif;
        endif;
          ?>
          <?php
          footer();
          ?>
          <script src="js/log-in.js"></script>

</html>