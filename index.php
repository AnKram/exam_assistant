<?php
header ("Content-Type: text/html; charset=utf-8");

session_start();
require_once ('config.php');
require_once ('functions.php');

require_once('engine/db.php');
require_once('engine/routing.php');

$path = trim($_SERVER['REQUEST_URI'], '/');

$tml = Routing::getAction($path);

// routing
require_once(TEAMPLATES_PATH . 'header.php');

if ($tml === 'room') {
	require_once ('controllers/room.php');
} else {
    require_once(TEAMPLATES_PATH . $tml . '.php');
}

require_once(TEAMPLATES_PATH . 'footer.php');
