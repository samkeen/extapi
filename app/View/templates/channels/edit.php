<?php $payload->title = t('Edit Channel'); ?>
<?php $channel = $payload->channel; ?>
<h1>Edit a Channel</h1>
<form action="/channels/add" method="post" accept-charset="utf8">
	<p><label>Name: <input id="channel-name" name="channel[name]" type="text" value="<?php $this->form_get('name'); ?>" /></label></p>
	<p><label>Short Code: <input id="channel-shortcode" name="channel[shortcode]" type="text" value="<?php $this->form_get('shortcode'); ?>" /></label></p>
	<p><label>API Key: <input id="channel-api_key" name="channel[api_key]" type="text" value="<?php $this->form_get('api_key'); ?>" /></label></p>
	<p><label>Keyword: <input id="channel-keyword" name="channel[keyword]" type="text" value="<?php $this->form_get('keyword'); ?>" /></label></p>
	<p><label>Signature Key: <input id="channel-signature_key" name="channel[signature_key]" value="<?php $this->form_get('signature_key'); ?>" /></label></p>
	<p><label>API Domain: <input id="channel-api_domain" name="channel[api_domain]" type="text" value="<?php $this->form_get('api_domain'); ?>" /></label></p>
	<p><label>API Scheme: <input id="channel-api_scheme" name="channel[api_scheme]" type="text" value="<?php $this->form_get('api_scheme'); ?>" /></label></p>
	<p><label>API Port: <input id="channel-api_port" name="channel[api_port]" type="text" value="<?php $this->form_get('api_port'); ?>" /></label></p>
	<p><label>API Username: <input id="channel-api_username" name="channel[api_username]" type="text" value="<?php $this->form_get('api_username'); ?>" /></label></p>
	<p><label>API Password: <input id="channel-api_password" name="channel[api_password]" type="text" value="<?php $this->form_get('api_password'); ?>" /></label></p>
	<p><input type="submit" name="submit" value="submit" /><input type="hidden" name="__method" value="post" /></p>
	<input type="hidden" name="channel[channel_id]" value="<?php echo($channel['channel_id']); ?>" />
	<input type="hidden" name="__method" value="post" />
</form>