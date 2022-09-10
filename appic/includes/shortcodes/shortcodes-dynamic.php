<?php
// Services carousel
function services_carousel_shortcode($atts, $content=null)
{
	$output = '';

	$queryArguments = array(
		'post_type' => 'service',
		'posts_per_page' => -1,
		'orderby' => !empty($atts['order']) ? $atts['order'] : 'menu_order',
		'order' => !empty($atts['reverse_order']) && strtolower($atts['reverse_order']) != 'yes' ? 'ASC' : 'DESC',
	);
	$metaQuery = array();

	if (!isset($atts['show_details_link'])) {
		$atts['show_details_link'] = '1';
	}

	if (!empty($atts['translate'])) {
		$queryArguments = apply_filters( 'widget_posts_args', $queryArguments);
	}

	if (!empty($atts['top_level_only'])) {
		$queryArguments['post_parent'] = '0';
	}

	if (!empty($atts['show_featured']) && strtolower($atts['show_featured']) == 'yes') {
		$metaQuery[] = array(
			'key' => 'service_meta',
			'value' => '"is_featured_service";s:1:"1"',
			'compare' => 'LIKE'
		);
	}

	if ($metaQuery) {
		$queryArguments['meta_query'] = $metaQuery;
	}

	$query = new Wp_Query($queryArguments);

	if (!$query->have_posts()) {
		return $output;
	}
	$items = $query->get_posts();

	$allowShowDetailsLink = !empty($atts['show_details_link']);

	$iconsHtml = '';
	$descriptionsHtml = '';

	$itemOrder = 1;
	foreach ($items as $item) {
		$serviceMeta = get_post_meta($item->ID, 'service_meta', true);

		$itemTitle = esc_html($item->post_title);
		$showDetailsLink = $allowShowDetailsLink && !empty($serviceMeta['is_shown_read_more']);
		$itemLink = '';
		if ($showDetailsLink) {
			$itemLink = !empty($serviceMeta['custom_url']) ? esc_url($serviceMeta['custom_url']) : get_permalink($item->ID);
		}
		$iconImage = !empty($serviceMeta['icon_image']) ? $serviceMeta['icon_image'] : '';
		$iconClass = !$iconImage && isset($serviceMeta['icon']) ? ' ' . $serviceMeta['icon'] : '';
		$itemDescription = '';

		if (!empty($serviceMeta['short_description'])) {
			$itemDescription = apply_filters('the_content', $serviceMeta['short_description']);
			if ($showDetailsLink) {
				$itemDescription .= ' <a href="' . $itemLink . '" class="link-button">' . $serviceMeta['read_more_text'] . '<span class="link-arrow"></span></a>';
			}
		} elseif ($item->post_content) {
			$itemDescription = apply_filters('the_content', $item->post_content);
		}

		$isActiveImageClass = ($itemOrder == 1) ? ' bxslider-active' : '';

		$iconsHtml .= '<li class="text-center' . $isActiveImageClass .'" data-order="' . $itemOrder . '">' .
			($itemLink ? '<a href="' . $itemLink . '" ' : '<div ') . 'class="bxslider-li-wrap">' .
				($iconClass ? '<i class="fa' . $iconClass . '"></i>' : '') .
				($iconImage ? '<span class="fa-icon-image" style="background-image:url('.esc_url($iconImage).');"></span>' : '') .
				'<h3>' . $itemTitle . '</h3>' .
			($itemLink ? '</a>' : '</div>') .
		'</li>';

		$isActiveDescrClass = ($itemOrder == 1) ? ' class="description-active"' : '';
		$descriptionsHtml .= '<li' . $isActiveDescrClass .'>' .
			'<p class="simple-text-14">' . $itemDescription .'</p>' .
		'</li>';

		$itemOrder++;
	}
	wp_reset_postdata();

	wp_enqueue_script('bxslider');
	JsClientScript::addScript('init-services-shortcodes',
		'$(".shortcode_carousel_lists").bxSlider({pager: false, minSlides: 2, maxSlides: 4, slideWidth: 270, slideMargin: 30}); ' .
		'$(".shortcode_carousel_lists").children().on("mouseenter",function(e) {' .
			'$(".shortcode_carousel_lists").children().removeClass("bxslider-active");' .
			'$(".bxslider-description").children().removeClass("description-active");' .
			'var number = parseInt( $(this).addClass("bxslider-active").data("order"));' .
			'$(".bxslider-description").children().eq(--number).addClass("description-active");' .
		'});'
	);

	return '<div class="shortcode services-wrap horizontal-blue-lines stretch-over-container">' .
		'<section class="services services-carousel">' .
			'<ul class="clear-list bxslider shortcode_carousel_lists">' . $iconsHtml . '</ul>' .
			'<ul class="clear-list bxslider-description">' . $descriptionsHtml . '</ul>' .
		'</section>' .
	'</div>';
}
add_shortcode ('services_carousel', 'services_carousel_shortcode');

