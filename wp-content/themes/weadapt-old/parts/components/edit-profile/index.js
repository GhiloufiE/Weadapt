/* globals vars */

import siteMessage from '../../../assets/js/lib/site-message';
import siteMessageClear from '../../../assets/js/lib/site-message-clear';

const controlOnDelete = select => {
	select.querySelectorAll('.select-pure__selected-label i').forEach(label => {
		label.setAttribute('tabindex', 0);

		label.addEventListener('keypress', event => {
			event.stopPropagation();

			if (event.key === 'Enter') {
				label.click();
			}
		});
	});
};

const controlOptionTabindex = option => {
	if (option.classList.contains('select-pure__option--selected')) {
		option.setAttribute('tabindex', -1);
	} else {
		option.setAttribute('tabindex', 0);
	}
};

const initSelectEvents = select => {
	select.setAttribute('tabindex', 0);

	select.addEventListener('keypress', event => {
		if (event.key === 'Enter') {
			select.click();
		}
	});

	controlOnDelete(select);

	select.querySelectorAll('.select-pure__option').forEach(option => {
		controlOptionTabindex(option);

		option.addEventListener('keypress', event => {
			event.stopPropagation();

			if (event.key === 'Enter') {
				option.click();
				select.focus();
			}
		});
	});
};

const initCustomSelects = selects => {
	const setOptionChecked = (selectedIds, selectWrap) => {
		selectWrap.querySelectorAll('select option[selected]').forEach(option => {
			option.removeAttribute('selected');
		});

		if (Array.isArray(selectedIds)) {
			selectedIds.forEach(id => {
				const option = selectWrap.querySelector(`option[value="${id}"]`);
				option.setAttribute('selected', true);
			});
		} else {
			const option = selectWrap.querySelector(`option[value="${selectedIds}"]`);
			option.setAttribute('selected', true);
		}

		selectWrap.querySelectorAll('.select-pure__option').forEach(option => {
			controlOptionTabindex(option);
		});

		controlOnDelete(selectWrap);
	};

	selects.forEach(selectWrap => {
		const select = selectWrap.querySelector('select');
		const selectOptions = selectWrap.querySelectorAll('select option');
		const customSelect = selectWrap.querySelector('.theme-select');

		setTimeout(() => {
			initSelectEvents(selectWrap.querySelector('.select-pure__select'));
		});

		const options = [];
		let selectedOptions;

		if (select.multiple) {
			selectedOptions = [];
		}

		Array.from(selectOptions).forEach(option => {
			const { text, value } = option;

			options.push({
				label: text,
				value,
			});

			if (option.hasAttribute('selected')) {
				if (select.multiple) {
					selectedOptions.push(value);
				} else {
					selectedOptions = value;
				}
			}
		});

		// eslint-disable-next-line no-undef
		new SelectPure(customSelect, {
			options,
			multiple: select.multiple,
			autocomplete: true,
			value: selectedOptions,
			onChange: values => {
				setOptionChecked(values, selectWrap);
			},
		});
	});
};

const updateUserData = async event => {
	event.preventDefault();
	siteMessageClear();

	const { target } = event;

	const form = document.querySelector('form.edit-profile');
	const formData = new FormData(form);
	target.classList.add('loading');

	try {
		const response = await fetch(vars.ajaxUrl, {
			method: 'POST',
			body: formData,
		});

		if (!response.ok) {
			throw new Error(response.statusText);
		}

		// const { status, message } = await response.json();
		// siteMessage(status, message);

		const data = await response.json();
		siteMessage(data.status, data.message);

		if (data.reload != undefined && data.reload) {
			setTimeout(() => {
				location.reload();
			}, 1000);
		}

		target.classList.remove('loading');
	} catch (error) {
		siteMessage('error', error.message);
		target.classList.remove('loading');
	}
};

const selects = document.querySelectorAll('.edit-profile .theme-select-wrap');
const saveBtn = document.querySelector('[data-profile-save]');
const trueFalseButtons = document.querySelectorAll('.edit-profile .true-false-button');

trueFalseButtons.forEach(button => {
	button.addEventListener('keypress', event => {
		if (event.key === 'Enter') {
			const checkbox = button.querySelector('input[type="checkbox"]');

			checkbox.checked ^= 1;
		}
	});
});


initCustomSelects(selects);
saveBtn.addEventListener('click', updateUserData);