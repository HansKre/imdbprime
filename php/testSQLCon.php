<?php
require_once ('commons.php');

/*
 * Root User: admin8Gu6jMU
   Root Password: 2cHfZj1thI91
   Database Name: imdbprime

Connection URL: mysql://$OPENSHIFT_MYSQL_DB_HOST:$OPENSHIFT_MYSQL_DB_PORT/
 * */

// tutorial: https://www.php-einfach.de/mysql-tutorial/crashkurs-pdo/
// http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers


define('DB_HOST', getenv('OPENSHIFT_MYSQL_DB_HOST'));
define('DB_PORT',getenv('OPENSHIFT_MYSQL_DB_PORT'));
define('DB_USER',getenv('OPENSHIFT_MYSQL_DB_USERNAME'));
define('DB_PASS',getenv('OPENSHIFT_MYSQL_DB_PASSWORD'));
define('DB_NAME',getenv('OPENSHIFT_GEAR_NAME'));

$dsn = 'mysql:dbname='.DB_NAME.';host='.DB_HOST.';port='.DB_PORT;
$pdo = new PDO($dsn, DB_USER, DB_PASS);

$sql = "SELECT * FROM users";
$rows = $pdo->query($sql);
echo "errorCode " .  $pdo->errorCode() . "<br>";
echo "errorInfo " .  $pdo->errorInfo() . "<br>";

echo "DB Rows <br>";

foreach ($rows as $row) {
    echo $row['datum'] . " , " . $row['firstname'] . " " . $row['lastname'] . "<br>";
}


// Die Tabelle imdbprime.users hat die spalen: id, datum, firstname, lastname
// spalte id ist primary key und darf deshalb A_I, also auto_increment aktiv haben
// deshalb wird dieser Wert von der DB automatisch gesetzt (darf nicht vom user gesetzt werden?)
$statement = $pdo->prepare("INSERT INTO " . DB_NAME . ".users (datum, firstname, lastname) VALUES (?, ?, ?)");
$statement->execute(array(nowAsString(), 'Hans', 'Lena'));
echo "errorCode " .  $pdo->errorCode() . "<br>";
echo "errorInfo " .  $pdo->errorInfo() . "<br>";
$neue_id = $pdo->lastInsertId();
echo "Neuer Nutzer mit id $neue_id angelegt";