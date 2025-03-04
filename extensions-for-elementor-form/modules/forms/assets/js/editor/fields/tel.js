module.exports = elementorModules.editor.utils.Module.extend( {

	renderField( inputField, item, i, settings ) {
		var itemClasses = _.escape(item.css_classes),
			required = (item.required) ? ' required' : '',
			placeholder = (item.placeholder) ? ' placeholder="' + _.escape(item.placeholder) + '"' : '';
	
		// You can adjust the pattern if needed. Here we use a sample pattern.
		var pattern = ' pattern="[0-9()-]+"';
	
		return `
			<label class="cool-form-text mdc-text-field mdc-text-field--outlined ${(item.field_label === '' || !settings.show_labels) ? 'mdc-text-field--no-label' : ''} cool-field-size-${settings.input_size}">
				<span class="mdc-notched-outline">
					<span class="mdc-notched-outline__leading"></span>
					<span class="mdc-notched-outline__notch">
						${ ( item.field_label !== '' && settings.show_labels )
							? `<span class="mdc-floating-label" id="tel-label-${i}">${_.escape(item.field_label)}</span>`
							: '' }
					</span>
					<span class="mdc-notched-outline__trailing"></span>
				</span>
				<input 
					type="${item.field_type}" 
					size="1" 
					${placeholder} ${pattern}
					class="mdc-text-field__input cool-form__field elementor-field elementor-size-${settings.input_size} ${itemClasses}"
					name="form_field_${i}" 
					id="form_field_${i}" 
					${required}
				>
				<i aria-hidden="true" class="material-icons mdc-text-field__icon mdc-text-field__icon--trailing cool-tel-error-icon" style="display:none">error</i>
			</label>
			<div class="mdc-text-field-helper-line">
				<div class="mdc-text-field-helper-text" id="cool-tel-error" aria-hidden="true"></div>
			</div>
		`;
	},
	
	onInit() {
		elementor.hooks.addFilter( 'cool_formkit/forms/content_template/field/tel', this.renderField, 10, 4 );
	},
} );
