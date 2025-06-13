<?php
// Información de la conexión
$serverName = "StockAvailableDB.mssql.somee.com";
$database = "StockAvailableDB";
$username = "esalazar_SQLLogin_2";
$password = "jxojqgthol";

// Verificar si la extensión sqlsrv está instalada
if (!extension_loaded('sqlsrv')) {
    die("ERROR: La extensión sqlsrv de PHP no está instalada. Por favor, instala la extensión para conectarte a SQL Server.");
}

// Intentar la conexión
try {
    // Configurar la conexión
    $connectionOptions = array(
        "Database" => $database,
        "Uid" => $username,
        "PWD" => $password,
        "TrustServerCertificate" => true,
        "Encrypt" => false
    );

    // Establecer la conexión
    $conn = sqlsrv_connect($serverName, $connectionOptions);

    // Verificar si la conexión fue exitosa
    if ($conn === false) {
        echo "<h1>Error de conexión</h1>";
        echo "<p>No se pudo establecer la conexión con la base de datos.</p>";
        echo "<h2>Detalles del error:</h2>";
        
        $errors = sqlsrv_errors();
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>SQLSTATE: " . $error['SQLSTATE'] . "<br>";
            echo "Código: " . $error['code'] . "<br>";
            echo "Mensaje: " . $error['message'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<h1>¡Conexión exitosa!</h1>";
        echo "<p>Se ha establecido correctamente la conexión con la base de datos SQL Server.</p>";
        
        // Intentar ejecutar una consulta simple
        echo "<h2>Probando consulta:</h2>";
        $sql = "SELECT @@VERSION as Version";
        $stmt = sqlsrv_query($conn, $sql);
        
        if ($stmt === false) {
            echo "<p>Error al ejecutar la consulta.</p>";
            $errors = sqlsrv_errors();
            foreach ($errors as $error) {
                echo "SQLSTATE: " . $error['SQLSTATE'] . "<br>";
                echo "Código: " . $error['code'] . "<br>";
                echo "Mensaje: " . $error['message'] . "<br><br>";
            }
        } else {
            if (sqlsrv_fetch($stmt)) {
                echo "<p>Versión del servidor: " . sqlsrv_get_field($stmt, 0) . "</p>";
            }
            sqlsrv_free_stmt($stmt);
        }
        
        // Cerrar la conexión
        sqlsrv_close($conn);
    }
} catch (Exception $e) {
    echo "<h1>Error de excepción</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
