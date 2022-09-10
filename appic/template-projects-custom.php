<?php
/*
 * Template Name: Projects custom page
 */
get_header();
?>

<div class="white-wrap">
	<div class="container price-page-content">
	<?php while(have_posts()) : the_post(); ?>
		<?php echo the_content(); ?>
	<?php endwhile; ?>

	<?php
		$paged = get_query_var('paged');
		$pageSize = get_theme_option('project_per_page');

		$projectsQuery = new WP_Query(array(
			'post_type' => 'project',
			'posts_per_page' => $pageSize > 0 ? $pageSize : 10,
			'paged' => $paged > 0 ? $paged : 1,
		));

		$tempWpQuery = $wp_query;
		$wp_query = $projectsQuery;
	?>
<?php if ($projectsQuery && $projectsQuery->have_posts()): ?>
	<?php $layoutType = get_theme_option('portfolio_layout'); ?>
	<?php switch ($layoutType) {
	case '3':
		get_template_part('includes/templates/project/layout-3columns');
		break;

	case 'random':
		get_template_part('includes/templates/project/layout-random');
		break;

	default:
		get_template_part('includes/templates/project/layout-2columns');
		break;
	} ?>
<?php endif; ?>
<?php
$wp_query = $tempWpQuery;
?>
	</div><!-- end of .container -->
</div><!-- end of .white-wrap -->

<?php get_footer(); ?>