import Utility from './Utility';
import MoreStepForm from './MoreStepForm';

export default class FormValidation {
  #formValidationSelector = '[data-powermail-validate]';

  validate() {
    const forms = document.querySelectorAll(this.#formValidationSelector);
    forms.forEach(function(form) {
      form = new Form(form);
      form.validate();
    });
  };
}


class Form {
  'use strict';

  /**
   * Form element (filled via constructor)
   */
  #form;

  /**
   * Has form any errors?
   *
   * @type {boolean}
   */
  #error = false;

  /**
   * List all fieldnames and errors
   * @type {{}}
   */
  #errorFields = {};

  #formErrorClass = 'powermail_form_error';
  #fieldsetErrorClass = 'powermail_fieldset_error';
  #fieldErrorClass = 'powermail_field_error';
  #errorContainerClass = 'data-powermail-class-handler';
  #errorMessageContainerClass = 'powermail-errors-list';

  /**
   * Validator configuration
   *
   * Add possibility to add own validators from outside like:
   *
   *    const forms = document.querySelectorAll('.powermail_form');
   *    forms.forEach(function(form) {
   *      let formValidation = form.powermailFormValidation;
   *
   *      formValidation.addValidator('custom100', function(field) {
   *        if (field.hasAttribute('data-powermail-custom100')) {
   *          // return true means validation has failed
   *          return field.value < parseInt(field.getAttribute('data-powermail-custom100'));
   *        }
   *        return false;
   *      });
   *    });
   *
   * @type {{name: function(*=, *): boolean}}
   */
  #validators = {
    'required': (field) => {
      return this.#isRequiredField(field) && this.#isValidationRequiredConfirmed(field) === false;
    },
    'email': (field) => {
      return this.#isEmailField(field) && this.#isValidationEmailConfirmed(field) === false;
    },
    'url': (field) => {
      return this.#isUrlField(field) && this.#isValidationUrlConfirmed(field) === false;
    },
    'pattern': (field) => {
      return this.#isPatternField(field) && this.#isValidationPatternConfirmed(field) === false;
    },
    'number': (field) => {
      return this.#isNumberField(field) && this.#isValidationNumberConfirmed(field) === false;
    },
    'minimum': (field) => {
      return this.#isMinimumField(field) && this.#isValidationMinimumConfirmed(field) === false;
    },
    'maximum': (field) => {
      return this.#isMaximumField(field) && this.#isValidationMaximumConfirmed(field) === false;
    },
    'length': (field) => {
      return this.#isLengthField(field) && this.#isValidationLengthConfirmed(field) === false;
    },
    'equalto': (field) => {
      return this.#isEqualtoField(field) && this.#isValidationEqualtoConfirmed(field) === false;
    },
    'powermailfilesize': (field) => {
      return this.#isUploadField(field) && this.#isValidationUploadFieldSizeConfirmed(field) === false;
    },
    'powermailfileextensions': (field) => {
      return this.#isUploadField(field) && this.#isValidationUploadFieldExtensionConfirmed(field) === false;
    },
  };