// Services list
function services_list_shortcode($atts, $content = null)
{
	extract(shortcode_atts(array(
		'number' => '5',
		'show_featured' => 'Yes',
		'show_details_link' => '1',
		'order' => 'menu_order',
		'reverse_order' => false,
	), $atts));

	$number = (int) $number;

	$queryArguments = array(
		'post_type' => 'service',
		'posts_per_page' => $number,
		'orderby' => $order ? $order : 'menu_order',
		'order' => strtolower($reverse_order) != 'yes' ? 'ASC' : 'DESC',
	);

	if(strtolower($show_featured) != 'yes') {
		$queryArguments['meta_query'] = array(
			array(
				'key' => 'service_meta',
				'value' => '"is_featured_service";s:1:"1"',
				'compare' => 'NOT LIKE'
			)
		);
	}

	if (!empty($atts['translate'])) {
		$queryArguments = apply_filters( 'widget_posts_args', $queryArguments);
	}

	if (!empty($atts['top_level_only'])) {
		$queryArguments['post_parent'] = '0';
	}

	$output = '';
	$query = new WP_Query($queryArguments);
	if( $query->have_posts() ) {
		$allowShowDetailsLink = !empty($show_details_link);

		$output = '<section class="shortcode available-services">' .
			'<ul class="clear-list service-list bxslider">';

		$align = 'right';
		while($query->have_posts()) {
			$query->the_post();
			$serviceMeta = get_post_meta(get_the_ID(), 'service_meta', true);
			$iconImage = !empty($serviceMeta['icon_image']) ? $serviceMeta['icon_image'] : '';
			$iconClass = !$iconImage && isset($serviceMeta['icon']) ? $serviceMeta['icon'] : '';
			$itemLink = '';
			if ($allowShowDetailsLink) {
				$itemLink = !empty($serviceMeta['custom_url']) ? esc_url($serviceMeta['custom_url']) : get_permalink($item->ID);
			}
			$itemDescription = '';

			if (!empty($serviceMeta['short_description'])) {
				$itemDescription = apply_filters('the_content', $serviceMeta['short_description']);
				if ($serviceMeta['is_shown_read_more'] && $itemLink) {
					$itemDescription .= ' <a href="' . $itemLink . '" class="link-button">' . $serviceMeta['read_more_text'] . '<span class="link-arrow"></span></a>';
				}
			} elseif ($itemContent = get_the_content()) {
				$itemDescription = apply_filters('the_content', $itemContent);
			}

			$align = $align == 'right' ? 'left' : 'right';
			$output .= '<li class="text-' . $align . '">' .
				'<div class="bxslider-active text-center">' .
					($itemLink ? '<a href="' . $itemLink . '" ' : '<div ') . 'class="bxslider-li-wrap">' .
						($iconClass ? '<span><i class="fa fa-4x '.$iconClass.'"></i></span>' : '') .
						($iconImage ? '<span class="fa-icon-image" style="background-image:url('.esc_url($iconImage).');"></span>' : '') .
						'<h3>' . get_the_title() . '</h3>' .
					($itemLink ? '</a>' : '</div>') .
				'</div>' .
				'<p class="simple-text-14">' . $itemDescription . '</p>' .
				'<div class="clearfix"></div>' .
			'</li>';
		}
		wp_reset_postdata();

		$output .= '</ul>';
		$output .= '</section>';
	}

	return $output;
}
add_shortcode('services_list', 'services_list_shortcode');

