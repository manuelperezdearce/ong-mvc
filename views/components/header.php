<?php
// session_start(); // Iniciar sesión al principio del archivo

$active = $_GET['controller'] ?? 'proyectos';
$itemCounter = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<header class="bg-white border-b rounded-b-2xl shadow-lg h-[100px]">
    <nav class="max-w-[1200px] m-auto h-full flex justify-between">
        <a class="flex items-center a-logo" href="index.php?controller=proyectos&action=list">
            <img class="logo" src="./views/public/onglogo.png" alt="Logo del Sitio ONG">
            <p class="text-2xl font-bold">Friendly ONG</p>
        </a>
        <ul class="flex flex-wrap justify-end [&>*]:mx-2 h-full [&>*]:w-[90px] [&>*]:[&>*]:mx-auto">
            <li class="flex items-center">
                <a href="index.php?controller=home&action=renderController"
                    class="<?= $active === 'home' ? 'active' : '' ?>">Inicio</a>
            </li>
            <li class="flex items-center">
                <a href="index.php?controller=proyectos&action=list"
                    class="<?= $active === 'proyectos' ? 'active' : '' ?>">Proyectos</a>
            </li>
            <li class="flex items-center">
                <a href="index.php?controller=eventos&action=list"
                    class="<?= $active === 'eventos' ? 'active' : '' ?>">Eventos</a>
            </li>
            <?php if (!empty($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                <li class="flex items-center">
                    <a href="index.php?controller=donaciones&action=list"
                        class="<?= $active === 'donaciones' ? 'active' : '' ?>">Donaciones</a>
                </li>
            <?php endif; ?>
        </ul>


        <ul class="flex flex-wrap justify-end [&>*]:mx-2 h-full  [&>*]:[&>*]:mx-auto relative">

            <?php if (!empty($_SESSION['username'])): ?>
                <li class="flex items-center relative">
                    <!-- Botón que abre/cierra el menú -->
                    <button onclick="toggleMenu()" class="flex items-center gap-2 focus:outline-none">
                        <img src="<?= htmlspecialchars($_SESSION['avatar'] ?? 'https://i.pravatar.cc/40?u=' . $_SESSION['username']) ?>"
                            alt="Avatar"
                            class="w-10 h-10 rounded-full border object-cover" />
                        <span class="text-sm font-medium"><?= htmlspecialchars($_SESSION['username']) ?></span>
                        <i class="fa fa-chevron-down text-xs"></i>
                    </button>

                    <!-- Menú desplegable -->
                    <ul id="userMenu" class="hidden absolute right-0 mt-12 bg-white border rounded shadow z-50 w-[160px]">
                        <li>
                            <a href="index.php?controller=usuarios&action=exitUserControl"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-100 hover:text-red-600 transition">
                                Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </li>
            <?php else: ?>
                <li class="flex items-center">
                    <a href="index.php?controller=usuarios&action=iniciarSesion"
                        class="<?= $active === 'usuarios' ? 'active' : '' ?>">
                        Ingresar
                    </a>
                </li>
            <?php endif; ?>

            <li class="flex items-center">
                <a href="index.php?controller=carrito&action=list"
                    class="relative <?= $active === 'carrito' ? 'active' : '' ?>">
                    Carrito
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>

                    <?php if ($itemCounter > 0): ?>
                        <span class="ico-cart-counter"><?= $itemCounter ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>



    </nav>
</header>