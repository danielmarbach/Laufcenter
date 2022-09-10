<?php 
/**
 * Template Name: Page with sidebar
 */
get_header();
?>

<div class="white-wrap container page-content">
	<?php if (have_posts()): ?>
	<div class="row">
		<div class="span8">
		<?php while(have_posts()) : the_post(); ?>
			<?php the_content(); ?>
		<?php endwhile; ?>
		</div>
		<aside class="span4">
			<div class="aside-wrap"><?php get_sidebar(); ?></div>
		</aside>
	</div>
	<?php else : ?>
		<?php get_template_part('404'); ?>
	<?php endif; ?>
</div>

<?php get_footer(); ?>