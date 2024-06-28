<?php
/**
 * Hero Block
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block( $block );
$name         = $block_object->name();

$image_alignment = get_field( 'image_alignment' );
$search_bar      = get_field( 'search_bar' );
$text_size       = get_field( 'title_size');
$image           = $block_object->image( 'hero__image' );
$attr_classes = '';
$attr_classes .= ! empty( $image ) ? ' has-image' : '';
$attr_classes .= ! empty( $search_bar) ? ' has-search-bar' : '';
$attr_classes .= ! empty( $text_size) ? ' title-' . $text_size : ' title-small';

$attr = $block_object->attr( $attr_classes );
?>
<style>

@media (min-width: 992px){

}
	.hero__image {
    max-width: max-content !important;
}

	.hero__image img{
    aspect-ratio: 4 / 4;
	object-fit: cover;}
	.hero, .hero__heading{
	overflow:hidden;
	background-color: #091968 !important;
    padding: 0 !important;
    color: white;
	}
	.hero-col-1{
	padding: 2rem;
    max-width: calc(1184px - 31%);
	align-self: center;
	}
	.hero__row {
    align-items: flex-start !important;
}
	.hero-col-2{
		text-align: -webkit-right; padding: 0; max-width: 40%;
	}
	@media (max-width: 967px) {
	.hero-svg {
		position : absolute; margin-top: -33px !important;fill: #fff;}}
	@media (min-width: 968px) {
	.hero-svg{
		position : absolute; margin-top: -100px !important; fill: #f4f4f4
	}}
	.alignment-right{
		text-align: -webkit-right;
	}

	
	@media only screen and (min-width: 2300px) {
	#page {
    margin-bottom: 250px !important;
    min-height: auto !important;
	}}

	.hero__container{
		margin-right: auto;
		margin-left: auto;
	}
</style>
<section <?php echo $attr; ?>>
	<?php echo load_inline_styles( __DIR__, $name ); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-heading/', 'core-heading'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
	<?php load_inline_dependencies( '/parts/gutenberg/core-button/', 'core-button'); ?>

	<div class=" hero__container">
		<div class=" row">
			<div class="hero-col-1 col-12 container <?php echo ( ! empty( $image ) ) ? 'col-lg-8' : '' ?>">
				<?php echo $block_object->subtitle( 'hero__subtitle', ); ?>
				<?php echo $block_object->title( 'hero__heading', 'h1' ); ?>
				<?php echo $block_object->desc( 'hero__description' ); ?>
				<?php
					if ( ! empty( $search_bar ) ) :
						$args = [
							'placeholder' => __( 'I would like to find out about...', 'weadapt' )
						];

						get_part('components/search-panel/index', $args);
					endif;
				?>
				<?php echo $block_object->button(); ?>
			</div>

			<?php if ( ! empty( $image ) ) : ?>
				<div  class="col-12 col-lg-4 hero__col alignment-<?php echo esc_attr( $image_alignment ); ?>" >
					<?php echo $image; ?>
				</div>
  </div>

</div>
			<?php endif; ?>
		</div>
		
		<svg class="hero-svg <?php echo esc_attr( $image_alignment ); ?> " viewBox="0 160 1440 160" preserveAspectRatio="none" class="" id="special_wave">
            <path d="M 0 177.778 C -0.427 177.524 507.134 139.697 712.604 195.779 C 918.074 251.861 984.272 260.415 1182.348 206.105 C 1298.957 174.132 1440 160 1440 160 L 1440 320 L 0 320 L 0 177.778 Z" style="stroke: none; fill: inherit"></path>
          </svg>
		</div>
</section>

