<?php

/**
 * Clase 'BBDD.php' -> Conexión con la Base de Datos (MySQLi)
 */

class BBDD {
    
    // <editor-fold defaultstate="collapsed" desc="Atributos">
    private $con;
    private $error;
    private $host;
    private $user ;
    private $pass;
    private $bd;
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Constructor">
    /**
     * Constructor de la clase, instancia los atributos...
     * @param type $h (string) host de la BBDD...
     * @param type $u (string) usuarios de la BBDD...
     * @param type $p (string) contraseña de la BBDD...
     * @param type $bd (string) nombre de la BBDD...
     */
    public function __construct(string $h="172.17.0.2", string $u="root", string $p="root", string $bd="dwes") {
        $this->host = $h;
        $this->user = $u;
        $this->pass = $p;
        $this->bd = $bd;
        $this->con = $this->conexion();        
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Métodos privados">
    /**
     * Función conecta con la BBDD, en caso de NO conexión informar del error...
     * @return \mysqli. Retornar la conexión con la BBD...
     */
    private function conexion(): mysqli { //: mysqli -> El tipo que devuelve...
        $con = new mysqli($this->host, $this->user, $this->pass, $this->bd);
        if ($con->connect_errno) {
            $this->error = "Se produjo un error en la conexion: <b>".$con->connect_error."</b>";
        }
        $con->set_charset("utf8"); //Establecer el conjunto de caracteres del cliente... UTF-8
        return $con;
    }
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Métodos públicos">
    /**
     * Función cierra la conexión con la Base de Datos... 
     */
    public function close() {
        $this->con->close();
    }
    
    /**
     * Función obtiene datos de la TABLA familia de la BBDD...
     * @param string $sql, Sentencia SQL parametrizada a realizar...
     * @return array, Obtener las FAMILIAS obtenidos de la BBDD en un vector asociativo.
     */
    public function select_familia(string $sql): array {
        $datos = [];
        // Si se pierde la conexion, volvemos a conectar...
        if ($this->con == null) {
            $this->con = $this->conexion();
        }
        
        // stmt_init() - Inicializa una sentencia y devuelve un objeto para usarlo con mysqli_stmt_prepare
        $stmt = $this->con->stmt_init(); // Creamos el objeto mysql_statement
        $stmt->prepare($sql); // Preparar una sentencia SQL parametrizada...
        $stmt->execute(); // Ejecutar la consulta...
        
        // Vincular variables de la sentencia preparada para almacenar los resultados...
        $stmt->bind_result($cod, $nombre);
        // obtener los valores de la sentencia preparada en las variables vinculadas
        while ($stmt->fetch()) {
            $datos[] = [$cod, $nombre]; //ADD al array asociativo los valores de la BD...
        }
        $stmt->close(); // Cerrar sentencia preparada...
        return $datos;
    }
    
    /**
     * Función obtiene datos de la TABLA productos de la familia seleccionada de la BD.
     * @param string $sql, Sentencia SQL parametrizada a realizar...
     * @param string $familia, Familia seleccionada para obtener los PRODUCTOS...
     * @return array, Obtener los PRODUCTOS obtenidos de la BBDD en un vector asociativo.
     */
    public function select_producto(string $sql, string $parametro): array {
        $datos = [];
        // Si se pierde la conexion, volvemos a conectar...
        if ($this->con == null) {
            $this->con = $this->conexion();
        }
        
        // stmt_init() - Inicializa una sentencia y devuelve un objeto para usarlo con mysqli_stmt_prepare
        $stmt = $this->con->stmt_init(); // Creamos el objeto mysql_statement
        $stmt->prepare($sql); // Preparar una sentencia SQL parametrizada...
        // Agrega variables a una sentencia preparada como parámetros. bind_param(tipo, variables)...
        // Asignar valor a los parametros, retorna (true=OK false=ERROR)...
        $stmt->bind_param('s', $parametro); 
        $stmt->execute(); // Ejecutar la consulta...

        // Vincular variables de la sentencia preparada para almacenar los resultados...
        $stmt->bind_result($cod, $name, $name_short, $descrip, $price, $family);
        // obtener los valores de la sentencia preparada en las variables vinculadas
        while ($stmt->fetch()) {
            $datos[] = [$cod, $name, $name_short, $descrip, $price, $family]; //ADD al array asociativo los valores de la BD...
        }
        $stmt->close(); // Cerrar sentencia preparada...
        return $datos;
    }
    
    /**
     * Función informa si se ha MODIFICADO en la BBDD el producto...
     * @param string $sql, Sentencia SQL parametrizada a realizar...
     * @param string $n, Campo NOMBRE del producto a actualizar BD...
     * @param string $ns, Campo NOMBRE_CORTO del producto a actualizar BD...
     * @param string $des, Campo DESCRIPCION del producto a actualizar BD...
     * @param float $p, Campo PVP del producto a actualizar BD...
     * @param string $id, Campo COD del producto a actualizar BD...
     * @return bool, Retorna el estado de la operación TRUE = Modificado, FALSE = No modificado...
     */
    public function update_producto(string $sql, string $n, string $ns, string $des, float $p, string $id): bool {
        // Si se pierde la conexion, volvemos a conectar...
        if ($this->con == null) {
            $this->con = $this->conexion();
        }
        
        // stmt_init() - Inicializa una sentencia y devuelve un objeto para usarlo con mysqli_stmt_prepare
        $stmt = $this->con->stmt_init(); // Creamos el objeto mysql_statement
        $stmt->prepare($sql); // Preparar una sentencia SQL parametrizada...
        $stmt->bind_param('sssds', $n, $ns, $des, $p, $id); // Asignar valor a los parametros, retorna (true=OK false=ERROR)...
        $stmt->execute(); // Ejecutar la consulta...
        var_dump($stmt);
        $result = ($stmt->affected_rows > 0) ? true : false; // Si se a actualizado o no los datos...
        $stmt->close(); // Cerrar sentencia preparada...
        return $result;
    }
        
    /**
     * Función obtiene los nombres de las columnas de la BBDD...
     * @param string $tabla, tabla de la BBDD a consultar...
     * @return array, Retorna en un vector los nombres de las columnas BBDD...
     */
    public function nombres_campos(string $tabla): array {
        $campos = [];
        // Preparar la consulta SQL...
        $consulta = "SELECT * FROM $tabla";
        $r = $this->con->query($consulta);
        $obj = $r->fetch_fields(); // Array de objetos de cada columna
        
        foreach ($obj as $value) {
            $campos[] = $value->name; // Obtenemos el nombre de las columnas BD...
        }        
        return $campos;
    }
    // </editor-fold>
}
