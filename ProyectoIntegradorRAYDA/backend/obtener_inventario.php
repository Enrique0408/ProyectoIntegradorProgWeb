    <?php
    include("conexion.php");

    header("Content-Type: application/json; charset=UTF-8");

    $query = "SELECT * FROM inventario";
    $result = $conexion->query($query);

    $productos = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
    }

    echo json_encode($productos);
    $conexion->close();
    ?>
