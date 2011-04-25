<rsp stat="ok">
    <method>director.api.gallery.get</method>
    <format>rest</format>
	<?php e($api->gallery($gallery, $albums, $this->data['preview'], $this->data['size'], $this->data['user_size'], $active, $controller, $users)); ?>
</rsp>