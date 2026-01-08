import Utility from './Utility';

export default function MoreStepForm() {
  'use strict';

  let formClass = 'powermail_morestep';

  let fieldsetClass = 'powermail_fieldset';

  let buttonActiveClass = 'active';

  let that = this;

  this.initialize = function() {
    showListener();
    initializeForms();
  };

  this.showFieldset = function(index, form, backwards) {
    index = parseInt(index, 10);
    if (form.classList.contains(formClass)) {
      hideAllFieldsets(form);
      let fieldsets = getAllFieldsetsOfForm(form);
      let nextIndex = backwards ? index - 1 : index + 1;
      // If the next page is hidden by powermail_cond, skip it
      if (fieldsets[index].classList.contains('powermail-cond-hidden') && fieldsets[nextIndex]) {
        Utility.showElement(fieldsets[nextIndex]);
      } else {
        Utility.showElement(fieldsets[index]);
      }
      updateButtonStatus(form);
    }
  };

  let initializeForms = function() {
    let moreStepForms = document.querySelectorAll('form.' + formClass);
    for (let i = 0; i < moreStepForms.length; i++) {
      initializeForm(moreStepForms[i]);
    }
  };

  let initializeForm = function(form) {
    that.showFieldset(0, form);
  };

  let showListener = function() {
    let moreButtons = document.querySelectorAll('[data-powermail-morestep-show]');
    for (let i = 0; i < moreButtons.length; i++) {
      moreButtons[i].addEventListener('click', function(event) {
        let targetFieldset = event.target.getAttribute('data-powermail-morestep-show');
        const backwards = event.target.getAttribute('data-powermail-morestep-previous') !== null;
        let form = event.target.closest('form');

        // CUSTOM visible field validation
        let validateVisibleFields = ['true', '1'].includes(event.target.getAttribute('data-powermail-morestep-validate'));
        let scrollIntoView = ['true', '1'].includes(event.target.getAttribute('data-powermail-morestep-scroll'));
        // validate visible fields if set before proceed
        if (validateVisibleFields
          && !form.powermailFormValidation.validateVisibleFields()
        ) {
          this.form.powermailFormValidation.scrollToFirstError();
          event.target.blur();
          event.preventDefault();
          return;
        }

        that.showFieldset(targetFieldset, form, backwards);
        if (scrollIntoView) {
          getAllFieldsetsOfForm(form)[targetFieldset]?.scrollIntoView({behavior:'smooth'});
        }
      });
    }
  }

  let hideAllFieldsets = function(form) {
    let fieldsets = getAllFieldsetsOfForm(form);
    for (let i = 0; i < fieldsets.length; i++) {
      Utility.hideElement(fieldsets[i]);
    }
  };

  let updateButtonStatus = function(form) {
    let buttons = form.querySelectorAll('[data-powermail-morestep-current]');
    let activePageIndex = getActivePageIndex(form);
    for (let i = 0; i < buttons.length; i++) {
      buttons[i].classList.remove(buttonActiveClass);
      if (i === activePageIndex) {
        buttons[i].classList.add(buttonActiveClass);
      }
    }
  };

  /**
   * Get index of current visible fieldset
   *
   * @param form
   * @returns {number}
   */
  let getActivePageIndex = function(form) {
    let fieldsets = getAllFieldsetsOfForm(form);
    for (let i = 0; i < fieldsets.length; i++) {
      if (fieldsets[i].style.display !== 'none') {
        return i;
      }
    }
  }

  let getAllFieldsetsOfForm = function(form) {
    return form.querySelectorAll('.' + fieldsetClass);
  };
}
