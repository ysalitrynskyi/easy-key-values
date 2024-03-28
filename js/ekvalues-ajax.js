function sanitizeTextField(str) {
    if (typeof str === 'string') {
        return str.trim().replace(/<\/?[^>]+(>|$)/g, "");
    }
    return '';
}

function disableBeforeUnloadTemporarily() {
    window.onbeforeunload = null;
    setTimeout(function() {
        window.onbeforeunload = function() {
            return ekvLang.unsavedChanges;
        };
    }, 100);
}

jQuery(document).ready(function($) {
    function updateIndicator($pair) {
        var originalKey = $pair.find('input[type="text"]').data('original-value');
        var originalValue = $pair.find('textarea').data('original-value');
        var originalVisibility = $pair.find("input[type='hidden']").data('original-value');
        var currentVisibility = $pair.find("input[type='hidden']").val();
        var currentKey = $pair.find('input[type="text"]').val();
        var currentValue = $pair.find('textarea').val();
        if (originalKey === currentKey && originalValue === currentValue && originalVisibility === currentVisibility) {
            $pair.find('.ekvalues-change-indicator').text('✅').css('color', 'green');
        } else {
            $pair.find('.ekvalues-change-indicator').text('🔸').css('color', 'red');
        }
    }

    function addIndicator() {
        $('.ekvalues-pair').each(function() {
            if ($(this).find('.ekvalues-change-indicator').length === 0) {
                $(this).prepend('<span class="ekvalues-change-indicator" style="padding-right: 10px; font-size: 20px; color: grey;">✅</span>');
            }
            $(this).find('input[type="text"], input[type="hidden"], textarea').each(function() {
                $(this).data('original-value', $(this).val());
            });
        });
    }
    addIndicator();

    $('body').on('click', '#ekvalues-add-pair', function(e) {
        e.preventDefault();
        var index = $('.ekvalues-pair').length;
        var html = `<div class='ekvalues-pair'>
                        <span class='ekvalues-change-indicator'>✅</span>
                        <input name='ekvalues_options[${index}][key]' size='20' type='text' />
                        <textarea name='ekvalues_options[${index}][value]' rows='1'></textarea>
                        <button class='button ekvalues-toggle-visibility dashicons dashicons-visibility' type='button'></button>
                        <input type='hidden' name='ekvalues_options[${index}][visibility]' value='1' />
                        <button class='button ekvalues-remove-pair' type='button'>X</button>
                    </div>`;
        $(html).insertBefore('#ekvalues-add-pair');
        addIndicator();
        if ($('#ekvalues-pairs').children().length === 1 && $('#ekvalues-pairs').children().first().is('p')) {
            $('#ekvalues-pairs').children().first().remove();
        }
    });

    $('body').on('click', '.ekvalues-toggle-visibility', function(e) {
        e.preventDefault();
        var $this = $(this);
        var $pair = $this.closest('.ekvalues-pair');
        var $visibilityInput = $pair.find('input[type="hidden"]');
        var isVisible = $visibilityInput.val() === '1';
        if ($this.hasClass('disabled')) {
            return;
        }
        if (isVisible) {
            $visibilityInput.val('0');
            $this.removeClass('dashicons-visibility').addClass('dashicons-hidden');
            $pair.find('textarea').attr('type', 'password');
        } else {
            $visibilityInput.val('1');
            $this.removeClass('dashicons-hidden').addClass('dashicons-visibility');
            $pair.find('textarea').attr('type', 'text');
        }
        updateIndicator($pair);
    });

    $('body').on('click', '.ekvalues-remove-pair', function(e) {
        e.preventDefault();
        var keyInput = $(this).closest('.ekvalues-pair').find('input[type="text"]').val();
        var valueTextarea = $(this).closest('.ekvalues-pair').find('textarea').val();
        var keyIsEmpty = !keyInput || keyInput.trim() === '';
        var valueIsEmpty = !valueTextarea || valueTextarea.trim() === '';
        if (keyIsEmpty && valueIsEmpty) {
            $(this).closest('.ekvalues-pair').remove();
        } else {
            if (confirm(ekvLang.confirmRemove) && confirm(ekvLang.confirmRemoveSure)) {
                $(this).closest('.ekvalues-pair').remove();
            }
        }
    });

    $('body').on('input', 'input[type="text"], textarea', function() {
        updateIndicator($(this).closest('.ekvalues-pair'));
    });

    $('#ekvalues-form').submit(function(e) {
        e.preventDefault();
        var isValid = true;
        var keys = {};

        var disabledInputs = $('.ekvalues-pair').find('input:disabled, textarea:disabled').prop('disabled', false);
        $('.ekvalues-pair').each(function(index) {
            $(this).find('input, textarea').each(function() {
                var name = $(this).attr('name').replace(/\[\d+\]/, '[' + index + ']');
                $(this).attr('name', name);
            });
        });

        $('.ekvalues-pair').each(function(index) {
            var keyInput = $(this).find('input[type="text"]').val();
            if (keyInput === "0") {
                alert("Key value '0' is not allowed.");
                isValid = false;
                return false;
            }
        });

        if (!isValid) {
            disabledInputs.prop('disabled', true);
            return;
        }

        $('.ekvalues-pair').each(function() {
            var keyInput = sanitizeTextField($(this).find('input[type="text"]').val());
            var valueTextarea = sanitizeTextField($(this).find('textarea').val());
            if (keyInput && keys[keyInput]) {
                alert(ekvLang.errorDuplicateKey);
                isValid = false;
                disabledInputs.prop('disabled', true);
                return false;
            }
            keys[keyInput] = true;
            if (!keyInput && valueTextarea) {
                alert(ekvLang.errorEmptyKey);
                isValid = false;
                disabledInputs.prop('disabled', true);
                return false;
            }
        });

        if (!isValid) {
            return;
        }

        var saveButton = $('.button-primary');
        saveButton.val(ekvLang.saving).prop('disabled', true);

        var data = {
            'action': 'ekvalues_save_options',
            'nonce': ekvLang.nonce,
            'options': $(this).serialize()
        };

        disabledInputs.prop('disabled', true);

        $.post(ekvLang.ajaxurl, data, function(response) {
            if (response.success) {
                alert(ekvLang.optionsSaved);
                window.onbeforeunload = null;
                $('.ekvalues-pair').each(function() {
                    $(this).find('.ekvalues-change-indicator').text('✅');
                    $(this).find('input[type="text"], textarea').each(function() {
                        $(this).data('original-value', $(this).val());
                    });
                });
            } else {
                alert(ekvLang.saveFailed);
            }
            saveButton.val(ekvLang.saveChanges).prop('disabled', false);
        }).fail(function() {
            alert(ekvLang.serverError);
            saveButton.val(ekvLang.saveChanges).prop('disabled', false);
        });
    });

    window.onbeforeunload = function() {
        return ekvLang.unsavedChanges;
    };
});