// Featured services
function featured_services_shortcode($atts, $content = null)
{
	extract(shortcode_atts(array(
		'title' => '',
		'columns' => '2',
		'number' => -1,
		'show_details_link' => '1',
		'order' => 'menu_order',
		'reverse_order' => false,
	), $atts));

	$number = (int) $number;
	$columns = (int) $columns;

	$output = '';

	$queryArguments = array(
		'post_type' => 'service',
		'posts_per_page' => !empty( $number ) ? $number : -1,
		'meta_query' => array(
			array(
				'key' => 'service_meta',
				'value' => '"is_featured_service";s:1:"1"',
				'compare' => 'LIKE'
			)
		),
		'orderby' => $order ? $order : 'menu_order',
		'order' => strtolower($reverse_order) != 'yes' ? 'ASC' : 'DESC',
	);

	if (!empty($atts['translate'])) {
		$queryArguments = apply_filters( 'widget_posts_args', $queryArguments);
	}

	$query = new WP_Query($queryArguments);
	if($query->have_posts()){
		if ($columns > 3 || $columns < 1) {
			$columns = $columns > 3 ? 3 : 1;
		}

		$services_array_html = array();
		$columnToSpanMap = array(
			'1' => '12',
			'2' => '6',
			'3' => '4'
		);
		$spanClass = 'span' . (isset($columnToSpanMap[$columns]) ? $columnToSpanMap[$columns] : 12);

		$allowShowDetailsLink = !empty($show_details_link);

		while($query->have_posts()){
			$query->the_post();
			$serviceMeta = get_post_meta(get_the_ID(), 'service_meta', true);
			$iconImage = !empty($serviceMeta['icon_image']) ? $serviceMeta['icon_image'] : '';
			$iconClass = !$iconImage && isset($serviceMeta['icon']) ? ' ' . $serviceMeta['icon'] : '';
			$itemLink = '';
			if ($allowShowDetailsLink) {
				$itemLink = !empty($serviceMeta['custom_url']) ? esc_url($serviceMeta['custom_url']) : get_permalink();
			}
			$itemDescription = '';

			if (!empty($serviceMeta['short_description'])) {
				$itemDescription = apply_filters('the_content', $serviceMeta['short_description']);
			} elseif ($itemContent = get_the_content()) {
				$itemDescription = apply_filters('the_content', $itemContent);
			}

			$services_array_html[] = '<div class="' . $spanClass . ' grey-block-position">' .
				'<div class="grey-block-wrap">' .
					($itemLink ? '<a href="' . $itemLink . '">' : '') .
						'<div class="grey-block service-grey-block">' .
							($iconClass ? '<span class="pull-left  position-icon"><i class="fa fa-4x' . $iconClass . '"></i></span>' : '') .
							($iconImage ? '<span class="pull-left  position-icon"><span class="fa-icon-image" style="background-image:url('.esc_url($iconImage).');"></span></span>' : '') .
							'<h4 class="font-style-24">' . get_the_title() . '</h4>' .
							'<p class="simple-text-12">' . $itemDescription . '</p>' .
							'<div class="clearfix"></div>' .
						'</div>' .
					($itemLink ? '</a>' : '') .
				'</div>' .
			'</div>';
		}
		wp_reset_postdata();

		$rows_html = array_chunk( $services_array_html, $columns );

		$service_html = '';
		foreach( $rows_html as $array_item_html ) {
			$service_html .= '<div class="row-fluid">' . join( '', $array_item_html ) . '</div>';
		}

		$title_html = !empty( $title ) ? '<h2 class="section-title">'.$title.'</h2>' : '';

		$output = '<section class="shortcode">' . $title_html . $service_html . '</section>';
	}

	return $output;
}
add_shortcode('featured_services', 'featured_services_shortcode');

