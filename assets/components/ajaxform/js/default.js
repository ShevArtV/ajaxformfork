import AjaxForm from "./ajaxform.class.js";

const AjaxFormConfigs = document.querySelectorAll('input[name="af_config"]'),
    AjaxForms = {};
if (AjaxFormConfigs.length) {
    AjaxFormConfigs.forEach(el => {
        let config = JSON.parse(el.value);
        AjaxForms[config.formSelector] = new AjaxForm('.' + config.formSelector, config);
    });
}