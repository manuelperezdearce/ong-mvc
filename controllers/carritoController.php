<?php

include_once "./views/CarritoView.php";
include_once "./models/DonacionesModel.php";

class carritoController
{
    public function list()
    {
        try {
            if ($this->isLogin()) {
                // Asegurar que el carrito esté definido
                $carrito = $_SESSION['cart'] ?? [];
                $carritoView = new CarritoView();
                $carritoView->renderLista($carrito);
            } else {
?>
                <div class="bg-white p-8 rounded-md max-w-lg text-center shadow-md mx-auto">
                    <h3>
                        Para acceder al carrito de compras debe
                        <a class="text-blue-600 font-bold" href="index.php?controller=usuarios&action=iniciarSesion">iniciar sesión</a>
                    </h3>
                </div>
<?php
            }
        } catch (\Throwable $th) {
            echo "Error en carrito: " . $th->getMessage();
        }
    }

    public function procesarPago()
    {
        if (!empty($_SESSION['cart'])) {
            $donacionesModel = new Donaciones();

            foreach ($_SESSION['cart'] as $donacion) {
                // Asegurarse de que los campos obligatorios están definidos
                if (isset($donacion['id_proyecto'], $donacion['id_usuario'], $donacion['monto'])) {
                    $nuevaDonacion = [
                        'monto' => $donacion['monto'],
                        'id_proyecto' => $donacion['id_proyecto'],
                        'id_usuario' => $donacion['id_usuario']
                    ];

                    try {
                        $donacionesModel->create($nuevaDonacion);
                    } catch (\Throwable $e) {
                        echo "Error al registrar donación: " . $e->getMessage();
                    }
                }
            }

            // Vaciar el carrito
            unset($_SESSION['cart']);
        }

        // Mostrar pantalla de agradecimiento
        $view = new CarritoView();
        $view->renderGracias();
    }

    private function isLogin()
    {
        return !empty($_SESSION['username']);
    }
}
