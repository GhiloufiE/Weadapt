// eslint-disable-next-line no-unused-vars
const { InnerBlocks, InspectorControls, PanelColorSettings, MediaUpload, MediaUploadCheck, getColorClassName, getColorObjectByColorValue } = wp.blockEditor;
const { registerBlockType } = wp.blocks;
// eslint-disable-next-line no-unused-vars
const { PanelBody, RangeControl, PanelRow, FocalPointPicker, ToggleControl, Button } = wp.components;
// eslint-disable-next-line no-unused-vars
const { select } = wp.data;
const { __ } = wp.i18n;

const createClassNames = attributes => {
	const {
		width,
		widthTablet,
		widthSmallDesktop,
		widthDesktop,
		order,
		orderTablet,
		orderSmallDesktop,
		orderDesktop,
	} = attributes;
	let { offset,
		offsetTablet,
		offsetSmallDesktop,
		offsetDesktop,
	} = attributes;
	let customClass = `col col-${width}`;

	offset = (offset === -1) ? 0 : offset;
	offsetTablet = (offsetTablet === -1) ? 0 : offsetTablet;
	offsetSmallDesktop = (offsetSmallDesktop === -1) ? 0 : offsetSmallDesktop;
	offsetDesktop = (offsetDesktop === -1) ? 0 : offsetDesktop;

	customClass += (widthTablet !== undefined) ? ` col-md-${widthTablet}` : '';
	customClass += (widthSmallDesktop !== undefined) ? ` col-lg-${widthSmallDesktop}` : '';
	customClass += (widthDesktop !== undefined) ? ` col-xl-${widthDesktop}` : '';
	customClass += (offset !== undefined) ? ` offset-${offset}` : '';
	customClass += (offsetTablet !== undefined) ? ` offset-md-${offsetTablet}` : '';
	customClass += (offsetSmallDesktop !== undefined) ? ` offset-lg-${offsetSmallDesktop}` : '';
	customClass += (offsetDesktop !== undefined) ? ` offset-xl-${offsetDesktop}` : '';
	customClass += (order !== undefined) ? ` order-${order}` : '';
	customClass += (orderTablet !== undefined) ? ` order-md-${orderTablet}` : '';
	customClass += (orderSmallDesktop !== undefined) ? ` order-lg-${orderSmallDesktop}` : '';
	customClass += (orderDesktop !== undefined) ? ` order-xl-${orderDesktop}` : '';

	return customClass;
};

const createAttributes = attributes => {
	const { backgroundColor, url, focalPoint, bgRepeat } = attributes;
	const attrObj = {};

	if (backgroundColor || url) {
		attrObj.style = {};
	}

	if (backgroundColor) {
		attrObj.style.backgroundColor = backgroundColor;
	}

	if (url) {
		attrObj.style = {};
		attrObj.style.backgroundImage = `url(${url})`;
		attrObj.style.backgroundPosition = `${focalPoint.x * 100}% ${focalPoint.y * 100}%`;
		if (! bgRepeat) {
			attrObj.style.backgroundRepeat = 'no-repeat';
			attrObj.style.backgroundSize = 'cover';
		}
	}

	return attrObj;
};

