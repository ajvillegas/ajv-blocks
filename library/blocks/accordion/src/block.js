/**
 * Custom 'Accordion' block.
 *
 * @package    AJV_Blocks
 * @subpackage AJV_Blocks/library
 * @author     Alexis J. Villegas
 * @link       https://www.alexisvillegas.com
 * @license    GPL-2.0+
 */

( function( blocks, editor, element, components ) {
	const __ = wp.i18n.__;
	const el = element.createElement;
	const registerBlockType = blocks.registerBlockType;
	const { InspectorControls, InnerBlocks, RichText } = editor;
	const { Fragment } = element;
	const { PanelBody, PanelRow, SelectControl } = components;
	const template = [
		[ 'core/paragraph', { placeholder: 'Add content...' } ]
	];

	registerBlockType( 'ajvbl/accordion', {
		title: __( 'Accordion', 'ajv-blocks' ),
		description: __( 'Add an accordion section.', 'ajv-blocks' ),
		icon: 'plus',
		category: 'ajv-blocks',
		keywords: [ 'Custom Block', 'Accordion', 'FAQ' ],
		attributes: {
			headingTag: {
				type: 'string',
				default: 'h3'
			},
			heading: {
				type: 'string',
				source: 'html',
				selector: 'h3'
			}
		},
		example: {
			attributes: {
				headingTag: 'h3',
				heading: __( 'How do bees pollinate flowers?', 'ajv-blocks' )
			},
			innerBlocks: [
				{
					name: 'core/paragraph',
					attributes: {
						content: __( 'When bees collect pollen and nectar from flowers, pollen from the male reproductive organ of the flower sticks to the hairs of the bee\'s body. Then upon landing on another flower for its pollen, the pollen sac falls off the bee and the pollen falls out of the sac. This is what creates the whole process of pollination.', 'ajv-blocks' )
					}
				}
			]
		},

		edit: function( props ) {
			return [
				el( Fragment,
					{
						key: 'fragment'
					},
					el( InspectorControls,
						{},
						el( PanelBody,
							{
								title: __( 'Accordion Settings', 'ajv-blocks' ),
								initialOpen: true
							},
							el( PanelRow,
								{},
								el( SelectControl,
									{
										label: __( 'Heading Tag', 'ajv-blocks' ),
										options: [
											{ label: 'H2', value: 'h2' },
											{ label: 'H3', value: 'h3' },
											{ label: 'H4', value: 'h4' },
											{ label: 'H5', value: 'h5' },
											{ label: 'H6', value: 'h6' }
										],
										onChange: ( value ) => {
											props.setAttributes({ headingTag: value });
										},
										value: props.attributes.headingTag
									}
								)
							)
						)
					)
				),
				el( 'div',
					{
						key: 'accordion-block',
						className: props.className
					},
					el( 'div',
						{
							className: 'accordion-header'
						},
						el( 'span',
							{
								className: 'accordion-toggle'
							}
						),
						el( RichText,
							{
								tagName: props.attributes.headingTag,
								className: 'heading',
								value: props.attributes.heading,
								allowedFormats: [],
								onChange: function( content ) {
									props.setAttributes({ heading: content });
								},
								placeholder: __( 'Add heading...', 'ajv-blocks' )
							}
						)
					),
					el( 'div',
						{
							className: 'accordion-content'
						},
						el( InnerBlocks,
							{
								template: template,
								templateLock: false
							}
						)
					)
				)
			];
		},

		save: function( props ) {
			return (
				el( 'details',
					{
						className: props.className
					},
					el( 'summary',
						{},
						el( 'span',
							{
								className: 'accordion-toggle'
							}
						),
						el( RichText.Content,
							{
								tagName: props.attributes.headingTag,
								className: 'heading',
								value: props.attributes.heading
							}
						)
					),
					el( 'div',
						{},
						el( InnerBlocks.Content )
					)
				)
			);
		}
	});
}(
	window.wp.blocks,
	window.wp.blockEditor,
	window.wp.element,
	window.wp.components
) );
