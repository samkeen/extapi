<?php $payload->title = t('New Channel'); ?>
<h1>Register a new Channel</h1>
<form action="/channels/add" method="post" accept-charset="utf8">
	<p><label>Name: <input id="channel-name" name="channel[name]" type="text" ></label></p>
	<p><label>Short Code: <input id="channel-shortcode" name="channel[shortcode]" type="text" ></label></p>
	<p><label>API Key: <input id="channel-api_key" name="channel[api_key]" type="text" ></label></p>
	<p><label>Keyword: <input id="channel-keyword" name="channel[keyword]" type="text" ></label></p>
	<p><label>Signature Key: <input id="channel-signature_key" name="channel[signature_key]" type="text" ></label></p>
	<p><label>API Domain: <input id="channel-api_domain" name="channel[api_domain]" type="text" ></label></p>
	<p><label>API Scheme: <input id="channel-api_scheme" name="channel[api_scheme]" type="text" ></label></p>
	<p><label>API Port: <input id="channel-api_port" name="channel[api_port]" type="text" ></label></p>
	<p><label>API Username: <input id="channel-api_username" name="channel[api_username]" type="text" ></label></p>
	<p><label>API Password: <input id="channel-api_password" name="channel[api_password]" type="text" ></label></p>
	<p><label><?php $form->renderSelectList('Channel.profile_id'); ?></label></p>
	<p><input type="submit" name="submit" value="submit" /><input type="hidden" name="__method" value="post" /></p>
</form>