  /**
   * Submit error callback configuration
   *
   * Add possibility to add own callbacks when a user submits a form and an error happens from outside like:
   *
   *    const forms = document.querySelectorAll('.powermail_form');
   *    forms.forEach(function(form) {
   *      let formValidation = form.powermailFormValidation;
   *
   *      formValidation.addSubmitErrorCallback('custom100', function() {
   *        // error happens, do something
   *      });
   *    });
   *
   * @type {{name: function(*=, *): boolean}}
   */
  #submitErrorCallbacks = {
    'openTabWithError': () => {
      const firstFieldWithError = this.#form.querySelector('.powermail_field_error');
      if (firstFieldWithError !== null) {
        let fieldsetError = firstFieldWithError.closest('.powermail_fieldset');
        const fieldsetErrorIndex = [...this.#form.querySelectorAll('.powermail_fieldset')].indexOf(fieldsetError);
        let moreStepForm = new MoreStepForm();
        moreStepForm.showFieldset(fieldsetErrorIndex, this.#form);
      }
    },
    'scrollToFirstError': () => {
      try {
        const fieldsWithError = this.#form.querySelectorAll('.powermail_field_error');
        fieldsWithError.forEach((field) => {
          if (Utility.isElementVisible(field)) {
            field.scrollIntoView({behavior:'smooth'});
            throw 'StopException';
          }
        });
      } catch (exception) {
        // stop, do nothing
      }
    }
  };

  constructor(form) {
    this.#form = form;
    this.#form.powermailFormValidation = this;
  }

  validate() {
    this.#validateFormSubmit();
    this.#validateFieldListener();
  }

  /**
   * @param name
   * @param validator
   */
  addValidator(name, validator) {
    this.#validators[name] = validator;
  }

  /**
   * @param name
   * @param callback
   */
  addSubmitErrorCallback(name, callback) {
    this.#submitErrorCallbacks[name] = callback;
  }

  #validateFormSubmit() {
    const that = this;
    this.#form.setAttribute('novalidate', 'novalidate')
    this.#form.addEventListener('submit', function(event) {
      that.#validateForm();
      if (that.#hasFormErrors() === true) {
        that.#runSubmitErrorCallbacks();
        event.preventDefault();
      }
    })
  };

  #validateFieldListener() {
    const fields = this.#getFieldsFromForm();
    fields.forEach((field) => {
      field.addEventListener('input', () => {
        // When user types something in a field
        this.#validateField(field);
      });
      field.addEventListener('blur', () => {
        // When field focus gets lost
        this.#validateField(field);
      });
      field.addEventListener('change', () => {
        // When a checkbox, radiobutton or option in a select was chosen
        this.#validateField(field);
      });
    });
  };

  #validateForm() {
    let fields = this.#getFieldsFromForm();
    for (let i = 0; i < fields.length; i++) {
      this.#validateField(fields[i]);
    }
  };

  #validateField(field) {
    let error = false;
    field = this.#getValidationField(field);

    for (let validator in this.#validators) {
      if (this.#validators.hasOwnProperty(validator) === false) {
        continue;
      }
      error = this.#runValidator(validator, this.#validators[validator], field, error);
    }

    this.#addFieldErrorStatus(field, error);
    this.#calculateError();
    this.#updateErrorClassesForFormAndFieldsets(field);
  };

  #runValidator(name, validator, field, error) {
    if (error === true) {
      return error;
    }
    error = validator(field);
    error ? this.#setError(name, field) : this.#removeError(name, field);
    return error;
  }

  #runSubmitErrorCallbacks() {
    for (let callback in this.#submitErrorCallbacks) {
      if (this.#submitErrorCallbacks.hasOwnProperty(callback) === false) {
        continue;
      }
      this.#submitErrorCallbacks[callback]();
    }
  }

  /*
   * Check for validations
   */

  #isRequiredField(field) {
    return field.hasAttribute('required') || field.getAttribute('data-powermail-required') === 'true';
  };

  #isEmailField(field) {
    return field.getAttribute('type') === 'email' || field.getAttribute('data-powermail-type') === 'email';
  };

  #isUrlField(field) {
    return field.getAttribute('type') === 'url' || field.getAttribute('data-powermail-type') === 'url';
  };

  #isPatternField(field) {
    return field.hasAttribute('pattern') || field.hasAttribute('data-powermail-pattern');
  };

  #isNumberField(field) {
    return field.getAttribute('type') === 'number' || field.getAttribute('data-powermail-type') === 'integer';
  };

  #isMinimumField(field) {
    return field.hasAttribute('min') || field.hasAttribute('data-powermail-min');
  };

  #isMaximumField(field) {
    return field.hasAttribute('max') || field.hasAttribute('data-powermail-max');
  };

  #isLengthField(field) {
    return field.hasAttribute('data-powermail-length');
  };

  #isEqualtoField(field) {
    return field.hasAttribute('data-powermail-equalto');
  };

  #isUploadField(field) {
    return field.getAttribute('type') === 'file';
  };

  /*
   * Single validators
   */

  #isValidationRequiredConfirmed(field) {
    return this.#getFieldValue(field) !== '';
  };

  #isValidationEmailConfirmed(field) {
    if (field.value === '') {
      return true;
    }
    let pattern = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    let constraint = new RegExp(pattern, '');
    return constraint.test(this.#getFieldValue(field));
  };

  #isValidationUrlConfirmed(field) {
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
    return constraint.test(this.#getFieldValue(field));
  };

  #isValidationPatternConfirmed(field) {
    if (field.value === '') {
      return true;
    }
    let pattern = field.getAttribute('data-powermail-pattern') || field.getAttribute('pattern');
    let constraint = new RegExp(pattern, '');
    return constraint.test(this.#getFieldValue(field));
  };

  #isValidationNumberConfirmed(field) {
    if (field.value === '') {
      return true;
    }
    return isNaN(field.value) === false;
  };

  #isValidationMinimumConfirmed(field) {
    if (field.value === '') {
      return true;
    }
    let minimum = field.getAttribute('min') || field.getAttribute('data-powermail-min');
    return parseInt(field.value) >= parseInt(minimum);
  };

  #isValidationMaximumConfirmed(field) {
    if (field.value === '') {
      return true;
    }
    let maximum = field.getAttribute('max') || field.getAttribute('data-powermail-max');
    return parseInt(field.value) <= parseInt(maximum);
  };

  #isValidationLengthConfirmed(field) {
    if (field.value === '') {
      return true;
    }
    let lengthConfiguration = field.getAttribute('data-powermail-length');
    let length = lengthConfiguration.replace('[', '').replace(']', '').split(',');
    let minimum = length[0].trim();
    let maximum = length[1].trim();
    return parseInt(field.value.length) >= parseInt(minimum) && parseInt(field.value.length) <= parseInt(maximum);
  };

  #isValidationEqualtoConfirmed(field) {
    const comparisonSelector = field.getAttribute('data-powermail-equalto');
    const comparisonField = this.#form.querySelector(comparisonSelector);
    if (comparisonField !== null) {
      return comparisonField.value === field.value;
    }
    return false;
  };

  #isValidationUploadFieldSizeConfirmed(field) {
    if (field.value === '') {
      return true;
    }
    let size = Utility.getLargestFileSize(field);
    let sizeConfiguration = field.getAttribute('data-powermail-powermailfilesize').split(',');
    return size <= parseInt(sizeConfiguration[0]);
  };

  #isValidationUploadFieldExtensionConfirmed(field) {
    if (field.value === '') {
      return true;
    }
    return Utility.isFileExtensionInList(Utility.getExtensionFromFileName(field.value), field.getAttribute('accept'));
  };

  /**
   * @param type like "required" or "pattern"
   * @param field
   */
  #setError(type, field) {
    this.#removeError(type, field);
    this.#addErrorClass(field);
    let message = field.getAttribute('data-powermail-' + type + '-message') ||
      field.getAttribute('data-powermail-error-message') || 'Validation error';
    this.#addErrorMessage(message, field);
  };

  /**
   * @param type like "required" or "pattern"
   * @param field
   */
  #removeError(type, field) {
    this.#removeErrorClass(field);
    this.#removeErrorMessages(field);
  };

  #addErrorClass(field) {
    if (field.getAttribute(this.#errorContainerClass)) {
      let elements = document.querySelectorAll(field.getAttribute(this.#errorContainerClass));
      for (let i = 0; i < elements.length; i++) {
        elements[i].classList.add(this.#fieldErrorClass);
      }
    } else {
      field.classList.add(this.#fieldErrorClass);
    }
  };

  #removeErrorClass(field) {
    if (field.getAttribute(this.#errorContainerClass)) {
      let elements = document.querySelectorAll(field.getAttribute(this.#errorContainerClass));
      for (let i = 0; i < elements.length; i++) {
        elements[i].classList.remove(this.#fieldErrorClass);
      }
    } else {
      field.classList.remove(this.#fieldErrorClass);
    }
  };

  #addErrorMessage(message, field) {
    let errorContainer = document.createElement('ul');
    errorContainer.classList.add(this.#errorMessageContainerClass);
    errorContainer.classList.add('filled');
    errorContainer.setAttribute('data-powermail-error', this.#getFieldIdentifier(field));
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

  #removeErrorMessages(field) {
    let errorMessageContainer = document.querySelector(
      '[data-powermail-error="' + this.#getFieldIdentifier(field) + '"]'
    );
    if (errorMessageContainer !== null) {
      errorMessageContainer.remove();
    }
  };

  #getFieldValue(field) {
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

  #getFieldIdentifier(field) {
    let name = field.getAttribute('name');
    return name.replace(/[^\w\s]/gi, '');
  };

  #getFieldsFromForm() {
    return this.#form.querySelectorAll(
      'input:not([data-powermail-validation="disabled"]):not([type="hidden"]):not([type="reset"]):not([type="submit"])'
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
  #getValidationField(field) {
    if (field.getAttribute('type') === 'radio' || field.getAttribute('type') === 'checkbox') {
      let name = field.getAttribute('name');
      let form = field.closest('form');
      let fields = form.querySelectorAll('[name="' + name + '"]');
      field = fields[0];
    }
    return field;
  };

  #hasFormErrors() {
    return this.#error;
  }

  #addFieldErrorStatus(field, error) {
    this.#errorFields[field.getAttribute('name')] = error;
  }

  #calculateError() {
    let error = false;
    for (let property in this.#errorFields) {
      if (this.#errorFields.hasOwnProperty(property) === false) {
        continue;
      }
      if (this.#errorFields[property] === true) {
        error = true;
      }
    }
    this.#error = error;
  }

  #updateErrorClassesForFormAndFieldsets(field) {
    const fieldset = field.closest('fieldset.powermail_fieldset');
    if (this.#hasFormErrors()) {
      this.#form.classList.add(this.#formErrorClass);
      if (fieldset !== null) {
        fieldset.classList.add(this.#fieldsetErrorClass);
      }
    } else {
      this.#form.classList.remove(this.#formErrorClass);
      if (fieldset !== null) {
        fieldset.classList.remove(this.#fieldsetErrorClass);
      }
    }
  }
}
