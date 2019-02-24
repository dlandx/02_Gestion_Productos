<?php
    // <editor-fold defaultstate="collapsed" desc="Información de la aplicación">
    /**
     * ----------------------------------------------------------------------------
     * Aplicación que gestiona los registros de la TABLA 'producto' - BDDD 'dwes'.
     * ----------------------------------------------------------------------------
     * SQL - Parametrizado:
     * 
     * 1º listado.php -> - Seleccionar FAMILIA - BTN lista productos de dicha familia.
     * --------------    - Información del PRODUCTO (Nombre corto, PVP, BTN Editar)...
     *                   - Al pulsar BTN Editar - Enviará el formulario a 'editar.php'.
     * 
     * 2º editar.php -> - Mostrar en el formulario los datos del productos a editar.
     * -------------    - Formulario (Nombre corto, Nombre, Descripción y PVP)...
     *                  - BTN Actualizar -> Envia el formulario a 'actualizar.php'.
     *                  - BTN Cancelar -> Deshace los cambios y vuelve a mostrar el form...
     * 
     * 3º actualizar.php -> - Redirigir a 'listado.php' SI se pulso BTN Actualizar...
     * ----------------    - Antes de redirigir ejecuta CONSULTA para actualizar datos.
     */
    // </editor-fold>
    
    // Cargamos los fichero '.php' que se van a utilizar...
    spl_autoload_register(function ($clase) {
        require "$clase.php";
    });
    // Variables iniciadas para evitar - Variable no definida en Windows...
    //$familia = ""; ó con ?? -> $view->viewFamilia($result, $familia ?? "");
    $productos = []; $selected = false;
    
    // Instanciar clase BBDD.php -> contendra la conexion con la BBDD...
    $bd = new BBDD("localhost","root","");
    $sql = "SELECT * FROM familia";
    $datos_familia = $bd->select_familia($sql); // Obtener los datos de la TABLA familia BBDD...

    // Acciones del BTN - Mostrar Productos...
    if (filter_input(INPUT_POST, 'productos')){
        $familia = filter_input(INPUT_POST, 'familia'); // Familia seleccionada.  
        // Sentencia para obtener los productos de la familia seleccionada...
        $sql = "SELECT * FROM producto WHERE familia = ?";
        $productos = $bd->select_producto($sql, $familia); // Obtener los productos de la familia...
        // Obtener cabecera de la TABLA
        $nameColumn = $bd->nombres_campos("producto");
        $selected = true;
    }
    $bd->close(); // Cerrar conexión con la BBDD...

    // Instanciar clase View.php -> Presentación de datos del modelo (resultado en select, tabla...)
    $view = new View(); 
    // Obtenemos las Familia (para el elemento - select->option)...
    $html_option = $view->viewFamilia($datos_familia, $familia ?? "");
    $html_thead = $view->tableHead($nameColumn ?? []); // Tabla head
    $html_tbody = $view->tableBody($productos); // Tabla body
    
    // Acciones del BTN - Modificar...
    $editar = filter_input(INPUT_POST, 'editar', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
    if(isset($editar)){
        // Boton Modificar -> Obtener el COD que es la key del Vector (Array[Key] = Value)
        foreach($editar as $key => $value){
            // Pasamos el CODIGO del producto a editar al formulario...
            header ("Location: editar.php?cod=$key");
        }
    }
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Listar Productos</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
        <div class="content">
            <h1>Selecionar una familia para listar los productos</h1>
            <form action="listado.php" method="POST">
                <select name="familia">
                    <?=$html_option?>
                </select>
                <input type="submit" value="Mostrar Productos" name="productos">            
                <br/>
                <?php
                    if ($selected):?>
                    <table>
                        <?php 
                            echo $html_thead;
                            echo $html_tbody;
                        ?>
                    </table>
                <?php endif; ?>                     
            </form>
        </div>
    </body>
</html>