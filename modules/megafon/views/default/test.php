<?php

$this->registerJsFile('js/megafon/test.js', ['depends'=>'app\assets\AppAsset']);
?>

<p>Получение списка сотрудников из АТС</p>
<form method="post" action="http://trsuleymanov.megapbx.ru/sys/crm_api.wcgp">
    <input type="text" name="cmd" value="accounts" /><br />
    <input type="text" name="token" value="6afb083a-2ec3-4e68-9a71-ef6828637c42" /><br />
    <button type="submit">Отправить запрос</button>
</form>

<br /><hr /><br />
<p>Позвонить от оператора к клиенту</p>
<form method="post" action="http://trsuleymanov.megapbx.ru/sys/crm_api.wcgp">
    <input type="text" name="cmd" value="makeCall" /><br />
    <label>Куда (телефон клиента):</label><input type="text" name="phone" value="79661128006" /><br />
    <label>От кого(логин/внутр.номер/прямой тел-й номер оператора):</label><input type="text" name="user" value="vlad" /><br />
    <input type="text" name="token" value="6afb083a-2ec3-4e68-9a71-ef6828637c42" /><br />
    <button type="submit">Отправить запрос</button>
</form>

<br /><hr /><br />
<p>История</p>
<form method="post" action="http://trsuleymanov.megapbx.ru/sys/crm_api.wcgp">
    <input type="text" name="cmd" value="history" /><br />
    <input type="text" name="token" value="6afb083a-2ec3-4e68-9a71-ef6828637c42" /><br />

    <label>начало периода:</label><input type="text" name="start" value="" /><br />
    <label>окончание периода:</label><input type="text" name="end" value="" /><br />
    <label>период (если указан период, то начало-окончание периода не учитывается):</label>
    <select name="period">
        <option value="">---</option>
        <option value="today">today</option>
        <option value="yesterday">yesterday</option>
        <option value="this_week">this_week</option>
        <option value="last_week">last_week</option>
        <option value="this_month">this_month</option>
        <option value="last_month">last_month</option>
    </select>
    <br />

    <label>тип:</label>
    <select name="type">
        <option value="all">все</option>
        <option value="in">входящие</option>
        <option value="out">исходящие</option>
        <option value="missed">пропущенные</option>
    </select>
    <br />

    <label>limit:</label><input type="text" name="limit" value="100" /><br />

    <button type="submit">Отправить запрос</button>
</form>