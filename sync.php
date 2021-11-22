<?php

require("db.php");

//'http://185.125.216.212/sync.json'
// Получать продукты с сервера, id продуктов с сервера == external id
// если  продукт есть в базе — обновить,
// если не существует — создать
// при обновлении обновлять дату updated
// при создании дату created


// Получаем продукты с сервера
$ch = curl_init('http://185.125.216.212/sync.json');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$res = curl_exec($ch);
echo "<h2>Продукты с сервера (строка)</h2>";
var_dump($res);
echo "<hr>";

//json-строку конвертирую в массив
$actual_products = json_decode($res, true);
echo "<h2>Продукты с сервера (массив)</h2>";
var_dump($actual_products);
echo "<hr>";



//Получаю все товары из базы данных в виде ассоциативного массива

    $query = $pdo->query('SELECT * FROM products');
    $products = $query->fetchAll();
    echo "<h2>Продукты из базы данных</h2>";
    var_dump($products);
    echo "<hr>";
















////перебираю id $actual_products (актуальные товары с сервера)
foreach ($actual_products as $a_product) {
    if(checkId($a_product['id'], $products)){
        echo "Продуктс ID: " . $a_product['id']. " существует — АПДЕЙТ! <br>";

    }else{
        echo "Продуктс ID: " . $a_product['id']. " не существует — ДОБАВИТЬ! <br>";
        insertProduct($a_product, $pdo);


        //Получаю все товары из базы данных в виде ассоциативного массива
        $query = $pdo->query('SELECT * FROM products');
        $products = $query->fetchAll();
        echo "<h2>Продукты из базы данных</h2>";
        var_dump($products);
        echo "<hr>";


    }


}


function checkId($a_product_id, $products){
    foreach ($products as $product){
        if( $product["external_id"] == $a_product_id ){

            return true;
        }else{

            return false;
        }
    }
}


function insertProduct($a_product, $pdo){
    $sql = "INSERT INTO products (name, description, price, img, external_id, creation_date, updation_date) VALUES (?,?,?,?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $a_product['name'],
        $a_product['description'],
        $a_product['price'],
        'img',
        $a_product['id'],
        date('Y-m-d H:i:s'),
        date('Y-m-d H:i:s')]);

}

