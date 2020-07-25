<?php
//header(‘Content-Type: text/html; charset=utf8’);

require_once ('/var/www/exam_assistant/config.php');
require_once (DEFAULT_PATH . 'functions.php');

class DB
{
    /**
     * @return mysqli
     */
    public function dbConnect()
    {
		//todo: from config
		$servername = 'localhost';
		$username = 'root';
		$password = 'k12tsd50';
		$database = 'exam';
		
        // Create connection
        $conn = new mysqli($servername, $username, $password, $database);
		
        // Check connection
        if ($conn->connect_error)
        {
            die("Connection failed: " . $conn->connect_error);
        }
		
        return $conn;
    }

    /**
     * @param $conn
     */
    private function dbClose($conn)
    {
        mysqli_close ($conn);
    }

    /**
     * @param $conn
     * @param $data
     * @return array
     */
    public function createRoom($conn, $data)
    {
        /* create user admin */
        $sql = 'INSERT INTO `users_t` (`role`, `name`) VALUES ("admin", "' . $data['user_name'] . '");';
        $query = mysqli_query($conn, $sql);
        if (!$query) {
            die("Query 1 failed: createRoom");
        }
        $insert_id_user = mysqli_insert_id($conn);

        /* create room */
        $room_code = generate_string('');
        $room_pass = md5($data['room_pass']);

        $sql = 'INSERT INTO `rooms` (`room_name`, `room_code`, `room_pass`, `id_room_admin`, `paper_count`) 
            VALUES ("' . $data['room_name'] . '", "' . $room_code . '", "' . $room_pass . '", "' . $insert_id_user . '", "'. $data['paper'] .'");';
        $query = mysqli_query($conn, $sql);
        if (!$query) {
            die("Query 2 failed: createRoom".$sql);
        } else {
            $room_id = $query;
        }
        $insert_id_room = mysqli_insert_id($conn);

        /* create a connection between the user and the room */
        $sql = 'INSERT INTO `users_to_rooms` (`user_id`, `room_id`) VALUES ("'. $insert_id_user .'", "'. $insert_id_room .'");';
        $query = mysqli_query($conn, $sql);

        $this->dbClose($conn);
        //$result = ['room_code' => $room_code];
        return [
            'room' => [
                'id' => $room_id,
                'code' => $room_code,
                'name' => $data['room_name'],
                'papers' => $data['paper']
            ],
            'user' => [
                'name' => $data['user_name']
            ]
        ];
    }

    /**
     * @param $conn
     * @param $data
     * @return array|bool
     */
    public function enterTheRoom($conn, $data)
    {
        /* request for a room, if it is, then let */
        $sql = 'SELECT * FROM `rooms` WHERE `room_code` = "' . $data['room_code'] . '";';
        $result = mysqli_query($conn, $sql);
        if ($result->num_rows > 1) {
            die('error, there are several rooms with the same code!');
        }
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $room = [
                'id' => $row['id'],
                'name' => $row['room_name'],
                'code' => $row['room_code'],
                'papers' => $row['paper_count'],
            ];
        }
        if(empty($room)) {
            $this->dbClose($conn);
            return false;
        }

        // create user
        $user_data = [
            'user_name' => $data['user_name']
        ];
        $user_id = $this->createUser($user_data);

        $user = [
            'id' => $user_id,
            'name' => $data['user_name']
        ];

        // create relation between user and room
        $this->createRelation($conn, $user_id, $room['id']);

