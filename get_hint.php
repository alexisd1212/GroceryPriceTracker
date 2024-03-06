<?php
// Connect to the database
require_once 'connect.php';

$sql = "SELECT name FROM grocery_items";
$statement = $pdo->prepare($sql);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

// Put all names into an array
$a = array();
foreach ($result as $row) {
    array_push($a, $row['name']);
}

// get the q parameter from URL
$q = $_REQUEST["q"];
$hint = "";

// lookup all hints from array if $q is different from ""
if ($q !== "") {
    $q = strtolower($q);
    $len = strlen($q);
    foreach ($a as $name) {
        // return all occurances where $a contains $q
        if (stristr($q, substr($name, 0, $len))) {
            if ($hint === "") {
                $hint = $name;
            } else {
                $hint .= ", $name";
            }
        }
    }
}

// Output "no suggestion" if no hint was found or output correct values
echo $hint === "" ? "no suggestion" : $hint;
?>