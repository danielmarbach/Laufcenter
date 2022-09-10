<?php
$blocks = array();

for ($i=1; $i<=3; $i++) {
	$sTitle = get_theme_option('home_ca_title_' . $i);
	$sBgImage = get_theme_option('home_ca_bg_' . $i);
	if (!$sTitle && !$sBgImage) {
		continue;
	}
	$btnUrl = get_theme_option('home_ca_url_' . $i);
	if ('#' == $btnUrl) {
		$btnUrl = '';
	}
	$blocks[] = array(
		'title' => $sTitle,
		'bgImage' => $sBgImage,
		'titleHover' => get_theme_option('home_ca_title_hover_' . $i),
		'bgImageHover' => get_theme_option('home_ca_bg_hover_' . $i),
		'description' => get_theme_option('home_ca_description_' . $i),
		'btnUrl' => $btnUrl,
	);
}

if (empty($blocks)) {
	return '';
}

$caMainTitle = get_theme_option('home_ca_title');
$caSubtitle = get_theme_option('home_ca_subtitle');
?>

<section class="action-area">
	<div class="pattern-wrap">
		<div class="lines-wrap horizontal-grey-lines">
			<div class="text-center">
			<?php if ($caMainTitle) { ?>
				<h2><?php echo $caMainTitle; ?></h2>
			<?php } ?>
			<?php if ($caSubtitle) { ?>
				<h3><?php echo $caSubtitle; ?></h3>
			<?php } ?>
				<ul class="ch-grid">
				<?php foreach ($blocks as $item) : ?>
					<?php 
						$bgStyleMain = $item['bgImage'] ? ' style="background-image:url('.$item['bgImage'].');"' : '';
						$bgStyleHover = $item['bgImageHover'] ? ' style="background-image:url('.$item['bgImageHover'].');"' : '';
						$itemTitle = $item['title'];
						$titleHover = $item['titleHover'];
						$description = $item['description'];
						$btnUrl = $item['btnUrl'];
					?>
					<li>
						<div class="ch-item">
							<div class="ch-info">
								<div class="ch-info-front"<?php echo $bgStyleMain; ?>>
								<?php if ($itemTitle) { ?>
									<h4><?php echo $itemTitle; ?></h4>
								<?php } ?>
								</div>
								<div class="ch-info-back"<?php echo $bgStyleHover; ?>>
								<?php if ($titleHover || $description || $btnUrl) { ?>
									<h4><?php echo $titleHover; ?></h4>
									<p class="hidden-phone"><?php echo $description ? $description : '&nbsp;'; ?></p>
									<?php if ($btnUrl) { ?>
										<a href="<?php echo esc_url($btnUrl); ?>"></a>
									<?php } ?>
								<?php } ?>
								</div>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</section>