<?php return array(
	array(
		'type'       => 'wpeditor',
		'name'       => 'short_description',
		'label'      => __('Short Description', 'appic'),
		'description'=> __('Will be displayed in services shortcodes (Dynamic > Services > Carousel/List/Featured)', 'appic'),
		'validation' => 'required',
	),
	array(
		'type'       => 'toggle',
		'name'       => 'is_shown_read_more',
		'label'      => __('Do you want a See more link appended to the short description?', 'appic'),
		'default'    => '1',
	),
	array(
		'type'       => 'textbox',
		'name'       => 'custom_url',
		'label'      => __('Custom Service Url', 'appic'),
		'description'=> __('Fill in url address for page used as details page if you would like to use some other page instead of default one.', 'appic'),
		'validation' => 'url',
		'default'    => '',
	),
	array(
		'type'       => 'textbox',
		'name'       => 'read_more_text',
		'label'      => __('See More Link Text', 'appic'),
		'default'    => __('See More', 'appic'),
	),
	array(
		'type'       => 'fontawesome',
		'name'       => 'icon',
		'label'      => __('Service Icon', 'appic'),
		'validation' => 'required',
	),
	array(
		'type'       => 'upload',
		'name'       => 'icon_image',
		'label'      => __('Service Icon Image', 'appic'),
		'description'=> __('Upload a custom image instead of the default icon. Recommended size is 132x132 or 132x264 (image with a hover state).', 'appic'),
	),
	array(
		'type'       => 'toggle',
		'name'       => 'is_featured_service',
		'label'      => __('Featured', 'appic'),
	),
);
