<?php

namespace Cool_FormKit\Includes\Frontend\Widget;

use \Elementor\Plugin as ElementorPlugin;
use \Elementor\Controls_Manager as ElementorControls;
use \Elementor\Repeater as ElementorRepeater;
use Elementor\Controls_Manager;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FME_Elementor_Forms_Mask {

	public $allowed_fields = [
		'text',
	];

	public function __construct() {
		add_action( 'elementor/element/form/section_form_fields/before_section_end', [ $this, 'add_mask_control' ], 100, 2 );
		add_filter( 'elementor_pro/forms/render/item', [ $this, 'add_mask_atributes' ], 10, 3 );
	}

	/**
	 * Add mask control
	 *
	 * @since 1.0
	 * @param $element
	 * @param $args
	 */
	public function add_mask_control( $element, $args ) {
		$elementor = ElementorPlugin::instance();
		$control_data = $elementor->controls_manager->get_control_from_stack( $element->get_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$controls_to_register = [
			'fme_mask_control' => [
				'label' => esc_html__( 'Mask Control', 'cool-formkit' ),
				'type' => ElementorControls::SELECT,
				'tab' => 'content',
				'tabs_wrapper' => 'form_fields_tabs',
				'inner_tab' => 'form_fields_advanced_tab',
				'default' => 'mask',
				'options' => [
					'mask' => esc_html__( 'Select Mask', 'cool-formkit' ),
					'ev-phone' => esc_html__( 'Phone', 'cool-formkit' ),
					'ev-time' => esc_html__( 'Date & Time', 'cool-formkit' ),
					'ev-money' => esc_html__( 'Money', 'cool-formkit' ),
					'ev-ccard' => esc_html__( 'Credit Card', 'cool-formkit' ),
					'ev-br_fr' => esc_html__( 'Brazilian Formats', 'cool-formkit' ),
					'ev-ip-address' => esc_html__( 'IP Address', 'cool-formkit' ),
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => $this->allowed_fields,
						],
					],
				],
			],
			'fme_mask_auto_placeholders' => [
				'label' => esc_html__( 'Mask Placeholders', 'cool-formkit' ),
				'type' => ElementorControls::SWITCHER,
				'tab' => 'content',
				'tabs_wrapper' => 'form_fields_tabs',
				'inner_tab' => 'form_fields_advanced_tab',
				'default' => '',
				'label_on' => esc_html__( 'On', 'cool-formkit' ),
				'label_off' => esc_html__( 'Off', 'cool-formkit' ),
				'conditions' => [
					'terms' => [
						[
							'name' => 'fme_mask_control',
							'operator' => 'in',
							'value' => ['ev-phone','ev-cpf','ev-cnpj','ev-money','ev-ccard','ev-cep','ev-time','ev-ip-address','ev-br_fr'],
						],
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => $this->allowed_fields,
						],
					],
				],
			],
			'fme_money_mask_format' => [
				'label' => esc_html__( 'Thousand separator', 'cool-formkit' ),
				'type' => ElementorControls::SELECT,
				'tab' => 'content',
				'tabs_wrapper' => 'form_fields_tabs',
				'inner_tab' => 'form_fields_advanced_tab',
				'default' => 'dot',
				'options' => [
					'dot' => esc_html__( 'Dot (.)', 'cool-formkit' ),
					'comma' => esc_html__( 'Comma (,)', 'cool-formkit' )
				],
				'conditions' => [
						'terms' => [
							[
								'name' => 'fme_mask_control',
								'operator' => 'in',
								'value' => ['ev-money'],
						],
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => $this->allowed_fields,
						],
					],
				],
			],
			'fme_money_mask_prefix' => [
				'label' => esc_html__( 'Mask Prefix', 'cool-formkit' ),
				'type' => ElementorControls::TEXT,
				'tab' => 'content',
				'tabs_wrapper' => 'form_fields_tabs',
				'inner_tab' => 'form_fields_advanced_tab',
				'default' => '',
				'ai'  => [
					'active' => false,
				],
				'conditions' => [
						'terms' => [
							[
								'name' => 'fme_mask_control',
								'operator' => 'in',
								'value' => ['ev-money'],
						],
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => $this->allowed_fields,
						],
					],
				],
			],
			'fme_money_mask_decimal_places' => [
				'label' => esc_html__( 'Mask Decimal Places', 'cool-formkit' ),
				'type' => ElementorControls::TEXT,
				'tab' => 'content',
				'tabs_wrapper' => 'form_fields_tabs',
				'inner_tab' => 'form_fields_advanced_tab',
				'default' => '2',
				'ai'  => [
					'active' => false,
				],
				'conditions' => [
						'terms' => [
							[
								'name' => 'fme_mask_control',
								'operator' => 'in',
								'value' => ['ev-money'],
						],
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => $this->allowed_fields,
						],
					],
				],
			],
			'fme_time_mask_format' => [
				'label' => esc_html__( 'Date Format', 'cool-formkit' ),
				'type' => ElementorControls::SELECT,
				'tab' => 'content',
				'tabs_wrapper' => 'form_fields_tabs',
				'inner_tab' => 'form_fields_advanced_tab',
				'default' => 'one',
				'options' => [
					'three' => esc_html__( 'Date (dd/mm/yyyy)', 'cool-formkit' ),
					'four' => esc_html__( 'Date (mm/dd/yyyy)', 'cool-formkit' ),
					'five' => esc_html__( 'DateTime (dd/mm/yyyy hh:mm)', 'cool-formkit' ),
					'six' => esc_html__( 'DateTime (mm/dd/yyyy hh:mm)', 'cool-formkit' ),
					'one' => esc_html__( 'Time (hh:mm)', 'cool-formkit' ),
					'two' => esc_html__( 'Time (hh:mm:ss)', 'cool-formkit' ),
					'seven' => esc_html__( 'Month/Year (mm/yyyy)', 'cool-formkit' ),
				],
				'conditions' => [
						'terms' => [
							[
								'name' => 'fme_mask_control',
								'operator' => 'in',
								'value' => ['ev-time'],
						],
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => $this->allowed_fields,
						],
					],
				],
			],
			'fme_brazilian_formats' => [
				'label' => esc_html__( 'Select Format', 'cool-formkit' ),
				'type' => ElementorControls::SELECT,
				'tab' => 'content',
				'tabs_wrapper' => 'form_fields_tabs',
				'inner_tab' => 'form_fields_advanced_tab',
				'default' => 'fme_cpf',
				'options' => [
					'fme_cpf' => esc_html__( 'CPF', 'cool-formkit' ),
					'fme_cnpj' => esc_html__( 'CNPJ', 'cool-formkit' ),
					'fme_cep' => esc_html__( 'CEP', 'cool-formkit' ),
				],
				'conditions' => [
						'terms' => [
							[
								'name' => 'fme_mask_control',
								'operator' => 'in',
								'value' => ['ev-br_fr'],
						],
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => $this->allowed_fields,
						],
					],
				],
			],
			'fme_credit_card_options' => [
				'label' => esc_html__( 'Credit Card Options', 'cool-formkit' ),
				'type' => ElementorControls::SELECT,
				'tab' => 'content',
				'tabs_wrapper' => 'form_fields_tabs',
				'inner_tab' => 'form_fields_advanced_tab',
				'default' => 'hyphen',
				'options' => [
					'space' => esc_html__( 'Credit card with space', 'cool-formkit' ),
					'hyphen' => esc_html__( 'Credit card with hyphen', 'cool-formkit' ),
					'credit_card_date' => esc_html__( 'Expiry Date (MM/YY)', 'cool-formkit' ),
					'credit_card_expiry_date' => esc_html__( 'Expiry Date (MM/YYYY)', 'cool-formkit' ),
				],
				'conditions' => [
						'terms' => [
							[
								'name' => 'fme_mask_control',
								'operator' => 'in',
								'value' => ['ev-ccard'],
						],
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => $this->allowed_fields,
						],
					],
				],
			],
			'fme_phone_format' => [
				'label' => esc_html__( 'Phone Format', 'cool-formkit' ),
				'type' => ElementorControls::SELECT,
				'tab' => 'content',
				'tabs_wrapper' => 'form_fields_tabs',
				'inner_tab' => 'form_fields_advanced_tab',
				'default' => 'phone_usa',
				'options' => [
					'phone_usa' => esc_html__( 'Phone (USA)', 'cool-formkit' ),
					'phone_d8' => esc_html__( 'Phone (8-digit)', 'cool-formkit' ),
					'phone_ddd8' => esc_html__( 'Phone (DDD + 8-digit)', 'cool-formkit' ),
					'phone_ddd9' => esc_html__( 'Phone (DDD + 9-digit)', 'cool-formkit' ),
				],
				'conditions' => [
						'terms' => [
							[
								'name' => 'fme_mask_control',
								'operator' => 'in',
								'value' => ['ev-phone'],
						],
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => $this->allowed_fields,
						],
					],
				],
			]
		];

		/**
		 * Filter to pro version change control.
		 *
		 * @since 1.5
		 */
		$controls_to_register = apply_filters( 'fme_after_mask_control_created', $controls_to_register );

		$controls_repeater = new ElementorRepeater();
		foreach ( $controls_to_register as $key => $control ) {
			$controls_repeater->add_control( $key, $control );
		}

		$pattern_field = $controls_repeater->get_controls();

		/**
		 * Register control in form advanced tab.
		 *
		 * @since 1.5.2
		 */
		$this->register_control_in_form_advanced_tab( $element, $control_data, $pattern_field );
	}

	/**
	 * Register control in form advanced tab
	 *
	 * @param object $element
	 * @param array $control_data
	 * @param array $pattern_field
	 * @return void
	 *
	 * @since 1.5.2
	 */
	public function register_control_in_form_advanced_tab( $element, $control_data, $pattern_field ) {
		foreach( $pattern_field as $key => $control ) {

			if( $key !== '_id' ) {

				$new_order = [];
				foreach ( $control_data['fields'] as $field_key => $field ) {

					if ( 'field_value' === $field['name'] ) {
						$new_order[$key] = $control;
					}
					$new_order[ $field_key ] = $field;
				}

				$control_data['fields'] = $new_order;
			}
		}

		return $element->update_control( 'form_fields', $control_data );
	}

	/**
	 * Render/add new mask atributes on input field.
	 *
	 * @since 1.0
	 * @param array $field
	 * @param string $field_index
	 * @return void
	 */
	public function add_mask_atributes( $field, $field_index, $form_widget ) {
		if ( 
			! empty( $field['fme_mask_control'] ) && 
			in_array( $field['field_type'], $this->allowed_fields ) && 
			$field['fme_mask_control'] !== 'mask' 
		) {			

			$form_widget->add_render_attribute( 
				'input' . $field_index, 
				'data-mask', 
				$field['fme_mask_control'] 
			);
	
			$form_widget->add_render_attribute(
				'input' . $field_index,
				'class',
				'fme-mask-input ' .
				'mask_control_@' . $field['fme_mask_control'] . ' ' .
				'money_mask_format_@' . $field['fme_money_mask_format'] . ' ' .
				'mask_prefix_@' . $field['fme_money_mask_prefix'] . ' ' .
				'mask_decimal_places_@' . $field['fme_money_mask_decimal_places'] . ' ' .
				'mask_time_mask_format_@' . $field['fme_time_mask_format'] . ' ' .
				'fme_phone_format_@' . $field['fme_phone_format'] . ' ' .
				'credit_card_options_@' . $field['fme_credit_card_options'] . ' ' . 
				'mask_auto_placeholder_@' . $field['fme_mask_auto_placeholders'] . ' ' .
				'fme_brazilian_formats_@' . $field['fme_brazilian_formats'] 
			);
		}
	
		/**
		 * After mask attribute added
		 *
		 * Action fired to allow pro version to add custom attributes.
		 *
		 * @since 1.5.2
		 */
		do_action( 'fme_after_mask_attribute_added', $field, $field_index, $form_widget );
	
		return $field;
	}	
}
