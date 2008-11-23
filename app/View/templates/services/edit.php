<?php $payload->title = t('Edit Service'); ?>
<?php $service = $payload->service; ?>
<h1>Edit Profile</h1>
<form action="/services/edit/<?php echo($service['service_id']); ?>" method="post" accept-charset="utf8">
	<p><label>Name: <input id="service-name" name="service[name]" type="text" value="<?php $this->form_get('name'); ?>" /></label></p>
	<p><label>API Key: <input id="service-api_key" name="service[api_key]" type="text" value="<?php $this->form_get('api_key'); ?>" /></label></p>
	<p><label>API URI: <input id="service-api_uri" name="service[api_uri]" type="text" value="<?php $this->form_get('api_uri'); ?>" /></label></p>
	<p><label>Active: <input id="service-active" name="service[active]" type="text" value="<?php $this->form_get('active'); ?>" /></label></p>
	<p><input type="submit" name="submit" value="submit" />
	<input type="hidden" name="service[service_id]" value="<?php echo($service['service_id']); ?>" />
	<input type="hidden" name="__method" value="post" />
	</p>
</form>