registerBlockType('custom/column', {
	title: 'Single Column',
	icon: 'welcome-add-page',
	category: 'theme_blocks',
	parent: ['custom/columns'],
	attributes: {
		width: {
			type: 'number',
			default: 6,
		},
		widthTablet: {
			type: 'number',
		},
		widthSmallDesktop: {
			type: 'number',
		},
		widthDesktop: {
			type: 'number',
		},
		offset: {
			type: 'number',
		},
		offsetTablet: {
			type: 'number',
		},
		offsetSmallDesktop: {
			type: 'number',
		},
		offsetDesktop: {
			type: 'number',
		},
		order: {
			type: 'number',
		},
		orderTablet: {
			type: 'number',
		},
		orderSmallDesktop: {
			type: 'number',
		},
		orderDesktop: {
			type: 'number',
		},
		backgroundColor: {
			type: 'string',
		},
		url:  {
			type: 'string',
			default: '',
		},
		bgRepeat: {
			type: 'bool',
			default: false,
		},
		focalPoint: {
			type: 'object',
			default: {
				// eslint-disable-next-line id-length
				x: 0.5,
				// eslint-disable-next-line id-length
				y: 0.5,
			},
		},
	},
	getEditWrapperProps (attributes) {
		const { width,
			widthTablet,
			widthSmallDesktop,
			widthDesktop,
			order,
			orderTablet,
			orderSmallDesktop,
			orderDesktop,
		} = attributes;
		let {   offset,
			offsetTablet,
			offsetSmallDesktop,
			offsetDesktop,
		} = attributes;

		offset = (offset === -1) ? 0 : offset;
		offsetTablet = (offsetTablet === -1) ? 0 : offsetTablet;
		offsetSmallDesktop = (offsetSmallDesktop === -1) ? 0 : offsetSmallDesktop;
		offsetDesktop = (offsetDesktop === -1) ? 0 : offsetDesktop;

		const offsetArray = [offset, offsetTablet, offsetSmallDesktop, offsetDesktop];
		const widthArray = [width, widthTablet, widthSmallDesktop, widthDesktop];
		const orderArray = [order, orderTablet, orderSmallDesktop, orderDesktop];

		let finalWidth;
		let finalOrder;
		let finalOffset = 0;

		offsetArray.forEach(val => {
			if (val !== undefined) {
				finalOffset = val;
			}
		});

		widthArray.forEach(val => {
			if (val !== undefined) {
				finalWidth = val;
			}
		});

		orderArray.forEach(val => {
			if (val !== undefined) {
				finalOrder = val;
			}
		});

		finalOffset = finalOffset / 12 * 100;
		finalWidth = finalWidth / 12 * 100;

		if (Number.isFinite(finalWidth)) {
			return {
				style: {
					flexBasis: `${finalWidth}%`,
					maxWidth: `${finalWidth}%`,
					marginLeft: `${finalOffset}%`,
					order: finalOrder,
				},
				'data-grid-col': true,
			};
		}
	},
	edit: props => {
		const { attributes, setAttributes } = props;
		const { width,
			widthTablet,
			widthSmallDesktop,
			widthDesktop,
			offset,
			offsetTablet,
			offsetSmallDesktop,
			offsetDesktop,
			order,
			orderTablet,
			orderSmallDesktop,
			orderDesktop,
			backgroundColor,
			url,
			bgRepeat,
			focalPoint,
		} = attributes;
		const ALLOWED_MEDIA_TYPES = ['image'];

		const getImageButton = openEvent => {
			if (url) {
				return (
					<img
						src={ url }
						onClick={ openEvent }
						className="image"
						alt="placeholder"
					/>
				);
			}
			return (
				<div className="button-container">
					<Button
						onClick={ openEvent }
						className="button button-large"
					>
						{__('Pick an image')}
					</Button>
				</div>
			);
		};

		const renderClear = () => {
			if (url) {
				return (
					<Button
						onClick={ () => {
							setAttributes({ url: undefined });
						} }
						className="button button-large"
					>{__('Clear Image')}</Button>
				);
			}
		};

		const renderFocal = () => {
			if (url) {
				return (
					<FocalPointPicker
						label={ __('Focal point picker') }
						url={ url }
						value={ focalPoint }
						onChange={ newFocalPoint =>
							setAttributes({
								focalPoint: newFocalPoint,
							})
						}
					/>
				);
			}
		};

		return (
			<>
				<InspectorControls>
					<PanelBody
						title="Settings"
					>
						<RangeControl
							label={ __('Column width') }
							value={ width || '' }
							onChange={ nextWidth => {
								setAttributes({ width: nextWidth });
							} }
							min={ 1 }
							max={ 12 }
							step={ 1 }
							required
						/>
						<RangeControl
							label={ __('Column Tablet width') }
							value={ widthTablet || '' }
							onChange={ nextWidth => {
								setAttributes({ widthTablet: nextWidth });
							} }
							min={ 1 }
							max={ 12 }
							step={ 1 }
							allowReset
						/>
						<RangeControl
							label={ __('Column Small Desktop width') }
							value={ widthSmallDesktop || '' }
							onChange={ nextWidth => {
								setAttributes({ widthSmallDesktop: nextWidth });
							} }
							min={ 1 }
							max={ 12 }
							step={ 1 }
							allowReset
						/>
						<RangeControl
							label={ __('Column Desktop width') }
							value={ widthDesktop || '' }
							onChange={ nextWidth => {
								setAttributes({ widthDesktop: nextWidth });
							} }
							min={ 1 }
							max={ 12 }
							step={ 1 }
							allowReset
						/>
						<RangeControl
							label={ __('Offset width') }
							value={ offset || '' }
							onChange={ nextWidth => {
								setAttributes({ offset: nextWidth });
							} }
							min={ -1 }
							max={ 11 }
							step={ 1 }
							allowReset
						/>
						<RangeControl
							label={ __('Offset Tablet width') }
							value={ offsetTablet || '' }
							onChange={ nextWidth => {
								setAttributes({ offsetTablet: nextWidth });
							} }
							min={ -1 }
							max={ 11 }
							step={ 1 }
							allowReset
						/>
						<RangeControl
							label={ __('Offset Small Desktop width') }
							value={ offsetSmallDesktop || '' }
							onChange={ nextWidth => {
								setAttributes({ offsetSmallDesktop: nextWidth });
							} }
							min={ -1 }
							max={ 11 }
							step={ 1 }
							allowReset
						/>
						<RangeControl
							label={ __('Offset Desktop width') }
							value={ offsetDesktop || '' }
							onChange={ nextWidth => {
								setAttributes({ offsetDesktop: nextWidth });
							} }
							min={ -1 }
							max={ 11 }
							step={ 1 }
							allowReset
						/>
						<PanelRow>{ __('Since it\'s not possible to set 0 above, please set -1 if you want to reset offset on specific breakpoints.') }</PanelRow>
					</PanelBody>
					<PanelBody
						title="Order"
						initialOpen={ false }
					>
						<RangeControl
							label={ __('Order') }
							value={ order || '' }
							onChange={ nextOrder => {
								setAttributes({ order: nextOrder });
							} }
							min={ 1 }
							max={ 12 }
							step={ 1 }
							allowReset
						/>
						<RangeControl
							label={ __('Order Tablet') }
							value={ orderTablet || '' }
							onChange={ nextOrder => {
								setAttributes({ orderTablet: nextOrder });
							} }
							min={ 1 }
							max={ 12 }
							step={ 1 }
							allowReset
						/>
						<RangeControl
							label={ __('Order Small Desktop') }
							value={ orderSmallDesktop || '' }
							onChange={ nextOrder => {
								setAttributes({ orderSmallDesktop: nextOrder });
							} }
							min={ 1 }
							max={ 12 }
							step={ 1 }
							allowReset
						/>
						<RangeControl
							label={ __('Order Desktop') }
							value={ orderDesktop || '' }
							onChange={ nextOrder => {
								setAttributes({ orderDesktop: nextOrder });
							} }
							min={ 1 }
							max={ 12 }
							step={ 1 }
							allowReset
						/>
					</PanelBody>
					<PanelBody title={ __('Background Image Settings') } initialOpen={ false }>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ media => {
									setAttributes({ url: media.url });
								} }
								allowedTypes={ ALLOWED_MEDIA_TYPES }

								render={ ({ open }) => getImageButton(open) }
							/>
						</MediaUploadCheck>

						{ renderClear() }

						{ renderFocal() }

						<ToggleControl
							label={ __('Background Repeat') }
							checked={ !! bgRepeat }
							onChange={ () =>
								setAttributes({ bgRepeat: ! bgRepeat })
							}
							help={
								bgRepeat ? 'Repeat background' : 'Toggle to repeat background'
							}
						/>
					</PanelBody>
					<PanelColorSettings
						title={ __('Color Settings') }
						initialOpen={ false }
						colorSettings={ [
							{
								value: backgroundColor,
								onChange: colorValue => setAttributes({ backgroundColor: colorValue }),
								label: __('Background Color'),
							},
						] }
					/>
				</InspectorControls>
				<div className='col--editor' {...createAttributes(attributes, true)}>
					<InnerBlocks
						renderAppender={ () => (
							<InnerBlocks.ButtonBlockAppender />
						) }
					/>
				</div>
			</>
		);
	},
	save: ({ attributes }) => {
		return (
			<div className={ createClassNames(attributes) } {...createAttributes(attributes, false)}>
				<InnerBlocks.Content />
			</div>
		);
	},
});