(function () {
    function initBlogNotifyForms() {
        var forms = document.querySelectorAll('[data-blog-notify-form]');
        var config = window.NexusBlogNotifyConfig || {};
        var endpoint = config.restEndpoint;

        if (!forms.length || !endpoint || typeof window.fetch !== 'function') {
            return;
        }

        function setFeedback(form, message, type) {
            var feedback = form.querySelector('[data-blog-notify-feedback]');

            if (!feedback) {
                return;
            }

            feedback.textContent = message || '';
            feedback.classList.remove('is-error', 'is-success');

            if (type) {
                feedback.classList.add(type === 'error' ? 'is-error' : 'is-success');
            }
        }

        function setPending(form, isPending) {
            var button = form.querySelector('button[type="submit"]');

            if (!button) {
                return;
            }

            if (!button.hasAttribute('data-default-label')) {
                button.setAttribute('data-default-label', button.textContent || '');
            }

            button.disabled = isPending;
            button.textContent = isPending ? 'Wird gesendet ...' : button.getAttribute('data-default-label');
        }

        function serializeForm(form) {
            var data = new window.FormData(form);
            var payload = {};

            data.forEach(function (value, key) {
                payload[key] = typeof value === 'string' ? value.trim() : value;
            });

            return payload;
        }

        forms.forEach(function (form) {
            var isSubmitting = false;
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                if (isSubmitting) return;

                setFeedback(form, '', '');

                var payload = serializeForm(form);
                var nonce = payload.nonce || config.nonce || '';
                isSubmitting = true;
                var unlockForm = window.NexusCore.lockForm(form);
                setPending(form, true);

                window.NexusCore.submitJson(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Nexus-Nonce': nonce
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload)
                })
                    .then(function (result) {
                        if (!result.ok || result.data.ok !== true) {
                            throw new Error((result.data && result.data.error) || config.errorMessage || 'Das hat gerade nicht funktioniert.');
                        }

                        setFeedback(
                            form,
                            result.data.message || config.successMessage || 'Fast geschafft. Bitte bestaetigen Sie Ihre Anmeldung ueber die E-Mail in Ihrem Postfach.',
                            'success'
                        );

                        form.reset();

                        var nonceInput = form.querySelector('input[name="nonce"]');
                        if (nonceInput && nonce) {
                            nonceInput.value = nonce;
                        }
                    })
                    .catch(function (error) {
                        setFeedback(
                            form,
                            error && error.message ? error.message : (config.errorMessage || 'Das hat gerade nicht funktioniert.'),
                            'error'
                        );
                    })
                    .finally(function () {
                        unlockForm();
                        isSubmitting = false;
                        setPending(form, false);
                    });
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBlogNotifyForms);
    } else {
        initBlogNotifyForms();
    }
})();
