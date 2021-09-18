/**
 * Extend core blocks with custom attributes and controls.
 *
 * @package    AJV_Blocks
 * @subpackage AJV_Blocks/core
 * @author     Alexis J. Villegas
 * @link       https://www.alexisvillegas.com
 * @license    GPL-2.0+
 */

/**
 * Columns block.
 *
 * Add responsive column settings and column gutter width controls.
 */
( function( editor, element, components, compose, hooks ) {
	const __ = wp.i18n.__;
	const el = element.createElement;
	const addFilter = hooks.addFilter;
	const { InspectorControls } = editor;
	const { Fragment } = element;
	const { PanelBody, PanelRow, RadioControl, RangeControl, ToggleControl } = components;
	const { createHigherOrderComponent } = compose;

	// Restrict to specific blocks.
	const allowedBlocks = [ 'core/columns' ];

	/**
	 * Add custom attributes.
	 *
	 * @param {Object} settings Settings for the block.
	 *
	 * @return {Object} settings Modified settings.
	 */
	const addResponsiveAttributes = function( settings ) {
		// Bail if it's another block than our defined ones.
		if ( ! allowedBlocks.includes( settings.name ) ) {
			return settings;
		}

		settings.attributes = Object.assign(
			settings.attributes,
			{
				responsiveBehavior: {
					type: 'string',
					default: 'default-stack'
				},
				adjustColumnGutter: {
					type: 'boolean',
					default: false
				},
				columnGutter: {
					type: 'number',
					default: 30
				},
				columnsLarge: {
					type: 'number',
					default: 1
				},
				columnsMedium: {
					type: 'number',
					default: 1
				},
				columnsSmall: {
					type: 'number',
					default: 1
				}
			}
		);

		return settings;
	};

	/**
	 * Add custom controls on settings sidebar.
	 *
	 * @param {function} BlockEdit Block edit component.
	 *
	 * @return {function} BlockEdit Modified block edit component.
	 */
	const withResponsiveControls = createHigherOrderComponent( function( BlockEdit ) {
		return function( props ) {
			// Bail if it's another block than our defined ones.
			if ( ! allowedBlocks.includes( props.name ) ) {
				return el( Fragment,
					{},
					el(
						BlockEdit,
						props
					)
				);
			}

			return el( Fragment,
				{},
				el(
					BlockEdit,
					props
				),
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{},
						el( PanelRow,
							{},
							el( ToggleControl,
								{
									label: __( 'Adjust columm gutter', 'ajv-blocks' ),
									onChange: ( value ) => {
										props.setAttributes({ adjustColumnGutter: value });
									},
									checked: props.attributes.adjustColumnGutter
								}
							)
						),
						true === props.attributes.adjustColumnGutter ?
							el( PanelRow,
								{},
								el( RangeControl,
									{
										min: 0,
										max: 100,
										initialPosition: 30,
										value: props.attributes.columnGutter,
										label: __( 'Column gutter in pixels', 'ajv-blocks' ),
										onChange: ( value ) => {
											0 < value ? props.setAttributes({ columnGutter: value }) : props.setAttributes({ columnGutter: 0 });
										}
									}
								)
							) : null,
					),
					el(
						PanelBody,
						{
							className: 'responsive-setting-options',
							title: __( 'Responsive settings', 'ajv-blocks' ),
							initialOpen: true
						},
						el( PanelRow,
							{},
							el( RadioControl,
								{
									options: [
										{
											label: __( 'Use the same column count on all screen sizes.', 'ajv-blocks' ),
											value: 'default-stack'
										},
										{
											label: __( 'Specify custom column counts for other screen sizes:', 'ajv-blocks' ),
											value: 'responsive-stack'
										}
									],
									onChange: ( value ) => {
										props.setAttributes({ responsiveBehavior: value });
									},
									selected: props.attributes.responsiveBehavior
								}
							)
						),
						el( PanelRow,
							{},
							el( RangeControl,
								{
									min: 1,
									max: 6,
									initialPosition: 1,
									value: props.attributes.columnsLarge,
									beforeIcon: 'laptop',
									label: __( 'Larger screens', 'ajv-blocks' ),
									onChange: ( value ) => {
										1 < value ? props.setAttributes({ columnsLarge: value }) : props.setAttributes({ columnsLarge: 1 });
									}
								}
							)
						),
						el( PanelRow,
							{},
							el( RangeControl,
								{
									min: 1,
									max: 6,
									initialPosition: 1,
									value: props.attributes.columnsMedium,
									beforeIcon: 'tablet',
									label: __( 'Medium screens', 'ajv-blocks' ),
									onChange: ( value ) => {
										1 < value ? props.setAttributes({ columnsMedium: value }) : props.setAttributes({ columnsMedium: 1 });
									}
								}
							)
						),
						el( PanelRow,
							{},
							el( RangeControl,
								{
									min: 1,
									max: 6,
									initialPosition: 1,
									value: props.attributes.columnsSmall,
									beforeIcon: 'smartphone',
									label: __( 'Small screens', 'ajv-blocks' ),
									onChange: ( value ) => {
										1 < value ? props.setAttributes({ columnsSmall: value }) : props.setAttributes({ columnsSmall: 1 });
									}
								}
							)
						)
					)
				)
			);
		};
	}, 'withResponsiveControls' );

	/**
	 * Add custom attributes to block edit component.
	 *
	 * @param {function} BlockListBlock Block edit wrapper component.
	 *
	 * @return {function} BlockListBlock Modified block edit wrapper component.
	 */
	 const applyEditorColumnClasses = createHigherOrderComponent( function( BlockListBlock ) {
		return function( props ) {
			// Bail if it's another block than our defined ones.
			if ( ! allowedBlocks.includes( props.name ) ) {
				return el(
					BlockListBlock,
					props
				);
			}

			// Assign column classes.
			if ( 'responsive-stack' === props.attributes.responsiveBehavior ) {
				props = lodash.assign(
					{},
					props, {
						className: `col-large-${props.attributes.columnsLarge} col-medium-${props.attributes.columnsMedium} col-small-${props.attributes.columnsSmall}`,
					}
				);
			}

			// Define inline CSS styles.
			if ( true === props.attributes.adjustColumnGutter ) {
				inlineStyles = '#block-' + props.clientId + '{ --column-gutter: ' + props.attributes.columnGutter + 'px }';
			}

			// Assign wrapper <div> to output inline CSS styles.
			if ( true === props.attributes.adjustColumnGutter ) {
				return el( 'div',
					{},
					el( 'style',
						{
							type: 'text/css'
						},
						inlineStyles
					),
					el(
						BlockListBlock,
						props
					)
				);
			} else {
				return el(
					BlockListBlock,
					props
				);
			}
		};
	}, 'applyEditorColumnClasses' );

	/**
	 * Add custom attributes to block save component.
	 *
	 * @param {Object} extraProps Block element.
	 * @param {Object} blockType Blocks object.
	 * @param {Object} attributes Blocks attributes.
	 *
	 * @return {Object} extraProps Modified block element.
	 */
	const applyColumnClasses = function( extraProps, blockType, attributes ) {
		// Bail if it's another block than our defined ones.
		if ( ! allowedBlocks.includes( blockType.name ) ) {
			return extraProps;
		}

		// Assign inline CSS styles.
		if ( true === attributes.adjustColumnGutter ) {
			Object.assign(
				extraProps.style,
				{
					'--column-gutter': attributes.columnGutter + 'px'
				}
			);
		}

		// Assign column classes.
		if ( 'responsive-stack' === attributes.responsiveBehavior ) {
			extraProps.className = extraProps.className + ` col-large-${attributes.columnsLarge} col-medium-${attributes.columnsMedium} col-small-${attributes.columnsSmall}`;
		}

		return extraProps;
	};

	// Add filters.
	addFilter(
		'blocks.registerBlockType',
		'ajvbl/add-responsive-attributes',
		addResponsiveAttributes
	);

	addFilter(
		'editor.BlockEdit',
		'ajvbl/with-responsive-controls',
		withResponsiveControls
	);

	addFilter(
		'editor.BlockListBlock',
		'ajvbl/apply-editor-column-classes',
		applyEditorColumnClasses
	);

	addFilter(
		'blocks.getSaveContent.extraProps',
		'ajvbl/apply-column-classes',
		applyColumnClasses
	);
}(
	window.wp.blockEditor,
	window.wp.element,
	window.wp.components,
	window.wp.compose,
	window.wp.hooks
) );

