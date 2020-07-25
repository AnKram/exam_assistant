<?php
$db_conn = new DB();
$conn = $db_conn->dbConnect();


$room_path = explode('/', $_SERVER['REQUEST_URI']);
$room_code = $room_path[2];
$room_info = $db_conn->getRoomInfoByCode($room_code);
if (count($room_info) > 1) {
	//todo: refactoring db method, mb it should return only one room.
    die('There are several rooms with this code! Try criate anoter room please.');
} elseif (count($room_info) === 0) {
	//todo: add exception
    die('There are not rooms with this code! Check the code with the room administrator please.');
} else {
    $room_info = $room_info[0];
}
if ($_SESSION['role'] === 'admin') {
    $admin_info = $db_conn->getUserById($room_info['id_room_admin']);
}

$users_info = $db_conn->getUsersInfoByRoomId($room_info['id']);
$tickets = [];
foreach ($users_info as $user) {
    if(!is_null($user['paper'])) {
        array_push($tickets, $user['paper']);
    }
}

$tickets_quantity = (int)$room_info['paper_count'] - count($tickets);

require_once (TEAMPLATES_PATH . 'room.php');



