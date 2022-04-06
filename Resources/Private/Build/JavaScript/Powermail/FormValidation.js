export default function FormValidation() {
  'use strict';

  let formValidationSelector = '[data-powermail-validate]';
  let fieldErrorClass = 'parsley-error';
  let errorContainerClass = 'data-powermail-class-handler';
  let errorMessageContainerClass = 'parsley-errors-list';
  let errorMessageRequiredClass = 'parsley-required';

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

    if (error === false) {
      if (isRequiredField(field) && isValidationRequiredConfirmed(field) === false) {
        setError('required', field);
        error = true;
      } else {
        removeError('required', field);
      }
    }

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

  let isRequiredField = function(field) {
    return field.hasAttribute('required') || field.getAttribute('data-powermail-required') === 'true';
  };

  let isPatternField = function(field) {
    return field.hasAttribute('pattern') || field.hasAttribute('data-powermail-pattern');
  };

  let isValidationRequiredConfirmed = function(field) {
    return getFieldValue(field) !== '';
  };

  let isValidationPatternConfirmed = function(field) {
    let pattern = field.getAttribute('data-powermail-pattern') || field.getAttribute('pattern');
    let constraint = new RegExp(pattern, '');
    return constraint.test(getFieldValue(field));
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
    errorElement.classList.add(errorMessageRequiredClass);
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
  }
}