        $this->dbClose($conn);
        return [
            'room' => $room,
            'user' => $user
        ];
    }

    /**
     * @param $conn
     * @param $data
     * @return array|bool
     */
    public function enterTheRoomAsAdmin($conn, $data)
    {
        /* request for a room for admin, if it is and pass is correct, then let */
        $sql = '
            SELECT * 
            FROM `rooms` 
            WHERE `room_code` = "' . $data['room_code'] . '" 
            AND `room_pass` = "' . md5($data['room_pass']) . '";';
        $result = mysqli_query($conn, $sql);
        $this->dbClose($conn);
        if ($result->num_rows > 0) {
            $rooms = [];
            while($row = $result->fetch_assoc()) {
                $rooms[] = [
                    'room' => [
                        'id' => $row['id'],
                        'code' => $row['room_code'],
                        'name' => $row['room_name'],
                        'papers' => $row['paper_count'],
                        'id_room_admin' => $row['id_room_admin']
                    ]
                ];
            }
            return $rooms;
        } else {
            return false;
        }
    }

    /**
     * @param $conn
     * @param $user_data
     * @return int|string
     */
    public function createUser($user_data)
    {
        $conn = $this->dbConnect();

		$sql = 'INSERT INTO `users_t` (`role`, `name`) VALUES ("user", "' . $user_data['user_name'] . '");';
		$result = $conn->query($sql);
        if (!$result) {
            die("Query failed: createUser");
        }

        return mysqli_insert_id($conn); // user id
    }

    /**
     * @param $conn
     * @param $user_id
     * @param $room_id
     */
    public function createRelation($conn, $user_id, $room_id)
    {
        $sql = 'INSERT INTO `users_to_rooms` (`user_id`, `room_id`) VALUES ("' . $user_id . '", "' . $room_id . '");';
        mysqli_query($conn, $sql);

        //todo: add check result of insert
        //todo: add return
    }

    /**
     * @param string $room_code
     * @return array
     */
    public function getRoomInfoByCode(string $room_code): array
    {
        $conn = $this->dbConnect();

        $sql = 'SELECT * FROM `rooms` WHERE `room_code` = "' . $room_code . '";';
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $info = [];
            while($row = $result->fetch_assoc()) {
                $info[] = $row;
            }
            return $info;
        } else {
            return [];
        }
    }

    public function getRoomInfoById(string $room_id): array
    {
        $conn = $this->dbConnect();

        $sql = 'SELECT * FROM `rooms` WHERE `id` = "' . $room_id . '";';
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $info = [];
            while($row = $result->fetch_assoc()) {
                $info[] = $row;
            }
            return $info;
        } else {
            return [];
        }
    }

    /**
     * @param int $user_id
     * @return array
     */
    public function getUserById(int $user_id): array
    {
        $conn = $this->dbConnect();

        $sql = 'SELECT * FROM `users_t` WHERE `id` = "' . $user_id . '";';
        $result = mysqli_query($conn, $sql);
        $conn->close();

        $user = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $user = [
                    'id' => $row['id'],
                    'role' => $row['role'],
                    'name' => $row['name'],
                    'paper' => $row['paper']
                ];
            }
        }
        return $user;
    }

    /**
     * @param int $room_id
     * @return array
     */
    public function getUsersInfoByRoomId(int $room_id): array
    {
        $conn = $this->dbConnect();

        $sql = '
            SELECT ut.`id`, ut.`name`, ut.`paper` 
            FROM `users_to_rooms` as utr
            LEFT JOIN `users_t` as ut ON utr.`user_id` = ut.`id`
            WHERE utr.`room_id` = "' . $room_id . '"
            AND ut.`role` = "user";';
        $result = mysqli_query($conn, $sql);

        $users = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $users[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'paper' => $row['paper']
                ];
            }
        }

        $this->dbClose($conn);

        return $users;
    }

    /**
     * @param $conn
     * @param $room_code
     * @return array
     */
    public function getFreePapers($conn, $room_code)
    {
        // select room_id and tickets quantity
        $room_info = $this->getRoomInfoByCode($room_code);

        if(count($room_info) === 1) {
            $room_info = $room_info[0];
        } else {
            die('sql. error, there are several rooms with the same code!');
        }

        // select all busy tickets
        $sql = '
            SELECT utr.`user_id`, ut.`paper`, ut.`name` 
            FROM `users_to_rooms` AS utr
            LEFT JOIN `users_t` AS ut ON(utr.`user_id` = ut.`id`)
            WHERE utr.`room_id` = "' . $room_info['id'] . '"
            AND ut.`role` != "admin"';
            //AND ut.`paper` IS NOT NULL;';
        $sql_result = mysqli_query($conn, $sql);
        $this->dbClose($conn);
        $result = $sql_result->fetch_all();

        return [
            'busy_tickets' => $result,
            'quantity' => $room_info['paper_count']
        ];
    }

    /**
     * @param int $ticket_num
     * @param int $user_id
     * @return bool
     */
    public function assignTicketToStudent(int $ticket_num, int $user_id): bool
    {
        $conn = $this->dbConnect();

        $sql = '
            UPDATE `users_t`
            SET `paper` = "' . $ticket_num . '"
            WHERE `id` = "' . $user_id . '";';

        $result = $conn->query($sql);
        $conn->close();
        if ($result) {
            return true;
        } else {
            return false;
        }

    }
}
