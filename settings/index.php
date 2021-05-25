<?php
$db['dsn'] = 'mysql:dbname=category_tz;host=localhost;port=3306;charset=UTF8';
$db['username'] = 'root';
$db['password'] = '';
$db['options'] = [
\PDO::ATTR_EMULATE_PREPARES => false,
\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION // throws PDOException.
];
$db['tablename'] = 'categories';