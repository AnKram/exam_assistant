<div class="wrapper">
	<div id="room-header">
		<div id="room_name">
			<h3>Комната: <?php echo $room_info['room_name']; ?></h3>
		</div>
		<div id="user_name">
			<h3>
				<?php echo ($_SESSION['role'] === 'admin' && !empty($admin_info['name'])) ? $admin_info['name'] : $_SESSION['user_name']; ?>
			</h3>
		</div>
	</div>
	<div id="exit" class="small-btn btn block">Выйти</div>
	<?php if ($_SESSION['role'] === 'admin') { ?>
		<div id="papers-block" class="<?php echo generate_string('', 5) . $room_info['id']; ?>">
			<i class="w100">Оставшиеся билеты:</i>
            <?php for($i = 1; $i <= $room_info['paper_count']; $i++) {
            	if (in_array($i, $tickets)) {
            		continue;
				}
            	?>
                <div class="paper-small">
                    <b><?php echo $i; ?></b>
                </div>
            <?php } ?>
        </div>
		<div id="buttons-block">
			<div id="reload" class="info block">Код комнаты: <?php echo $room_info['room_code']; ?></div>
		</div>
		<div id="info_table">
			<div class="row">
				<div class="student-name-title">Имя студента</div>
				<div class="student-paper-title">Номер билета</div>
			</div>
            <?php foreach ($users_info as $user) { ?>
                <div class="row">
                    <div class="student-name"><?php echo $user['name']; ?></div>
                    <div class="student-paper"><?php echo is_null($user['paper']) ? 'билет не выбран' : 'билет №<b>' . $user['paper'] . '</b>'; ?></div>
                </div>
            <?php } ?>
        </div>
	<?php } elseif ($_SESSION['role'] === 'user') { ?>
        <?php if (empty($_SESSION['ticket'][$_SESSION['user_id']])) { ?>
			<div id="papers-block">
				<?php if($tickets_quantity === 0) { ?>
					<h2 id="ticket-num">Билетов больше нет</h2>
				<?php } ?>
                <?php for($i = 1; $i <= (int)$tickets_quantity; $i++) { ?>
					<div class="paper-big <?php echo $i; ?>">
						<b><?php echo $i; ?></b>
					</div>
                <?php } ?>
			</div>
		<?php } else { ?>
			<h2 id="ticket-num">Ваш билет №<?php echo $_SESSION['ticket'][$_SESSION['user_id']]; ?></h2>
		<?php } ?>
	<?php } ?>
</div>