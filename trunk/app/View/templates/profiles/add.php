<?php $payload->title = t('Add Profile'); ?>
<h1>Add a new Profile</h1>
<form action="/profiles/add?debug=1" method="post" accept-charset="utf8">
	<p><label>Name: <input id="profile-name" name="profile[name]" type="text" ></label></p>
	<p><label>Active: <input id="profile-active" name="profile[active]" type="text" ></label></p>
	<p><label><?php $form->renderSelectList('Profile.user_id',$payload->users); ?></label></p>
	<p><input type="submit" name="submit" value="submit" /><input type="hidden" name="__method" value="post" /></p>
</form>
