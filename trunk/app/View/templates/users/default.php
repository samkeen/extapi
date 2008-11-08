<h1>Users</h1>
<table>
<tr>
<th>Id</th><th>Username</th><th>SMS Number</th><th>XMPP Jid</th><th>Active</th>
</tr>
<?php foreach ($payload->users as $user) {?>
<tr>
<td><?php h($user['user_id']); ?></td><td><?php h($user['username']); ?></td><td><?php h($user['sms_number']); ?></td>
<td><?php h($user['xmpp_jid']); ?></td><td><?php h($user['active']); ?></td>
</tr>
<?php } ?>
</table>
<a href="/users/add">Add User</a>