// Testimonials shortcode
function testimonials_shortcode($atts, $content=null)
{
	$output = '';

	extract(shortcode_atts(array(
		'order' => '',
		'auto_play' => '0'
	), $atts));

	$queryArguments = array(
		'post_type' => 'testimonial',
		'posts_per_page' => -1,
	);

	if (!empty($atts['translate'])) {
		$queryArguments = apply_filters( 'widget_posts_args', $queryArguments);
	}

	$query = new WP_Query($queryArguments);
	if(!$query->have_posts()){
		return $output;
	}

	$items = $query->get_posts();

	if ('rand' == $order) {
		shuffle($items);
	}

	foreach($items as $item) {
		$title = $item->post_title;
		$desctiption = $item->post_content;
		$imageTag = '';
		if(has_post_thumbnail($item->ID)){
			$imageTag = get_the_post_thumbnail($item->ID, array(100,100));
			if(empty($imageTag)){
				$imageTag = '<img src="http://placehold.it/100x100">';
			}
		}

		if ($desctiption) {
			$desctiption = apply_filters('get_the_content', $desctiption);
		}
		$output .= '<li>' .
			'<div class="client-photo-wrap pull-left">' .
				'<figure class="client-photo">' . $imageTag .
					'<figcaption>' . $title . '</figcaption>' .
				'</figure>' .
			'</div>' .
			'<div class="simple-text-16 item-text-content"><em>' . $desctiption . '</em></div>' .
		'</li>';
	}

	wp_enqueue_script('bxslider');

	$jsConfig = array(
		'pager' => false,
		'minSlides'=>1,
		'maxSlides'=>1
	);

	if ($auto_play > 0) {
		$jsConfig['auto'] = true;
		$jsConfig['pause'] = $auto_play * 1000;
		$jsConfig['autoHover'] = true;
	}

	JsClientScript::addScript('init-testimonials-shortcodes',
		'$(".client-say-slider").bxSlider('.json_encode($jsConfig).');'
	);

	return '<section class="shortcode">' .
		'<ul class="clear-list client-say-slider">' . $output . '</ul>' .
	'</section>';
}
add_shortcode('testimonials', 'testimonials_shortcode');

// Team
function team_shortcode($atts, $content = null)
{
	$output = '';

	$atts = shortcode_atts(array(
		'move_slides' => '',
		'infinite_loop' => '1',
		'translate' => '',
	), $atts);

	$queryArguments = array(
		'post_type' => 'team',
		'posts_per_page' => -1,
	);

	if (!empty($atts['translate'])) {
		$queryArguments = apply_filters( 'widget_posts_args', $queryArguments);
	}

	$query = new WP_Query($queryArguments);
	if(!$query->have_posts()){
		return $output;
	}

	$items = $query->get_posts();

	wp_enqueue_script('bxslider');
	JsClientScript::addScript('init-team-shortcodes',
		'window.tx = $(".shortcode .appic-team").bxSlider({pager:false, ' .
			'infiniteLoop:' . ($atts['infinite_loop'] > 0 ? 'true' : 'false') . ', ' .
			'moveSlides:' . ($atts['move_slides'] > 0 ? $atts['move_slides'] : '0') . ', ' .
			'minSlides: 1, maxSlides: 4, ' .
			'slideWidth: 270, slideMargin: 30' .
		'});'
	);

	$output .= '<section class="shortcode container services">' .
			'<ul class="clear-list appic-team">';
	foreach ($items as $item) {
		$itemTitle = esc_html($item->post_title);
		$itemDescription = $item->post_content ? apply_filters('the_content', $item->post_content) : '';

		$thumbnail = '';
		if( has_post_thumbnail($item->ID) ){
			$thumbnail = get_the_post_thumbnail($item->ID);
			if(empty($thumbnail)){
				$thumbnail = '<img src="http://placehold.it/200x200">';
			}
		}
		$teamMeta = get_post_meta($item->ID, 'team_meta', true);
		$position = !empty($teamMeta['position']) ? $teamMeta['position'] : '<br>';

		$output.= '<li class="text-center">' .
			'<div class="author-post-photo-wrap">' . $thumbnail .
				'<div class="holder-author-photo"></div>' .
			'</div>' .
			'<div class="author-info border-triangle">' .
				'<h4 class="simple-text-16 bold"><a href="#" class="link">' . $itemTitle . '</a></h4>' .
				'<h5 class="simple-text-12 light-grey-text">' . $position . '</h5>' .
				'<div class="author-bio simple-text-12">' . $itemDescription . '</div>' .
				'<ul class="clear-list social clearfix">';
		foreach($teamMeta['social_sevices_tema_group'] as $itemMeta){
			$output .= '<li><a href="'.$itemMeta['service_url_team'].'" class="'.strtolower($itemMeta['service_name_team']).'-icon"></a></li>';
		}
		$output .= '</ul>'. 
			'</div>' .
		'</li>';
	}
	$output .= '</ul>' .
	'</section>';

	return $output;
}
add_shortcode('team', 'team_shortcode');

