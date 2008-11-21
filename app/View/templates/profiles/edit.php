<?php $payload->title = t('Edit Profile'); ?>
<?php $profile = $payload->profile; ?>
<h1>Edit Profile</h1>
<form action="/profiles/edit/<?php echo($profile['profile_id']); ?>" method="post" accept-charset="utf8">
	<p><label>Name: <input id="profile-name" name="profile[name]" type="text" value="<?php $this->form_get('name'); ?>" /></label></p>
	<p><label>Active: <input id="profile-active" name="profile[active]" type="text" value="<?php $this->form_get('active'); ?>" /></label></p>
	<p><input type="submit" name="submit" value="submit" />
	<input type="hidden" name="profile[profile_id]" value="<?php echo($profile['profile_id']); ?>" />
	<input type="hidden" name="__method" value="post" />
	</p>
</form>