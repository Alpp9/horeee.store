<?php

namespace foxizElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use foxizElementorControl\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Hierarchical_3
 * @package foxizElementor\Widgets
 */
class Hierarchical_3 extends Widget_Base {

	public function get_name() {

		return 'foxiz-hierarchical-3';
	}

	public function get_title() {

		return esc_html__( 'Foxiz - Hierarchical 3', 'foxiz-core' );
	}

	public function get_icon() {

		return 'eicon-favorite';
	}

	public function get_categories() {

		return array( 'foxiz' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'query_filters', array(
				'label' => esc_html__( 'Query Settings', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		if ( \foxiz_is_ruby_template() ) {
			$this->add_control(
				'dynamic_query_info',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => Options::dynamic_query_info(),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-success',
				)
			);
			$this->add_control(
				'dynamic_query_category_info',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => Options::dynamic_query_category_info(),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				)
			);
			$this->add_control(
				'category',
				array(
					'label'       => esc_html__( 'Category Filter', 'foxiz-core' ),
					'type'        => Controls_Manager::SELECT,
					'description' => Options::category_description(),
					'options'     => Options::cat_dropdown( true ),
					'default'     => '0',
				)
			);
		} else {
			$this->add_control(
				'category',
				array(
					'label'       => esc_html__( 'Category Filter', 'foxiz-core' ),
					'type'        => Controls_Manager::SELECT,
					'description' => Options::category_description(),
					'options'     => Options::cat_dropdown(),
					'default'     => '0',
				)
			);
		}
		$this->add_control(
			'categories',
			array(
				'label'       => esc_html__( 'Categories Filter', 'foxiz-core' ),
				'type'        => Controls_Manager::TEXT,
				'description' => Options::categories_description(),
				'default'     => '',
			)
		);
		$this->add_control(
			'tags',
			array(
				'label'       => esc_html__( 'Tags Slug Filter', 'foxiz-core' ),
				'type'        => Controls_Manager::TEXT,
				'description' => Options::tags_description(),
				'default'     => '',
			)
		);
		$this->add_control(
			'tag_not_in',
			array(
				'label'       => esc_html__( 'Exclude Tags Slug', 'foxiz-core' ),
				'type'        => Controls_Manager::TEXT,
				'description' => Options::tag_not_in_description(),
				'default'     => '',
			)
		);
		$this->add_control(
			'format',
			array(
				'label'       => esc_html__( 'Post Format', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::format_description(),
				'options'     => Options::format_dropdown(),
				'default'     => '0',
			)
		);
		$this->add_control(
			'author',
			array(
				'label'       => esc_html__( 'Author Filter', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::author_description(),
				'options'     => Options::author_dropdown(),
				'default'     => '0',
			)
		);
		$this->add_control(
			'post_not_in',
			array(
				'label'       => esc_html__( 'Exclude Post IDs', 'foxiz-core' ),
				'type'        => Controls_Manager::TEXT,
				'description' => Options::post_not_in_description(),
				'default'     => '',
			)
		);
		$this->add_control(
			'post_in',
			array(
				'label'       => esc_html__( 'Post IDs Filter', 'foxiz-core' ),
				'type'        => Controls_Manager::TEXT,
				'description' => Options::post_in_description(),
				'default'     => '',
			)
		);
		$this->add_control(
			'order',
			array(
				'label'       => esc_html__( 'Sort Order', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::order_description(),
				'options'     => Options::order_dropdown(),
				'default'     => 'date_post',
			)
		);
		$this->add_control(
			'posts_per_page',
			array(
				'label'       => esc_html__( 'Number of Posts', 'foxiz-core' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => Options::posts_per_page_description(),
				'default'     => '5',
			)
		);
		$this->add_control(
			'offset',
			array(
				'label'       => esc_html__( 'Post Offset', 'foxiz-core' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => Options::offset_description(),
				'default'     => '',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'unique_section', array(
				'label' => esc_html__( 'Unique Post', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);
		$this->add_control(
			'unique',
			array(
				'label'       => esc_html__( 'Unique Post', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::unique_description(),
				'options'     => Options::switch_dropdown( false ),
				'default'     => '-1',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'featured_image_section', array(
				'label' => esc_html__( 'Featured Image', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'crop_size',
			array(
				'label'       => esc_html__( 'Featured Image Size', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::crop_size(),
				'options'     => Options::crop_size_dropdown(),
				'default'     => '0',
			)
		);
		$this->add_responsive_control(
			'display_ratio', array(
				'label'       => esc_html__( 'Custom Featured Ratio', 'foxiz-core' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => Options::display_ratio_description(),
				'selectors'   => array(
					'{{WRAPPER}} .p-featured' => 'padding-bottom: {{VALUE}}%',
				),
			)
		);
		$this->add_responsive_control(
			'feat_spacing', array(
				'label'       => esc_html__( 'Custom Featured Margin', 'foxiz-core' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => Options::featured_spacing_description(),
				'devices'     => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'   => array( '{{WRAPPER}} .p-wrap' => '--featured-spacing: {{VALUE}}px;' ),
			)
		);
		$this->add_control(
			'box_border',
			array(
				'label'       => esc_html__( 'Border Radius', 'foxiz-core' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => Options::border_description(),
				'selectors'   => array(
					'{{WRAPPER}} .p-wrap' => '--wrap-border: {{VALUE}}px;',
				),
			)
		);
		$this->add_control(
			'feat_hover',
			array(
				'label'       => esc_html__( 'Hover Effect', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::feat_hover_description(),
				'options'     => Options::feat_hover_dropdown(),
				'default'     => '0',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'entry_category_section', array(
				'label' => esc_html__( 'Entry Category', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'entry_category',
			array(
				'label'       => esc_html__( 'Entry Category', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::entry_category_description(),
				'options'     => Options::extended_entry_category_dropdown( false ),
				'default'     => 'bg-1',
			)
		);
		$this->add_responsive_control(
			'entry_category_size', array(
				'label'       => esc_html__( 'Entry Category Size', 'foxiz-core' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => Options::entry_category_size_description(),
				'devices'     => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'   => array( '{{WRAPPER}} .p-category' => 'font-size: {{VALUE}}px !important;' ),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Custom Entry Category Font', 'foxiz-core' ),
				'name'     => 'category_font',
				'selector' => '{{WRAPPER}} .p-categories',
			)
		);
		$this->add_control(
			'hide_category',
			array(
				'label'       => esc_html__( 'Hide Entry Category', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::hide_category_description(),
				'options'     => Options::hide_dropdown( false ),
				'default'     => '0',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'entry_title_section', array(
				'label' => esc_html__( 'Post Title', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'title_tag',
			array(
				'label'       => esc_html__( 'Title HTML Tag', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::heading_html_description(),
				'options'     => Options::heading_html_dropdown(),
				'default'     => '0',
			)
		);
		$this->add_control(
			'sub_title_tag',
			array(
				'label'       => esc_html__( 'Secondary Title HTML Tag', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::sub_heading_html_description(),
				'options'     => Options::heading_html_dropdown(),
				'default'     => '0',
			)
		);
		$this->add_responsive_control(
			'title_tag_size', array(
				'label'       => esc_html__( 'Title Font Size', 'foxiz-core' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => Options::title_size_description(),
				'devices'     => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'   => array( '{{WRAPPER}} .entry-title' => 'font-size: {{VALUE}}px;' ),
			)
		);
		$this->add_responsive_control(
			'sub_title_tag_size', array(
				'label'       => esc_html__( 'Secondary Title Font Size', 'foxiz-core' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => Options::sub_title_size_description(),
				'devices'     => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'   => array( '{{WRAPPER}} .p-list-inline > .entry-title' => 'font-size: {{VALUE}}px;' ),
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Custom Post Title Font', 'foxiz-core' ),
				'name'     => 'title_font',
				'selector' => '{{WRAPPER}} .entry-title',
			)
		);
		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'label'    => esc_html__( 'Secondary Post Title Font', 'foxiz-core' ),
				'name'     => 'sub_title_font',
				'selector' => '{{WRAPPER}} .p-list-inline > .entry-title',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'entry_meta_section', array(
				'label' => esc_html__( 'Entry Meta', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'entry_meta',
			array(
				'label'       => esc_html__( 'Entry Meta Tags', 'foxiz-core' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'description' => Options::entry_meta_tags_description(),
				'placeholder' => Options::entry_meta_tags_placeholder(),
				'default'     => 'avatar,author,update'
			)
		);
		$this->add_control(
			'review',
			array(
				'label'       => esc_html__( 'Review Meta', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::review_description(),
				'options'     => Options::review_dropdown( false ),
				'default'     => 'replace',
			)
		);
		$this->add_control(
			'review_meta',
			array(
				'label'       => esc_html__( 'Review Meta Description', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::review_meta_description(),
				'options'     => Options::switch_dropdown( false ),
				'default'     => '1',
			)
		);
		$this->add_control(
			'sponsor_meta',
			array(
				'label'       => esc_html__( 'Sponsored Meta', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::sponsor_meta_description(),
				'options'     => Options::sponsor_dropdown( false ),
				'default'     => '1',
			)
		);
		$this->add_responsive_control(
			'entry_meta_size', array(
				'label'       => esc_html__( 'Entry Meta Size', 'foxiz-core' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => Options::entry_category_size_description(),
				'devices'     => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'   => array( '{{WRAPPER}} .is-meta' => 'font-size: {{VALUE}}px;' ),
			)
		);
		$this->add_responsive_control(
			'avatar_size', array(
				'label'       => esc_html__( 'Author Avatar Size', 'foxiz-core' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => Options::avatar_size_description(),
				'devices'     => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'   => array( 'body {{WRAPPER}} .meta-avatar img' => 'width: {{VALUE}}px; height: {{VALUE}}px;' ),
			)
		);
		$this->add_control(
			'tablet_hide_meta',
			array(
				'label'       => esc_html__( 'Hide Entry Meta on Tablet', 'foxiz-core' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'description' => Options::tablet_hide_meta_description(),
				'placeholder' => esc_html__( 'avatar, author', 'foxiz-core' ),
				'default'     => array()
			)
		);
		$this->add_control(
			'mobile_hide_meta',
			array(
				'label'       => esc_html__( 'Hide Entry Meta on Mobile', 'foxiz-core' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'description' => Options::mobile_hide_meta_description(),
				'placeholder' => esc_html__( 'avatar, author', 'foxiz-core' ),
				'default'     => array()
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'block_design', array(
				'label' => esc_html__( 'Bookmark', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'bookmark',
			array(
				'label'       => esc_html__( 'Bookmark Icon', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::bookmark_description(),
				'options'     => Options::switch_dropdown( false ),
				'default'     => '-1',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'entry_format_section', array(
				'label' => esc_html__( 'Post Format', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'entry_format',
			array(
				'label'       => esc_html__( 'Post Format Icon', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::entry_format_description(),
				'options'     => Options::entry_format_dropdown( false ),
				'default'     => '-1',
			)
		);
		$this->add_responsive_control(
			'entry_format_size', array(
				'label'       => esc_html__( 'Icon Size', 'foxiz-core' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => Options::entry_format_size_description(),
				'devices'     => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'   => array( '{{WRAPPER}} .p-format' => 'font-size: {{VALUE}}px !important;' ),
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'excerpt_section', array(
				'label' => esc_html__( 'Excerpt', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'excerpt_length',
			array(
				'label'       => esc_html__( 'Excerpt - Max Length', 'foxiz-core' ),
				'type'        => Controls_Manager::TEXT,
				'description' => Options::max_excerpt_description(),
				'default'     => '',
			)
		);
		$this->add_control(
			'excerpt_source',
			array(
				'label'       => esc_html__( 'Excerpt - Source', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::excerpt_source_description(),
				'options'     => Options::excerpt_source_dropdown(),
				'default'     => '0',
			)
		);
		$this->add_responsive_control(
			'entry_excerpt_size', array(
				'label'       => esc_html__( 'Entry Excerpt Size', 'foxiz-core' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => Options::excerpt_size_description(),
				'devices'     => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'   => array( '{{WRAPPER}} .entry-summary' => 'font-size: {{VALUE}}px;' ),
			)
		);
		$this->add_control(
			'hide_excerpt',
			array(
				'label'       => esc_html__( 'Hide Excerpt', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::hide_excerpt_description(),
				'options'     => Options::hide_dropdown( false ),
				'default'     => '0',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'readmore_section', array(
				'label' => esc_html__( 'Read More', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'readmore',
			array(
				'label'       => esc_html__( 'Read More Button', 'foxiz-core' ),
				'type'        => Controls_Manager::SELECT,
				'description' => Options::readmore_description(),
				'options'     => Options::switch_dropdown( false ),
				'default'     => '-1',
			)
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'spacing_section', array(
				'label' => esc_html__( 'Spacing', 'foxiz-core' ),
				'tab'   => Controls_Manager::TAB_LAYOUT,
			)
		);
		$this->add_responsive_control(
			'el_spacing', array(
				'label'       => esc_html__( 'Custom Element Spacing', 'foxiz-core' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => Options::el_spacing_description(),
				'devices'     => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'   => array( '{{WRAPPER}} .p-content' => '--el-spacing: {{VALUE}}px;' ),
			)
		);
		$this->add_responsive_control(
			'bottom_margin', array(
				'label'       => esc_html__( 'Custom Bottom Margin', 'foxiz-core' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => Options::el_margin_description(),
				'devices'     => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'   => array( '{{WRAPPER}} .block-wrap' => '--bottom-spacing: {{VALUE}}px;' ),
			)
		);
		$this->add_responsive_control(
			'first_bottom_margin', array(
				'label'       => esc_html__( 'First Post Bottom Margin', 'foxiz-core' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => esc_html__( 'Input custom bottom margin values (px) for the first post.', 'foxiz-core' ),
				'devices'     => array( 'desktop', 'tablet', 'mobile' ),
				'selectors'   => array( '{{WRAPPER}} .p-content' => 'margin-bottom: {{VALUE}}px; padding-bottom: {{VALUE}}px' ),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {

		if ( function_exists( 'foxiz_get_hierarchical_3' ) ) {
			$settings         = $this->get_settings();
			$settings['uuid'] = 'uid_' . $this->get_id();

			echo \foxiz_get_hierarchical_3( $settings );
		}
	}
}