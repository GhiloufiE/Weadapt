<?php

$post_ID = ! empty( $args['post_ID'] ) ? $args['post_ID'] : 0;

if ( ! empty( $post_ID ) ) :
	$post_meta = [
		['icon-calendar', get_the_date( '', $post_ID )],
		['icon-clock', get_estimate_reading_time( get_post_field( 'post_content' ,$post_ID ) )],
	];

	if ( post_type_supports( get_post_type(), 'comments' ) ) {
		$comments_count = wp_count_comments( $post_ID );
		$post_meta[]    = ['icon-chat-20', sprintf( _n( '%s Comment', '%s Comments', $comments_count->approved, 'weadapt' ), number_format_i18n( $comments_count->approved ) )];
	}

	$contributors  = get_field( 'people_contributors', $post_ID ) ? get_field( 'people_contributors', $post_ID ) : [];
	$author_class  = 'cpt-list-item__author';

	if ( count( $contributors ) > 1 ) {
		$author_class .= ' cpt-list-item__author--multiple';
	}

	$status = get_post_status();
?>

<article class="cpt-list-item blog-list-item">
	<?php
		the_post_status_html( $status );
		the_post_thumbnail_html( $post_ID );
	?>

	<div class="cpt-list-item__content">
		<?php
			the_post_author_html( $contributors, $author_class, get_post_type() );
			the_post_title_html( $post_ID, $contributors );
			the_post_excerpt_html( $post_ID );
			the_post_meta_html( $post_meta );
			/* the_post_tag_html( $post_ID ); */
		?>
	</div>

	<?php the_post_edit_buttons_html( $post_ID, $status ); ?>
</article>

<?php endif; ?>
