<?php

class ProyectosView
{
    public function renderLista($proyectos)
    {
?>
        <h1 class="text-2xl my-4 text-center font-bold">Lista de Proyectos</h1>


        <section class="container-controllers">
            <?php include_once __DIR__ . "/components/crearButton.php"; ?>
            <form id="filtroProyectos" class="flex flex-row flex-wrap gap-2" action="index.php?controller=proyectos&action=listPopular" method="POST">
                <div class="filter-group">
                    <label for="filtro-q-donaciones">Por cantidad de donaciones</label>
                    <select name="filtro-q-donaciones" id="filtro-q-donaciones">
                        <option value="0">Seleccione una opción</option>
                        <option value="1">Más de 1</option>
                        <option value="5">Más de 5</option>
                        <option value="10">Más de 10</option>
                    </select>
                </div>
            </form>
            <div class="filter-group">
                <label for="filtro-ordenar-nombre">Ordenar por nombre</label>
                <select name="filtro-ordenar-nombre" id="filtro-ordenar-nombre">
                    <option value="0">Seleccione una opción</option>
                    <option value="2">A...Z</option>
                    <option value="10">Z...A</option>
                </select>
            </div>

            <script>
                document.querySelectorAll('#filtroProyectos select').forEach(select => {
                    select.addEventListener('change', function() {
                        document.getElementById('filtroProyectos').submit();
                    });
                });
            </script>

        </section>

        <?php if (empty($proyectos)): ?>
            <p>No hay proyectos disponibles.</p>
        <?php else: ?>
            <section class="container">

                <?php foreach ($proyectos as $proyecto): ?>
                    <?php
                    $recaudado = floatval($proyecto["presupuesto"]);
                    $meta = floatval($proyecto["goal"] ?? 1);
                    $porcentaje = min(100, intval(($recaudado / $meta) * 100));
                    ?>
                    <div class="listItem bg-white rounded-xl shadow-lg overflow-hidden flex flex-col md:flex-row md:items-stretch hover:shadow-xl transition-shadow duration-300">
                        <!-- Imagen del proyecto -->
                        <div class="md:w-1/3 h-60 md:h-auto overflow-hidden">
                            <img src="<?= htmlspecialchars($proyecto['image']) ?>" alt="Imagen del proyecto"
                                class="w-full h-full object-cover object-center transition-transform duration-300 hover:scale-105">
                        </div>

                        <!-- Contenido del proyecto -->
                        <div class="p-6 md:w-2/3 flex flex-col justify-between gap-3">
                            <div>
                                <h2 class="text-2xl font-semibold text-gray-800 mb-1"><?= htmlspecialchars($proyecto["nombre"]) ?></h2>
                                <p class="text-gray-600 mb-2"><?= htmlspecialchars($proyecto["descripcion"]) ?></p>
                                <p class="text-sm text-gray-500"><strong>Estado:</strong> <?= htmlspecialchars($proyecto["status"]) ?></p>
                            </div>

                            <!-- Acciones -->
                            <div class="mt-4 flex flex-wrap gap-3">
                                <p class="text-sm text-gray-700">
                                    <strong>Recaudado:</strong> $<?= number_format($recaudado, 0, '', '.') ?>
                                    <span class="text-gray-500">de $<?= number_format($meta, 0, '', '.') ?></span>
                                </p>
                                <!-- Barra de progreso -->
                                <div class="mt-2 w-full bg-gray-300 rounded-full h-4 overflow-hidden">
                                    <div class="bg-green-500 h-4 rounded-full transition-all duration-500 ease-out" style="width: <?= $porcentaje ?>%;"></div>
                                </div>
                                <span class="text-sm text-gray-600"><?= $porcentaje ?>% de la meta alcanzado</span>
                                <a href="index.php?controller=proyectos&action=watch&id=<?= urlencode($proyecto["id_proyecto"]) ?>"
                                    class="flex items-center gap-1 text-blue-700 hover:text-blue-900 font-medium transition">
                                    <i class="fa fa-eye"></i> Ver detalle
                                </a>

                                <a href="index.php?controller=proyectos&action=donar&id=<?= urlencode($proyecto["id_proyecto"]) ?>"
                                    class="flex items-center gap-1 text-green-600 hover:text-green-800 font-medium transition">
                                    <i class="fa fa-heart"></i> Donar
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>

        <?php endif; ?>
    <?php
    }