// Team grid
function team_grid_shortcode($atts, $content = null)
{
	$output = '';

	$atts = shortcode_atts(array(
		'translate' => '',
		'per_row' => 3
	), $atts);

	$queryArguments = array(
		'post_type' => 'team',
		'posts_per_page' => -1,
	);

	if (!empty($atts['translate'])) {
		$queryArguments = apply_filters( 'widget_posts_args', $queryArguments);
	}

	$query = new WP_Query($queryArguments);
	if(!$query->have_posts()){
		return $output;
	}

	$items = $query->get_posts();
	if ($atts['per_row'] < 2) {
		$atts['per_row'] = 2;
	} elseif ($atts['per_row'] > 4) {
		$atts['per_row'] = 4;
	}

	$rowItems = array_chunk($items, $atts['per_row']);
	$colClass = 'span' . (12 / $atts['per_row']);

	$output .= '<section class="shortcode container services appic-team appic-team--grid">';
	foreach ($rowItems as $rowSet) {
		$output .= '<div class="row">';
		foreach ($rowSet as $item) {
			$itemTitle = esc_html($item->post_title);
			$itemDescription = $item->post_content ? apply_filters('the_content', $item->post_content) : '';

			$thumbnail = '';
			if( has_post_thumbnail($item->ID) ){
				$thumbnail = get_the_post_thumbnail($item->ID);
				if(empty($thumbnail)){
					$thumbnail = '<img src="http://placehold.it/200x200">';
				}
			}
			$teamMeta = get_post_meta($item->ID, 'team_meta', true);
			$position = !empty($teamMeta['position']) ? $teamMeta['position'] : '<br>';

			$output.= '<div class="appic-team__grid-item ' . $colClass . '">' .
				'<div class="author-post-photo-wrap">' . $thumbnail .
					'<div class="holder-author-photo"></div>' .
				'</div>' .
				'<div class="author-info border-triangle">' .
					'<h4 class="simple-text-16 bold"><a href="#" class="link">' . $itemTitle . '</a></h4>' .
					'<h5 class="simple-text-12 light-grey-text">' . $position . '</h5>' .
					'<div class="author-bio simple-text-12">' . $itemDescription . '</div>' .
					'<ul class="clear-list social clearfix">';
			foreach($teamMeta['social_sevices_tema_group'] as $itemMeta){
				$output .= '<li><a href="'.$itemMeta['service_url_team'].'" class="'.strtolower($itemMeta['service_name_team']).'-icon"></a></li>';
			}
			$output .= '</ul>'. 
				'</div>' .
			'</div>';
		}
		$output .= '</div>'; // .row
	}
	$output .= '</section>';

	return $output;
}
add_shortcode('team_grid', 'team_grid_shortcode');


