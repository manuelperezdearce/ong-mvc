<?php

class CarritoView
{
    public function renderLista($carrito)
    {
        // Asegurarse de que $carrito sea un array
        $carrito = is_array($carrito) ? $carrito : [];

        // Calcular total a pagar
        $total = 0;
        foreach ($carrito as $item) {
            $monto = $item['monto'] ?? 0;
            $total += floatval($monto);
        }
?>
        <section class="container flex flex-wrap bg-white rounded p-4 justify-evenly">
            <h1 class="w-full text-2xl my-4 text-center font-bold">Carrito de Compras</h1>
            <!-- Columna izquierda: ítems -->
            <article class="w-auto">
                <?php foreach ($carrito as $item): ?>
                    <div class="cart-list-item border p-4 mb-4 shadow rounded flex items-center justify-between gap-4">
                        <p class="text-sm text-gray-600 mb-1"><strong>ID:</strong> <?= $item["id_proyecto"] ?></p>
                        <h2 class="text-lg font-semibold mb-1 text-right">
                            <?= htmlspecialchars($item["title"] ?? $item["nombre"] ?? "Sin título") ?>
                        </h2>
                        <!-- <p class="text-gray-700 mb-2"><?= htmlspecialchars($item["description"] ?? "") ?></p> -->

                        <div class="flex items-center gap-2">
                            <label for="amount_<?= $item["id_proyecto"] ?>" class="text-sm font-medium">Monto Donación:</label>
                            <div class="relative w-40">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-600 font-medium">$</span>
                                <input
                                    type="number"
                                    name="montos[<?= $item["id_proyecto"] ?>]"
                                    id="amount_<?= $item["id_proyecto"] ?>"
                                    class="pl-7 pr-3 py-1 w-full border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    value="<?= htmlspecialchars($item["monto"] ?? "0") ?>"
                                    min="0"
                                    step="1000"
                                    disabled>
                            </div>
                        </div>
                        <div>
                            <!-- <i class="fa fa-pencil" aria-hidden="true"></i> -->
                            <a
                                href="index.php?controller=carrito&action=deleteFromCart&itemID=<?= $item["id_proyecto"] ?>"
                                title="Eliminar artículo del carrito">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </article>

            <!-- Columna derecha: resumen -->
            <article class="max-w-[400px] p-4 border shadow rounded flex flex-col gap-4 self-start">
                <h2 class="text-xl font-bold">Resumen del carrito</h2>

                <p class="mb-2 text-[1.5rem]"><strong>Total a pagar:</strong> $<?= number_format($total, 0, ',', '.') ?></p>

                <p class="text-sm text-gray-700">
                    Al presionar el botón "Realizar pago", usted será redirigido al portal de pago seguro correspondiente a la pasarela seleccionada. El procesamiento de la transacción se realizará bajo los estándares de seguridad establecidos por dicha plataforma.
                </p>

                <form action="index.php?controller=carrito&action=procesarPago" method="post" class="flex flex-col gap-4">
                    <div class="mb-2">
                        <h3 class="font-bold mb-1">Seleccione su método de pago</h3>

                        <label class="flex items-center gap-2 mb-2 cursor-pointer justify-center">
                            <input type="radio" name="pasarela" value="mercadopago" required>
                            <img src="views/public/mercadopago-logo.png" alt="Mercado Pago" class="h-20 w-32 object-contain">
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer justify-center">
                            <input type="radio" name="pasarela" value="webpay" required>
                            <img src="views/public/webpay-logo.png" alt="WebPay" class="h-20 w-32 object-contain">
                        </label>
                    </div>

                    <div>
                        <p class="text-sm text-gray-700 mb-2">
                            Al continuar, usted acepta los términos y condiciones del servicio, y declara haber leído la política de privacidad asociada a este sitio. Le recomendamos revisar cuidadosamente los montos antes de confirmar su donación.
                        </p>

                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="acepta_terminos" required>
                            <span class="text-sm">He leído y acepto los términos y condiciones</span>
                        </label>
                    </div>

                    <input type="hidden" name="total" value="<?= htmlspecialchars($total) ?>">

                    <button type="submit"
                        class="w-full bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 transition">
                        Realizar Pago
                    </button>
                </form>
            </article>
        </section>
    <?php
    }

    public function renderGracias()
    { ?>
        <div class="max-w-md mx-auto bg-white shadow-md rounded p-6 text-center mt-10">
            <h1 class="text-2xl font-bold text-green-700 mb-4">¡Gracias por tu donación!</h1>
            <p class="text-gray-700 mb-4">
                Tu aporte hace posible que sigamos trabajando por nuestras causas y apoyando a quienes más lo necesitan.
            </p>
            <i class="fa fa-heart text-red-500 text-4xl mb-4"></i>
            <a href="index.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                Volver al inicio
            </a>
        </div>
    <?php
    }

    public function renderAccessDenied()
    { ?>
        <div class="bg-white p-8 rounded-md max-w-lg text-center shadow-md mx-auto">
            <h3>
                Para acceder al carrito de compras primero debes </br>
                <a class="text-blue-600 font-bold" href="index.php?controller=usuarios&action=iniciarSesion">iniciar sesión</a>
            </h3>
        </div>
    <?php
    }
    public function renderEmptyCart()
    { ?>
        <div class="bg-white p-8 rounded-md max-w-lg text-center shadow-md mx-auto">
            <h3>
                Ups! el carrito de compras está vacío!</br> Te invitamos a realizar una donación en cualquiera de nuestros </br>
                <a class="text-blue-600 font-bold" href="index.php?controller=proyectos&action=list">Proyectos</a>
            </h3>
        </div>
<?php
    }
}
