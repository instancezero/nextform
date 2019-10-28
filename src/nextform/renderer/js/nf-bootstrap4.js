/*
 * NextForm Support for Bootstrap4. Assumes jQuery
 */
"use strict";

class NextForm {

    constructor(form) {
        this.form = form;
        this.groupCollector();
    }

    disableGroup(group, disable) {
        if (this.groupList[group] === undefined) return;
        this.groupList[group].forEach(function (jqElement) {
            jqElement.prop('disabled', disable);
        });
    }

    displayGroup(group, show) {
        if (this.groupList[group] === undefined) return;
        this.groupList[group].forEach(function (jqElement) {
            jqElement.toggle(show);
        });
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

}

