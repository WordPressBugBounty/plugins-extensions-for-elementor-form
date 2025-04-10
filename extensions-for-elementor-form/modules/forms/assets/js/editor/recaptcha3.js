module.exports = elementorModules.editor.utils.Module.extend( {


    enqueueRecaptchaJs(url, type) {
        if (!elementorFrontend.elements.$body.find('[src="' + url + '"]').length) {
          elementorFrontend.elements.$body.append('<scr' + 'ipt src="' + url + '" id="recaptcha-' + type + '"></scri' + 'pt>');
        }
      },
      renderField(inputField, item) {
        inputField += '<div class="cool-form-field ' + item.field_type + '">';
        inputField += this.getDataSettings(item);
        inputField += '</div>';
        return inputField;
      },
      getDataSettings(item) {
        const config = elementor.config.forms.recaptcha_v3;
        const srcURL = 'https://www.google.com/recaptcha/api.js?onload=recaptchaLoaded&render=explicit';
        if (!config.enabled) {
          console.log('reCAPTCHA is not enabled');
          return '<div class="elementor-alert elementor-alert-info"> To use reCAPTCHA V3, you need to add the API Key and complete the setup process in Dashboard > Elementor > Cool FormKit Lite > Settings > reCAPTCHA V3. </div>';
        }
        let recaptchaData;
        if (item.field_type == "recaptcha_v3") {
          recaptchaData = 'data-sitekey="' + config.site_key + '" data-type="' + config.type + '"';
          recaptchaData += ' data-action="Form"';
          recaptchaData += ' data-badge="' + item.recaptcha_badge + '"';
          recaptchaData += ' data-size="invisible"';
        }
        this.enqueueRecaptchaJs(srcURL, config.type);
        return '<div class="cool-form-recaptcha" ' + recaptchaData + '></div>';
      },
      filterItem(item) {
        if ('recaptcha' === item.field_type) {
          item.field_label = false;
        }
        return item;
      },
      onInit() {
        elementor.hooks.addFilter('cool_formkit/forms/content_template/item', this.filterItem);
        elementor.hooks.addFilter('cool_formkit/forms/content_template/field/recaptcha_v3', this.renderField, 10, 4);
      }

})