<?php

/**
 * Test the typography block supports.
 *
 * @package Gutenberg
 */

class WP_Block_Supports_Colors_Test extends WP_UnitTestCase {
	/**
	 * @var string|null
	 */
	private $test_block_name;

	function setUp() {
		parent::setUp();
		$this->test_block_name = null;
	}

	function tearDown() {
		unregister_block_type( $this->test_block_name );
		$this->test_block_name = null;
		parent::tearDown();
	}

	function test_color_slugs_with_numbers_are_kebab_cased_properly() {
		$this->test_block_name = 'test/color-slug-with-numbers';
		register_block_type(
			$this->test_block_name,
			array(
				'api_version' => 2,
				'attributes'  => array(
					'textColor'       => array(
						'type' => 'string',
					),
					'backgroundColor' => array(
						'type' => 'string',
					),
					'gradient'        => array(
						'type' => 'string',
					),
				),
				'supports'    => array(
					'color' => array(
						'text'       => true,
						'background' => true,
						'gradients'  => true,
					),
				),
			)
		);
		$registry   = WP_Block_Type_Registry::get_instance();
		$block_type = $registry->get_registered( $this->test_block_name );

		$block_atts = array(
			'textColor'       => 'fg1',
			'backgroundColor' => 'bg2',
			'gradient'        => 'gr3',
		);

		$actual   = gutenberg_apply_colors_support( $block_type, $block_atts );
		$expected = array( 'class' => 'has-text-color has-fg-1-color has-background has-bg-2-background-color has-background has-gr-3-gradient-background' );

		$this->assertSame( $expected, $actual );
	}

	function test_color_with_skipped_serialization_block_supports() {
		$this->test_block_name = 'test/color-with-skipped-serialization-block-supports';
		register_block_type(
			$this->test_block_name,
			array(
				'api_version' => 2,
				'attributes'  => array(
					'style' => array(
						'type' => 'object',
					),
				),
				'supports'    => array(
					'color' => array(
						'text'                            => true,
						'gradients'                       => true,
						'__experimentalSkipSerialization' => true,
					),
				),
			)
		);

		$registry   = WP_Block_Type_Registry::get_instance();
		$block_type = $registry->get_registered( $this->test_block_name );
		$block_atts = array(
			'style' => array(
				'color' => array(
					'text'     => '#d92828',
					'gradient' => 'linear-gradient(135deg,rgb(6,147,227) 0%,rgb(223,13,13) 46%,rgb(155,81,224) 100%)',
				),
			),
		);

		$actual   = gutenberg_apply_colors_support( $block_type, $block_atts );
		$expected = array();

		$this->assertSame( $expected, $actual );
	}

	function test_gradient_with_individual_skipped_serialization_block_supports() {
		$this->test_block_name = 'test/gradient-with-individual-skipped-serialization-block-support';
		register_block_type(
			$this->test_block_name,
			array(
				'api_version' => 2,
				'attributes'  => array(
					'style' => array(
						'type' => 'object',
					),
				),
				'supports'    => array(
					'color' => array(
						'text'                            => true,
						'gradients'                       => true,
						'__experimentalSkipSerialization' => array( 'gradients' ),
					),
				),
			)
		);

		$registry   = WP_Block_Type_Registry::get_instance();
		$block_type = $registry->get_registered( $this->test_block_name );
		$block_atts = array(
			'style' => array(
				'color' => array(
					'text' => '#d92828',
				),
			),
		);

		$actual   = gutenberg_apply_colors_support( $block_type, $block_atts );
		$expected = array(
			'class' => 'has-text-color',
			'style' => 'color: #d92828;',
		);

		$this->assertSame( $expected, $actual );
	}
}
