<?php $payload->title = t('New Registration'); ?>
<h1>Register a new account</h1>
<form action="/register/add" method="post" accept-charset="utf8">
	<p><label>Username: <input id="user-username" name="user[username]" type="text" ></label></p>
	<p><label>Password: <input id="user-password" name="user[password]" type="password" ></label></p>
	<p><label>Jabber Name (JID): <input id="user-xmpp_jid" name="user[xmpp_jid]" type="text" ></label></p>
	<p><label>SMS Number: <input id="user-sms_number" name="user[sms_number]" type="text" ></label></p>
	<p><input type="submit" name="submit" value="submit" /><input type="hidden" name="__method" value="post" /></p>
</form>