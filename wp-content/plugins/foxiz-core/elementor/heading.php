<?php

namespace foxizElementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use foxizElementorControl\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Foxiz_Heading
 * @package foxizElementor\Widgets
 */
class Block_Heading extends Widget_Base {

	public function get_name() {

		return 'foxiz-heading';
	}

	public function get_title() {

		return esc_html__( 'Foxiz - Heading', 'foxiz-core' );
	}

	public function get_icon() {

		return 'eicon-favorite';
	}

	public function get_categories() {

		return array( 'foxiz' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_title', array(
				'label' => esc_html__( 'Content', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Heading', 'foxiz-core' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'description' => esc_html__( 'Input a heading, Support the i tag (raw HTML) for displaying icon. e.g: <i class="rbi rbi-trending"></i> Your Heading', 'foxiz-core' ),
				'default'     => esc_html__( 'Latest News', 'foxiz-core' ),
			)
		);
		$this->add_control(
			'tagline',
			array(
				'label'       => esc_html__( 'Tagline', 'foxiz-core' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'description' => esc_html__( 'Input a tagline text for this heading block.', 'foxiz-core' ),
				'default'     => '',
			)
		);
		$this->add_control(
			'link',
			array(
				'label' => esc_html__( 'Custom Link', 'foxiz-core' ),
				'type'  => Controls_Manager::URL
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'heading_style_section', array(
				'label' => esc_html__( 'Heading', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'html_tag',
			array(
				'label'   => esc_html__( 'Heading HTML Tag', 'foxiz-core' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'span' => 'span',
					'p'    => 'p',
				),
				'default' => 'h2',
			)
		);
		$this->add_responsive_control(
			'heading_size',
			array(
				'label'       => esc_html__( 'Font Size', 'foxiz-core' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Input a custom font size value (px) for this heading. Leave this option blank to set the default value.', 'foxiz-core' ),
				'default'     => '',
				'devices'     => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'   => array(
					'{{WRAPPER}} .heading-title > *' => 'font-size: {{VALUE}}px;'
				)
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Heading Font', 'foxiz-core' ),
				'name'     => 'title_font',
				'selector' => '{{WRAPPER}} .heading-title > *',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'tagline_section', array(
				'label' => esc_html__( 'Tagline', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'tagline_size',
			array(
				'label'       => esc_html__( 'Tagline - Font Size', 'foxiz-core' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Input a custom font size value (px) for this tagline. Leave this option blank to set the default value.', 'foxiz-core' ),
				'default'     => '',
				'devices'     => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'   => array(
					'{{WRAPPER}} .heading-tagline > *' => 'font-size: {{VALUE}}px;'
				)
			)
		);
		$this->add_control(
			'tagline_arrow',
			array(
				'label'       => esc_html__( 'Tagline Arrow', 'foxiz-core' ),
				'description' => esc_html__( 'Show an arrow icon at the right of the tagline.', 'foxiz-core' ),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes'
			)
		);
		$this->add_control(
			'tagline_color',
			array(
				'label'       => esc_html__( 'Tagline Text Color', 'foxiz-core' ),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__( 'Select a text color for the tagline.', 'foxiz-core' ),
				'default'     => '',
				'selectors'   => array(
					'{{WRAPPER}} ' => '--heading-tagline-color: {{VALUE}};'
				)
			)
		);
		$this->add_control(
			'dark_tagline_color',
			array(
				'label'       => esc_html__( 'Dark Mode - Tagline Text Color', 'foxiz-core' ),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__( 'Select a text color for the tagline in the dark mode.', 'foxiz-core' ),
				'default'     => '',
				'selectors'   => array(
					'[data-theme="dark"] {{WRAPPER}}, {{WRAPPER}} .light-scheme' => '--heading-tagline-color: {{VALUE}};'
				)
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Custom Tagline Font', 'foxiz-core' ),
				'name'     => 'category_font',
				'selector' => '{{WRAPPER}} .heading-tagline > *',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_color', array(
				'label' => esc_html__( 'Colors', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'primary_color',
			array(
				'label'       => esc_html__( 'Primary Color', 'foxiz-core' ),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__( 'Select a primary color for this heading.', 'foxiz-core' ),
				'selectors'   => array(
					'{{WRAPPER}} .heading-title' => '--heading-color: {{VALUE}};'
				)
			)
		);
		$this->add_control(
			'accent_color',
			array(
				'label'       => esc_html__( 'Accent Color', 'foxiz-core' ),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__( 'Select a accent color for this heading.', 'foxiz-core' ),
				'default'     => '',
				'selectors'   => array(
					'{{WRAPPER}}' => '--heading-sub-color: {{VALUE}};'
				)
			)
		);
		$this->add_control(
			'dark_primary_color',
			array(
				'label'       => esc_html__( 'Dark Mode - Primary Color', 'foxiz-core' ),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__( 'Select a primary color for this heading in the dark mode.', 'foxiz-core' ),
				'selectors'   => array(
					'[data-theme="dark"] {{WRAPPER}} .heading-title, {{WRAPPER}} .light-scheme .heading-title' => '--heading-color: {{VALUE}};'
				)
			)
		);

		$this->add_control(
			'dark_accent_color',
			array(
				'label'       => esc_html__( 'Dark Mode - Accent Color', 'foxiz-core' ),
				'type'        => Controls_Manager::COLOR,
				'description' => esc_html__( 'Select a accent color for this heading in the dark mode.', 'foxiz-core' ),
				'default'     => '',
				'selectors'   => array(
					'[data-theme="dark"] {{WRAPPER}}, {{WRAPPER}} .light-scheme' => '--heading-sub-color: {{VALUE}};'
				)
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'color_section', array(
				'label' => esc_html__( 'Text Color Scheme', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'color_scheme_info',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => Options::color_scheme_info_description(),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-success',
			)
		);
		$this->add_control(
			'color_scheme',
			array(
				'label'       => esc_html__( 'Text Color Scheme', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::color_scheme_description(),
				'options'     => array(
					'0' => esc_html__( 'Default (Dark Text)', 'foxiz-core' ),
					'1' => esc_html__( 'Light Text', 'foxiz-core' )
				),
				'default'     => '0',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'layout_section', array(
				'label' => esc_html__( 'Layouts', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_LAYOUT,
			)
		);
		$this->add_control(
			'layout',
			array(
				'label'       => esc_html__( 'Layout', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => esc_html__( 'Select a layout for this heading block.', 'foxiz-core' ),
				'options'     => array(
					'0'   => esc_html__( '- Default -', 'foxiz-core' ),
					'1'   => esc_html__( '01 - Two Slashes', 'foxiz-core' ),
					'2'   => esc_html__( '02 - Left Dot', 'foxiz-core' ),
					'3'   => esc_html__( '03 - Bold Underline', 'foxiz-core' ),
					'4'   => esc_html__( '04 - Multiple Underline', 'foxiz-core' ),
					'5'   => esc_html__( '05 - Top Line', 'foxiz-core' ),
					'6'   => esc_html__( '06 - Parallelogram Background', 'foxiz-core' ),
					'7'   => esc_html__( '07 - Left Border', 'foxiz-core' ),
					'8'   => esc_html__( '08 - Half Elegant Background', 'foxiz-core' ),
					'9'   => esc_html__( '09 - Small Corners', 'foxiz-core' ),
					'10'  => esc_html__( '10 - Only Text', 'foxiz-core' ),
					'11'  => esc_html__( '11 - Big Overlay Tagline', 'foxiz-core' ),
					'12'  => esc_html__( '12 - Mixed Underline', 'foxiz-core' ),
					'13'  => esc_html__( '13 - Rectangle Background', 'foxiz-core' ),
					'14'  => esc_html__( '14 - Top Solid', 'foxiz-core' ),
					'15'  => esc_html__( '15 - Top & Bottom Solid', 'foxiz-core' ),
					'16'  => esc_html__( '16 - Mixed Background', 'foxiz-core' ),
					'17'  => esc_html__( '17 - Centered Solid', 'foxiz-core' ),
					'18'  => esc_html__( '18 - Centered Dotted', 'foxiz-core' ),
					'c1'  => esc_html__( 'Center 01 - Two Slashes', 'foxiz-core' ),
					'c2'  => esc_html__( 'Center 02 - Two Dots', 'foxiz-core' ),
					'c3'  => esc_html__( 'Center 03 - Underline', 'foxiz-core' ),
					'c4'  => esc_html__( 'Center 04 - Bold Underline', 'foxiz-core' ),
					'c5'  => esc_html__( 'Center 05 - Top Line', 'foxiz-core' ),
					'c6'  => esc_html__( 'Center 06 - Parallelogram Background', 'foxiz-core' ),
					'c7'  => esc_html__( 'Center 07 - Two Square Dots', 'foxiz-core' ),
					'c8'  => esc_html__( 'Center 08 - Elegant Lines', 'foxiz-core' ),
					'c9'  => esc_html__( 'Center 09 - Small Corners', 'foxiz-core' ),
					'c10' => esc_html__( 'Center 10 - Only Text', 'foxiz-core' ),
					'c11' => esc_html__( 'Center 11 - Big Overlay Tagline', 'foxiz-core' ),
					'c12' => esc_html__( 'Center 12 - Mixed Underline', 'foxiz-core' ),
					'c13' => esc_html__( 'Center 13 - Rectangle Background', 'foxiz-core' ),
					'c14' => esc_html__( 'Center 14 - Top Solid', 'foxiz-core' ),
					'c15' => esc_html__( 'Center 15 - Top & Bottom Solid', 'foxiz-core' ),
				),
				'default'     => '0',
			)
		);
		$this->add_responsive_control(
			'heading_spacing',
			array(
				'label'       => esc_html__( 'Spacing', 'foxiz-core' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Input a custom spacing value (px) value between the heading text and graphic elements (line, border...).', 'foxiz-core' ),
				'default'     => '',
				'devices'     => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'   => array(
					'{{WRAPPER}}' => '--heading-spacing: {{VALUE}}px;'
				)
			)
		);
		$this->add_responsive_control(
			'tagline_margin',
			array(
				'label'       => esc_html__( 'Center Layouts - Tagline Top Margin', 'foxiz-core' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Input a custom top margin value (px) for the tagline in center layouts.', 'foxiz-core' ),
				'default'     => '',
				'devices'     => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'   => array(
					'{{WRAPPER}}' => '--heading-tagline-margin: {{VALUE}}px;'
				)
			)
		);
		$this->end_controls_section();
	}

	/**
	 * render layout
	 */
	protected function render() {

		if ( function_exists( 'foxiz_get_heading' ) ) {
			$settings         = $this->get_settings();
			$settings['uuid'] = 'uid_' . $this->get_id();

			echo \foxiz_get_heading( $settings );
		}
	}
}