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
                if (!empty($carrito)) {
                    $carritoView = new CarritoView();
                    $carritoView->renderLista($carrito);
                } else {
                    $carritoView = new CarritoView();
                    $carritoView->renderEmptyCart();
                }
            } else {
                $carritoView = new CarritoView();
                $carritoView->renderAccessDenied();
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

    public function deleteFromCart()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $itemID = $_GET["itemID"];
            echo "Vamos a eliminar el ID " . $itemID . " del carrito";
            $ids = array_column($_SESSION['cart'], "id_proyecto");
            var_dump($ids);
            // Buscar la posición del item
            $key = array_search($itemID, $ids);

            if ($key !== false) {
                unset($_SESSION['cart'][$key]); // eliminar
                $_SESSION['cart'] = array_values($_SESSION['cart']); // reindexar
                echo "Item eliminado con éxito.";
                header("Location: index.php?controller=carrito&action=list");
            } else {
                echo "Item no encontrado en el carrito.";
            }
        }
    }

    private function isLogin()
    {
        return !empty($_SESSION['username']);
    }
}
