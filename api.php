<?php

require_once ('common_include.php');


if (!isset($_GET["method"])) {
    die('Error: no method specified');
}

function get() {
    $result = mysql_query('SELECT id, content FROM items ORDER BY id ASC');
    $data = array();
    if ($result) {
        while ($row = mysql_fetch_assoc($result)) {
            array_push(
                $data,
                array(
                    'id' => $row['id'],
                    'content' => $row['content']
                )
            );
        }
        echo json_encode($data);
    } else {
        die('Error: Invalid query: ' . mysql_error());
    }
}

$method = $_GET["method"];
if ($method === 'get') {
    get();
} elseif ($method === 'put') {
    $post_vars = json_decode(file_get_contents("php://input"));
    $content = mysql_real_escape_string($post_vars->content);
    if (empty($content)) {
        die("empty");
    }
    $result = mysql_query("INSERT INTO items (content) VALUES('$content')");
    if (!$result) {
        die('Error: Invalid query: ' . mysql_error());
    }
    echo mysql_insert_id();
} elseif ($method === 'clear') {
    $post_vars = json_decode(file_get_contents("php://input"));
    $itemIds = $post_vars->itemIds;
    $itemIdsString = implode(", ", $itemIds);
    if (empty($itemIds)) {
        die("empty");
    }
    $result = mysql_query("DELETE FROM items WHERE id IN ($itemIdsString)");
    if (!$result) {
        die('Error: Invalid query: ' . mysql_error());
    }
    get();
}
