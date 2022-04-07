import Utility from './Utility';

export default function FormValidation() {
  'use strict';

  let formValidationSelector = '[data-powermail-validate]';
  let fieldErrorClass = 'powermail_field_error';
  let errorContainerClass = 'data-powermail-class-handler';
  let errorMessageContainerClass = 'powermail-errors-list';

  this.validate = function() {
    validateFormsSubmit();
    validateFieldListener();
  };

  let validateFormsSubmit = function() {
    let forms = document.querySelectorAll(formValidationSelector);
    for (let i = 0; i < forms.length; i++) {
      let form = forms[i];
      form.setAttribute('novalidate', 'novalidate')
      form.addEventListener('submit', function(event) {
        event.preventDefault();
        let errorResult = validateForm(forms[i]);
        if (errorResult === false) {
          //event.target.submit();
          alert('todo submit');
        }
      })
    }
  };

  let validateFieldListener = function() {
    let forms = document.querySelectorAll(formValidationSelector);
    for (let i = 0; i < forms.length; i++) {
      let fields = getFieldsFromForm(forms[i]);
      for (let j = 0; j < fields.length; j++) {
        fields[j].addEventListener('blur', function() {
          validateField(fields[j]);
        });
        fields[j].addEventListener('change', function() {
          validateField(fields[j]);
        });
      }
    }
  };

  let validateForm = function(form) {
    let error = false;
    let fields = getFieldsFromForm(form);
    for (let i = 0; i < fields.length; i++) {
      let errorResult = validateField(fields[i]);
      if (errorResult === true) {
        error = errorResult;
      }
    }
    return error;
  };

  let validateField = function(field) {
    field = getValidationField(field);
    let error = false;
    error = validateFieldRequired(field, error);
    error = validateFieldEmail(field, error);
    error = validateFieldUrl(field, error);
    error = validateFieldPattern(field, error);
    error = validateFieldNumber(field, error);
    error = validateFieldMinimum(field, error);
    error = validateFieldMaximum(field, error);
    error = validateFieldLength(field, error);
    error = validateUploadFieldSize(field, error);
    error = validateUploadFieldExtension(field, error);
    return error;
  };

  /*
   * Initialize single validations
   */

  let validateFieldRequired = function(field, error) {
    if (error === false) {
      if (isRequiredField(field) && isValidationRequiredConfirmed(field) === false) {
        setError('required', field);
        error = true;
      } else {
        removeError('required', field);
      }
    }
    return error;
  };

  let validateFieldEmail = function(field, error) {
    if (error === false) {
      if (isEmailField(field) && isValidationEmailConfirmed(field) === false) {
        setError('email', field);
        error = true;
      } else {
        removeError('email', field);
      }
    }
    return error;
  };

  let validateFieldUrl = function(field, error) {
    if (error === false) {
      if (isUrlField(field) && isValidationUrlConfirmed(field) === false) {
        setError('url', field);
        error = true;
      } else {
        removeError('url', field);
      }
    }
    return error;
  };

  let validateFieldPattern = function(field, error) {
    if (error === false) {
      if (isPatternField(field) && isValidationPatternConfirmed(field) === false) {
        setError('pattern', field);
        error = true;
      } else {
        removeError('pattern', field);
      }
    }
    return error;
  };

  let validateFieldNumber = function(field, error) {
    if (error === false) {
      if (isNumberField(field) && isValidationNumberConfirmed(field) === false) {
        setError('number', field);
        error = true;
      } else {
        removeError('number', field);
      }
    }
    return error;
  };

  let validateFieldMinimum = function(field, error) {
    if (error === false) {
      if (isMinimumField(field) && isValidationMinimumConfirmed(field) === false) {
        setError('min', field);
        error = true;
      } else {
        removeError('min', field);
      }
    }
    return error;
  };

  let validateFieldMaximum = function(field, error) {
    if (error === false) {
      if (isMaximumField(field) && isValidationMaximumConfirmed(field) === false) {
        setError('max', field);
        error = true;
      } else {
        removeError('max', field);
      }
    }
    return error;
  };

  let validateFieldLength = function(field, error) {
    if (error === false) {
      if (isLengthField(field) && isValidationLengthConfirmed(field) === false) {
        setError('length', field);
        error = true;
      } else {
        removeError('length', field);
      }
    }
    return error;
  };

  let validateUploadFieldSize = function(field, error) {
    if (error === false) {
      if (isUploadField(field) && isValidationUploadFieldSizeConfirmed(field) === false) {
        setError('powermailfilesize', field);
        error = true;
      } else {
        removeError('powermailfilesize', field);
      }
    }
    return error;
  };

  let validateUploadFieldExtension = function(field, error) {
    if (error === false) {
      if (isUploadField(field) && isValidationUploadFieldExtensionConfirmed(field) === false) {
        setError('powermailfileextensions', field);
        error = true;
      } else {
        removeError('powermailfileextensions', field);
      }
    }
    return error;
  };

  /*
   * Check for validations
   */

  let isRequiredField = function(field) {
    return field.hasAttribute('required') || field.getAttribute('data-powermail-required') === 'true';
  };

  let isEmailField = function(field) {
    return field.getAttribute('type') === 'email' || field.getAttribute('data-powermail-type') === 'email';
  };

  let isUrlField = function(field) {
    return field.getAttribute('type') === 'url' || field.getAttribute('data-powermail-type') === 'url';
  };

  let isPatternField = function(field) {
    return field.hasAttribute('pattern') || field.hasAttribute('data-powermail-pattern');
  };

  let isNumberField = function(field) {
    return field.getAttribute('type') === 'number' || field.getAttribute('data-powermail-type') === 'integer';
  };

  let isMinimumField = function(field) {
    return field.hasAttribute('min') || field.hasAttribute('data-powermail-min');
  };

  let isMaximumField = function(field) {
    return field.hasAttribute('max') || field.hasAttribute('data-powermail-max');
  };

  let isLengthField = function(field) {
    return field.hasAttribute('data-powermail-length');
  };

  let isUploadField = function(field) {
    return field.getAttribute('type') === 'file';
  };

  /*
   * Single validators
   */

  let isValidationRequiredConfirmed = function(field) {
    return getFieldValue(field) !== '';
  };

  let isValidationEmailConfirmed = function(field) {
    if (field.value === '') {
      return true;
    }
    let pattern = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    let constraint = new RegExp(pattern, '');
    return constraint.test(getFieldValue(field));
  };

  let isValidationUrlConfirmed = function(field) {
    if (field.value === '') {
      return true;
    }
    let pattern = '^(https?:\\/\\/)?'+ // protocol
      '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
      '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
      '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
      '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
      '(\\#[-a-z\\d_]*)?$'
    let constraint = new RegExp(pattern, '');
    return constraint.test(getFieldValue(field));
  };

  let isValidationPatternConfirmed = function(field) {
    if (field.value === '') {
      return true;
    }
    let pattern = field.getAttribute('data-powermail-pattern') || field.getAttribute('pattern');
    let constraint = new RegExp(pattern, '');
    return constraint.test(getFieldValue(field));
  };

  let isValidationNumberConfirmed = function(field) {
    if (field.value === '') {
      return true;
    }
    return isNaN(field.value) === false;
  };

  let isValidationMinimumConfirmed = function(field) {
    if (field.value === '') {
      return true;
    }
    let minimum = field.getAttribute('min') || field.getAttribute('data-powermail-min');
    return parseInt(field.value) >= parseInt(minimum);
  };

  let isValidationMaximumConfirmed = function(field) {
    if (field.value === '') {
      return true;
    }
    let maximum = field.getAttribute('max') || field.getAttribute('data-powermail-max');
    return parseInt(field.value) <= parseInt(maximum);
  };

  let isValidationLengthConfirmed = function(field) {
    if (field.value === '') {
      return true;
    }
    let lengthConfiguration = field.getAttribute('data-powermail-length');
    let length = lengthConfiguration.replace('[', '').replace(']', '').split(',');
    let minimum = length[0].trim();
    let maximum = length[1].trim();
    return parseInt(field.value.length) >= parseInt(minimum) && parseInt(field.value.length) <= parseInt(maximum);
  };

  let isValidationUploadFieldSizeConfirmed = function(field) {
    if (field.value === '') {
      return true;
    }
    let size = Utility.getLargestFileSize(field);
    let sizeConfiguration = field.getAttribute('data-powermail-powermailfilesize').split(',');
    return parseInt(size) <= parseInt(sizeConfiguration[0]);
  };

  let isValidationUploadFieldExtensionConfirmed = function(field) {
    if (field.value === '') {
      return true;
    }
    return Utility.isFileExtensionInList(Utility.getExtensionFromFileName(field.value), field.getAttribute('accept'));
  };

  /**
   * @param type like "required" or "pattern"
   * @param field
   */
  let setError = function(type, field) {
    removeError(type, field);
    addErrorClass(field);
    let message = field.getAttribute('data-powermail-' + type + '-message') ||
      field.getAttribute('data-powermail-error-message') || 'Validation error';
    addErrorMessage(message, field);
  };

  /**
   * @param type like "required" or "pattern"
   * @param field
   */
  let removeError = function(type, field) {
    removeErrorClass(field);
    removeErrorMessages(field);
  };

  let addErrorClass = function(field) {
    if (field.getAttribute(errorContainerClass)) {
      let elements = document.querySelectorAll(field.getAttribute(errorContainerClass));
      for (let i = 0; i < elements.length; i++) {
        elements[i].classList.add(fieldErrorClass);
      }
    } else {
      field.classList.add(fieldErrorClass);
    }
  };

  let removeErrorClass = function(field) {
    if (field.getAttribute(errorContainerClass)) {
      let elements = document.querySelectorAll(field.getAttribute(errorContainerClass));
      for (let i = 0; i < elements.length; i++) {
        elements[i].classList.remove(fieldErrorClass);
      }
    } else {
      field.classList.remove(fieldErrorClass);
    }
  };

  let addErrorMessage = function(message, field) {
    let errorContainer = document.createElement('ul');
    errorContainer.classList.add(errorMessageContainerClass);
    errorContainer.classList.add('filled');
    errorContainer.setAttribute('data-powermail-error', getFieldIdentifier(field));
    let errorElement = document.createElement('li');
    errorContainer.appendChild(errorElement);
    let textNode = document.createTextNode(message);
    errorElement.appendChild(textNode);

    if (field.getAttribute('data-powermail-errors-container') !== null) {
      let parentContainer = document.querySelector(field.getAttribute('data-powermail-errors-container'));
      if (parentContainer !== null) {
        parentContainer.appendChild(errorContainer);
      }
    } else {
      field.parentNode.appendChild(errorContainer);
    }
  };

  let removeErrorMessages = function(field) {
    let errorMessageContainer = document.querySelector('[data-powermail-error="' + getFieldIdentifier(field) + '"]');
    if (errorMessageContainer !== null) {
      errorMessageContainer.remove();
    }
  };

  let getFieldValue = function(field) {
    let value = field.value;

    // Special case radiobuttons & checkboxes: take value from selected field
    if (field.getAttribute('type') === 'radio' || field.getAttribute('type') === 'checkbox') {
      value = '';
      let name = field.getAttribute('name');
      let form = field.closest('form');
      let selectedField = form.querySelector('input[name="' + name + '"]:checked');
      if (selectedField !== null) {
        value = selectedField.value;
      }
    }
    return value;
  };

  let getFieldIdentifier = function(field) {
    let name = field.getAttribute('name');
    return name.replace(/[^\w\s]/gi, '');
  };

  let getFieldsFromForm = function(form) {
    return form.querySelectorAll(
      'input:not([data-powermail-validation="disabled"]):not([type="hidden"]):not([type="submit"])'
      + ', textarea:not([data-powermail-validation="disabled"])'
      + ', select:not([data-powermail-validation="disabled"])'
    );
  };

  /**
   * Special case for radiobuttons & checkboxes: take first field
   *
   * @param field
   * @returns {*}
   */
  let getValidationField = function(field) {
    if (field.getAttribute('type') === 'radio' || field.getAttribute('type') === 'checkbox') {
      let name = field.getAttribute('name');
      let form = field.closest('form');
      let fields = form.querySelectorAll('[name="' + name + '"]');
      field = fields[0];
    }
    return field;
  };
}
