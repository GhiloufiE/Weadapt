<?php

/**
 * Tandem Circle Block
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
if (!function_exists('render_content')) {
    function render_content($field_name, $is_svg = false)
    {
        global $section_fields;

        if ($is_svg) {
            render_svg_with_text($section_fields[$field_name]);
        } else {
            echo $section_fields[$field_name];
        }
    }
}
if (!function_exists('enforce_character_limit')) {
    function enforce_character_limit($html, $char_limit)
    {
        if (empty($html)) {
            return ''; // If the HTML is empty, return an empty string.
        }

        $total_characters = 0;
        $output = '';
        $dom = new DOMDocument;
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new DOMXPath($dom);

        foreach ($xpath->query('//text()') as $text) {
            $text_content = $text->textContent;
            $remaining_characters = $char_limit - $total_characters;

            if ($remaining_characters > 0) {
                if (strlen($text_content) <= $remaining_characters) {
                    $total_characters += strlen($text_content);
                } else {
                    $text->textContent = substr($text_content, 0, $remaining_characters) . '...';
                    $total_characters = $char_limit;
                }
            } else {
                $text->textContent = '';
            }
        }

        $output = $dom->saveHTML();
        return $output;
    }
}

if (!function_exists('get_sanitized_field')) {
    function get_sanitized_field($field_name, $allowed_tags = '<strong><em><b><i><br>', $char_limit = null)
    {
        $field_value = get_field($field_name);

        if ($field_value === null) {
            return ''; // Return an empty string if the field value is null.
        }

        $sanitized_value = strip_tags($field_value, $allowed_tags);
        
        if ($char_limit && strlen($sanitized_value) > $char_limit) {
            $sanitized_value = substr($sanitized_value, 0, $char_limit) . '...';
        }

        return $sanitized_value;
    }
}

if (!function_exists('get_sanitized_field_with_limit')) {
    function get_sanitized_field_with_limit($field_name, $char_limit = null, $allowed_tags = '<strong><em><b><i><br>')
    {
        $field_value = get_field($field_name);

        if ($field_value === null || empty($field_value)) {
            return ''; // If the field value is null or empty, return an empty string.
        }

        if ($char_limit) {
            return enforce_character_limit($field_value, $char_limit);
        } else {
            return strip_tags($field_value, $allowed_tags);
        }
    }
}

if (!function_exists('get_section_fields')) {
    function get_section_fields()
    {
        $svg_fields = [
            'red_title' => get_sanitized_field('red_circle_title', '<strong><em><b><i><br>'),
            'green_title' => get_sanitized_field('green_circle_title', '<strong><em><b><i><br>'),
            'orange_title' => get_sanitized_field('orange_circle_title', '<strong><em><b><i><br>'),
            'blue_title' => get_sanitized_field('blue_circle_title', '<strong><em><b><i><br>'),
        ];
        $limited_text_fields = [
            'red_top_title' => get_sanitized_field_with_limit('red_top_title', 40),
            'red_left_title' => get_sanitized_field_with_limit('red_left_title', 40),
            'red_right_title' => get_sanitized_field_with_limit('red_right_title', 40),
            'orange_top_title' => get_sanitized_field_with_limit('orange_top_title', 40),
            'orange_left_title' => get_sanitized_field_with_limit('orange_left_title', 40),
            'orange_right_title' => get_sanitized_field_with_limit('orange_right_title', 40),
            'green_top_title' => get_sanitized_field_with_limit('green_top_title', 40),
            'green_left_title' => get_sanitized_field_with_limit('green_left_title', 40),
            'green_right_title' => get_sanitized_field_with_limit('green_right_title', 40),
            'blue_top_title' => get_sanitized_field_with_limit('blue_top_title', 80),
            'blue_left_title' => get_sanitized_field_with_limit('blue_left_title', 40),
            'blue_right_title' => get_sanitized_field_with_limit('blue_right_title', 40),
            'middle_rectangle' => get_sanitized_field_with_limit('middle_rectangle', 200),
        ];

        return array_merge($svg_fields, $limited_text_fields);
    }
}

if (!function_exists('render_svg_with_text')) {
	function render_svg_with_text($dynamicText)
	{
		$svgFile = get_theme_file_path('/assets/images/svg/text-above.svg');
		$svgContent = file_get_contents($svgFile);
		$svgContent = str_replace('Your text here', $dynamicText, $svgContent);
		echo $svgContent;
	}
}

$block_object = new Block($block);
$name = $block_object->name();
$attr = $block_object->attr('has-image');
$section_fields = get_section_fields();

?>
<?php echo load_inline_styles(__DIR__, $name); ?>
<?php load_inline_dependencies('/parts/gutenberg/core-heading/', 'core-heading'); ?>
<?php load_inline_dependencies('/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
<?php load_inline_dependencies('/parts/gutenberg/core-button/', 'core-button'); ?>
<link rel="stylesheet" src="<?php echo get_template_directory_uri(); ?>/parts/gutenberg/acf-tandem-circle/style.css" />
<section <?php echo $attr; ?>>
	<?php load_inline_dependencies('/parts/gutenberg/core-heading/', 'core-heading'); ?>
<style>
.rectangle-content{
	display:none; 
}
.blue-content{
	display:none; 
}
.green-content{
	display:none; 
}
.orange-content{
	display:none; 
}
.red-content{
	display:block; 
}
.rectangle-cards{
	display:none; 
}
.blue-cards{
	display:none; 
}
.green-cards{
	display:none; 
}
.orange-cards{
	display:none; 
}
.red-cards{
	display:block; 
}
</style>
	<div class="tandem-container">
		<div class="tandem-full-content">
			<div class="tandem-first-content-wrapper">
				<?php echo $block_object->title('tandem-header'); ?>
				<div class="tandem-first-content">

					<div class="tandem-inside-content">
						<img class="first-grand-arrow"
							src="<?php echo get_theme_file_uri('/assets/images/svg/prime-arrow.svg'); ?>" />
						<img class="second-grand-arrow"
							src="<?php echo get_theme_file_uri('/assets/images/svg/second-arrow.svg'); ?>" />
						<img class="third-grand-arrow"
							src="<?php echo get_theme_file_uri('/assets/images/svg/third-arrow.svg'); ?>" />
						<img class="fourth-grand-arrow"
							src="<?php echo get_theme_file_uri('/assets/images/svg/fourth-arrow.svg'); ?>" />


						<div class="tandem-first-circle">
							<?php render_svg_with_text($section_fields['red_title']); ?>
							<div class="tandem-first-in-cercle">
								<div class="tandem-inside-red-circle">
									<a href="/tandem/vulnerability-scope-risks-impacts/">
									<p><?php echo $section_fields['red_top_title']; ?></p>
									</a>
								</div>
								<img class="red-two-arrow" src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-1.svg'); ?>" />
								<img class="red-two-arrow2" src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-2.svg'); ?>" />
								<img class="red-two-arrow3" src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-3.svg'); ?>" />

								<div class="tandem-inside-red-two-c">
									<div class="tandem-inside-red-circle">
										<a href="/tandem/stakeholders-engage/">
										
											<p><?php echo $section_fields['red_left_title']; ?></p>
										</a>
									</div>
									<div class="tandem-inside-red-circle">
										<a href="/tandem/stakeholders-identify/">
										<p><?php echo $section_fields['red_right_title']; ?></p>
										</a>
									</div>
								</div>
							</div>
						</div>

						<div class="tandem-middle-content">
							<div class="tandem-second-circle">
								<?php render_svg_with_text($section_fields['blue_title']); ?>
								<div class="tandem-second-in-cercle">
									<div class="tandem-inside-red-circle">
										<a href="/tandem/co-explore-and-understand-context">
										<?php render_svg_with_text($section_fields['blue_top_title']); ?>
										</a>
									</div>
									<img class="red-two-arrow"
										src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-1.svg'); ?>" />
									<img class="red-two-arrow2"
										src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-2.svg'); ?>" />
									<img class="red-two-arrow3"
										src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-3.svg'); ?>" />
									<div class="tandem-inside-red-two-c">
										<div class="tandem-inside-red-circle">
										<?php render_svg_with_text($section_fields['blue_left_title']); ?>
										</div>
										<div class="tandem-inside-red-circle">
										<?php render_svg_with_text($section_fields['blue_right_title']); ?>
										</div>
									</div>
								</div>
							</div>

							<img class="rectangle-arrow"
								src="<?php echo get_theme_file_uri('/assets/images/svg/arrow-top.svg'); ?>" />
							<img class="rectangle-arrow-left"
								src="<?php echo get_theme_file_uri('/assets/images/svg/arrow-left.svg'); ?>" />
							<div class="tandem-middle-content-rectangle">
								<div class="tandem-middle-content-rectangle-widh">
    <?php echo $section_fields['middle_rectangle']; ?>
</div>
							</div>
							<img class="rectangle-arrow-bottom"
								src="<?php echo get_theme_file_uri('/assets/images/svg/arrow-bottom.svg'); ?>" />
							<img class="rectangle-arrow-right"
								src="<?php echo get_theme_file_uri('/assets/images/svg/arrow-right.svg'); ?>" />
							<div class="tandem-third-circle">
								<?php render_svg_with_text($section_fields['orange_title']); ?>
								<div class="tandem-third-in-cercle">
									<div class="tandem-inside-red-circle">
									<?php render_svg_with_text($section_fields['orange_top_title']); ?>
									</div>
									<img class="red-two-arrow"
										src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-1.svg'); ?>" />
									<img class="red-two-arrow2"
										src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-2.svg'); ?>" />
									<img class="red-two-arrow3"
										src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-3.svg'); ?>" />
									<div class="tandem-inside-red-two-c">
										<div class="tandem-inside-red-circle">
											<?php render_svg_with_text($section_fields['orange_left_title']); ?>
										</div>
										<div class="tandem-inside-red-circle">
										<?php render_svg_with_text($section_fields['orange_right_title']); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="tandem-fourth-circle">
							<?php render_svg_with_text($section_fields['green_title']); ?>
							<div class="tandem-fourth-in-cercle">

								<div class="tandem-inside-red-circle">
								<?php render_svg_with_text($section_fields['green_top_title']); ?>
									
								</div>
								<img class="red-two-arrow"
									src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-1.svg'); ?>" />
								<img class="red-two-arrow2"
									src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-2.svg'); ?>" />
								<img class="red-two-arrow3"
									src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-3.svg'); ?>" />
								<div class="tandem-inside-red-two-c">
									<div class="tandem-inside-red-circle">
									<?php render_svg_with_text($section_fields['green_left_title']); ?>							
									</div>
									<div class="tandem-inside-red-circle">
									<?php render_svg_with_text($section_fields['green_right_title']); ?>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
				<div class="color-indicator"></div>
				<div class="tandem-container-nav-zoom">
					<button>
						<p style="margin-bottom: 0rem;">Zoom out</p>
						<img class="zoom-icon"
							src="<?php echo get_theme_file_uri('/assets/images/svg/zoom-first-nav.svg'); ?>" />
					</button>
				</div>
			</div>

		</div>
		<div class="arrow-left">
			<img src="<?php echo get_theme_file_uri('/assets/images/svg/left-arrow.svg'); ?>" alt="Left Arrow" />
		</div>

		<!-- Right Arrow -->
		<div class="arrow-right">
			<img src="<?php echo get_theme_file_uri('/assets/images/svg/right-arrow.svg'); ?>" alt="Right Arrow" />
		</div>
		<div class="tandem-nav">
			<button class="tandem-nav1" data-target="red">
				<img class="svg-nav" src="<?php echo get_theme_file_uri('/assets/images/svg/red-nav.svg'); ?>" />
			</button>
			<button class="tandem-nav2 " data-target="orange">
				<img class="svg-nav" src="<?php echo get_theme_file_uri('/assets/images/svg/orange-nav.svg'); ?>" />
			</button>
			<button class="tandem-nav3 " data-target="green">
				<img class="svg-nav" src="<?php echo get_theme_file_uri('/assets/images/svg/green-nav.svg'); ?>" />
			</button>
			<button class="tandem-nav4 " data-target="blue">
				<img class="svg-nav" src="<?php echo get_theme_file_uri('/assets/images/svg/blue-nav.svg'); ?>" />
			</button>
			<button class="tandem-nav5" data-target="rectangle">
				<img class="svg-nav" src="<?php echo get_theme_file_uri('/assets/images/svg/gray-nav.svg'); ?>" />

			</button>
		</div>
	</div>
</section>
 
<script src="https://d3js.org/d3.v7.min.js"></script>
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

function scrollToSection(targetSelector, callback) {
    const targetElement = document.querySelector(targetSelector);
    
    if (targetElement) {
        targetElement.scrollIntoView({
            behavior: 'smooth',
            block: 'start', 
        });


        setTimeout(() => {
            if (typeof callback === 'function') {
                callback();
            }
        }, 250); 
    } else {
        console.error('Target section not found:', targetSelector);
    }
}

	window.onload = function() {
		if (document.querySelector('.inner-circle')) {
			var footer = document.querySelector('.main-footer');
			if (footer) {
				footer.style.paddingTop = '3rem';
			}
		}
	};
	var zoomTargets = {
		red: {
			element: '.tandem-first-circle',
			scale: 3
		},
		orange: {
			element: '.tandem-third-circle',
			scale: 3
		},
		green: {
			element: '.tandem-fourth-circle',
			scale: 3
		},
		blue: {
			element: '.tandem-second-circle',
			scale: 3
		},
		rectangle: {
			element: '.tandem-middle-content-rectangle',
			scale: 3
		}
	};
	var themeUri = '<?php echo get_theme_file_uri('/assets/images/svg/'); ?>';
	var svgMappings = {
		red: themeUri + 'red-selected.svg',
		orange: themeUri + 'orange-selected.svg',
		green: themeUri + 'green-selected.svg',
		blue: themeUri + 'blue-selected.svg',
		rectangle: themeUri + 'gray-selected.svg'
	};


	function getTargetElement(targetClass) {
		var target = zoomTargets[targetClass];
		if (!target) {
			return null;
		}
		return d3.select(target.element).node();
	}

	function calculateTransform(targetClass) {
		var targetElement = getTargetElement(targetClass);
		if (!targetElement) return null;

		var containerElement = d3.select('.tandem-first-content').node();

		var targetRect = targetElement.getBoundingClientRect();
		var containerRect = containerElement.getBoundingClientRect();

		var targetCenterX = targetRect.left + targetRect.width / 2;
		var targetCenterY = targetRect.top + targetRect.height / 2;

		var containerCenterX = containerRect.left + containerRect.width / 2;
		var containerCenterY = containerRect.top + containerRect.height / 2;

		var translateX = containerCenterX - targetCenterX;
		var translateY = containerCenterY - targetCenterY;

		return {
			translateX,
			translateY,
			scale: zoomTargets[targetClass].scale
		};
	}

	function toggleLinks(enable) {
		d3.selectAll('.tandem-inside-red-circle a').attr('tabindex', enable ? 0 : -1);
		d3.selectAll('.tandem-inside-red-circle a').style('pointer-events', enable ? 'auto' : 'none');
	}

	function applyTransform(transform, duration, targetClass) {
		if (transform) {
			d3.select('.tandem-first-content')
				.transition()
				.duration(duration)
				.style('transform', `translate(${transform.translateX}px, ${transform.translateY}px) scale(${transform.scale})`)
				.on('end', function() {
					d3.select(this).style('transform', `translate(${transform.translateX}px, ${transform.translateY}px) scale(${transform.scale})`);
					// Enable links only if the zoom scale is equal to the target scale
					toggleLinks(transform.scale === zoomTargets[targetClass].scale);
				});
		}
	}


	function addGradientOverlay(targetClass, transform, duration) {
		if (transform) {
			var targetElement = getTargetElement(targetClass);
			if (!targetElement) return;

			var targetRect = targetElement.getBoundingClientRect();
			var containerRect = d3.select('.tandem-first-content').node().getBoundingClientRect();

			// Calculate the relative position of the target center within the container
			var targetCenterX = targetRect.left + targetRect.width / 2 - containerRect.left;
			var targetCenterY = targetRect.top + targetRect.height / 2 - containerRect.top;

			var targetPercentX = (targetCenterX / containerRect.width) * 100;
			var targetPercentY = (targetCenterY / containerRect.height) * 100;

			// Create and apply the gradient overlay, with a class specific to the targetClass
			d3.select('.tandem-first-content')
				.append('div')
				.attr('class', `gradient-overlay ${targetClass}`)
				.style('position', 'absolute')
				.style('top', `0%`)
				.style('left', `0%`)
				.style('width', `100%`)
				.style('height', `100%`)
				.style('background', `radial-gradient(circle at ${targetPercentX}% ${targetPercentY}%, rgba(255, 255, 255, 0) 10%, rgba(255, 255, 255, 1) 40%)`)
				.style('pointer-events', 'none')
				.style('transition', `opacity ${duration}ms`)
				.style('opacity', 0)
				.transition()
				.style('opacity', 1);
		}
	}


	let zoomOrder = ['red', 'orange', 'green', 'blue', 'rectangle']; // Define the order of targets
	let currentTargetIndex = -1; // Initialize the current target index

	window.onload = function() {
  
    initZoomHandlers();
	initClickableAreas(); // Add this line
};

function zoomToTarget(targetClass) {
    // Check if the gradient overlay already exists for the current target
    if (d3.select('.tandem-first-content').select(`.gradient-overlay.${targetClass}`).node()) {
        return; // If it exists, don't reapply the gradient
    }
  // Hide all target sections first
 	 d3.selectAll('.red-content, .orange-content, .green-content, .blue-content, .rectangle-content').style('display', 'none');
 	 d3.selectAll('.red-cards, .orange-cards, .green-cards, .blue-cards, .rectangle-cards').style('display', 'none');

    // Update the current target index
    currentTargetIndex = zoomOrder.indexOf(targetClass);
    if (currentTargetIndex === -1) return; // Invalid target class

    // Show the navigation arrows
    d3.select('.arrow-left').style('display', 'block');
    d3.select('.arrow-right').style('display', 'block');

    // Clear any previous overlays
    d3.select('.tandem-first-content')
        .select('.gradient-overlay')
        .transition()
        .duration(0)
        .style('opacity', 0)
        .on('end', function() {
            d3.select(this).remove();
        });

    var lastTransform = calculateTransform(targetClass);
    if (!lastTransform) return;

    applyTransform(lastTransform, 100, targetClass); // Use a longer transition for smoothness

    function handleMutation() {
        lastTransform = calculateTransform(targetClass);
        if (lastTransform) {
            applyTransform(lastTransform, 50, targetClass); // Reapply with a smooth transition
        }
    }

    setTimeout(function() {
        addGradientOverlay(targetClass, lastTransform, 0);
    }, 0);

    var observer = new MutationObserver(handleMutation);
    observer.observe(d3.select('.tandem-first-content').node(), {
        attributes: true,
        childList: true,
        subtree: true
    });
    setTimeout(function() {
        observer.disconnect();
    }, 200);

    // Show the zoom out button when zoomed in
    d3.select('.tandem-container-nav-zoom button').style('display', 'flex');

    // Update the color indicator based on the targetClass
    var colors = {
        red: '#B94343',
        orange: '#F6B552',
        green: '#679F5A',
        blue: '#7BC9CC',
        rectangle: '#C5E4FF'
    };

    d3.select('.color-indicator')
        .transition()
        .duration(300)
        .style('background-color', colors[targetClass]);

    d3.selectAll('.tandem-nav button').each(function() {
        var button = d3.select(this);
        var buttonTarget = button.attr('data-target');
        var img = button.select('img.svg-nav');

        if (buttonTarget === targetClass) {
            img.attr('src', svgMappings[buttonTarget]);
        } else {
            // Reset to the original SVG if it's not the selected button
            var originalSrc = themeUri + buttonTarget + '-nav.svg';
            img.attr('src', originalSrc);
        }
    });

  
    // Show the section corresponding to the clicked target (e.g., .red-content, .orange-content, etc.)
    d3.select('.' + targetClass + '-content').style('display', 'block');
    d3.select('.' + targetClass + '-cards').style('display', 'block');
}



	function resetZoom() {
		d3.select('.arrow-left').style('display', 'none');
		d3.select('.arrow-right').style('display', 'none');
		d3.select('.tandem-first-content')
			.transition()
			.duration(500)
			.style('transform', 'translate(0, 0) scale(1)')
			.on('start', function() {
				// Remove the gradient overlay when resetting zoom
				d3.select(this).select('.gradient-overlay')
					.transition()
					.duration(200)
					.style('opacity', 0)
					.on('end', function() {
						d3.select(this).remove();
					});
			})
			.on('end', function() {
				d3.select(this).style('transform', 'translate(0, 0) scale(1)');
				d3.select('.color-indicator')
					.transition()
					.duration(300)
					.style('background-color', "rgb(197,228,255)");

				d3.selectAll('.tandem-nav button').each(function() {
					var button = d3.select(this);
					var buttonTarget = button.attr('data-target');
					var img = button.select('img.svg-nav');

					var originalSrc = themeUri + buttonTarget + '-nav.svg';
					img.attr('src', originalSrc);
				});

				d3.select('.tandem-container-nav-zoom button').style('display', 'none');

				// Disable all links when zoom is reset
				toggleLinks(false);
			});
	}

	// Initialize the handlers when the script loads
	initZoomHandlers();

	// Disable all links initially
	toggleLinks(false);


	function handleResize() {
		var currentTransform = d3.select('.tandem-first-content').style('transform');
		if (currentTransform && currentTransform !== 'none') {
			var scaleMatch = currentTransform.match(/scale\(([^)]+)\)/);
			var scale = scaleMatch ? parseFloat(scaleMatch[1]) : 1;

			var targetClass = Object.keys(zoomTargets).find(function(key) {
				return Math.abs(zoomTargets[key].scale - scale) < 0.01;
			});

			if (targetClass) {
				var recalculatedTransform = calculateTransform(targetClass);
				if (recalculatedTransform) {
					applyTransform(recalculatedTransform, 0, targetClass);
				}
			}
		}
	}

	function debounce(func, wait) {
		let timeout;
		return function(...args) {
			const later = () => {
				clearTimeout(timeout);
				func.apply(this, args);
			};
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
		};
	}

	function navigate(direction) {
		if (currentTargetIndex === -1) return; // If no target is set, do nothing

		var newIndex = (currentTargetIndex + direction + zoomOrder.length) % zoomOrder.length;
		var newTarget = zoomOrder[newIndex];

		zoomToTarget(newTarget);
	}

	function initZoomHandlers() {
		d3.selectAll('.tandem-nav button[data-target]').on('click', function() {
			var targetClass = d3.select(this).attr('data-target');
			zoomToTarget(targetClass);
		});

		Object.keys(zoomTargets).forEach(function(targetClass) {
			d3.select(zoomTargets[targetClass].element).on('click', function() {
				zoomToTarget(targetClass);
			});
		});

		d3.select('.tandem-container-nav-zoom button').on('click', resetZoom);

		d3.select('.tandem-container-nav-zoom button').style('display', 'none');

		// Add click handlers for the navigation arrows
		d3.select('.arrow-right').on('click', function() {
			if (currentTargetIndex < zoomOrder.length - 1) {
				currentTargetIndex++;
				zoomToTarget(zoomOrder[currentTargetIndex]);
			}
		});

		d3.select('.arrow-left').on('click', function() {
			if (currentTargetIndex > 0) {
				currentTargetIndex--;
				zoomToTarget(zoomOrder[currentTargetIndex]);
			}
		});
	}


	// Initialize the handlers when the script loads
	initZoomHandlers();
</script>