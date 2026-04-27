(function () {
  'use strict';

  const config = window.wopCfData || {};
  const i18n = config.i18n || {};

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.wop-cf-form').forEach(initForm);
  });

  function initForm(form) {
    const phoneInput = form.querySelector('input[name="phone"]');
    const fileInput = form.querySelector('input[type="file"]');
    const dropzone = form.querySelector('[data-dropzone]');
    const preview = form.querySelector('[data-file-preview]');
    const messageBox = form.querySelector('[data-message]');

    if (phoneInput && window.Inputmask) {
      new window.Inputmask(config.phoneMask || '+999 (99) 999-99-99', {
        placeholder: '_',
        showMaskOnHover: false
      }).mask(phoneInput);
    }

    if (dropzone && fileInput) {
      initDropzone(dropzone, fileInput, preview);
    }

    form.querySelectorAll('input, textarea').forEach(function (field) {
      field.addEventListener('input', function () {
        clearFieldError(form, field.name);
      });
    });

    form.addEventListener('submit', function (event) {
      event.preventDefault();
      handleSubmit(form, messageBox);
    });
  }

  function initDropzone(dropzone, fileInput, preview) {
    ['dragenter', 'dragover'].forEach(function (type) {
      dropzone.addEventListener(type, function (event) {
        event.preventDefault();
        event.stopPropagation();
        dropzone.classList.add('is-dragover');
      });
    });

    ['dragleave', 'drop'].forEach(function (type) {
      dropzone.addEventListener(type, function (event) {
        event.preventDefault();
        event.stopPropagation();
        dropzone.classList.remove('is-dragover');
      });
    });

    dropzone.addEventListener('drop', function (event) {
      const file = event.dataTransfer && event.dataTransfer.files[0];
      if (!file) {
        return;
      }

      const dt = new DataTransfer();
      dt.items.add(file);
      fileInput.files = dt.files;
      renderPreview(preview, file);
    });

    fileInput.addEventListener('change', function () {
      const file = fileInput.files[0];
      if (file) {
        renderPreview(preview, file);
      } else {
        clearPreview(preview);
      }
    });
  }

  function renderPreview(preview, file) {
    if (!preview) {
      return;
    }
    preview.hidden = false;
    preview.innerHTML = '';

    const name = document.createElement('span');
    name.textContent = file.name;

    const remove = document.createElement('button');
    remove.type = 'button';
    remove.className = 'wop-cf-file-remove';
    remove.textContent = '✕';
    remove.addEventListener('click', function () {
      const fileInput = preview.closest('.wop-cf-dropzone').querySelector('input[type="file"]');
      fileInput.value = '';
      clearPreview(preview);
    });

    preview.appendChild(name);
    preview.appendChild(remove);
  }

  function clearPreview(preview) {
    if (!preview) {
      return;
    }
    preview.hidden = true;
    preview.innerHTML = '';
  }

  function validate(form) {
    const errors = {};

    const fullName = form.querySelector('input[name="full_name"]');
    if (!fullName.value.trim()) {
      errors.full_name = i18n.requiredFullName;
    }

    const phone = form.querySelector('input[name="phone"]');
    const phoneDigits = phone.value.replace(/\D/g, '');
    if (phoneDigits.length < 10) {
      errors.phone = i18n.requiredPhone;
    }

    const email = form.querySelector('input[name="email"]');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email.value.trim())) {
      errors.email = i18n.invalidEmail;
    }

    const fileInput = form.querySelector('input[type="file"]');
    const file = fileInput && fileInput.files[0];
    if (file) {
      const ext = (file.name.split('.').pop() || '').toLowerCase();
      const allowed = config.allowedExt || [];
      if (allowed.indexOf(ext) === -1) {
        errors.photo = i18n.fileWrongType;
      } else if (file.size > (config.maxFileMb || 5) * 1024 * 1024) {
        errors.photo = (i18n.fileTooLarge || '').replace('%d', config.maxFileMb);
      }
    }

    return errors;
  }

  function showFieldErrors(form, errors) {
    form.querySelectorAll('.wop-cf-field, .wop-cf-dropzone').forEach(function (el) {
      el.classList.remove('has-error');
    });

    Object.keys(errors).forEach(function (name) {
      const input = form.querySelector('[name="' + name + '"]');
      if (!input) {
        return;
      }
      const wrapper = input.closest('.wop-cf-field') || input.closest('.wop-cf-dropzone');
      if (wrapper) {
        wrapper.classList.add('has-error');
      }
      const errorEl = form.querySelector('[data-error-for="' + name + '"]');
      if (errorEl) {
        errorEl.textContent = errors[name];
      }
    });
  }

  function clearFieldError(form, name) {
    const input = form.querySelector('[name="' + name + '"]');
    if (!input) {
      return;
    }
    const wrapper = input.closest('.wop-cf-field');
    if (wrapper) {
      wrapper.classList.remove('has-error');
    }
    const errorEl = form.querySelector('[data-error-for="' + name + '"]');
    if (errorEl) {
      errorEl.textContent = '';
    }
  }

  function showMessage(box, type, text) {
    if (!box) {
      return;
    }
    box.hidden = false;
    box.className = 'wop-cf-message is-' + type;
    box.textContent = text;
  }

  function clearMessage(box) {
    if (!box) {
      return;
    }
    box.hidden = true;
    box.className = 'wop-cf-message';
    box.textContent = '';
  }

  function handleSubmit(form, messageBox) {
    clearMessage(messageBox);

    const errors = validate(form);
    showFieldErrors(form, errors);
    if (Object.keys(errors).length > 0) {
      return;
    }

    const submitBtn = form.querySelector('.wop-cf-submit');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = i18n.sending || 'Sending…';

    const formData = new FormData(form);
    formData.append('action', config.action);
    formData.append('nonce', config.nonce);

    fetch(config.ajaxUrl, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    })
      .then(function (response) {
        return response.json().then(function (body) {
          return {ok: response.ok, body: body};
        });
      })
      .then(function (result) {
        if (result.ok && result.body && result.body.success) {
          form.reset();
          clearAllFieldErrors(form);
          const previewEl = form.querySelector('[data-file-preview]');
          clearPreview(previewEl);
          showMessage(messageBox, 'success', result.body.data.message);
        } else {
          const data = (result.body && result.body.data) || {};
          if (data.errors) {
            showFieldErrors(form, data.errors);
          }
          showMessage(messageBox, 'error', data.message || i18n.genericError);
        }
      })
      .catch(function () {
        showMessage(messageBox, 'error', i18n.genericError);
      })
      .finally(function () {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      });
  }

  function clearAllFieldErrors(form) {
    form.querySelectorAll('.wop-cf-field, .wop-cf-dropzone').forEach(function (el) {
      el.classList.remove('has-error');
    });
    form.querySelectorAll('.wop-cf-error').forEach(function (el) {
      el.textContent = '';
    });
  }
})();
