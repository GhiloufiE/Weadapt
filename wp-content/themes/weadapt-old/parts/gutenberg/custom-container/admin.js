// eslint-disable-next-line no-unused-vars
const { InnerBlocks, InspectorControls, PanelColorSettings } = wp.blockEditor;
const { registerBlockType } = wp.blocks;
// eslint-disable-next-line no-unused-vars
const { PanelBody, ToggleControl } = wp.components;
// eslint-disable-next-line no-unused-vars
const { select } = wp.data;
// eslint-disable-next-line no-unused-vars
const { getColorClassName, getColorObjectByColorValue } = wp.editor;
const { __ } = wp.i18n;

const createCustomClass = attributes => {
	const { spacingTop, spacingBottom, marginTop, marginBottom } = attributes;
	let customClass = 'block-content ';

	customClass += spacingTop ? ' block-spacing--pt' : '';
	customClass += spacingBottom ? ' block-spacing--pb' : '';
	customClass += marginTop ? ' block-spacing--mt' : '';
	customClass += marginBottom ? ' block-spacing--mb' : '';

	return customClass;
};

registerBlockType('custom/container', {
	title: 'Container',
	icon: 'welcome-add-page',
	category: 'theme_blocks',
	supports: {
		align: ['wide', 'full'],
		anchor: true,
		color: {
			gradients: true,	//	enable gradients
			text: false,		//	disable color
			// background: false	//	disable background color
		},
	},
	attributes: {
		align: {
			type: 'string',
			default: '',
		},
		spacingTop: {
			type: 'boolean',
			default: false,
		},
		spacingBottom: {
			type: 'boolean',
			default: false,
		},
		marginTop: {
			type: 'boolean',
			default: false,
		},
		marginBottom: {
			type: 'boolean',
			default: false,
		},
	},
	edit: ({ attributes, setAttributes }) => {
		const { spacingTop, spacingBottom, marginTop, marginBottom } = attributes;
		return (
			<>
				<InspectorControls>
					<PanelBody title={ __('Settings') }>
						<ToggleControl
							label={ __('Top Spacing') }
							checked={ !! spacingTop }
							onChange={ () =>
								setAttributes({ spacingTop: ! spacingTop })
							}
							help={
								spacingTop ? 'Adding Top spacing' : 'Toggle to add Top spacing.'
							}
						/>
						<ToggleControl
							label={ __('Bottom Spacing') }
							checked={ !! spacingBottom }
							onChange={ () =>
								setAttributes({ spacingBottom: ! spacingBottom })
							}
							help={
								spacingBottom ? 'Adding Bottom spacing' : 'Toggle to add Bottom spacing.'
							}
						/>
						<ToggleControl
							label= { __('Top margin') }
							checked={ !! marginTop }
							onChange={ () =>
								setAttributes({ marginTop: ! marginTop })
							}
							help={
								marginTop ? 'Adding Top margin' : 'Toggle to add Top margin.'
							}
						/>
						<ToggleControl
							label={ __('Bottom margin') }
							checked={ !! marginBottom }
							onChange={ () =>
								setAttributes({ marginBottom: ! marginBottom })
							}
							help={
								marginBottom ? 'Adding Bottom margin' : 'Toggle to add Bottom margin.'
							}
						/>
					</PanelBody>
				</InspectorControls>
				<section className={ createCustomClass(attributes) }>
					<div className={ `container container--${attributes.align}` }>
						<div className="container__body">
							<InnerBlocks />
						</div>
					</div>
				</section>
			</>
		);
	},
	save: ({ attributes }) => {
		const { align } = attributes;
		const alignClass = align ? ` container--${align}` : '';
		return (
			<section className={ createCustomClass(attributes) }>
				<div data-key="custom-container" />
				<div className={ `container${alignClass}` }>
					<div className="container__body">
						<InnerBlocks.Content />
					</div>
				</div>
			</section>
		);
	},
});