    public function renderOne($proyecto)
    {
    ?>
        <h1 class="text-3xl my-6 text-center font-bold text-blue-800">Detalle del Proyecto</h1>

        <?php if (empty($proyecto)): ?>
            <p class="text-center text-red-500">Proyecto no encontrado.</p>
        <?php else: ?>
            <div class="max-w-5xl mx-auto bg-white shadow-md rounded p-6 border border-gray-200">
                <div class="flex flex-col md:flex-row gap-6 items-start">
                    <!-- Información del proyecto -->
                    <dl class="flex-1 space-y-4">
                        <?php foreach ($proyecto as $campo => $valor): ?>
                            <?php if ($campo !== 'image'): ?>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-600"><?= htmlspecialchars(ucfirst($campo)) ?>:</dt>
                                    <dd class="text-md text-gray-800"><?= nl2br(htmlspecialchars($valor)) ?></dd>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </dl>

                    <!-- Imagen del proyecto -->
                    <?php if (!empty($proyecto['image'])): ?>
                        <div class="md:w-1/3 w-full">
                            <img src="<?= htmlspecialchars($proyecto['image']) ?>"
                                alt="Imagen del Proyecto"
                                class="w-full h-auto max-h-64 object-cover rounded-lg shadow-sm border border-gray-300" />
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-6 flex gap-3 justify-end">
                    <a href="index.php?controller=proyectos&action=list"
                        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                        Volver a la lista
                    </a>

                    <?php if (!empty($_SESSION['username']) && ($_SESSION['rol'] ?? '') === 'admin'): ?>
                        <a href="index.php?controller=proyectos&action=edit&id=<?= urlencode($proyecto['id_proyecto'] ?? '') ?>"
                            class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                            <i class="fa fa-pencil mr-1"></i>Editar
                        </a>

                        <a href="index.php?controller=proyectos&action=delete&id=<?= urlencode($proyecto['id_proyecto'] ?? '') ?>"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
                            onclick="return confirm('¿Seguro que deseas eliminar este proyecto?');">
                            <i class="fa fa-trash mr-1"></i>Eliminar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php
    }

    public function renderEdit($proyecto, $campos)
    {
    ?>
        <h1 class="text-2xl my-4 text-center font-bold">Editar Proyecto</h1>
        <form action="index.php?controller=proyectos&action=edit&id=<?= urlencode($proyecto['id_proyecto']) ?>" method="POST" class="max-w-md mx-auto listItem">
            <?php foreach ($campos as $campo): ?>
                <div class="mb-4">
                    <label for="<?= $campo['name'] ?>" class="block font-semibold mb-1"><?= $campo['label'] ?></label>
                    <?php if ($campo['type'] === 'textarea'): ?>
                        <textarea id="<?= $campo['name'] ?>" name="<?= $campo['name'] ?>" class="w-full border px-3 py-2 rounded" <?= $campo['required'] ? 'required' : '' ?>><?= htmlspecialchars($proyecto[$campo['name']] ?? '') ?></textarea>
                    <?php else: ?>
                        <input
                            type="<?= $campo['type'] ?>"
                            id="<?= $campo['name'] ?>"
                            name="<?= $campo['name'] ?>"
                            class="w-full border px-3 py-2 rounded"
                            value="<?= htmlspecialchars($proyecto[$campo['name']] ?? '') ?>"
                            <?= $campo['required'] ? 'required' : '' ?>>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">Guardar cambios</button>
            <a href="index.php?controller=proyectos&action=list" class="ml-2 text-blue-600 hover:underline">Cancelar</a>
        </form>
    <?php
    }
    public function renderAccessDenied()
    {
    ?>

        <div class="bg-white p-8 rounded-md max-w-lg text-center shadow-md mx-auto">
            <h3>
                Para realizar donaciones primero debes </br>
                <a class="text-blue-600 font-bold" href="index.php?controller=usuarios&action=iniciarSesion">iniciar sesión</a>
            </h3>
        </div>
<?php

    }
}
