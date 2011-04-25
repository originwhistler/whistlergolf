<rsp stat="ok">
    <method>director.api.content.get</method>
    <format>rest</format>
	<?php e($api->image($image['Image'], $image['Album'], $this->data['size'], $this->data['user_size'], $active, $controller, $users)); ?>
</rsp>