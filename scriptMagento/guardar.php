<?php

include "clases.php";


$servername = "mysql";
$username = "root";
$password = "root";
$dbname = "db_productos";
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



// insertar array
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit']))
{
  
    $nickname =$_POST['nickname'];//no importa si es camelcase anda igual
    $resultado = new Productos($nickname);
    $productos = $resultado->mostraProductos();

    
      
      
        $id = "nico" ;
        $title = "nico" ;
        $price = 12.3 ;
        
        $sql = "INSERT INTO productos
          (id, title, price)
          VALUES
          ('$id', '$title', '$price')";
          $conn->query($sql);
          
      

}

    



?>