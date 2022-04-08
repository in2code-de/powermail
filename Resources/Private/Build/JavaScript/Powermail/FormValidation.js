import Utility from './Utility';

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

  /**
   * Already validated?
   *
   * @type {boolean}
   */
  #validated = false;

  #formErrorClass = 'powermail_form_error';
  #fieldsetErrorClass = 'powermail_fieldset_error';
  #fieldErrorClass = 'powermail_field_error';
  #errorContainerClass = 'data-powermail-class-handler';
  #errorMessageContainerClass = 'powermail-errors-list';

  constructor(form) {
    this.#form = form;
    this.#form.powermailFormValidation = this;
  }

  validate() {
    this.#validateFormSubmit();
    this.#validateFieldListener();
  }

  /**
   * Add possibility to add own validators from outside like:
   *
   *    const forms = document.querySelectorAll('.powermail_form');
   *    forms.forEach(function(form) {
   *      let formValidation = form.powermailFormValidation;
   *
   *      formValidation.addValidator('zip', function(field, error) {
   *        if (field.hasAttribute('data-powermail-custom100')) {
   *          if (error === false) {
   *            if (field.value < parseInt(field.getAttribute('data-powermail-custom100'))) {
   *              formValidation.setError('zip', field);
   *              error = true;
   *            } else {
   *              formValidation.removeError('zip', field);
   *            }
   *          }
   *        }
   *        return error;
   *      });
   *    });
   *
   * @param name
   * @param validator
   */
  addValidator(name, validator) {
    this.#validators[name] = validator;
  }

  #validateFormSubmit() {
    const that = this;
    this.#form.setAttribute('novalidate', 'novalidate')
    this.#form.addEventListener('submit', function(event) {
      if (that.#validated === false || that.#hasFormErrors()) {
        event.preventDefault();
      }
      that.#validateForm();
      if (that.#hasFormErrors() === false) {
        that.#form.requestSubmit();
      }
    })
  };

  #validateFieldListener() {
    const fields = this.#getFieldsFromForm();
    fields.forEach((field) => {
      field.addEventListener('blur', () => {
        this.#validateField(field);
      });
      field.addEventListener('change', () => {
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

  /**
   * Validator configuration
   *
   * @type {{name: function(*=, *): boolean}}
   */
  #validators = {
    'required': (field, error) => {
      if (error === false) {
        if (this.#isRequiredField(field) && this.#isValidationRequiredConfirmed(field) === false) {
          this.setError('required', field);
          error = true;
        } else {
          this.removeError('required', field);
        }
      }
      return error;
    },
    'email': (field, error) => {
      if (error === false) {
        if (this.#isEmailField(field) && this.#isValidationEmailConfirmed(field) === false) {
          this.setError('email', field);
          error = true;
        } else {
          this.removeError('email', field);
        }
      }
      return error;
    },
    'url': (field, error) => {
      if (error === false) {
        if (this.#isUrlField(field) && this.#isValidationUrlConfirmed(field) === false) {
          this.setError('url', field);
          error = true;
        } else {
          this.removeError('url', field);
        }
      }
      return error;
    },
    'pattern': (field, error) => {
      if (error === false) {
        if (this.#isPatternField(field) && this.#isValidationPatternConfirmed(field) === false) {
          this.setError('pattern', field);
          error = true;
        } else {
          this.removeError('pattern', field);
        }
      }
      return error;
    },
    'number': (field, error) => {
      if (error === false) {
        if (this.#isNumberField(field) && this.#isValidationNumberConfirmed(field) === false) {
          this.setError('number', field);
          error = true;
        } else {
          this.removeError('number', field);
        }
      }
      return error;
    },
    'minimum': (field, error) => {
      if (error === false) {
        if (this.#isMinimumField(field) && this.#isValidationMinimumConfirmed(field) === false) {
          this.setError('min', field);
          error = true;
        } else {
          this.removeError('min', field);
        }
      }
      return error;
    },
    'maximum': (field, error) => {
      if (error === false) {
        if (this.#isMaximumField(field) && this.#isValidationMaximumConfirmed(field) === false) {
          this.setError('max', field);
          error = true;
        } else {
          this.removeError('max', field);
        }
      }
      return error;
    },
    'length': (field, error) => {
      if (error === false) {
        if (this.#isLengthField(field) && this.#isValidationLengthConfirmed(field) === false) {
          this.setError('length', field);
          error = true;
        } else {
          this.removeError('length', field);
        }
      }
      return error;
    },
    'uploadSize': (field, error) => {
      if (error === false) {
        if (this.#isUploadField(field) && this.#isValidationUploadFieldSizeConfirmed(field) === false) {
          this.setError('powermailfilesize', field);
          error = true;
        } else {
          this.removeError('powermailfilesize', field);
        }
      }
      return error;
    },
    'uploadExtensions': (field, error) => {
      if (error === false) {
        if (this.#isUploadField(field) && this.#isValidationUploadFieldExtensionConfirmed(field) === false) {
          this.setError('powermailfileextensions', field);
          error = true;
        } else {
          this.removeError('powermailfileextensions', field);
        }
      }
      return error;
    },
  };

  #validateField(field) {
    let error = false;
    field = this.#getValidationField(field);

    for (let validator in this.#validators) {
      if (this.#validators.hasOwnProperty(validator) === false) {
        continue;
      }
      error = this.#validators[validator](field, error);
    }

    this.#addFieldErrorStatus(field, error);
    this.#calculateError();
    this.#updateErrorClassesForFormAndFieldsets(field);
    this.#validated = true;
  };

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
  setError(type, field) {
    this.removeError(type, field);
    this.#addErrorClass(field);
    let message = field.getAttribute('data-powermail-' + type + '-message') ||
      field.getAttribute('data-powermail-error-message') || 'Validation error';
    this.#addErrorMessage(message, field);
  };

  /**
   * @param type like "required" or "pattern"
   * @param field
   */
  removeError(type, field) {
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
