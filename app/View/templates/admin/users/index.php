<?php $payload->title = "List of Users"; ?>
<h1>Users</h1>
<table>
<tr>
<th>Id</th><th>Username</th><th>SMS Number</th><th>XMPP Jid</th><th>Age</th><th>Active</th><th>action</th>
</tr>
<?php foreach ($payload->users as $user) {?>
<tr>
<td><?php h($user['user_id']); ?></td><td><?php h($user['username']); ?></td><td><?php h($user['sms_number']); ?></td>
<td><?php h($user['xmpp_jid']); ?></td>
<td><?php h($user['age']); ?></td>
<td><?php h($user['active']?'Y':'N'); ?></td>
<td><a href="/admin/users/view/<?php h($user['user_id']); ?>">view</a> :<a href="/admin/users/edit/<?php h($user['user_id']); ?>">edit</a> : <a href="/admin/users/delete/<?php h($user['user_id']); ?>">delete</a></td>
</tr>
<?php } ?>
</table>
<a href="/admin/users/add">Add User</a>