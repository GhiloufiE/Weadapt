<?php

/**
 * Hero Tandem Block
 *
 * @package WordPress
 * @subpackage weadapt
 * @since weadapt 1.0
 */
$block_object = new Block($block);
$name = $block_object->name();
$attr = $block_object->attr('has-image');
?>

<section <?php echo $attr; ?>>
	<link rel="stylesheet" src="<?php echo get_template_directory_uri(); ?>/parts/gutenberg/acf-hero-tandem/styles.css" />
	<?php echo load_inline_styles(__DIR__, $name); ?>
	<?php load_inline_dependencies('/parts/gutenberg/core-heading/', 'core-heading'); ?>
	<?php load_inline_dependencies('/parts/gutenberg/core-paragraph/', 'core-paragraph'); ?>
	<?php load_inline_dependencies('/parts/gutenberg/core-button/', 'core-button'); ?>

	<div class="container hero-tandem__container">
		<div class="hero-tandem__row row">
			<div class="col-12 col-lg-6 hero-tandem__col hero-tandem__col--text">
				<?php echo $block_object->title('hero-tandem__heading', 'h1'); ?>
				<?php echo $block_object->subtitle('hero-tandem__subtitle', ); ?>
				<?php echo $block_object->desc('hero-tandem__description'); ?>
			</div>

			<div class="col-12 col-lg-6 hero-tandem__col alignment-right">
				<div class="image-wrapper" style="position: relative; display: inline-block;">
			    <img src="<?php echo get_theme_file_uri('/assets/images/diagram.png'); ?>" style="width: 100%;" />
			    <!-- Clickable Areas -->
			    <div id="scope-review" class="clickable-area" style="background-color: red; position: absolute; top: 15%; left: 38%; width: 100px; height: 100px;"></div>
			    <div id="integrate-knowledge" class="clickable-area" style="background-color: green;position: absolute; top: 40%; left: 12%; width: 100px; height: 100px;"></div>
			    <div id="co-design" class="clickable-area" style="background-color: yellow;position: absolute; top: 66%; left: 38%; width: 100px; height: 100px;"></div>
			    <div id="co-explore" class="clickable-area" style="background-color: blue;position: absolute; top: 40%; left: 65%; width: 100px; height: 100px;"></div>
			</div>

				<!-- <svg class="hero-tandem__svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 441.3333 441.3333">
					<g class="circle">
						<path
							d="M286.8916,605.4951A219.6667,219.6667,0,1,1,442.22,541.1562,218.2292,218.2292,0,0,1,286.8916,605.4951Z"
							transform="translate(-66.2251 -165.1617)"></path>
						<polygon points="139.516 16.402 107.717 13.825 121.674 42.849 139.516 16.402"></polygon>
						<polygon points="386.11 364.725 415.559 352.454 389.86 333.043 386.11 364.725"></polygon>
						<polygon points="33.165 335.215 35.674 367.019 62.112 348.628 33.165 335.215"></polygon>
					</g>
					<g class="circles">
						<?php
						$post_name = get_post_field('post_name');

						foreach ([['slug' => 'identify-and-engage-relevant-stakeholders', 'title' => __('This can include partners, champions and a local organising team.', 'weadapt'), 'circle' => '<circle class="cls-6" cx="249.7321" cy="73.0451" r="52.6667"></circle>', 'text' => '<text class="cls-12" transform="translate(218.5911 55.0297)">Identify and <tspan x="13.3623" y="13.8003">engage </tspan><tspan x="10.377" y="27.6001">relevant </tspan><tspan x="-1.168" y="41.3999">stakeholders</tspan></text>'], ['slug' => 'co-explore-and-understand-context', 'title' => __('Adaptation issues, climate change impacts, policy and governance landscape, current research and practice.', 'weadapt'), 'circle' => '<circle class="cls-5" cx="355.0654" cy="148.845" r="52.6667"></circle>', 'text' => '<text class="cls-13" transform="translate(317.9777 140.4073)">Co-explore and <tspan x="9.1494" y="13.2002">understand </tspan><tspan x="19.1426" y="26.4004">context</tspan></text>'], ['slug' => 'set-focus-and-learning-objectives', 'title' => __('Contributing to an ongoing monitoring, evaluation and learning process.', 'weadapt'), 'circle' => '<circle class="cls-7" cx="363.3916" cy="281.2468" r="52.6667"></circle>', 'text' => '<text class="cls-12" transform="translate(329.6573 272.2323)">Set focus and <tspan x="12.6484" y="13.8003">learning </tspan><tspan x="8.5557" y="27.6001">objectives</tspan></text>'], ['slug' => 'identify-and-respond-to-training-or-capacity-needs', 'title' => __('Needs may have emerged from the co-exploration process.', 'weadapt'), 'circle' => '<circle class="cls-8" cx="263.8583" cy="368.6051" r="52.6667"></circle>', 'text' => '<text class="cls-14" transform="translate(232.7861 353.59)">Identify and <tspan x="3.8975" y="13.8003">respond to </tspan><tspan x="3.9004" y="27.6001">training or </tspan><tspan x="-5.8594" y="41.3999">capacity needs</tspan></text>'], ['slug' => 'identify-solutions-recommendations-and-ways-forward', 'title' => __('Adaptation measures from other contexts may provide inspiration.', 'weadapt'), 'circle' => '<circle class="cls-9" cx="131.2924" cy="346.2328" r="52.6667"></circle>', 'text' => '<text class="cls-14" transform="translate(86.5269 338.2158)">Identify solutions <tspan x="-1.3389" y="13.8003">recommendations </tspan><tspan x="-0.2783" y="27.6001">and ways forward</tspan></text>'], ['slug' => 'co-explore-and-distil-relevant-information-from-data', 'title' => __('Specific information needs may now be articulated.', 'weadapt'), 'circle' => '<circle class="cls-10" cx="67.7963" cy="232.2597" r="52.6667"></circle>', 'text' => '<text class="cls-14" transform="translate(29.8556 217.2458)">Co explore and <tspan x="0.666" y="13.8003">\'distil\' relevant </tspan><tspan x="7.4736" y="27.6001">information </tspan><tspan x="13.1514" y="41.3999">from data</tspan></text>'], ['slug' => 'strategically-engage-senior-decision-makers', 'title' => __('Consider differentiated engagements with subsets of stakeholders.', 'weadapt'), 'circle' => '<circle class="cls-11" cx="120.463" cy="106.407" r="52.6667"></circle>', 'text' => '<text class="cls-14" transform="translate(88.9924 94.3927)">Strategically <tspan x="-3.4277" y="13.8003">engage senior </tspan><tspan x="-10.3857" y="27.6001">decision-makers</tspan></text>']] as $slug => $item) {
							$class = ($post_name === $item['slug']) ? 'tooltip current' : 'tooltip';

							echo sprintf(
								'<a href="/tandem/%s" class="%s" data-title="%s">%s%s</a>',
								$item['slug'],
								$class,
								$item['title'],
								$item['circle'],
								$item['text']
							);
						}
						?>
						<a href="/tandem/">
							<text class="cls-15" transform="translate(172.6213 203.8667)">Encouraging <tspan x="10.2852"
									y="20.3999">long-term </tspan>
								<tspan x="-2.6973" y="40.7998">sustainability</tspan>
							</text>
						</a>
					</g>
					<g class="arrows">
						<g>
							<path class="cls-16"
								d="M380.6245,247.2588c1.75-.3045,3.7889-.6524,6.1661-1.05,20.1114-3.3641,17.6371,7.6249,11.6884,17.8419"
								transform="translate(-66.2251 -165.1617)"></path>
							<polygon class="cls-2" points="317.971 89.102 303.842 84.198 315.154 74.413 317.971 89.102">
							</polygon>
							<path class="cls-16"
								d="M358.0024,303.1269c-1.2778.4044-2.7158.8554-4.3343,1.3582-17.5757,5.46-18.5464-4.9561-16.08-15.0686"
								transform="translate(-66.2251 -165.1617)"></path>
							<polygon class="cls-2"
								points="287.289 131.518 301.953 134.458 292.076 145.689 287.289 131.518"></polygon>
						</g>
						<g class="cls-17">
							<path class="cls-16"
								d="M459.0051,371.8857c1.3306,1.1761,2.8763,2.5512,4.6717,4.1591,15.19,13.6033,5.0633,18.5357-6.631,20.2726"
								transform="translate(-66.2251 -165.1617)"></path>
							<polygon class="cls-2"
								points="389.541 213.889 384.545 199.792 399.252 202.513 389.541 213.889"></polygon>
							<path class="cls-16"
								d="M401.2471,389.12c-1.1141-.7452-2.3646-1.5865-3.7682-2.5363-15.2428-10.3138-7.7153-17.5785,1.7223-21.9694"
								transform="translate(-66.2251 -165.1617)"></path>
							<polygon class="cls-2"
								points="337.252 216.426 344.117 229.713 329.177 229.016 337.252 216.426"></polygon>
						</g>
						<g class="cls-18">
							<path class="cls-16"
								d="M405.8139,509.6874c-.0962,1.7733-.2151,3.8387-.3614,6.2444-1.238,20.3533-11.3908,15.4745-20.0109,7.3834"
								transform="translate(-66.2251 -165.1617)"></path>
							<polygon class="cls-2"
								points="331.96 346.433 339.912 333.767 346.908 346.987 331.96 346.433"></polygon>
							<path class="cls-16"
								d="M356.4526,475.0975c-.1072-1.336-.2237-2.8386-.35-4.5287-1.3739-18.3529,8.9943-16.96,18.2946-12.2853"
								transform="translate(-66.2251 -165.1617)"></path>
							<polygon class="cls-2"
								points="297.518 307.01 291.361 320.639 282.634 308.492 297.518 307.01"></polygon>
						</g>
						<g class="cls-19">
							<path class="cls-16"
								d="M264.0406,558.5851c-1.4273,1.0566-3.0943,2.2819-5.0409,3.703-16.4688,12.0236-19.1837,1.0915-18.4469-10.708"
								transform="translate(-66.2251 -165.1617)"></path>
							<polygon class="cls-2"
								points="191.482 388.763 206.31 386.813 200.586 400.631 191.482 388.763"></polygon>
							<path class="cls-16"
								d="M259.2148,498.5043c.9608-.9344,2.0441-1.9823,3.2654-3.1573,13.2625-12.76,18.8-3.8847,21.1288,6.2605"
								transform="translate(-66.2251 -165.1617)"></path>
							<polygon class="cls-2"
								points="199.892 337.093 185.467 341.04 189.26 326.572 199.892 337.093"></polygon>
						</g>
						<g class="cls-20">
							<path class="cls-16"
								d="M141.1778,476.9769c-1.7192-.4453-3.72-.9705-6.0494-1.59-19.7057-5.2414-12.9142-14.2278-3.2773-21.0763"
								transform="translate(-66.2251 -165.1617)"></path>
							<polygon class="cls-2" points="74.593 303.96 85.435 314.262 71.092 318.502 74.593 303.96">
							</polygon>
							<path class="cls-16"
								d="M184.8521,435.437c1.3307.1593,2.8267.3425,4.5084.5529,18.2618,2.2853,14.8444,12.1727,8.4219,20.3641"
								transform="translate(-66.2251 -165.1617)"></path>
							<polygon class="cls-2" points="120.053 278 107.911 269.268 121.545 263.118 120.053 278">
							</polygon>
						</g>
						<g class="cls-21">
							<path class="cls-16"
								d="M123.5825,329.2319c-.7806-1.5951-1.6839-3.4563-2.7292-5.628-8.8431-18.3736,2.4-19.0643,13.871-16.2033"
								transform="translate(-66.2251 -165.1617)"></path>
							<polygon class="cls-2" points="63.088 158.685 62.321 173.622 49.768 165.49 63.088 158.685">
							</polygon>
							<path class="cls-16"
								d="M183.5441,335.3637c.745,1.1141,1.5794,2.3692,2.5139,3.7831,10.1481,15.3536.4167,19.1926-9.9824,19.6462"
								transform="translate(-66.2251 -165.1617)"></path>
							<polygon class="cls-2"
								points="112.381 176.311 111.111 161.41 124.653 167.76 112.381 176.311"></polygon>
						</g>
						<g class="cls-22">
							<path class="cls-16"
								d="M237.4978,229.0116c.9358-1.5093,2.0317-3.264,3.3151-5.304,10.858-17.26,17.4216-8.1054,21.0985,3.1309"
								transform="translate(-66.2251 -165.1617)"></path>
							<polygon class="cls-2" points="178.88 65.839 165.823 73.132 166.034 58.177 178.88 65.839">
							</polygon>
							<path class="cls-16"
								d="M264.19,283.0534c-.5474,1.2234-1.1667,2.5974-1.8671,4.1407-7.6065,16.7588-16.0326,10.5588-21.9465,1.993"
								transform="translate(-66.2251 -165.1617)"></path>
							<polygon class="cls-2"
								points="190.165 116.959 202.11 107.959 203.933 122.804 190.165 116.959"></polygon>
						</g>
					</g>
				</svg> -->
				<div class="hero-tandem__tooltip"></div>
			</div>
		</div>
	</div>
</section>

