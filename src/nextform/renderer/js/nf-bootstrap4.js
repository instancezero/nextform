/*
 * NextForm Support for Bootstrap4. Assumes jQuery
 */
"use strict";

class NextForm {

    constructor(form) {
        this.form = form;
        this.groupCollector();
    }

    /**
     * Get a list of JQuery objects for the elements in each group.
     *
     * @returns {Boolean}
     */
    groupCollector() {
        let rawGroups = [];
        this.form.find('*[data-nf-group]').each(function (i, element) {
            $(element).data('nf-group').forEach(function (group) {
                if (rawGroups[group] === undefined) {
                    rawGroups[group] = [];
                }
                rawGroups[group].push($(element));
            });
        });
        this.groupList = rawGroups;
        return true;
    }

    groupDisplay(group, show) {
        if (this.groupList[group] === undefined) return;
        this.groupList[group].forEach(function (jqElement) {
            jqElement.toggle(show);
        });
    }
}

/* example code below
var form_1 = new NextForm($('#form_1'));
$('[name=members_membershipType]').change(function () {
    if (this.value === 'IND' || this.value === 'STU') {
        form_1.groupDisplay('family', false);
        form_1.groupDisplay('business', false);
    }
    if (this.value === 'FAM') {
        form_1.groupDisplay('family', true);
        form_1.groupDisplay('business', false);
    }
    if (['BUS', 'FEL'].includes(this.value)) {
        form_1.groupDisplay('family', false);
        form_1.groupDisplay('business', true);
    }
    if (['PAT', 'SPO'].includes(this.value)) {
        form_1.groupDisplay('family', false);
        form_1.groupDisplay('business', true);
    }
});
*/