/**
 * Cover and Group blocks.
 *
 * Add inner container width setting controls.
 */
( function( editor, element, components, compose, hooks ) {
	const __ = wp.i18n.__;
	const el = element.createElement;
	const addFilter = hooks.addFilter;
	const { InspectorControls } = editor;
	const { Fragment } = element;
	const { PanelBody, PanelRow, RangeControl, ToggleControl } = components;
	const { createHigherOrderComponent } = compose;

	// Restrict to specific block names.
	const allowedBlocks = [ 'core/cover', 'core/group' ];

	/**
	 * Add custom attributes.
	 *
	 * @param {Object} settings Settings for the block.
	 *
	 * @return {Object} settings Modified settings.
	 */
	const addContainerAttributes = function( settings ) {
		// Bail if it's another block than our defined ones.
		if ( ! allowedBlocks.includes( settings.name ) ) {
			return settings;
		}

		settings.attributes = Object.assign(
			settings.attributes,
			{
				fullContentWidth: {
					type: 'boolean',
					default: false
				},
				adjustContentWidth: {
					type: 'boolean',
					default: false
				},
				contentWidth: {
					type: 'number',
					default: 800
				}
			}
		);

		return settings;
	};

	/**
	 * Add custom controls on settings sidebar.
	 *
	 * @param {function} BlockEdit Block edit component.
	 *
	 * @return {function} BlockEdit Modified block edit component.
	 */
	const withWidthControls = createHigherOrderComponent( function( BlockEdit ) {
		return function( props ) {
			// Bail if it's another block than our defined ones.
			if ( ! allowedBlocks.includes( props.name ) ) {
				return el( Fragment,
					{},
					el(
						BlockEdit,
						props
					)
				);
			}

			return el( Fragment,
				{},
				el(
					BlockEdit,
					props
				),
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{
							className: 'content-width-options',
							title: __( 'Content width', 'ajv-blocks' ),
							initialOpen: true
						},
						false === props.attributes.adjustContentWidth ?
							el( PanelRow,
								{},
								el( ToggleControl,
									{
										label: __( 'Full width container', 'ajv-blocks' ),
										onChange: ( value ) => {
											props.setAttributes({ fullContentWidth: value });
										},
										checked: props.attributes.fullContentWidth
									}
								)
							) : null,
						false === props.attributes.fullContentWidth ?
							el( PanelRow,
								{},
								el( ToggleControl,
									{
										label: __( 'Adjust container width', 'ajv-blocks' ),
										onChange: ( value ) => {
											props.setAttributes({ adjustContentWidth: value });
										},
										checked: props.attributes.adjustContentWidth
									}
								)
							) : null,
						true === props.attributes.adjustContentWidth ?
							el( PanelRow,
								{},
								el( RangeControl,
									{
										min: 100,
										max: 2000,
										initialPosition: 800,
										value: props.attributes.contentWidth,
										label: __( 'Max-width in pixels', 'ajv-blocks' ),
										onChange: ( value ) => {
											100 < value ? props.setAttributes({ contentWidth: value }) : props.setAttributes({ contentWidth: 100 });
										}
									}
								)
							) : null
					)
				)
			);
		};
	}, 'withWidthControls' );

	/**
	 * Add custom attributes to block edit component.
	 *
	 * @param {function} BlockListBlock Block edit component and all toolbars.
	 *
	 * @return {function} BlockListBlock Modified block edit component and toolbars.
	 */
	const applyEditorWidthStyles = createHigherOrderComponent( function( BlockListBlock ) {
		return function( props ) {
			// Bail if it's another block than our defined ones.
			if ( ! allowedBlocks.includes( props.name ) ) {
				return el(
					BlockListBlock,
					props
				);
			}

			let containerClassName;
			let containerStyles;

			// Assign custom class.
			if ( true === props.attributes.fullContentWidth || true === props.attributes.adjustContentWidth ) {
				props = lodash.assign(
					{},
					props, {
						className: 'has-custom-inner-width',
					}
				);
			}

			// Define inner container class name.
			if ( 'core/cover' === props.name ) {
				containerClassName = '.wp-block-cover__inner-container';
			} else if ( 'core/group' === props.name ) {
				containerClassName = '.wp-block-group__inner-container';
			}

			// Define inline CSS styles.
			if ( true === props.attributes.fullContentWidth ) {
				containerStyles = '#block-' + props.clientId + '.has-custom-inner-width ' + containerClassName + '{ max-width: none !important }';
			} else if ( true === props.attributes.adjustContentWidth ) {
				containerStyles = '#block-' + props.clientId + '.has-custom-inner-width ' + containerClassName + '{ max-width: ' + props.attributes.contentWidth + 'px !important }';
			}

			// Assign wrapper <div> to output inline CSS styles.
			if ( true === props.attributes.fullContentWidth || true === props.attributes.adjustContentWidth ) {
				return el( 'div',
					{},
					el( 'style',
						{
							type: 'text/css'
						},
						containerStyles
					),
					el(
						BlockListBlock,
						props
					)
				);
			} else {
				return el(
					BlockListBlock,
					props
				);
			}
		};
	}, 'withWidthStyles' );

	/**
	 * Add custom attributes to block save component.
	 *
	 * @param {Object} extraProps Block element.
	 * @param {Object} blockType Blocks object.
	 * @param {Object} attributes Blocks attributes.
	 *
	 * @return {Object} extraProps Modified block element.
	 */
	const applyWidthStyles = function( extraProps, blockType, attributes ) {
		// Bail if it's another block than our defined ones.
		if ( ! allowedBlocks.includes( blockType.name ) ) {
			return extraProps;
		}

		// Assign inline CSS styles.
		if ( true === attributes.fullContentWidth ) {
			Object.assign(
				extraProps.style,
				{
					'--inner-container-width': 'none'
				}
			);
		} else if ( true === attributes.adjustContentWidth ) {
			Object.assign(
				extraProps.style,
				{
					'--inner-container-width': attributes.contentWidth + 'px'
				}
			);
		}

		// Assign custom class.
		if ( true === attributes.fullContentWidth || true === attributes.adjustContentWidth ) {
			extraProps.className = extraProps.className + ' has-custom-inner-width';
		}

		return extraProps;
	};

	// Add filters.
	addFilter(
		'blocks.registerBlockType',
		'ajvbl/add-container-attributes',
		addContainerAttributes
	);

	addFilter(
		'editor.BlockEdit',
		'ajvbl/with-width-controls',
		withWidthControls
	);

	addFilter(
		'editor.BlockListBlock',
		'ajvbl/apply-editor-width-styles',
		applyEditorWidthStyles
	);

	addFilter(
		'blocks.getSaveContent.extraProps',
		'ajvbl/apply-width-styles',
		applyWidthStyles
	);
}(
	window.wp.blockEditor,
	window.wp.element,
	window.wp.components,
	window.wp.compose,
	window.wp.hooks
) );

