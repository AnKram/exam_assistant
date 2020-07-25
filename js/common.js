$(document).ready(function(){
    /* add forms */
    $('#create-room').on('click', function () {
        $('#form-create').removeClass('hidden');
        $('#form-in-root').addClass('hidden');
        $('#form-in').addClass('hidden');
    });
    $('#enter-room').on('click', function () {
        $('#form-in').removeClass('hidden');
        $('#form-in-root').addClass('hidden');
        $('#form-create').addClass('hidden');
    });
    $('#enter-room-root').on('click', function () {
        $('#form-in').addClass('hidden');
        $('#form-create').addClass('hidden');
        $('#form-in-root').removeClass('hidden');
    });

    /* forms processing */
    $('#btn-create').on('click', function () {
        let user_name_cr = $('#user_name_cr');
        let room_name_cr = $('#room_name_cr');
        let room_pass_cr = $('#room_pass_cr');
        let paper_cr = $('#paper_cr');

        let user_name_cr_val = user_name_cr.val();
        let room_name_cr_val = room_name_cr.val();
        let room_pass_cr_val = room_pass_cr.val();
        let paper_cr_val = paper_cr.val();

        let send_flag = true;

        $('#form-create .form-el .alert').each(function () {
            $(this).removeClass('active');
        });

        if (user_name_cr_val.length < 2) {
            user_name_cr.next().addClass('active');
            send_flag = false;
        }
        if (room_name_cr_val.length < 2) {
            room_name_cr.next().addClass('active');
            send_flag = false;
        }
        if (room_pass_cr_val.length < 4 || room_pass_cr_val.length > 6) {
            room_pass_cr.next().addClass('active');
            send_flag = false;
        }
        if (Number(paper_cr_val) < 2 || isNaN(Number(paper_cr_val)) || Number(paper_cr_val) > 100) {
            paper_cr.next().addClass('active');
            send_flag = false;
        }

        if (send_flag) {
            let data = {
                form: 'create',
                user_name: user_name_cr_val,
                room_name: room_name_cr_val,
                room_pass: room_pass_cr_val,
                paper: Number(paper_cr_val)
            };
            sendAjaxFromFormsToServer(data);
        }
    });

    $('#btn-in').on('click', function () {
        let user_name_i = $('#user_name_i');
        let room_code_i = $('#room_code_i');

        let user_name_i_val = user_name_i.val();
        let room_code_i_val = room_code_i.val();

        let send_flag = true;

        $('#form-in .form-el .alert').each(function () {
            $(this).removeClass('active');
        });

        if (user_name_i_val.length < 2) {
            user_name_i.next().addClass('active');
            send_flag = false;
        }
        if (room_code_i_val.length !== 15) {
            room_code_i.next().addClass('active');
            send_flag = false;
        }

        if (send_flag) {
            let data = {
                form: 'in',
                user_name: user_name_i_val,
                room_code: room_code_i_val,
            };
            sendAjaxFromFormsToServer(data);
        }
    });

    $('#btn-in-root').on('click', function () {
        let room_pass_ir = $('#room_pass_ir');
        let room_code_ir = $('#room_code_ir');

        let room_pass_ir_val = room_pass_ir.val();
        let room_code_ir_val = room_code_ir.val();

        let send_flag = true;

        $('#form-in-root .form-el .alert').each(function () {
            $(this).removeClass('active');
        });

        if (room_pass_ir_val.length < 2) {
            room_pass_ir.next().addClass('active');
            send_flag = false;
        }
        if (room_code_ir_val.length !== 15) {
            room_code_ir.next().addClass('active');
            send_flag = false;
        }

        if (send_flag) {
            let data = {
                form: 'in-root',
                room_pass: room_pass_ir_val,
                room_code: room_code_ir_val,
            };
            sendAjaxFromFormsToServer(data);
        }
    });

	//exit
	$('#exit').on('click', function () {
		window.location.replace(window.location.origin);
	});

    // paper selection
    $('#papers-block .paper-big').each(function (i, elem) {
        $(this).on('click', function () {
            let classes = $(this).attr('class');
            let paper_num = classes.split(' ')[1];
            sendAjaxPaperNum(paper_num);
        })
    });

    // upload data about students
    let delay = 5000;
    let upload_data = setTimeout(function uploadData() {
        let some_string = $('#papers-block').attr('class');
		if (some_string) {
			sendAjaxUpdate(some_string);
			upload_data = setTimeout(uploadData, delay); // (*)
		}
    }, delay);
});

// send json ajax (post) to server
function sendAjaxFromFormsToServer(data) {
    let json = JSON.stringify(data);
	cl(json);
    $.ajax({
        url: '../controllers/room_endpoint.php',
        dataType: 'json',
        type: 'post',
        contentType: 'application/x-www-form-urlencoded',
        data: {'data':json},
        success: function(data){
            if(data['result']) {
				let current_location = window.location.href;
                window.location.replace(current_location + "room/" + data['result']);
            }
        },
        error: function(errorThrown){
            console.log( errorThrown );
        }
    });
}

function sendAjaxPaperNum(num) {
    let url_elements = location.href.split('/');
    let room_code = url_elements[url_elements.length - 1];
    let data = {
        'ticket': true,
        'room': room_code,
        'num': num
    };
    let json = JSON.stringify(data);
    $.ajax({
        url: '../controllers/room_endpoint.php',
        dataType: 'json',
        type: 'post',
        contentType: 'application/x-www-form-urlencoded',
        data: {'data':json},
        success: function(data, textStatus, jQxhr){
            if(data['result']) {
                location.reload();
            }
        },
        error: function(jqXhr, textStatus, errorThrown){
            console.log( errorThrown );
        }
    });
}

function sendAjaxUpdate(some_string) {
    let data = {
        'update': true,
        'some_string': some_string,
    };
    let json = JSON.stringify(data);
    $.ajax({
        url: '../controllers/room_endpoint.php',
        dataType: 'json',
        type: 'post',
        contentType: 'application/x-www-form-urlencoded',
        data: {'data':json},
        success: function(data){
            if(data['result']) {
                let tickets_quantity = data['result']['room_info'][0]['paper_count'];
                let tickets = [];
                let users_info = data['result']['users_info'];

                let tickets_html = '<i class="w100">Оставшиеся билеты:</i>';
                let list_html = '<div class="row">' +
                    '<div class="student-name-title">Имя студента</div>' +
                    '<div class="student-paper-title">Номер билета</div>' +
                    '</div>';

                for (let el = 0; el < users_info.length; el++) {
                    if (users_info[el]['paper'] !== null) {
                        tickets.push(Number(users_info[el]['paper']));
                        list_html += '<div class="row">' +
                            '<div class="student-name">' + users_info[el]['name'] + '</div>\n' +
                            '<div class="student-paper">билет №<b>' + users_info[el]['paper'] + '</b></div>\n' +
                            '</div>'
                    } else {
                        list_html += '<div class="row">' +
                            '<div class="student-name">' + users_info[el]['name'] + '</div>\n' +
                            '<div class="student-paper">билет не выбран</div>\n' +
                            '</div>'
                    }
                }

                for (let el = 1; el <= tickets_quantity; el++) {
                    if (!tickets.includes(el)) {
                        tickets_html += '<div class="paper-small"><b>' + el + '</b></div>'
                    }
                }

                $('#papers-block').html(tickets_html);
                $('#info_table').html(list_html);
            }
        },
        error: function(jqXhr, textStatus, errorThrown){
            console.log( errorThrown );
        }
    });
}

function cl(a) {
    console.log(a);
}
