import FormValidator from '@yaireo/validator'
import MoreStepForm from './MoreStepForm';

function PowermailForm() {
  'use strict';

  this.initialize = function () {
    formValidationListener();
    moreStepFormListener();
  };

  let formValidationListener = function() {
    // todo

    var validator = new FormValidator();
    // select your "form" element from the DOM and attach an "onsubmit" event handler to it:
    document.forms[0].onsubmit = function (e) {
      var validatorResult = validator.checkAll(this); // "this" reffers to the currently submitetd form element

      return !!validatorResult.valid;
    };
  };

  let moreStepFormListener = function() {
    let moreStepForm = new MoreStepForm();
    moreStepForm.initialize();
  };
}

let powermailForm = new PowermailForm();
powermailForm.initialize();
