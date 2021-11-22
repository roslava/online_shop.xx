<?php
var_dump("Hi!");


require("db.php");



$query = $pdo ->query('SELECT * FROM products');
//var_dump($pdo);

//var_dump($query->fetchAll());
$products = $query->fetchAll();
var_dump($products);


foreach ($products as $product){

    echo "Название продукта " . $product['name']."<br>";
    echo "Описани: " . $product['description']."<br>";
    echo "Цена: " . $product['price']."<br>";
    echo "Внешний ID: " . $product['external_id']."<br>";
    echo "<hr>";
}




