<div class="wrapper">
    <div id="enter-room" class="block btn big-btn">
        <span>Войти в комнату</span>
    </div>
    <div id="enter-room-root" class="block btn big-btn">
        <span>Войти с правами преподавателя</span>
    </div>
    <div id="create-room" class="block btn big-btn">
        <span>Создать комнату</span>
    </div>
</div>
<div class="wrapper">
    <div id="form-create" class="form hidden">
        <form>
            <div class="form-el">
                <label for="user_name">Имя (будет видно остальным участникам)</label>
                <input name="user_name" id="user_name_cr">
                <b class="alert">!</b>
            </div>
            <div class="form-el">
                <label for="room_name">Название комнаты</label>
                <input name="room_name" id="room_name_cr">
                <b class="alert">!</b>
            </div>
            <div class="form-el">
                <label for="room_pass">Пароль (4-6 символов)</label>
                <input name="room_pass" id="room_pass_cr">
                <b class="alert">!</b>
            </div>
            <div class="form-el">
                <label for="paper">Количество билетов</label>
                <input class="small-input" name="paper" id="paper_cr">
                <b class="alert">!</b>
            </div>
            <div class="form-el">
                <div id="btn-create" class="small-btn btn block">Создать</div>
            </div>
        </form>
    </div>
    <div id="form-in-root" class="form hidden">
        <form>
            <div class="form-el">
                <label for="room_code">Код комнаты</label>
                <input name="room_code" id="room_code_ir">
            </div>
            <div class="form-el">
                <label for="room_pass">Пароль</label>
                <input name="room_pass" id="room_pass_ir">
            </div>
            <div class="form-el">
                <div id="btn-in-root" class="small-btn btn block">Войти</div>
            </div>
        </form>
    </div>
    <div id="form-in" class="form hidden">
        <form>
            <div class="form-el">
                <label for="user_name">Имя</label>
                <input name="user_name" id="user_name_i">
            </div>
            <div class="form-el">
                <label for="room_code">Код комнаты</label>
                <input name="room_code" id="room_code_i">
            </div>
            <div class="form-el">
                <div id="btn-in" class="small-btn btn block">Войти</div>
            </div>
        </form>
    </div>
</div>
