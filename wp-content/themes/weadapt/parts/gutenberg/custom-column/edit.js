/**
 * WordPress dependencies
 */
/*global wp*/
const { InnerBlocks, InspectorControls } = wp.blockEditor;
const { withSelect } = wp.data;
const { compose } = wp.compose;
const { PanelBody, RangeControl, PanelRow } = wp.components;
const { __ } = wp.i18n;

function SingleColumnEdit(props) {
    const { attributes, setAttributes, hasChildBlocks } = props;
    const {
        width,
        widthTablet,
        widthSmallDesktop,
        widthDesktop,
        offset,
        offsetTablet,
        offsetSmallDesktop,
        offsetDesktop,
    } = attributes;

    return (
        <>
            <InspectorControls>
                <PanelBody title="Settings">
                    <RangeControl
                        label={__('Column width')}
                        value={width || ''}
                        onChange={(nextWidth) => {
                            setAttributes({ width: nextWidth });
                        }}
                        min={1}
                        max={12}
                        step={1}
                        required
                    />
                    <RangeControl
                        label={__('Column Tablet width')}
                        value={widthTablet || ''}
                        onChange={(nextWidth) => {
                            setAttributes({ widthTablet: nextWidth });
                        }}
                        min={1}
                        max={12}
                        step={1}
                        allowReset
                    />
                    <RangeControl
                        label={__('Column Small Desktop width')}
                        value={widthSmallDesktop || ''}
                        onChange={(nextWidth) => {
                            setAttributes({ widthSmallDesktop: nextWidth });
                        }}
                        min={1}
                        max={12}
                        step={1}
                        allowReset
                    />
                    <RangeControl
                        label={__('Column Desktop width')}
                        value={widthDesktop || ''}
                        onChange={(nextWidth) => {
                            setAttributes({ widthDesktop: nextWidth });
                        }}
                        min={1}
                        max={12}
                        step={1}
                        allowReset
                    />
                    <RangeControl
                        label={__('Offset width')}
                        value={offset || ''}
                        onChange={(nextWidth) => {
                            setAttributes({ offset: nextWidth });
                        }}
                        min={-1}
                        max={11}
                        step={1}
                        allowReset
                    />
                    <RangeControl
                        label={__('Offset Tablet width')}
                        value={offsetTablet || ''}
                        onChange={(nextWidth) => {
                            setAttributes({ offsetTablet: nextWidth });
                        }}
                        min={-1}
                        max={11}
                        step={1}
                        allowReset
                    />
                    <RangeControl
                        label={__('Offset Small Desktop width')}
                        value={offsetSmallDesktop || ''}
                        onChange={(nextWidth) => {
                            setAttributes({ offsetSmallDesktop: nextWidth });
                        }}
                        min={-1}
                        max={11}
                        step={1}
                        allowReset
                    />
                    <RangeControl
                        label={__('Offset Desktop width')}
                        value={offsetDesktop || ''}
                        onChange={(nextWidth) => {
                            setAttributes({ offsetDesktop: nextWidth });
                        }}
                        min={-1}
                        max={11}
                        step={1}
                        allowReset
                    />
                    <PanelRow>
                        {__(
                            "Since it's not possible to set 0 above, please set -1 if you want to reset offset on specific breakpoints."
                        )}
                    </PanelRow>
                </PanelBody>
            </InspectorControls>
            <div className="col--editor">
                <InnerBlocks
                    templateLock={false}
                    renderAppender={
                        hasChildBlocks
                            ? undefined
                            : () => <InnerBlocks.ButtonBlockAppender />
                    }
                />
            </div>
        </>
    );
}

export default compose(
    withSelect((select, ownProps) => {
        const { clientId } = ownProps;
        const { getBlockOrder } = select( 'core/block-editor' );

        return {
            hasChildBlocks: getBlockOrder(clientId).length > 0,
        };
    })
)(SingleColumnEdit);
