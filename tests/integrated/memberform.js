/*
 * Custom support for the member form
 */

/**
 * Update limits on user-entered fees based on a membership level change.
 * 
 * @param {HtmlElement} element
 * @returns {undefined}
 */
function setFeeAttributes(element) {
    var form = element.form;
    var fees = $(element).data('nf-sidecar').fee;
    var target = $('[name=scratch_feeSelect]', form);
    if (fees.minValue === undefined) {
        target.attr('min', 15);
    } else {
        target.attr('min', fees.minValue);
    }
    if (fees.maxValue === undefined) {
        target.removeAttr('max');
    } else {
        target.attr('max', fees.maxValue);
    }
    target.val(fees.default);
    $('[name=members_membershipFee]', form).val(fees.default);
    var help = $('#' + target.attr('id') + '_help');
    if (help !== undefined) {
        help.text($('[for=' + element.id + ']', form).text());
    }
}

function updateFee(element) {
    var jqe = $(element);
    var value = Number.parseInt(jqe.val());
    var test = jqe.attr('min');
    if (test !== undefined && value < test) return;
    var test = jqe.attr('max');
    if (test !== undefined && value > test) return;
    $('[name=members_membershipFee]', element.form).val(value);
}

