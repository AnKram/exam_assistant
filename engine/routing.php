<?php

class Routing
{
    private static $routes = [
        'index' => 'start_page',
        'room' => 'room',
        '404' => '404'
    ];

    public static function getAction($path) {
        if (!$path) {
            return Routing::$routes['index'];
        } else {
            $path_parts = explode('/', $path);

            if(count($path_parts) === 2 && $path_parts[0] === 'room' && strlen($path_parts[1]) === ROOM_CODE_LEN) {
                return Routing::$routes['room'];
            } else {
                return Routing::$routes['404'];
            }

        }
    }
}