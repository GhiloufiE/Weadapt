<?php

/**
 * Tandem Circle Block
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block($block);
$name = $block_object->name();
$attr = $block_object->attr(' has-image');
?>
<?php echo load_inline_styles(__DIR__, $name); ?>
<?php load_inline_dependencies('/parts/gutenberg/core-heading/', 'core-heading'); ?>
<?php load_inline_dependencies('/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
<?php load_inline_dependencies('/parts/gutenberg/core-button/', 'core-button'); ?>
<link rel="stylesheet" src="<?php echo get_template_directory_uri(); ?>/parts/gutenberg/acf-tandem-circle/style.css" />
<section>
	<div class="tandem-container">
		<div class="tandem-full-content">
			<div class="tandem-container-nav">
				<div class="tandem-container-nav-title">
				</div>
			</div>
			<div class="tandem-first-content-wrapper">
				<h2 style="text-align:center; position:relative; top: 0;
	width: 100%;
	z-index: 1000;">Enhanced legacy & sustainability</h2>

				<div class="tandem-first-content">
				<img class="left-arrow"
				src="<?php echo get_theme_file_uri('/assets/images/svg/left-arrow.svg'); ?>" />
					<div class="tandem-inside-content">
					
						<!-- -------------------------------- grand-arrow------------------------- -->
						<img class="first-grand-arrow"
							src="<?php echo get_theme_file_uri('/assets/images/svg/prime-arrow.svg'); ?>" />
						<img class="second-grand-arrow"
							src="<?php echo get_theme_file_uri('/assets/images/svg/second-arrow.svg'); ?>" />
						<img class="third-grand-arrow"
							src="<?php echo get_theme_file_uri('/assets/images/svg/third-arrow.svg'); ?>" />
						<img class="fourth-grand-arrow"
							src="<?php echo get_theme_file_uri('/assets/images/svg/fourth-arrow.svg'); ?>" />
						<!-- -------------------------------- end grand-arrow------------------------- -->

						<!-- ---------------------- first circle ----------------------------- -->
						<div class="tandem-first-circle">
							<div class="tandem-first-in-cercle">
								<!-- SVG and other content -->
								<div class="tandem-inside-red-circle">
									<a href="/tandem/identify-and-engage-relevant-stakeholders">
										<p>Vulnerability Scope risks, & impacts</p>
									</a>
								</div>
								<div class="tandem-inside-red-two-c">
									<div class="tandem-inside-red-circle">
										<a href="/tandem/set-focus-and-learning-objectives">
											<p>Stakeholders Engage</p>
										</a>
									</div>
									<div class="tandem-inside-red-circle">
										<a href="/tandem/co-explore-and-understand-context">
											<p>Stakeholders Identify</p>
										</a>
									</div>
								</div>
							</div>
						</div>
			
						<!-- -------------------------------end first circle ---------------------------- -->
						<div class="tandem-middle-content">
							<!-- ---------------------- second circle ----------------------------- -->
							<div class="tandem-second-circle">
								<!-- <p>Scope & review risks, vurnerability & impact</p> -->
								<div class="tandem-second-in-cercle">
									<div class="tandem-inside-red-circle">
										<a href="https://google.com">
											<p>
												<span style="font-weight: 800">Monitor, </span><br />
												as confidence, Monitor, relationships knowledge, &
												capacity increase
											</p>
										</a>
									</div>
									<!-- ---------------- svg arrow --------------------- -->
									<img class="red-two-arrow"
										src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-1.svg'); ?>" />
									<img class="red-two-arrow2"
										src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-2.svg'); ?>" />
									<img class="red-two-arrow3"
										src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-3.svg'); ?>" />
									<!-- ------------------------- end svg arrow --------------------------- -->
									<div class="tandem-inside-red-two-c">
										<div class="tandem-inside-red-circle">
											<p>
												Iterative & reflexive Iterative <br />
												<span style="font-weight: 800">learning</span>
											</p>
										</div>
										<div class="tandem-inside-red-circle">
											<p>
												<span style="font-weight: 800">Evaluate</span> progress
												towards goals
											</p>
										</div>
									</div>
								</div>
							</div>

							<!-- -------------------------------end second circle ---------------------------- -->
							<!-- ---------------------- svg arrow -------------- -->
							<img class="rectangle-arrow"
								src="<?php echo get_theme_file_uri('/assets/images/svg/arrow-top.svg'); ?>" />
							<img class="rectangle-arrow-left"
								src="<?php echo get_theme_file_uri('/assets/images/svg/arrow-left.svg'); ?>" />
							<div class="tandem-middle-content-rectangle">
								<div class="tandem-middle-content-rectangle-widh">
									<p>Cross-cutting components</p>
									<li>Begin early co-design of MEL framework</li>
									<li>Tailored communication of climate information & risks</li>
									<li>Co-develop capacities of providers & users</li>
									<li>Partnership development</li>
									<li>Explore financing models</li>
								</div>
							</div>
							<img class="rectangle-arrow-bottom"
								src="<?php echo get_theme_file_uri('/assets/images/svg/arrow-bottom.svg'); ?>" />
							<img class="rectangle-arrow-right"
								src="<?php echo get_theme_file_uri('/assets/images/svg/arrow-right.svg'); ?>" />
							<div class="tandem-third-circle">
								<!-- <p>Scope & review risks, vurnerability & impact</p> -->
								<div class="tandem-third-in-cercle">
									<div class="tandem-inside-red-circle">
										<p>Challenges & goals</p>
									</div>
									<!-- ---------------- svg arrow --------------------- -->
									<img class="red-two-arrow"
										src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-1.svg'); ?>" />
									<img class="red-two-arrow2"
										src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-2.svg'); ?>" />
									<img class="red-two-arrow3"
										src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-3.svg'); ?>" />
									<!-- ------------------------- end svg arrow --------------------------- -->
									<div class="tandem-inside-red-two-c">
										<div class="tandem-inside-red-circle">
											<p>Information needs</p>
										</div>
										<div class="tandem-inside-red-circle">
											<p>Governance context</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="tandem-fourth-circle">

							<!-- <p>Scope & review risks, vurnerability & impact</p> -->
							<div class="tandem-fourth-in-cercle">
								
								<div class="tandem-inside-red-circle">
									<p>Co-design solutions</p>
								</div>
								<!-- ---------------- svg arrow --------------------- -->
								<img class="red-two-arrow"
									src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-1.svg'); ?>" />
								<img class="red-two-arrow2"
									src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-2.svg'); ?>" />
								<img class="red-two-arrow3"
									src="<?php echo get_theme_file_uri('/assets/images/svg/top-arrow-3.svg'); ?>" />
								<!-- ------------------------- end svg arrow --------------------------- -->
								<div class="tandem-inside-red-two-c">
									<div class="tandem-inside-red-circle">
										<p>solutions Appraise</p>
									</div>
									<div class="tandem-inside-red-circle">
										<p>Co-explore & identify solutions</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<img class="right-arrow"
					src="<?php echo get_theme_file_uri('/assets/images/svg/right-arrow.svg'); ?>" />
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
			console.error('No zoom target defined for class:', targetClass);
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

	function applyTransform(transform, duration, targetClass) {
		if (transform) {
			d3.select('.tandem-first-content')
				.transition()
				.duration(duration)
				.style('transform', `translate(${transform.translateX}px, ${transform.translateY}px) scale(${transform.scale})`)
				.on('end', function () {
					d3.select(this).style('transform', `translate(${transform.translateX}px, ${transform.translateY}px) scale(${transform.scale})`);
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


	function zoomToTarget(targetClass) {
		// Check if the gradient overlay already exists for the current target
		if (d3.select('.tandem-first-content').select(`.gradient-overlay.${targetClass}`).node()) {
			return; // If it exists, don't reapply the gradient
		}

		// Clear any previous overlays
		d3.select('.tandem-first-content')
			.select('.gradient-overlay')
			.transition()
			.duration(0)
			.style('opacity', 0)
			.on('end', function () {
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

		setTimeout(function () {
			addGradientOverlay(targetClass, lastTransform, 0);
		}, 0);

		var observer = new MutationObserver(handleMutation);
		observer.observe(d3.select('.tandem-first-content').node(), {
			attributes: true,
			childList: true,
			subtree: true
		});
		setTimeout(function () {
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

		d3.selectAll('.tandem-nav button').each(function () {
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
	}
	function resetZoom() {
		d3.select('.tandem-first-content')
			.transition()
			.duration(500)
			.style('transform', 'translate(0, 0) scale(1)')
			.on('start', function () {
				// Remove the gradient overlay when resetting zoom
				d3.select(this).select('.gradient-overlay')
					.transition()
					.duration(200)
					.style('opacity', 0)
					.on('end', function () {
						d3.select(this).remove();
					});
			})
			.on('end', function () {
				d3.select(this).style('transform', 'translate(0, 0) scale(1)');
				// Reset the color indicator to transparent when zoom is reset
				d3.select('.color-indicator')
					.transition()
					.duration(300)
					.style('background-color', "rgb(197,228,255)");

				// Reset all SVGs to their original state
				d3.selectAll('.tandem-nav button').each(function () {
					var button = d3.select(this);
					var buttonTarget = button.attr('data-target');
					var img = button.select('img.svg-nav');

					// Revert to the original SVG
					var originalSrc = themeUri + buttonTarget + '-nav.svg';
					img.attr('src', originalSrc);
				});

				// Hide the zoom out button when zoom is reset
				d3.select('.tandem-container-nav-zoom button').style('display', 'none');
			});
	}



	function handleResize() {
		var currentTransform = d3.select('.tandem-first-content').style('transform');
		if (currentTransform && currentTransform !== 'none') {
			var scaleMatch = currentTransform.match(/scale\(([^)]+)\)/);
			var scale = scaleMatch ? parseFloat(scaleMatch[1]) : 1;

			var targetClass = Object.keys(zoomTargets).find(function (key) {
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
		return function (...args) {
			const later = () => {
				clearTimeout(timeout);
				func.apply(this, args);
			};
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
		};
	}

	function initZoomHandlers() {
		d3.selectAll('.tandem-nav button[data-target]').on('click', function () {
			var targetClass = d3.select(this).attr('data-target');
			zoomToTarget(targetClass);
		});

		Object.keys(zoomTargets).forEach(function (targetClass) {
			d3.select(zoomTargets[targetClass].element).on('click', function () {
				zoomToTarget(targetClass);
			});
		});

		d3.select('.tandem-container-nav-zoom button').on('click', resetZoom);

		d3.select('.tandem-container-nav-zoom button').style('display', 'none');
	}

	// Initialize the handlers when the script loads
	initZoomHandlers();
</script>