import Utility from './Utility';

export default function MoreStepForm() {
  'use strict';

  let formClass = 'powermail_morestep';

  let fieldsetClass = 'powermail_fieldset';

  let buttonActiveClass = 'btn-primary';

  let that = this;

  this.initialize = function() {
    showListener();
    initializeForms();
  };

  this.showFieldset = function(index, form) {
    if (form.classList.contains(formClass)) {
      hideAllFieldsets(form);
      let fieldsets = getAllFieldsetsOfForm(form);
      Utility.showElement(fieldsets[index]);
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
        let form = event.target.closest('form');
        that.showFieldset(targetFieldset, form);
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
