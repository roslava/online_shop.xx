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


//json-строку конвертирую в массив
$actual_products = json_decode($res, true);


//Получаю все товары из базы данных в виде ассоциативного массива (для просмотра)
echo "<h2>Состояние таблицы products</h2>";
    $query = $pdo->query('SELECT * FROM products');
    $products = $query->fetchAll();
    echo "<h2>Продукты из базы данных</h2>";
    var_dump($products);
    echo "<hr>";


//перебираю массив $actual_products (актуальные товары с сервера)
foreach ($actual_products as $a_product) {

    $stmt = $pdo->prepare('SELECT * FROM products WHERE external_id =:external_id');
    $stmt->bindValue(':external_id', $a_product["id"], PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        $stmt = $pdo->prepare('UPDATE products SET name =:name, description =:description, price =:price, img =:img, updation_date =:updation_date WHERE external_id = :external_id');
        $stmt->bindValue(':name', $a_product["name"], PDO::PARAM_STR);
        $stmt->bindValue(':description', $a_product["description"], PDO::PARAM_STR);
        $stmt->bindValue(':price', $a_product["price"], PDO::PARAM_STR);
        array_key_exists('img', $a_product) ? $stmt->bindValue(':img', $a_product["img"], PDO::PARAM_STR) : $stmt->bindValue(':img', NULL, PDO::PARAM_STR); //в json нет поля img
        $stmt->bindValue(':external_id', $a_product["id"], PDO::PARAM_STR);
        $stmt->bindValue(':updation_date', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->execute();
    }
    else {
        $data = [
            'name' => $a_product["name"],
            'description' => $a_product["description"],
            'price' => $a_product["price"],
            'img' => '...',
            'external_id' => $a_product["id"],
            'creation_date' => date('Y-m-d H:i:s'),
            'updation_date' => date('Y-m-d H:i:s')
         ];

        $sql = "INSERT INTO products (name, description, price,img,external_id,creation_date,updation_date) VALUES (:name, :description, :price, :img, :external_id, :creation_date, :updation_date)";
        $stmt= $pdo->prepare($sql);
        $stmt->execute($data);
    }
}