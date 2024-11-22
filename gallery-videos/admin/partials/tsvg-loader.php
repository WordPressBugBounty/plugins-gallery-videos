<?php
echo sprintf(
	'
  	<section id="tsvg_loader" class="tsvg_flex_col">
  		<div id="tsvg_load_circle"></div>
  		<img src="%1$s" class="tsvg_load_img">
  	</section>
  	',
    esc_url( plugin_dir_url( __DIR__ ) . 'img/ts-video-gallery-logo.png' )
);