'use strict';

function exAutomationConfigSetKeyAsValue(inputEl) {
    if (inputEl.checkValidity()) {
		inputEl.parentNode.querySelector('.value-input').setAttribute('name', 'body_fields[' + inputEl.value + ']');
    }
}

function exAutomationConfigAddTr() {
    let newFields = document.querySelector('#key-value-template').content.cloneNode(true);
    document.querySelector('.key-value-table').appendChild(newFields);
};
