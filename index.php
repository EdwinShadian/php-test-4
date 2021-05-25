<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/funcs.php';

$db['dsn'] = 'mysql:dbname=category_tz;host=localhost;port=3306;charset=UTF8';
$db['username'] = 'root';
$db['password'] = '';
$db['options'] = [
    \PDO::ATTR_EMULATE_PREPARES => false,
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION // throws PDOException.
];
$db['tablename'] = 'categories';

$NestedSet = new \Rundiz\NestedSet\NestedSet(['pdoconfig' => $db, 'tablename' => $db['tablename']]);

 

// $stmt = $NestedSet->Database->PDO->prepare("INSERT INTO category (parent_id,name,position) VALUES(?,?,?)");

// $stmt->execute([0,'Книги',2]);
// $stmt->execute([0,'Путешествия',2]);

// $stmt->execute([204,'Мэверик',3]);
// $NestedSet->rebuild();


// $stmt->execute();
$options=[];
$options['unlimited'] = true;
$list_txn = $NestedSet->listTaxonomy($options);


if (is_array($list_txn) && array_key_exists('items', $list_txn)) {
    echo renderTaxonomyTree($list_txn['items'], $NestedSet);
}