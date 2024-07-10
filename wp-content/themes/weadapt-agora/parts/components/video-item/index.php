<?php 
    $title       = ! empty( $args['title'] ) ? $args['title'] : '';
    $video_url   = ! empty( $args['video_url'] ) ? $args['video_url'] : '';
    $description = ! empty( $args['description'] ) ? $args['description'] : '';
?>

<?php if ( ! empty( $video_url ) ) : ?>

<div class="video-item">
	<?php load_inline_styles( __DIR__, 'video-item' ); ?>

	<div class="container">
		<div class="row">
			<div class="col-12 col-md-7 col--iframe">
				<div class="video-item__iframe"><?php echo $video_url; ?></div>
			</div>

			<div class="col-12 col-md-5 col--text">
				<?php if ( $title ) : ?>
					<h2 class="video-item__title"><?php echo $title; ?></h2>
				<?php endif; ?>

				<?php if ( $description ) : ?>
					<div class="video-item__description"><?php echo $description; ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>