<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<table>
	<thead>
		<tr>
			<th>UserID</th>
			<th>Create Date</th>
			<th>Title</th>
			<th>Description</th>
			<th>Map</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($points_pending as $point): ?>
			<tr>
				<td><?php echo $point['createdbyid'] ?></td>
				<td><?php echo $point['createdate'] ?></td>
				<td><?php echo $point['title'] ?></td>
				<td><?php echo $point['description'] ?></td>
				<td>&nbsp;</td>
				<td><a href="<?= base_url('index.php/admin/approvepoints').'/'.$point['id'] ?>">Approve</a></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
