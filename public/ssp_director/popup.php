<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title><?php echo(strip_tags($_GET['title'])); ?></title> 
		<style type="text/css" media="screen">
		/* <![CDATA[ */
			* { margin:0; padding:0; }
		/* ]]> */
		</style>
		
		<script type="text/javascript" charset="utf-8">
			function isIE() {
		  		return /msie/i.test(navigator.userAgent) && !/opera/i.test(navigator.userAgent);
			}
			
			function resize(img_w, img_h) {
				if (isIE()) {
					var h = document.documentElement.clientHeight;
				} else {
					var h = window.innerHeight;
				}
				var w = document.body.clientWidth;
				var adj_w = img_w - w; 
				var adj_h = img_h - h;
		       window.resizeBy(adj_w, adj_h);
			}
		</script>
	</head>
	
	<body>
		<?php
		
			if (strpos($_GET['src'], 'p.php?a=') !== false) {
				$src = $_GET['src'];
				$bits = explode('?a=', $src);
				$src = $bits[0] . '?a=' . urlencode($bits[1]);
			} else {
				$src = strip_tags($_GET['src']);
			}
		
		?>
    	<img onload="resize(this.width, this.height);" src="<?php echo($src); ?>" alt="<?php echo(strip_tags($_GET['title'])); ?>" />
	</body>
</html>