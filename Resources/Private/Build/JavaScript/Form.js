import Utility from './Utility';
import MoreStepForm from './MoreStepForm';
import FormValidation from './FormValidation';
import moment from 'moment';

class PowermailForm {
  'use strict';

  initialize = function () {
    const that = this;
    that.#formValidationListener();
    that.#moreStepFormListener();
    that.#ajaxSubmitListener();
    that.#locationFieldListener();
    that.#hidePasswordsListener();
    that.#deleteAllFilesListener();
    that.#prefillDateFieldsListener();
  };

  #formValidationListener() {
    let formValidation = new FormValidation();
    formValidation.validate();
  };

  #moreStepFormListener() {
    let moreStepForm = new MoreStepForm();
    moreStepForm.initialize();
  };

  #ajaxSubmitListener() {
    const that = this;
    const forms = document.querySelectorAll('form[data-powermail-ajax]');
    forms.forEach(function(form) {
      form.addEventListener('submit', function(event) {
        event.preventDefault();
        if (form.classList.contains('powermail_form_error') === false) {
          const url = form.getAttribute('action');
          const formUid = form.getAttribute('data-powermail-form');
          const redirectUri = form.getAttribute('data-powermail-ajax-uri');
          that.#addProgressbar(form);

          fetch(url, {body: new FormData(form), method: 'post'})
            .then((resp) => resp.text())
            .then(function(data) {
              const parser = new DOMParser();
              const htmlDocument = parser.parseFromString(data, 'text/html');
              const section = htmlDocument.documentElement.querySelector('[data-powermail-form="' + formUid + '"]');
              if (section !== null) {
                const container = document.querySelector('[data-powermail-form="' + formUid + '"]')
                  .closest('.tx-powermail');
                container.innerHTML = '';
                container.appendChild(section);
              } else {
                // no form markup found try to redirect via javascript
                if (redirectUri !== null) {
                  Utility.redirectToUri(redirectUri);
                } else {
                  // fallback if no location found (but will effect 2x submit)
                  form.submit();
                }
              }

              // Fire existing listener again
              that.#ajaxSubmitListener();
              that.#formValidationListener();
              that.#moreStepFormListener();
              that.#reloadCaptchaImages();
            })
            .catch(function(error) {
              console.log(error);
            });
        }
      });
    });
  };

  #locationFieldListener() {
    if (document.querySelector('[data-powermail-location="prefill"]') !== null) {
      navigator.geolocation.getCurrentPosition(function(position) {
        let lat = position.coords.latitude;
        let lng = position.coords.longitude;
        let base = document.querySelector('[data-powermail-eidurl]').getAttribute('data-powermail-eidurl');
        let url = base + '?eID=' + 'powermailEidGetLocation&lat=' + lat + '&lng=' + lng;

        fetch(url)
          .then((resp) => resp.text())
          .then(function(data) {
            let elements = document.querySelectorAll('[data-powermail-location="prefill"]');
            for (let i = 0; i < elements.length; i++) {
              elements[i].value = data;
            }
          })
          .catch(function(error) {
            console.log(error);
          });
      });
    }
  };

  #addProgressbar(form) {
    this.#removeProgressbar(form);
    const submit = form.querySelector('.powermail_submit');
    if (submit !== null) {
      submit.parentNode.appendChild(this.#getProgressbar());
    } else {
      const powermailContainer = form.closest('.tx-powermail');
      if (powermailContainer !== null) {
        powermailContainer.appendChild(this.#getProgressbar());
      }
    }
  };

  #removeProgressbar(form) {
    const powermailContainer = form.closest('.tx-powermail');
    if (powermailContainer !== null) {
      const progressbar = powermailContainer.querySelector('.powermail_progressbar');
      if (progressbar !== null) {
        progressbar.remove();
      }
    }
  };

  #getProgressbar() {
    const outer = document.createElement('div');
    outer.classList.add('powermail_progressbar');
    const center = document.createElement('div');
    center.classList.add('powermail_progress');
    const inner = document.createElement('div');
    inner.classList.add('powermail_progress_inner');
    outer.appendChild(center);
    center.appendChild(inner);
    return outer;
  };

  #hidePasswordsListener() {
    let elements = document.querySelectorAll('.powermail_all_type_password.powermail_all_value');
    for (let i = 0; i < elements.length; i++) {
      elements[i].innerText = '********';
    }
  };

  #reloadCaptchaImages() {
    const images = document.querySelectorAll('img.powermail_captchaimage');
    images.forEach(function(image) {
      let source = Utility.getUriWithoutGetParam(image.getAttribute('src'));
      image.setAttribute('src', source + '?hash=' + Utility.getRandomString(5));
    });
  };

  #deleteAllFilesListener() {
    const that = this;
    const deleteAllFiles = document.querySelectorAll('.deleteAllFiles');
    deleteAllFiles.forEach(function(file) {
      let fileWrapper = file.closest('.powermail_fieldwrap_file');
      if (fileWrapper !== null) {
        let element = fileWrapper.querySelector('input[type="file"]');
        that.#disableUploadField(element);
      }

      file.addEventListener('click', function() {
        let fileWrapper = file.closest('.powermail_fieldwrap_file');
        if (fileWrapper !== null) {
          let element = fileWrapper.querySelector('input[type="hidden"]');
          that.#enableUploadField(element);
        }
        let ul = file.closest('ul');
        if (ul !== null) {
          ul.remove();
        }
      });
    });
  };

  #prefillDateFieldsListener() {
    const forms = document.querySelectorAll('form.powermail_form');
    forms.forEach(function(form) {
      let fields = form.querySelectorAll('input');
      fields.forEach(function(field) {
        const type = field.getAttribute('type');
        if (type === 'date' || type === 'datetime-local' || type === 'time') {
          let formatOutput = 'YYYY-MM-DD';
          if (type === 'datetime-local') {
            formatOutput = 'YYYY-MM-DD\THH:mm';
          } else if (type === 'time') {
            formatOutput = 'HH:mm';
          }
          let value = field.getAttribute('data-date-value');
          if (value !== null) {
            let formatInput = field.getAttribute('data-datepicker-format');
            let momentDate = moment(value, formatInput);
            if (momentDate.isValid) {
              field.value = momentDate.format(formatOutput);
            }
          }
        }
      });
    });
  };

  #disableUploadField($element) {
    $element.prop('disabled', 'disabled').addClass('hide').prop('type', 'hidden');
  };

  #enableUploadField($element) {
    $element.prop('disabled', false).removeClass('hide').prop('type', 'file');
  };
}

let powermailForm = new PowermailForm();
powermailForm.initialize();
