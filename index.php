<?php

session_start();
ob_start();



// Obtener el controlador en la URL
$controllerName = isset($_GET['controller']) ? $_GET['controller'] . 'Controller' : 'proyectosController';

// Obtener el nombre de la acción en la URL
$action = isset($_GET["action"]) ? $_GET["action"] : 'list';

// Ruta para encontrar los controladores en el MVC
$controllerPath = "./controllers/" . $controllerName . ".php";

// debug de la sesion
// var_dump($_SESSION);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="views/styles/main.css">
    <!-- CDN FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="icon" type="image/png" href="./views/public/onglogo.ico">
    <title>Friendly ONG</title>
</head>

<body>

    <?php include_once "./views/components/header.php" ?>

    <main class="max-w-[1200px] mx-auto">
        <!-- Acá se renderiza lo que genere devuelva el controlador -->
        <?php
        if (file_exists($controllerPath)) {
            // Traer archivo del path
            include_once $controllerPath;
            // Crear instancia del controlador
            try {
                $controller = new $controllerName;
                if (method_exists($controller, $action)) {
                    $controller->$action();
                } else {
                    echo "Acción no exsite";
                }
            } catch (\Throwable $th) {
                echo "Clase no existe";
            }
        } else {
            echo "Controlador no existe";
        }
        ?>
    </main>
    <?php include_once "./views/components/footer.php" ?>

    <script src="./views//components//header.js"></script>
</body>

<?php
ob_end_flush();
?>