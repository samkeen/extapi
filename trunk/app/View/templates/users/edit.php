<?php $payload->title = t('Edit User'); ?>
<?php $user = $payload->user; ?>
<h1>Edit User Account</h1>
<form action="/users/edit/<?php echo($user['user_id']); ?>" method="post" accept-charset="utf8">
	<p><label>Username: <input id="user-username" name="user[username]" type="text" value="<?php $this->form_get('username'); ?>" /></label></p>
	<p><label>Password: <input id="user-password" name="user[password]" type="password" value="<?php $this->form_get('password'); ?>" /></label></p>
	<p><label>Jabber Name (JID): <input id="user-xmpp_jid" name="user[xmpp_jid]" type="text" value="<?php $this->form_get('xmpp_jid'); ?>" /></label></p>
	<p><label>SMS Number: <input id="user-sms_number" name="user[sms_number]" type="text" value="<?php $this->form_get('sms_number'); ?>" /></label></p>
	<p><label>Age: <input id="user-age" name="user[age]" type="text" value="<?php $this->form_get('age'); ?>" /></label></p>
	<p><input type="submit" name="submit" value="submit" />
	<input type="hidden" name="user[user_id]" value="<?php echo($user['user_id']); ?>" />
	<input type="hidden" name="__method" value="post" />
	</p>
</form>