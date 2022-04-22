<?php
/**
 * Tests the Style Engine class and associated functionality.
 *
 * @package    Gutenberg
 * @subpackage style-engine
 */

require __DIR__ . '/../class-wp-style-engine.php';

/**
 * Tests for registering, storing and generating styles.
 */
class WP_Style_Engine_Test extends WP_UnitTestCase {
	/**
	 * Tests generating styles and classnames based on various manifestations of the $block_styles argument.
	 *
	 * @dataProvider data_generate_styles_fixtures
	 */
	function test_generate_styles( $block_styles, $expected_output ) {
		$style_engine     = wp_get_style_engine();
		$generated_styles = $style_engine->generate( $block_styles );
		$this->assertSame( $expected_output, $generated_styles );
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function data_generate_styles_fixtures() {
		return array(
			'default_return_value'                         => array(
				'block_styles'    => array(),
				'expected_output' => null,
			),

			'inline_invalid_block_styles_empty'            => array(
				'block_styles'    => 'hello world!',
				'expected_output' => null,
			),

			'inline_invalid_block_styles_unknown_style'    => array(
				'block_styles'    => array(
					'pageBreakAfter' => 'verso',
				),
				'expected_output' => array(),
			),

			'inline_invalid_block_styles_unknown_definition' => array(
				'block_styles'    => array(
					'pageBreakAfter' => 'verso',
				),
				'expected_output' => array(),
			),

			'inline_invalid_block_styles_unknown_property' => array(
				'block_styles'    => array(
					'spacing' => array(
						'gap' => '1000vw',
					),
				),
				'expected_output' => array(),
			),

			'valid_inline_css_and_classnames'              => array(
				'block_styles'    => array(
					'color'   => array(
						'text' => 'var:preset|color|texas-flood',
					),
					'spacing' => array(
						'margin' => '111px',
					),
					'border'  => array(
						'color' => 'var:preset|border-color|cool-caramel',
						'width' => '2rem',
						'style' => 'dotted',
					),
				),
				'expected_output' => array(
					'css'        => 'border-style: dotted; border-width: 2rem; margin: 111px;',
					'classnames' => 'has-text-color has-texas-flood-color has-border-color has-cool-caramel-border-color',
				),
			),

			'inline_valid_box_model_style'                 => array(
				'block_styles'    => array(
					'spacing' => array(
						'padding' => array(
							'top'    => '42px',
							'left'   => '2%',
							'bottom' => '44px',
							'right'  => '5rem',
						),
						'margin'  => array(
							'top'    => '12rem',
							'left'   => '2vh',
							'bottom' => '2px',
							'right'  => '10em',
						),
					),
					'border'  => array(
						'radius' => array(
							'topLeft'     => '99px',
							'topRight'    => '98px',
							'bottomLeft'  => '97px',
							'bottomRight' => '96px',
						),
					),
				),
				'expected_output' => array(
					'css' => 'border-top-left-radius: 99px; border-top-right-radius: 98px; border-bottom-left-radius: 97px; border-bottom-right-radius: 96px; padding-top: 42px; padding-left: 2%; padding-bottom: 44px; padding-right: 5rem; margin-top: 12rem; margin-left: 2vh; margin-bottom: 2px; margin-right: 10em;',
				),
			),

			'inline_valid_typography_style'                => array(
				'block_styles'    => array(
					'typography' => array(
						'fontSize'       => 'clamp(2em, 2vw, 4em)',
						'fontFamily'     => 'Roboto,Oxygen-Sans,Ubuntu,sans-serif',
						'fontStyle'      => 'italic',
						'fontWeight'     => '800',
						'lineHeight'     => '1.3',
						'textDecoration' => 'underline',
						'textTransform'  => 'uppercase',
						'letterSpacing'  => '2',
					),
				),
				'expected_output' => array(
					'css' => 'font-family: Roboto,Oxygen-Sans,Ubuntu,sans-serif; font-style: italic; font-weight: 800; line-height: 1.3; text-decoration: underline; text-transform: uppercase; letter-spacing: 2;',
				),
			),

			'valid_classnames_deduped'                     => array(
				'block_styles'    => array(
					'color'      => array(
						'text'       => 'var:preset|color|copper-socks',
						'background' => 'var:preset|background-color|splendid-carrot',
						'gradient'   => 'var:preset|background|like-wow-dude',
					),
					'typography' => array(
						'fontSize'   => 'var:preset|font-size|fantastic',
						'fontFamily' => 'var:preset|font-family|totally-awesome',
					),
				),
				'expected_output' => array(
					'classnames' => 'has-text-color has-copper-socks-color has-background has-splendid-carrot-background-color has-like-wow-dude-gradient-background has-fantastic-font-size has-totally-awesome-font-family',
				),
			),

			'valid_classnames_with_null_style_values'      => array(
				'block_styles'    => array(
					'color' => array(
						'text'       => '#fff',
						'background' => null,
					),
				),
				'expected_output' => array(
					'css'        => 'color: #fff;',
					'classnames' => 'has-text-color',
				),
			),

			'invalid_classnames_preset_value'              => array(
				'block_styles'    => array(
					'color'   => array(
						'text'       => 'var:cheese|color|fantastic',
						'background' => 'var:preset|fromage|fantastic',
					),
					'spacing' => array(
						'margin'  => 'var:cheese|spacing|margin',
						'padding' => 'var:preset|spacing|padding',
					),
				),
				'expected_output' => array(
					'classnames' => 'has-text-color has-background',
				),
			),

			'invalid_classnames_options'                   => array(
				'block_styles'    => array(
					'typography' => array(
						'fontSize'   => array(
							'tomodachi' => 'friends',
						),
						'fontFamily' => array(
							'oishii' => 'tasty',
						),
					),
				),
				'expected_output' => array(),
			),

			'inline_valid_box_model_style_with_sides'      => array(
				'block_styles'    => array(
					'border' => array(
						'top'    => array(
							'color' => '#fe1',
							'width' => '1.5rem',
							'style' => 'dashed',
						),
						'right'  => array(
							'color' => '#fe2',
							'width' => '1.4rem',
							'style' => 'solid',
						),
						'bottom' => array(
							'color' => '#fe3',
							'width' => '1.3rem',
						),
						'left'   => array(
							'color' => 'var:preset|color|swampy-yellow',
							'width' => '0.5rem',
							'style' => 'dotted',
						),
					),
				),
				'expected_output' => array(
					'css' => 'border-top-color: #fe1; border-top-width: 1.5rem; border-top-style: dashed; border-right-color: #fe2; border-right-width: 1.4rem; border-right-style: solid; border-bottom-color: #fe3; border-bottom-width: 1.3rem; border-left-color: var(--wp--preset--color--swampy-yellow); border-left-width: 0.5rem; border-left-style: dotted;',
				),
			),
		);
	}
}
