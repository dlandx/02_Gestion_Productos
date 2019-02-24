<?php

    // Cargamos los fichero '.php' que se van a utilizar...
    spl_autoload_register(function ($clase) {
        require "$clase.php";
    });
    
    $id = $_GET['cod'] ?? ""; // Obtenemos el id enviado por GET al pulsar el BTN...
    // Instanciar clase BBDD.php -> contendra la conexion con la BBDD...
    $bd = new BBDD("localhost","root","");
    $sql = "SELECT * FROM producto WHERE cod=?"; // Preparar sentencia SQL parametrizada...
    $productos = $bd->select_producto($sql, $id); // Obtener los datos del producto seleccionado...

    switch (filter_input(INPUT_POST, 'btn')) {
        case "Actualizar":
            // Obtenemos los valores...
            $id = filter_input(INPUT_POST, 'id_cod'); // input hidden...
            $name_short = filter_input(INPUT_POST, 'name_short');
            $name = filter_input(INPUT_POST, 'name');
            $des = filter_input(INPUT_POST, 'description');
            $price = floatval(filter_input(INPUT_POST, 'price'));
            
            // Preparar sentencia SQL parametrizada... 
            $sql = "UPDATE producto SET nombre=?, nombre_corto=?, descripcion=?, PVP=? WHERE cod=?";
            $result = $bd->update_producto($sql, $name, $name_short, $des, $price, $id); // Filas afectadas
            $bd->close(); // Cerrar conexión con la BBDD...

            header ("Location: actualizar.php?r=$result");// Redirigir (enviando 1=Si modifico, 0=No)
            break;

        case "Cancelar":
            // Deshacer cambios que se haya podido realizar en los campos del formulario...
            // Obtener o recargar la página con los datos que tenga en la BBDD...
            $id = filter_input(INPUT_POST, 'id_cod');
            // Preparar sentencia SQL parametrizada... 
            $sql = "SELECT * FROM producto WHERE cod=?"; // Preparar sentencia SQL parametrizada...
            $productos = $bd->select_producto($sql, $id); // Obtener los datos del producto seleccionado...
            $bd->close(); // Cerrar conexión con la BBDD...
            break;
        default:
            break;
    }
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Modificar Producto</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <div class="content">
            <h1>Ingrese los nuevos datos para editar el producto</h1>
            
            <fieldset>
                <legend>Modificar Producto</legend>
                <form action="editar.php" method="POST">
                    <div id="inputs">
                        <div class="filds">
                            <input type="hidden" name="id_cod" value="<?=$productos[0][0] ?? null?>">
                        </div>
                        
                        <div class="filds">
                            <input type="text" placeholder="Ingresar nombre corto del producto" name="name_short" value="<?=$productos[0][2] ?? null?>">
                            <label for="">Nombre corto del producto</label>
                        </div>
                        
                        <div class="filds">
                            <input type="text" placeholder="Ingresar nombre del producto"  name="name" value="<?=$productos[0][1] ?? null?>">
                            <label for="">Nombre del producto</label>
                        </div>

                        <div class="filds">
                            <textarea placeholder="Ingrese la descripción del producto..." name="description" cols="30" rows="7"><?=$productos[0][3] ?? null?></textarea> 
                            <label for="">Descripción del producto</label>
                        </div>

                        <div class="filds">
                            <input type="text" placeholder="Ingresar el precio del producto" name="price" value="<?=$productos[0][4] ?? null?>">
                            <label for="">Precio de venta al público</label>
                        </div>
                    </div>
                         
                    <div id="btn">
                        <input type="submit" id="success" value="Actualizar" name="btn">
                        <input type="submit" id="cancel" value="Cancelar" name="btn">
                    </div>                    
                </form>
            </fieldset>
        </div>        
    </body>
</html>