// Recent posts
function recent_posts_shortcode($atts, $content = null)
{
	extract(shortcode_atts(array(
		'type' => 'post',
		'number' => '5',
		'thumb_width' => '170',
		'thumb_height' => '170',
		'class' => 'posts',
		'title' => __('Latest', 'appic'),
		'subtitle' => __('posts', 'appic'),
		'show_content' => 'no',
	), $atts));

	$output = '';

	global $post;

	// apply_filters - has been added to implement functionality related on the WMPL
	// to add conditions that will filter posts in the same language as current page
	$postsQuery = new Wp_Query( apply_filters( 'widget_posts_args', array(
		'post_type' => $type,
		'posts_per_page' => $number,
		'orderby' => 'post_date',
		'order' => 'DESC'
	)));

	if (!$postsQuery->have_posts()) {
		return $output;
	}

	$output = '<section class="shortcode"><h2 class="article-title">' . $title . '<span>' . $subtitle . '</span></h2>';

	$isContentMode = 'yes' == strtolower($show_content) ? true : false;

	if ( ! $isContentMode ) {
		ThemeFlags::set( 'theme_excerpt_more_link_fixed_mode', '[just-arrow]' );
	} else {
		global $more;
		$oldMore = $more;
	}

	while($postsQuery->have_posts()) {
		$postsQuery->the_post();
		$postLink = get_permalink();
		$postTitle = get_the_title();

		if ( $isContentMode ) {
			$more = 0;
			$content = apply_filters( 'the_content', get_the_content( theme_excerpt_more_link('[just-arrow]') ) );
		} else {
			$content = get_the_excerpt();
		}

		$output .= '<article class="'.$class.'">';
		$styleLeftNot = '';
		if (has_post_thumbnail() ) {
			$thumb = get_the_post_thumbnail();
			
			if(empty($thumb)) {
				$thumbUrl = "http://placehold.it/".$thumb_width."x".$thumb_height;
			} else {
				$attachment_url = wp_get_attachment_url( get_post_thumbnail_id());
				$thumbUrl = aq_resize($attachment_url, $thumb_width, $thumb_height, true);
			}

			$output .= '<div class="image-wrap pull-left">' .
				'<img src="' . $thumbUrl . '" alt="' . $postTitle . '">' .
				'<a href="' . $postLink . '" title="' . $postTitle . '" class="mask"></a>' .
			'</div>';
		} else {
			$styleLeftNot = ' style="margin-left:0;"';
		}
		$output .= '<h3' . $styleLeftNot . '><a class="link" href="'.$postLink.'">'.$postTitle.'</a></h3>' .
			'<p' . $styleLeftNot . ' class="date light-grey-text">'. get_the_time( get_option( 'date_format' ) ) .'</p>' .
			'<div'.$styleLeftNot . ' class="light-grey-text">' . $content .
			'</div>' .
			'<div class="clearfix"></div>' .
		'</article>';
	}

	if (!$isContentMode) {
		ThemeFlags::set('theme_excerpt_more_link_fixed_mode', false);
	} else {
		$more = $oldMore;
	}

	$output .= '</section>';

	wp_reset_postdata();

	return $output;
}
add_shortcode('recent_posts', 'recent_posts_shortcode');

// Recent projects
function recent_projects_shortcode($atts, $content = null)
{
	$output = '';

	extract(shortcode_atts(array(
		'number' => -1,
		'title' => __('Recent','appic'),
		'subtitle' => __('Projects','appic'),
	), $atts));

	$queryArguments = array(
		'post_type' => 'project',
		'posts_per_page' => $number,
		//to get posts with featured image only
		'meta_query' => array(
			array('key' => '_thumbnail_id'),
		),
		'orderby' => 'ID',
		'order' => 'DESC'
	);
	if (!empty($atts['translate'])) {
		$queryArguments = apply_filters( 'widget_posts_args', $queryArguments);
	}

	$projects = new WP_Query($queryArguments);
	if (!$projects->have_posts()) {
		return $output;
	}

	$elements = array();
	while($projects->have_posts()) {
		$projects->the_post();
		$itemTitle = get_the_title();
		$thumbUrl = wp_get_attachment_url(get_post_thumbnail_id(), 'full');
		if( empty($thumbUrl) ){
			$thumbUrl = "http://placehold.it/270x160";
		}else{
			$thumbUrl = aq_resize($thumbUrl, 270, 160, true);
		}

		$elements[] = '<li>' .
				'<div class="view hover-effect-image">' .
				'<img src="' . $thumbUrl . '" alt="'. $itemTitle .'" />' .
				'<a href="' . get_permalink() .'" class="mask-no-border"><span class="mask-icon" title="' . $itemTitle . '">' . $itemTitle . '</span></a>' .
			'</div>' .
		'</li>';
	};
	wp_reset_postdata();

	$curIndex = 0;
	while (count($elements) < 4) {
		$elements[] = $elements[$curIndex];
		$curIndex++;
	}

	$output .= '<div class="shortcode similar-projects-wrap horizontal-blue-lines stretch-over-container">' .
		'<section class="container similar-projects grey-lines">' .
			'<h2 class="article-title">' . $title . '<span>' . $subtitle . '</span></h2>' .
			'<ul class="bxslider recent-projects">' .
				join('', $elements) .
			'</ul>' .
		'</section>' .
	'</div>';

	wp_enqueue_script('bxslider');
	JsClientScript::addScript('initSimilarProjects',
		'$(".recent-projects").bxSlider({pager:false,minSlides:1,maxSlides:4,slideWidth:270,slideMargin:30});'
	);

	return $output;
}
add_shortcode('recent_projects', 'recent_projects_shortcode');
