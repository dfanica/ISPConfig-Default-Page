<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="UTF-8"/>
		<meta name="viewport" content="initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0"/>
		<meta name="description" content=""/>
		<meta name="keywords" content=""/>
		<meta name="author" content="Daniel Fanica"/>
		<title>Daniel Fanica - 404 Not Found</title>
		<link rel="icon" href="favicon.ico"/>
		<link rel="stylesheet" href="resources/css/style.css"/>
		<link rel="stylesheet" href="resources/css/framework.css"/>
		<script type="text/javascript" src="resources/js/jquery.js"></script>
		<script type="text/javascript" src="resources/js/spin.js"></script>
		<script type="text/javascript" src="resources/js/plugins.js"></script>
		<script type="text/javascript" src="resources/js/analytics.js"></script>
	</head>
	<body>
		<div class="spinload"></div>
		<div id="gallery">
			<canvas class="animation"></canvas>
			<div class="wallpaper" style="background-image: url(resources/images/wallpaper.jpg); background-size: cover;"></div>
			<div class="content">
				<div class="wrap medium">
					<p class="error">Nothing to see here</p>
					<p class="info">The page you are looking for does not exist.</p>
				</div>
				<div class="wrap small">
					<div class="left size-6">
						<a id="back_button" class="button border fast" href="">Back</a>
					</div>
					<div class="left size-6">
						<a class="button border fast" href="http://<?php echo parse_url($_SERVER['HTTP_HOST'], PHP_URL_HOST); ?>">Root Page</a>
					</div>
					<div class="clear"></div>
				</div>
			</div>
			<script type="text/javascript">
				$(function () {
					$("#gallery").engine();
					$('a.back').click(function(){
						parent.history.back();
						return false;
					});
				});
			</script>
		</div>
		<div class="interface">
			<div id="custom">
				<div class="content">
					<div class="wrap large">
						<div class="left size-6">
							> Oops, ERROR 404 NOT FOUND... You may have mis-typed the URL or the page has been removed.
							<div class="clear"></div>
						</div>
						<div class="right size-6">
							Actually, there is nothing to see here... Click on the links above to do something, Thanks!
							<div class="clear"></div>
						</div>
						<div class="clear"></div>
					</div>
				</div>
				<script type="text/javascript"></script>
			</div>
			<div id="legal">
				<div class="content">
					<div class="wrap large">
						<p class="copyright"><?php echo date('Y'); ?> &copy; MyWebsiteCrew.Com - All rights reserved</p>
					</div>
				</div>
				<script type="text/javascript"></script>
			</div>
		</div>
	</body>
</html>
