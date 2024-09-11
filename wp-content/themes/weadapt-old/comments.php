<?php
/**
 * The template for displaying comments
 *
 * @package    WordPress
 * @subpackage weadapt
 * @since      weadapt 1.0
 */

if ( ! function_exists( 'weadapt_list_comments_callback' ) ) :

	/**
	 * Callback function to use in wp_list_comments.
	 */
	function weadapt_list_comments_callback( $comment, $args, $depth ) {
		global $post;

		?>
			<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
				<div class="comment__author">

				<?php if ( 'pingback' !== $comment->comment_type ) : ?>
					<div class="comment__author__avatar"><?php echo get_avatar( $comment, 80 ); ?></div>
				<?php endif; ?>

					<div class="comment__author__data">
						<div class="comment__author__name">
							<?php
								$author_url = '';

								if ( 'pingback' === $comment->comment_type ) {
									$author_url = $comment->comment_author_url;
								}
								if ( $comment->user_id ) {
									$author_url = get_author_posts_url( $comment->user_id );
								}
								if ( $comment->comment_author_url ) {
									$author_url = $comment->comment_author_url;
								}

								if ( ! empty( $author_url ) ) {
									printf( '<a rel="external nofollow ugc" href="%s" target="_self">%s</a>',
										esc_url( $author_url ),
										esc_html( $comment->comment_author )
									);
								}
								else {
									echo esc_html( $comment->comment_author );
								}
							?>
						</div>

						<div class="comment__date"><?php
							// translators: %1$s is replaced with the comment date, %2$s is replaced with the comment time
							printf( esc_html__('%1$s at %2$s', 'weadapt' ), get_comment_date(), get_comment_time());
						?></div>
					</div>
				</div>
				<div class="comment__content">
					<?php
						if ( '0' === $comment->comment_approved ) {
							printf( '<p><em class="comment-awaiting-moderation">%s</em></p>', esc_html__( 'Your comment is awaiting moderation.', 'weadapt' ) );
						}

						comment_text();
					?>
				</div>
				<div class="comment__footer">
					<div class="comment__actions">
						<?php
							if ( is_user_logged_in() ) {
								$comment_ID    = get_comment_ID();
								$comment_likes = (int) get_comment_meta( $comment_ID, '_like_count', true );

								$class_name = 'comment__link comment__link--like';

								if ( isset( $_COOKIE['like_comment_' . $comment_ID] ) && $_COOKIE['like_comment_' . $comment_ID] === '1' ) {
									$class_name .= ' liked';
								}

								echo sprintf( '<button class="%s" aria-label="%s" data-id="%s">%s<span>%s</span></button>',
									$class_name,
									__( 'Like Comment', 'weadapt' ),
									$comment_ID,
									get_img( 'icon-thumb-up' ),
									sprintf( _n( '%s Like', '%s Likes', $comment_likes, 'weadapt' ), number_format_i18n( $comment_likes ) )
								);
							}

							if ( ! empty( $children = count( $comment->get_children() ) ) ) {
								echo sprintf( '<button class="comment__link comment__link--children" aria-label="%s">%s%s</button>',
									__( 'Show Replies', 'weadapt' ),
									get_img( 'icon-chat-20-light' ),
									sprintf( _n( '%s Reply', '%s Replies', $children, 'weadapt' ), number_format_i18n( $children ) )
								);
							}
						?>
					</div>
					<?php
						if ( is_user_logged_in() && 'pingback' !== $comment->comment_type ) {
							comment_reply_link( array(
								'reply_text'    => __( 'Reply', 'weadapt' ),
								'login_text'    => __( 'Login to Reply', 'weadapt' ),
								'reply_to_text' => __( 'Replying to %s', 'weadapt' ),
								'depth'      => $depth,
								'max_depth'  => $args['max_depth'],
								'before'     => '<div class="wp-block-comment-reply-link">',
								'after'      => '</div>'
							) );
						}
					?>
				</div>
		<?php
	}
endif;

?>
<div id="comments" class="wp-block-comments comments">
	<h2 class="wp-block-comments-title">
		<?php
			 $post_type = get_post_type();
			$comments_count = wp_count_comments( get_the_ID() );
			// translators: %s is replaced with the comments number
			if ( 'forum' === $post_type && ! empty( $post_forum_ID = get_field( 'forum' ) ) ) {
			printf( _n( '(%s) Replies', '(%s) Replies', $comments_count->approved, 'weadapt' ), number_format_i18n( $comments_count->approved ) );
			}else{
			printf( _n( '(%s) Comment', '(%s) Comments', $comments_count->approved, 'weadapt' ), number_format_i18n( $comments_count->approved ) );
			}
			
	?>
	</h2>

	<?php
		if ( comments_open() ) :
			if ( is_user_logged_in() ) :
				?><div class="comments__form"><?php
					$current_user = wp_get_current_user();

					ob_start();
					?>
						<div class="comments__form__author">
							<div class="comments__form__author__avatar"><?php echo get_avatar( $current_user->ID, 80 ); ?></div>
							<div class="comments__form__author__name"><?php echo $current_user->display_name; ?></div>
						</div>
						<p class="comment-form-comment">
  					  	<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" placeholder="<?php echo ( 'forum' === $post_type ) ? __( 'Write your reply here…', 'weadapt' ) : __( 'Write your comments here…', 'weadapt' ); ?>"></textarea>
						</p>

					<?php
					$comment_field = ob_get_clean();
					$comment_title = ( 'forum' === $post_type ) ? __( 'Write your reply here…', 'weadapt' ) : __( 'Write your comments here…', 'weadapt' );
					comment_form( array(
						'class_container'      => 'comment-respond wp-block-post-comments-form',
						'comment_field'        => $comment_field,
						'label_submit'         => __( 'Post', 'weadapt' ),
						'cancel_reply_link'    => get_img( 'icon-close' ),
						'title_reply'          => $comment_title,
						'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" data-post-text="' . __( 'Post', 'weadapt' ) . '" data-respond-text="' . __( 'Respond', 'weadapt' ) . '" />',
						'title_reply_to'       => '',
						'must_log_in'          => '',
						'logged_in_as'         => '',
						'comment_notes_before' => '',
						'comment_notes_after'  => ''
					) );
				?></div><?php
			else :
				if ( ! have_comments() ) :
					_e( 'There is no content', 'weadapt' );
				endif;
			endif;
		else :
			if ( ! have_comments() ) :
				_e( 'There is no content', 'weadapt' );
			endif;
		endif;
	?>

	<?php if ( have_comments() ): ?>
		<ol class="wp-block-comment-template">
			<?php
				wp_list_comments( array(
					'style'    => 'ol',
					'callback' => 'weadapt_list_comments_callback'
				) );
			?>
		</ol>
		<div class="wp-block-comments-pagination is-layout-flex">
			<div class="wp-block-comments-pagination-numbers">
				<?php paginate_comments_links(); ?>
			</div>
		</div>
	<?php endif; ?>
</div>