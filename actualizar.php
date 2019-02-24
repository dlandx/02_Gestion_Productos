<?php
    $result = $_GET['r']; // Resultado enviado por GET al pulsar el BTN Actualizar...
    // Informamos del resultado obtenido... Si se a realizado cambios (affected_rows)
    $info = ($result === "1") ? "Datos actualizados correctamente" : "No se realizo cambios o se produjo un error...";
?>

<!doctype html>
<html lang="en">
    <head>
        <!-- Redirigimos a listado.php -->
        <meta http-equiv="refresh" content="1; url=listado.php">
        <title>Producto Actualizado</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <div class="content">
            <h1><?=$info;?></h1>
        </div>
    </body>
</html>