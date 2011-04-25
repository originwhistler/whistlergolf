<rsp stat="ok">
    <method>director.api.content.list</method>
    <format>rest</format>
	<contents>
		<?php foreach($data as $image): ?>
			<?php e($api->image($image['Image'], $image['Album'], $this->data['size'], $this->data['user_size'], $active, $controller, $users)); ?>
		<?php endforeach; ?>
	</contents>
</rsp>