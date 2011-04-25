<rsp stat="ok">
    <method>director.api.gallery.list</method>
    <format>rest</format>
	<galleries>
	<?php foreach ($galleries as $gallery): ?>
		<?php e($api->gallery($gallery, @$gallery['Tag'], $this->data['preview'], $this->data['size'], $this->data['user_size'], $active, $controller, $users)); ?>
	<?php endforeach; ?>
	</galleries>
</rsp>