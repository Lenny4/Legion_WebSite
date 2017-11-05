<!-- sidebar -->
<aside class="sidebar col-sm-2 col-sm-offset-0 col-xs-10 col-xs-offset-2 hidden-xs" role="complementary">

	<?php get_template_part('searchform'); ?>

	<div class="sidebar-widget">
		<?php if(!function_exists('dynamic_sidebar') || !dynamic_sidebar('widget-area-1')) ?>
	</div>

	<div class="sidebar-widget">
		<?php if(!function_exists('dynamic_sidebar') || !dynamic_sidebar('widget-area-2')) ?>
	</div>

</aside>

<div class="col-sm-1-offset"></div>
<!-- /sidebar -->
