<?php
session_start();
include_once('../engine/db.php');

/* database connection */
$db = new DB();
$conn = $db->dbConnect();


// accept data from forms, determine which form and what we will do
if (!empty($_POST['data'])) {
    $data = json_decode($_POST['data'], true);
    if(!empty($data['form'])) {
        switch ($data['form']) {
            case 'create':
                // create room
                $result_data = $db->createRoom($conn, $data);
                $_SESSION['role'] = 'admin';
                $_SESSION['room_code'] = $result_data['room']['code'];
                $_SESSION['room_name'] = $result_data['room']['name'];
                $_SESSION['papers'] = $result_data['room']['papers'];
                $_SESSION['user_name'] = $result_data['user']['name'];
                break;
            case 'in':
                // enter room
                $result_data = $db->enterTheRoom($conn, $data);
                $_SESSION['role'] = 'user';
                $_SESSION['room_id'] = $result_data['room']['id'];
                $_SESSION['user_id'] = $result_data['user']['id'];
                $_SESSION['user_name'] = $result_data['user']['name'];
                break;
            case 'in-root':
                // enter room as admin
                $rooms = $db->enterTheRoomAsAdmin($conn, $data);
                if(count($rooms) === 1) {
                    $result_data = $rooms[0];
                } else {
                    die('error, there are several rooms with the same code and pass!');
                }

                $_SESSION['role'] = 'admin';
                break;
        }
		
		if (!empty($result_data['room']['code'])) {
			echo json_encode(['result' => $result_data['room']['code']]);
		} else {
			echo json_encode(['result' => false, 'error' => 'result_data is wrong (form in)']);
		}
        
    } elseif (!empty($data['ticket'])) {
        // get the ticket number
        $tickets_info = $db->getFreePapers($conn, $data['room']);
        $ticket_num = getTicketFromFree($tickets_info, $data['num']);
        $assign = $db->assignTicketToStudent($ticket_num, $_SESSION['user_id']);
        if (!$assign) {
            die('assign ticket not successful');
        }
        $_SESSION['ticket'] = [];
        $_SESSION['ticket'][$_SESSION['user_id']] = $ticket_num;

        echo json_encode(['result' => $ticket_num]);
    } elseif (!empty($data['update'])) {
        if (!empty($data['some_string'])) {
			$room_id = substr($data['some_string'], 5);
		} else {
			echo json_encode(['result' => false, 'error' => 'some_string unspecified']);
			//todo: mb die(bububu)
			die();
		}
        if (!empty($room_id) && strlen($room_id) > 0) {
            $users_info = $db->getUsersInfoByRoomId($room_id);
            $room_info = $db->getRoomInfoById($room_id);
            echo json_encode(['result' => ['users_info' => $users_info, 'room_info' => $room_info]]);
        } else {
            echo json_encode(['result' => false, 'error' => 'something wrong with room id']);
        }
    }

}


//todo: transfer function to other file, maybe in functions.php
/**
 * @param array $tickets_info
 * @param int $num
 * @return int
 */
function getTicketFromFree(array $tickets_info, int $num): int
{
    // looking for any busy tickets
    $busy_tickets = [];
    if(!empty($tickets_info['busy_tickets'])) {
        foreach ($tickets_info['busy_tickets'] as $busy_ticket) {
            if ($busy_ticket[1] !== 'NULL') {
                $busy_tickets[] = $busy_ticket[1];
            }
        }
    }

    // create tickets array
    $tickets = [];
    for ($i = 1; $i <= $tickets_info['quantity']; $i++) {
        if (!in_array($i, $busy_tickets)) {
            $tickets[] = $i;
        }
    }

    shuffle($tickets);
    //$ticket_num = $shuffle_tickets[$num] ?? array_pop($shuffle_tickets);
    return count($tickets) > $num ? $tickets[$num] : end($tickets);
}