const { __ } = wp.i18n;
const { registerBlockStyle } = wp.blocks;

const styles = [
	{
		name: 'uppercase',
		label: __('Uppercase', 'weadapt'),
	},
	{
		name: 'subheading',
		label: __('Subheading', 'weadapt'),
	},
	{
		name: 'leadparagraph',
		label: __('Leadparagraph', 'weadapt'),
	},
];

wp.domReady(() => {
	styles.forEach(style => {
		registerBlockStyle('core/paragraph', style);
	});
});