/**
 * Spacer block.
 *
 * Add responsive height setting controls.
 */
( function( editor, element, components, compose, hooks ) {
	const __ = wp.i18n.__;
	const el = element.createElement;
	const addFilter = hooks.addFilter;
	const { InspectorControls } = editor;
	const { Fragment } = element;
	const { PanelBody, PanelRow, RadioControl, RangeControl } = components;
	const { createHigherOrderComponent } = compose;

	// Restrict to specific block names.
	const allowedBlocks = [ 'core/spacer' ];

	/**
	 * Add custom attributes.
	 *
	 * @param {Object} settings Settings for the block.
	 *
	 * @return {Object} settings Modified settings.
	 */
	const addResponsiveAttributes = ( settings ) => {
		// Bail if it's another block than our defined ones.
		if ( ! allowedBlocks.includes( settings.name ) ) {
			return settings;
		}

		settings.attributes =
		Object.assign(
			settings.attributes,
			{
				responsiveBehavior: {
					type: 'string',
					default: 'default-height'
				},
				heightLarge: {
					type: 'number',
					default: 0
				},
				heightMedium: {
					type: 'number',
					default: 0
				},
				heightSmall: {
					type: 'number',
					default: 0
				}
			}
		);

		return settings;
	};

	/**
	 * Add custom controls on settings sidebar.
	 *
	 * @param {function} BlockEdit Block edit component.
	 *
	 * @return {function} BlockEdit Modified block edit component.
	 */
	const withResponsiveControls = createHigherOrderComponent( function( BlockEdit ) {
		return function( props ) {
			// Bail if it's another block than our defined ones.
			if ( ! allowedBlocks.includes( props.name ) ) {
				return el( Fragment,
					{},
					el(
						BlockEdit,
						props
					)
				);
			}

			return el( Fragment,
				{},
				el(
					BlockEdit,
					props
				),
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{
							className: 'responsive-setting-options',
							title: __( 'Responsive height settings', 'ajv-blocks' ),
							initialOpen: true
						},
						el( PanelRow,
							{},
							el( RadioControl,
								{
									options: [
										{
											label: __( 'Use the same spacer height on all screen sizes.', 'ajv-blocks' ),
											value: 'default-height'
										},
										{
											label: __( 'Specify custom spacer height for other screen sizes:', 'ajv-blocks' ),
											value: 'responsive-height'
										}
									],
									onChange: ( value ) => {
										props.setAttributes({ responsiveBehavior: value });
									},
									selected: props.attributes.responsiveBehavior
								}
							)
						),
						el( PanelRow,
							{},
							el( RangeControl,
								{
									min: 0,
									max: 500,
									initialPosition: 100,
									value: props.attributes.heightLarge,
									beforeIcon: 'laptop',
									label: __( 'Larger screens', 'ajv-blocks' ),
									onChange: ( value ) => {
										0 < value ? props.setAttributes({ heightLarge: value }) : props.setAttributes({ heightLarge: 0 });
									}
								}
							)
						),
						el( PanelRow,
							{},
							el( RangeControl,
								{
									min: 0,
									max: 500,
									initialPosition: 100,
									value: props.attributes.heightMedium,
									beforeIcon: 'tablet',
									label: __( 'Medium screens', 'ajv-blocks' ),
									onChange: ( value ) => {
										0 < value ? props.setAttributes({ heightMedium: value }) : props.setAttributes({ heightMedium: 0 });
									}
								}
							)
						),
						el( PanelRow,
							{},
							el( RangeControl,
								{
									min: 0,
									max: 500,
									initialPosition: 100,
									value: props.attributes.heightSmall,
									beforeIcon: 'smartphone',
									label: __( 'Small screens', 'ajv-blocks' ),
									onChange: ( value ) => {
										0 < value ? props.setAttributes({ heightSmall: value }) : props.setAttributes({ heightSmall: 0 });
									}
								}
							)
						)
					)
				)
			);
		};
	}, 'withResponsiveControls' );

	/**
	 * Add custom attributes to block save component.
	 *
	 * @param {Object} extraProps Block element.
	 * @param {Object} blockType Blocks object.
	 * @param {Object} attributes Blocks attributes.
	 *
	 * @return {Object} extraProps Modified block element.
	 */
	const applySpacerStyle = function( extraProps, blockType, attributes ) {
		// Bail if it's another block than our defined ones.
		if ( ! allowedBlocks.includes( blockType.name ) ) {
			return extraProps;
		}

		if ( 'responsive-height' === attributes.responsiveBehavior ) {
			Object.assign(
				extraProps.style,
				{
					'--spacer-large': attributes.heightLarge + 'px',
					'--spacer-medium': attributes.heightMedium + 'px',
					'--spacer-small': attributes.heightSmall + 'px'
				}
			);
		}

		return extraProps;
	};

	// Add filters.
	addFilter(
		'blocks.registerBlockType',
		'ajvbl/add-responsive-attributes',
		addResponsiveAttributes
	);

	addFilter(
		'editor.BlockEdit',
		'ajvbl/with-responsive-controls',
		withResponsiveControls
	);

	addFilter(
		'blocks.getSaveContent.extraProps',
		'ajvbl/apply-spacer-style',
		applySpacerStyle
	);
}(
	window.wp.blockEditor,
	window.wp.element,
	window.wp.components,
	window.wp.compose,
	window.wp.hooks
) );
