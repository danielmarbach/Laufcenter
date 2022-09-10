<?php
function init_metaboxes()
{
	new VP_Metabox(array(
		'id'          => 'service_meta',
		'types'       => array('service'),
		'title'       => __('Additional Information', 'appic'),
		'priority'    => 'high',
		'is_dev_mode' => false,
		'template'    => PARENT_DIR . '/includes/metabox/service-meta.php'
	));
	
	new VP_Metabox(array(
			'id'          => 'service_call_action_meta',
			'types'       => array('service'),
			'title'       => __('Call to Action Block Settings', 'appic'),
			'priority'    => 'high',
			'is_dev_mode' => false,
			'template'    => PARENT_DIR . '/includes/metabox/service-call-action-meta.php'
	));

	new VP_Metabox(array(
		'id'          => 'project_meta',
		'types'       => array('project'),
		'title'       => __('Additional Information', 'appic'),
		'priority'    => 'high',
		'is_dev_mode' => false,
		'template'    => PARENT_DIR . '/includes/metabox/project-meta.php'
	));

	new VP_Metabox(array(
		'id'           => 'page_call_action_meta',
		'types'        => array('page'),
		'title'        => __('Call to Action Block Settings', 'appic'),
		'priority'     => 'high',
		'is_dev_mode'  => false,
		'template'     => PARENT_DIR . '/includes/metabox/page-call-action-meta.php',
	));
	
	new VP_Metabox(array(
		'id'           => 'team_meta',
		'types'        => array('team'),
		'priority'     => 'high',
		'is_dev_mode'  => false,
		'template'     => PARENT_DIR . '/includes/metabox/team-meta.php',
	));
}

// The safest hook to use, since Vafpress Framework may exists in Theme or Plugin
add_action('after_setup_theme', 'init_metaboxes');

// dependency function used for the logo management
function vp_dep_value_equal_image($value) {
	return $value == 'image';
}
VP_Security::instance()->whitelist_function('vp_dep_value_equal_image');

/**
 * Functions related on fonts selection with caching to speed up loading of the option pages
 */
function cached_vp_get_fonts_object()
{
	static $fonts;
	if ( ! $fonts ) {
		$fonts = file_get_contents(PARENT_DIR . '/framework/vafpress/data/gwf.json');
		$fonts = json_decode($fonts);
	}

	return $fonts;
}

function cached_vp_get_gwf_family()
{
	$fonts = cached_vp_get_fonts_object();
	$fonts = array_keys(get_object_vars($fonts));

	$fonts = apply_filters('theme-css-generator-get-font-families', $fonts);

	foreach ($fonts as $font) {
		$result[] = array('value' => $font, 'label' => $font);
	}

	return $result;
}
VP_Security::instance()->whitelist_function('cached_vp_get_gwf_family');

function cached_vp_get_gwf_weight($face)
{
	$result = array();
	$weights = array();
	if(!empty($face)) {
		$fonts = cached_vp_get_fonts_object();
		if( property_exists($fonts, $face) ) {
			$weights = $fonts->{$face}->weights;
		}
	}

	$weights = apply_filters('theme-css-generator-get-family-weights', $weights, $face);
	if ($weights) {
		foreach ($weights as $weight) {
			$result[] = array('value' => $weight, 'label' => $weight);
		}
	}

	return $result;
}
VP_Security::instance()->whitelist_function('cached_vp_get_gwf_weight');

function cached_vp_get_gwf_style($face)
{
	$result = array();
	$styles = array();
	if(!empty($face)) {
		$fonts = cached_vp_get_fonts_object();
		if( property_exists($fonts, $face) ) {
			$styles = $fonts->{$face}->styles;
		}
	}
	$styles = apply_filters('theme-css-generator-get-family-styles', $styles, $face);
	if ($styles) {
		foreach ($styles as $style) {
			$result[] = array('value' => $style, 'label' => $style);
		}
	}

	return $result;
}

VP_Security::instance()->whitelist_function('cached_vp_get_gwf_style');


function cached_vp_get_gwf_subset($face)
{
	if(empty($face)) {
		return array();
	}

	$fonts = cached_vp_get_fonts_object();
	if( !property_exists($fonts, $face) ) {
		return null;
	}
	$subsets = $fonts->{$face}->subsets;

	foreach ($subsets as $subset) {
		$result[] = array('value' => $subset, 'label' => $subset);
	}

	return $result;
}
VP_Security::instance()->whitelist_function('cached_vp_get_gwf_subset');


function dep_any_true() {
	$args = func_get_args();
	foreach ($args as $value) {
		if ($value) {
			return true;
		}
	}
	return false;
}

VP_Security::instance()->whitelist_function('dep_any_true');
// [end] Functions related on fonts selection with caching to speed up loading of the option pages
