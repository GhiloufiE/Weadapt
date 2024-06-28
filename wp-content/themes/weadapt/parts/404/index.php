<?php
$content = get_field('page_404_content', 'options');

load_inline_styles( __DIR__, 'parts/404' );

if ( !empty($content) ) :
	echo $content;
else :
	$home_url = get_bloginfo('url');
?>
	<h1 style="text-align:center;"><?php _e( '404 - Page not found', 'weadapt' ); ?></h1>
	<p style="text-align:center;"><?php echo __( 'Take me back to ', 'weadapt' ) . "<a href='$home_url'>$home_url</a>"; ?></p>
<?php
endif;