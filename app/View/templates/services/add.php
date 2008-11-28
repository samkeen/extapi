<?php $payload->title = t('Add Service'); ?>
<h1>Add a new Service</h1>
<form action="/services/add?debug=1" method="post" accept-charset="utf8">
	<p><label>Name: <input id="service-name" name="service[name]" type="text" ></label></p>
	<p><label>API Key: <input id="service-api_key" name="service[api_key]" type="text" ></label></p>
	<p><label>API URI: <input id="service-api_uri" name="service[api_uri]" type="text" ></label></p>
	<p><label>Active: <input id="service-active" name="service[active]" type="text" ></label></p>
	<p><label><?php $form->renderSelectList('Service.profile_id',$payload->profiles); ?></label></p>
	<p><input type="submit" name="submit" value="submit" /><input type="hidden" name="__method" value="post" /></p>
</form>