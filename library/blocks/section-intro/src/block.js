/**
 * Custom 'Section Intro' block.
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
	const { InspectorControls, RichText } = editor;
	const { Fragment } = element;
	const { PanelBody, PanelRow, RangeControl, SelectControl } = components;

	registerBlockType( 'ajvbl/section-intro', {
		title: __( 'Section Intro', 'ajv-blocks' ),
		description: __( 'Add a section intro with heading and paragraph.', 'ajv-blocks' ),
		icon: 'star-filled',
		category: 'ajv-blocks',
		keywords: [ 'Custom Block', 'Intro', 'Section Intro' ],
		attributes: {
			headingTag: {
				type: 'string',
				default: 'h2'
			},
			heading: {
				type: 'string',
				source: 'html',
				selector: 'h2'
			},
			text: {
				type: 'string',
				source: 'html',
				selector: 'p'
			},
			alignment: {
				type: 'string',
				default: 'flex-start'
			},
			maxWidth: {
				type: 'number',
				default: 800
			}
		},
		example: {
			attributes: {
				headingTag: 'h2',
				heading: __( 'Golden<br>Pheasant', 'ajv-blocks' ),
				text: __( 'Undoubtedly one of the most colorful birds on the planet. The male is adorned with a golden yellow crest, a scarlet red breast and a mix of rust and cobalt blue tail.', 'ajv-blocks' ),
				maxWidth: 800
			}
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
								title: __( 'Block settings', 'ajv-blocks' ),
								initialOpen: true
							},
							el( PanelRow,
								{},
								el( SelectControl,
									{
										label: __( 'Heading Tag', 'ajv-blocks' ),
										options: [
											{ label: 'H1', value: 'h1' },
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
							),
							el( PanelRow,
								{},
								el( SelectControl,
									{
										label: __( 'Block alignment', 'ajv-blocks' ),
										options: [
											{ label: 'Left', value: 'flex-start' },
											{ label: 'Center', value: 'center' },
											{ label: 'Right', value: 'flex-end' }
										],
										onChange: ( value ) => {
											props.setAttributes({ alignment: value });
										},
										value: props.attributes.alignment
									}
								)
							),
							el( PanelRow,
								{},
								el( RangeControl,
									{
										min: 500,
										max: 2000,
										initialPosition: 800,
										value: props.attributes.maxWidth,
										label: __( 'Max-width in pixels', 'ajv-blocks' ),
										onChange: ( value ) => {
											500 < value ? props.setAttributes({ maxWidth: value }) : props.setAttributes({ maxWidth: 500 });
										}
									}
								)
							),
						)
					)
				),
				el( 'div',
					{
						key: 'section-intro',
						className: props.className
					},
					el( 'div',
						{
							style: {
								display: 'flex',
								justifyContent: props.attributes.alignment
							}
						},
						el( 'div',
							{
								className: 'intro-content',
								style: {
									maxWidth: props.attributes.maxWidth + 'px'
								}
							},
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
							),
							el( RichText,
								{
									tagName: 'p',
									className: 'intro',
									value: props.attributes.text,
									allowedFormats: [ 'core/bold', 'core/italic', 'core/link' ],
									onChange: function( content ) {
										props.setAttributes({ text: content });
									},
									placeholder: __( 'Add content...', 'ajv-blocks' )
								}
							)
						)
					)
				)
			];
		},

		save: function( props ) {
			return (
				el( 'div',
					{
						className: props.className
					},
					el( 'div',
						{
							style: {
								display: 'flex',
								justifyContent: props.attributes.alignment
							}
						},
						el( 'div',
							{
								className: 'intro-content',
								style: {
									maxWidth: props.attributes.maxWidth + 'px'
								}
							},
							el( RichText.Content,
								{
									tagName: props.attributes.headingTag,
									className: 'heading',
									value: props.attributes.heading
								}
							),
							el( RichText.Content,
								{
									tagName: 'p',
									className: 'intro',
									value: props.attributes.text
								}
							)
						)
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
