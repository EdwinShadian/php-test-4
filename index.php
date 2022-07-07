<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/funcs.php';

/**
 * Дублирование кода: Непонятно, зачем 9-18 строки существуют и тут, и вынесены в файл ./settings/config.php, \
 * и существуют в файле ./settings/index.php.
 *
 * В документации к библиотеке NestedSet есть пример, как подключиться к БД:
 * https://github.com/Rundiz/nested-set#connect-to-db. Он намного грамотнее и сделан через объект PDO, так что вызывает
 * больше доверия.
 */
$db['dsn'] = 'mysql:dbname=category_tz;host=localhost;port=3306;charset=UTF8';
$db['username'] = 'root';
$db['password'] = '';
$db['options'] = [
    /**
     * В composer.json просится добавить ext-pdo
     */
    \PDO::ATTR_EMULATE_PREPARES => false,
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION // throws PDOException.
];
$db['tablename'] = 'categories';

$NestedSet = new \Rundiz\NestedSet\NestedSet(['pdoconfig' => $db, 'tablename' => $db['tablename']]);


/**
 * В чем смысл этого куска закомментированного кода? Если он для потомков полезен - так лучше так и написать.
 * Если нет - то его лучше просто удалить.
 */
// $stmt = $NestedSet->Database->PDO->prepare("INSERT INTO category (parent_id,name,position) VALUES(?,?,?)");

// $stmt->execute([0,'Книги',2]);
// $stmt->execute([0,'Путешествия',2]);

// $stmt->execute([204,'Мэверик',3]);
// $NestedSet->rebuild();


// $stmt->execute();
$options=[];
$options['unlimited'] = true;
/**
 * То, что list_txn == listTaxonomy - понятно. Но зачем тратить каждый раз время на чтения, если можно один раз
 * потратить на нормальный нэйминг?
 */
$list_txn = $NestedSet->listTaxonomy($options);

/**
 * Проверка абсолютно бессмысленная - если подключение к БД уже состоялось, то listTaxonomy нам в любом случае вернет
 * массив с ключами "total" и "items". Если не хотим рендерить пустое дерево - тогда есть смысл проверять значения
 * по этим ключам на пустоту.
 */
if (is_array($list_txn) && array_key_exists('items', $list_txn)) {
    echo renderTaxonomyTree($list_txn['items'], $NestedSet);
}