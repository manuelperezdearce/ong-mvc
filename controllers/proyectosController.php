<?php

include_once "./models/ProyectosModel.php";
include_once "./views/ProyectosView.php";

class proyectosController
{
    public function list()
    {
        try {
            // Crear una instancia del modelo
            $proyectosModel = new Proyectos();
            // Llamar al método y guardar datos en una variable
            $proyectos = $proyectosModel->getAllWithPresupuesto();
            // Crear una instancia de la vista
            $proyectosView = new ProyectosView();
            // Llamar al método y Enviar datos a la vista
            $proyectos = $proyectosView->renderLista($proyectos);
        } catch (\Throwable $th) {
            echo $th;
        }
    }
    public function listPopular()
    {
        try {
            $minDonaciones = isset($_POST['filtro-q-donaciones']) ? intval($_POST['filtro-q-donaciones']) : 3;

            $proyectosModel = new Proyectos();
            $proyectos = $proyectosModel->getWithMinDonations($minDonaciones);

            $proyectosView = new ProyectosView();
            $proyectosView->renderLista($proyectos);
        } catch (\Throwable $th) {
            echo "Error al cargar proyectos con donaciones: " . $th->getMessage();
        }
    }
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger datos del formulario dinámicamente
            $proyectosModel = new Proyectos();
            $estructura = $proyectosModel->getAll();
            $primerProyecto = $estructura[0] ?? [];
            $nuevoProyecto = [];
            foreach ($primerProyecto as $campo => $valor) {
                $nuevoProyecto[$campo] = $_POST[$campo] ?? '';
            }

            try {
                $proyectosModel->create($nuevoProyecto);

                // Redirigir a la lista de proyectos después de crear
                header("Location: index.php?controller=proyectos&action=list");
                exit;
            } catch (\Throwable $th) {
                echo "Error al crear el proyecto: " . $th->getMessage();
            }
        } else {
            // Leer la estructura del primer proyecto para generar los campos
            $proyectosModel = new Proyectos();
            $estructura = $proyectosModel->getAll();
            $primerProyecto = $estructura[0] ?? [];

            // Generar array de campos dinámicamente
            $campos = [];
            foreach ($primerProyecto as $campo => $valor) {
                $tipo = 'text';
                if (is_string($valor) && strlen($valor) > 100) {
                    $tipo = 'textarea';
                }
                $campos[] = [
                    'name' => $campo,
                    'label' => ucfirst($campo),
                    'type' => $tipo,
                    'required' => true
                ];
            }
            $controller = 'proyectos'; // o 'eventos', 'donaciones', etc.
            $action = 'create';
            include "./views/components/crear.php";
        }
    }

    public function watch()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $id = $_GET["id"];
            // Crear instancia de la entidad
            $proyectoModel = new Proyectos();
            // Llamar al método y guardar datos en una variable
            $proyecto = $proyectoModel->getOneByID($id);
            // Crear una instancia de la vista
            $proyectosView = new ProyectosView();
            // Llamar al método y Enviar datos a la vista
            $proyectosView->renderOne($proyecto);
        }
    }
    public function edit()
    {
        $proyectosModel = new Proyectos();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger datos del formulario dinámicamente
            $estructura = $proyectosModel->getAll();
            $primerProyecto = $estructura[0] ?? [];
            $proyectoEditado = [];
            foreach ($primerProyecto as $campo => $valor) {
                $proyectoEditado[$campo] = $_POST[$campo] ?? '';
            }
            $id = $_GET['id'] ?? $proyectoEditado['id'] ?? null;
            try {
                $proyectosModel->edit($id, $proyectoEditado);

                // Redirigir a la lista de proyectos después de editar
                header("Location: index.php?controller=proyectos&action=list");
                exit;
            } catch (\Throwable $th) {
                echo "Error al editar el proyecto: " . $th->getMessage();
            }
        } else {
            // Obtener el proyecto actual por ID
            $id = $_GET['id'] ?? null;
            $proyecto = $proyectosModel->getOneByID($id);

            // Leer la estructura para generar los campos
            $estructura = $proyectosModel->getAll();
            $primerProyecto = $estructura[0] ?? [];
            $campos = [];
            foreach ($primerProyecto as $campo => $valor) {
                $tipo = 'text';
                if (is_string($valor) && strlen($valor) > 100) {
                    $tipo = 'textarea';
                }
                $campos[] = [
                    'name' => $campo,
                    'label' => ucfirst($campo),
                    'type' => $tipo,
                    'required' => true
                ];
            }

            // Mostrar el formulario de edición
            $proyectosView = new ProyectosView();
            $proyectosView->renderEdit($proyecto, $campos);
        }
    }
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $proyectosModel = new Proyectos();

            try {
                $proyectosModel->delete($id);

                // Redirigir a la lista de proyectos después de eliminar
                header("Location: index.php?controller=proyectos&action=list");
                exit;
            } catch (\Throwable $th) {
                echo "Error al eliminar el proyecto: " . $th->getMessage();
            }
        } else {
            echo "ID de proyecto no especificado.";
        }
    }

    public function donar()
    {
        $proyectosModel = new Proyectos();

        // POST: Agregar al carrito
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idProyecto = isset($_POST['id']) ? intval($_POST['id']) : 0;
            $monto = isset($_POST['monto']) ? floatval($_POST['monto']) : 0;

            if ($idProyecto > 0 && $monto > 0) {
                $proyecto = $proyectosModel->getOneByID($idProyecto);

                if ($proyecto) {
                    if (!isset($_SESSION['user_id'])) {
                        $proyectosView = new ProyectosView();
                        $proyectosView->renderAccessDenied();
                        return;
                    }

                    $donacion = [
                        "id_proyecto" => $proyecto['id_proyecto'],
                        "nombre" => $proyecto['nombre'],
                        "monto" => $monto,
                        "id_usuario" => $_SESSION['user_id']
                    ];

                    if (!isset($_SESSION['cart'])) {
                        $_SESSION['cart'] = [];
                    }

                    $_SESSION['cart'][] = $donacion;

                    header("Location: index.php?controller=proyectos&action=list");
                    exit;
                } else {
                    echo "Proyecto no encontrado.";
                }
            } else {
                echo "ID o monto inválido.";
            }

            // GET: Mostrar formulario
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            $idProyecto = intval($_GET['id']);
            $proyecto = $proyectosModel->getOneByID($idProyecto);

            if ($proyecto):
?>
                <h1 class="text-2xl my-4 text-center font-bold">Realizar Donación</h1>
                <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
                    <h2 class="font-semibold mb-2"><?= htmlspecialchars($proyecto['nombre']) ?></h2>
                    <form action="index.php?controller=proyectos&action=donar" method="POST">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($idProyecto) ?>">

                        <label for="monto" class="block mb-2">Monto a donar:</label>
                        <input type="number" min="1" step="any" name="monto" id="monto"
                            class="border px-3 py-2 rounded w-full mb-4" required>

                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Agregar al carrito</button>
                        <a href="index.php?controller=proyectos&action=watch&id=<?= urlencode($idProyecto) ?>"
                            class="ml-2 text-blue-600 hover:underline">Cancelar</a>
                    </form>
                </div>
<?php
            else:
                echo "Proyecto no encontrado.";
            endif;
        } else {
            echo "ID del proyecto no especificado.";
        }
    }
}
