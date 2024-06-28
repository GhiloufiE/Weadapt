<?php
	$query_args = array(
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'post_type'      => get_allowed_post_types( [ 'article', 'blog', 'course', 'case-study', 'event', 'theme', 'network', 'solutions-portal' ] ),
		'orderby'        => 'date',
		'order'          => 'DESC',
		'meta_query'     => [ [
			'key'      => 'relevant_organizations',
			'value'    => sprintf( ':"%d";', get_the_ID() ),
			'compare'  => 'LIKE'
		] ],
		'ignore_sticky_posts' => false,
		'theme_query'         => true, // multisite fix
	);
?>
<?php
	$organisation_name = get_the_title();
	$section_heading = esc_html( $organisation_name, 'weadapt' ) . esc_html__( ' Contributed To...', 'weadapt' );
?>

<section class="single-organisation-content">
	<?php load_inline_styles( __DIR__, 'content' ); ?>
	<div class="container">
		<h2 class="section-title">
			<?php echo $section_heading; ?>
		</h2>
		<?php
			get_part( 'components/cpt-resources-query/index', [
				'query_args' => $query_args
			]);
		?>
	</div>
</section>