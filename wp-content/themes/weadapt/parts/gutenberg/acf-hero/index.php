<?php

/**
 * Hero Block
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */

$block_object = new Block($block);
$name = $block_object->name();

$image_alignment = get_field('image_alignment');
$search_bar = get_field('search_bar');
$text_size = get_field('title_size');
$image = $block_object->image('hero__image');
$custom_classes = isset($block['className']) ? $block['className'] : '';
$has_new_class = strpos($custom_classes, 'tandem_hero') !== false;

$attr_classes = '';
$attr_classes .= !empty($image) ? ' has-image' : '';
$attr_classes .= !empty($search_bar) ? ' has-search-bar' : '';
$attr_classes .= !empty($text_size) ? ' title-' . $text_size : ' title-small';

$attr = $block_object->attr($attr_classes);
?>

<section <?php echo $attr; ?>>
    <?php echo load_inline_styles(__DIR__, $name); ?>
    <?php load_inline_dependencies('/parts/gutenberg/core-heading/', 'core-heading'); ?>
    <?php load_inline_dependencies('/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
    <?php load_inline_dependencies('/parts/gutenberg/core-button/', 'core-button'); ?>

    <div class="container hero__container">
        <div class="hero__row row">
            <div class="col-12 hero__col <?php echo (!empty($image)) ? 'col-lg-6' : '' ?>">
                <?php echo $block_object->subtitle('hero__subtitle'); ?>
                <?php echo $block_object->title('hero__heading', 'h1'); ?>
                <?php echo $block_object->desc('hero__description'); ?>

                <?php if (!empty($search_bar)) : ?>
                    <?php
                    $args = [
                        'placeholder' => __('I would like to find out about...', 'weadapt')
                    ];
                    get_part('components/search-panel/index', $args);
                    ?>
                <?php endif; ?>

                <?php echo $block_object->button(); ?>
            </div>

            <?php if (!empty($image)) : ?>
                <div class="col-12 col-lg-6 hero__col alignment-<?php echo esc_attr($image_alignment); ?>">
                    <div class="image-wrapper" style="position: relative; display: inline-block;">
                        <?php echo $image; ?>

                        <?php if ($has_new_class) : ?>
                            <div id="scope-review" class="clickable-area" style="position: absolute; top: 26%; left: 50%; width: 100px; height: 100px;">
                                <span class="clickable-text" style="pointer-events: none;">Scope & review risks & vulnerability & impact</span>
                            </div>
                            <div id="integrate-knowledge" class="clickable-area" style="position: absolute; top: 51%; left: 23.5%; width: 100px; height: 100px;">
                                <span class="clickable-text" style="pointer-events: none;">Integrate new knowledge & partners</span>
                            </div>
                            <div id="co-design" class="clickable-area" style="position: absolute; top: 73%; left: 50%; width: 100px; height: 100px;">
                                <span class="clickable-text co-design" style="pointer-events: none;">Co-design</span>
                            </div>
                            <div id="co-explore" class="clickable-area" style="position: absolute; top: 51%; left: 77%; width: 100px; height: 100px;">
                                <span class="clickable-text" style="pointer-events: none;">Co-explore</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <script>
                        function initClickableAreas() {
                            d3.select('#scope-review').on('click', function() {
                                scrollToSection('.tandem-first-content-wrapper', function() {
                                    zoomToTarget('red');
                                });
                            });

                            d3.select('#integrate-knowledge').on('click', function() {
                                scrollToSection('.tandem-first-content-wrapper', function() {
                                    zoomToTarget('blue');
                                });
                            });

                            d3.select('#co-design').on('click', function() {
                                scrollToSection('.tandem-first-content-wrapper', function() {
                                    zoomToTarget('green');
                                });
                            });

                            d3.select('#co-explore').on('click', function() {
                                scrollToSection('.tandem-first-content-wrapper', function() {
                                    zoomToTarget('orange');
                                });
                            });
                        }

                        initClickableAreas();
                        document.querySelectorAll('.clickable-area').forEach(area => {
                            let top = area.getAttribute('data-top');
                            let left = area.getAttribute('data-left');

                            area.style.top = top;
                            area.style.left = left;
                        });
                    </script>
                </div><?php endif; ?>
        </div>
</section>