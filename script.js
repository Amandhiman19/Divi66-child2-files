const { __, _x, _n, _nx } = wp.i18n;

jQuery('.tab-area .tabs .tab').removeClass('active');
jQuery('.tab-area .tabs .tab:first-child').addClass('active');
jQuery('.tab-area .tab-content:first-child').addClass('active');

// on click envent on tab
jQuery('.tab-area .tabs .tab').click(function () {
	jQuery('.tab-area .tabs .tab').removeClass('active');
	jQuery(this).addClass('active');
	var tab_id = jQuery(this).attr('data-tab');
	console.log(tab_id);
	jQuery('.tab-area').find('.tab-content').removeClass('active');
	jQuery('#' + tab_id).addClass('active');
});

jQuery('.profile-tabs .profile-tab:first-child').addClass('active');
jQuery('.profile-content:first-child').addClass('active');
jQuery('.profile-tabs .profile-tab').click(function () {
	jQuery('.profile-tabs .profile-tab').removeClass('active');
	jQuery(this).addClass('active');
	var tab_id = jQuery(this).attr('data-tab');
	console.log(tab_id);
	jQuery('.tab-area').find('.profile-content').removeClass('active');
	jQuery('#' + tab_id).addClass('active');
});

jQuery('#create-team-form').submit(function (e) {
	e.preventDefault();

	let file_data = jQuery('#team_picture').prop('files')[0];

	let form_data = new FormData();
	let input_serialized = jQuery(this).serializeArray();

	form_data.append('image', file_data);
	form_data.append('action', 'greenplay_create_team');

	// loop through form data and append to form_data
	jQuery.each(input_serialized, function (index, value) {
		form_data.append(value.name, value.value);
	});

	// get all form data
	jQuery('.message').html('');

	jQuery.ajax({
		contentType: false,
		processData: false,
		url: greenplayAjax.ajaxurl,
		data: form_data,
		type: 'post', // POST
		beforeSend: function () {
			jQuery('.is-loading').show();
		},
		complete: function () {
			jQuery('.is-loading').hide();
			console.log('complete');
		},
		success: function (data) {
			const html = `<div class="alert alert-success">${data.data}</div>`;
			console.log(html);
			jQuery('.message').html(html);
			if (data.success) {
			window.location.reload();
			}
		},
		error: function (data) {
			const html = `<div class="alert alert-error">${data.data}</div>`;
			jQuery('.message').html(html);
		},
	});
});



jQuery('#update-team-form').submit(function (e) {
	e.preventDefault();

	let file_data = jQuery('#team_picture').prop('files')[0];

	let form_data = new FormData();
	let input_serialized = jQuery(this).serializeArray();

	form_data.append('image', file_data);
	form_data.append('action', 'greenplay_create_team');

	// loop through form data and append to form_data
	jQuery.each(input_serialized, function (index, value) {
		form_data.append(value.name, value.value);
	});

	// get all form data
	jQuery('.message').html('');

	jQuery.ajax({
		contentType: false,
		processData: false,
		url: greenplayAjax.ajaxurl,
		data: form_data,
		type: 'post', // POST
		beforeSend: function () {
			jQuery('.is-loading').show();
		},
		complete: function () {
			jQuery('.is-loading').hide();
			console.log('complete');
		},
		success: function (data) {
			const html = `<div class="alert alert-success">${data.data}</div>`;
			console.log(html);
			jQuery('.message').html(html);
			if (data.success) {
			//	window.location.reload();
			}
		},
		error: function (data) {
			const html = `<div class="alert alert-error">${data.data}</div>`;
			jQuery('.message').html(html);
		},
	});
});

jQuery('.mepr_training_done').click(function (e) {
	// get user id from data-user-id
	const thisClass = jQuery(this);
	const userId = jQuery(this).attr('data-user-id');
	const checked = jQuery(this).is(':checked');
	const input_serialized = {
		id: userId,
		checked: checked ? 'on' : '',
		action: 'greenplay_training_done',
	};

	jQuery.ajax({
		url: greenplayAjax.ajaxurl,
		data: input_serialized,
		type: 'post', // POST
		beforeSend: function () {
			// add disable attr to button
			thisClass.attr('disabled', 'disabled');
		},
		complete: function () {
			// remove disable attr to button
			thisClass.removeAttr('disabled');
		},
	});
});

// mepr_practical_training
jQuery('.mepr_practical_training').click(function (e) {
	// get user id from data-user-id
	const thisClass = jQuery(this);
	const userId = jQuery(this).attr('data-user-id');
	const checked = jQuery(this).is(':checked');
	const input_serialized = {
		id: userId,
		checked: checked ? 'on' : '',
		action: 'greenplay_practical_training',
	};

	jQuery.ajax({
		url: greenplayAjax.ajaxurl,
		data: input_serialized,
		type: 'post', // POST
		beforeSend: function () {
			// add disable attr to button
			thisClass.attr('disabled', 'disabled');
		},
		complete: function () {
			// remove disable attr to button
			thisClass.removeAttr('disabled');
		},
	});
});

function convertFormToJSON(form) {
	const array = jQuery(form).serializeArray(); // Encodes the set of form elements as an array of names and values.
	const json = {};
	jQuery.each(array, function () {
		json[this.name] = this.value || '';
	});
	return json;
}

function onTeamDelete($userId, $teamId) {
	const confirm = window.confirm(greenplayAjax.strings.alert_delete);
	if (confirm) {
		jQuery.ajax({
			url: greenplayAjax.ajaxurl,
			data: {
				action: 'greenplay_delete_team',
				user_id: $userId,
			},
			type: 'post', // POST
			beforeSend: function () {
				jQuery('.team-list').addClass('isLoading');
				console.log('loading');
			},
			complete: function () {
				jQuery('.team-list').removeClass('isLoading');
			},
			success: function (data) {
				// reload page
				const html = `<div class="alert alert-success">${data.data}</div>`;
				jQuery('.message').html(html);
				window.location.reload();
			},
			error: function (data) {
				const html = `<div class="alert alert-error">${data.data}</div>`;
				jQuery('.message').html(html);
				console.log(data);
			},
		});
	}
}

// profile update
jQuery('#update-profile').submit(function (e) {
	e.preventDefault();

	let file_data = jQuery('#profile-image').prop('files')[0];

	let form_data = new FormData();
	let input_serialized = jQuery(this).serializeArray();

	form_data.append('profile_image', file_data);
	form_data.append('action', 'greenplay_update_profile');

	// loop through form data and append to form_data
	$.each(input_serialized, function (index, value) {
		form_data.append(value.name, value.value);
	});

	// disable submit button
	jQuery('#update-profile button[type="submit"]').attr('disabled', true);

	jQuery.ajax({
		contentType: false,
		processData: false,
		data: form_data,

		url: greenplayAjax.ajaxurl,
		type: 'post', // POST
		beforeSend: function () {
			jQuery('.is-loading').show();
		},
		complete: function () {
			jQuery('.is-loading').hide();
			jQuery('#update-profile button[type="submit"]').attr('disabled', false);
		},
		success: function (data) {
			jQuery('.profile-img-src').attr('src', data.data);
		},
	});
});

// set to url params
function setParams(name, value) {
	const searchSlug =
		typeof window !== 'undefined' ? window.location.search : '';
	const urlParams = new URLSearchParams(searchSlug);

	urlParams.set(name, value);
	window.history.pushState(
		{},
		'',
		`${window.location.pathname}?${urlParams.toString()}`
	);
}

// delete
function removeParams(name) {
	const searchSlug =
		typeof window !== 'undefined' ? window.location.search : '';
	const urlParams = new URLSearchParams(searchSlug);
	urlParams.delete(name);
	window.history.pushState(
		{},
		'',
		`${window.location.pathname}?${urlParams.toString()}`
	);
}

// get params from url
function getParams(name) {
	const searchSlug =
		typeof window !== 'undefined' ? window.location.search : '';
	const urlParams = new URLSearchParams(searchSlug);
	return urlParams.get(name) || '';
}

// race edition
jQuery('.register-race').submit(function (e) {
	e.preventDefault();
	jQuery('.register-race .message').html('');

	// get all form data
	let input_serialized = convertFormToJSON(jQuery(this));
	const datas = {
		action: 'greenplay_register_race',
		arriere_babord: input_serialized.arriere_babord || '',
		arriere_tribord: input_serialized.arriere_tribord || '',
		associate_team: input_serialized.associate_team || 'free-agent',
		avant_babord: input_serialized.avant_babord || '',
		avant_tribord: input_serialized.avant_tribord || '',
		barreur: input_serialized.barreur || '',
		race_type: input_serialized.race_type || '',
		race_captain: input_serialized.race_captain || '',
	};

	// return if one value is empty
	let error = false;
	Object.values(datas).map(data => {
		if (!data) {
			error = true;
		}
	});

	const message = error
		? '<div class="alert alert-error">' +
		  greenplayAjax.strings.race_register_error +
		  '</div>'
		: '';

	jQuery('#register-race .message').html(message);

	if (error) return;

	jQuery.ajax({
		url: greenplayAjax.ajaxurl,
		data: datas,
		type: 'post', // POST
		beforeSend: function () {
			jQuery('.is-loading').show();
		},
		success: function (data) {
			let html = '';
			if (data.success) {
				html = '<div class="alert alert-success">' + data?.data + '</div>';
				Fancybox.close();
				Fancybox.show([
					{
						src: '#payment-' + input_serialized.race_type,
						type: 'inline',
						closeClick: false,
					},
				]);
			} else {
				html = '<div class="alert alert-error">' + data?.data + '</div>';
			}

			jQuery('.is-loading').hide();
			jQuery('.message').html(html);
		},
	});
});

function raceRegistershow(id) {
	jQuery('.race-form-fields').hide();
	jQuery('#race-' + id).show();
}

function table_sort() {
	const styleSheet = document.createElement('style');
	styleSheet.innerHTML = `
        .order-inactive span {
            visibility:hidden;
        }
        .order-inactive:hover span {
            visibility:visible;
        }
        .order-active span {
            visibility: visible;
        }
    `;
	document.head.appendChild(styleSheet);

	document.querySelectorAll('th.order').forEach(th_elem => {
		let asc = true;
		const span_elem = document.createElement('span');
		span_elem.style = 'font-size:0.8rem; margin-left:0.5rem';
		span_elem.innerHTML = '▼';
		th_elem.appendChild(span_elem);
		th_elem.classList.add('order-inactive');

		const index = Array.from(th_elem.parentNode.children).indexOf(th_elem);
		th_elem.addEventListener('click', e => {
			document.querySelectorAll('th.order').forEach(elem => {
				elem.classList.remove('order-active');
				elem.classList.add('order-inactive');
			});
			th_elem.classList.remove('order-inactive');
			th_elem.classList.add('order-active');

			if (!asc) {
				th_elem.querySelector('span').innerHTML = '▲';
			} else {
				th_elem.querySelector('span').innerHTML = '▼';
			}
			const arr = Array.from(
				th_elem.closest('table').querySelectorAll('tbody tr')
			);
			arr.sort((a, b) => {
				const a_val = a.children[index].innerText;
				const b_val = b.children[index].innerText;
				return asc ? a_val.localeCompare(b_val) : b_val.localeCompare(a_val);
			});
			arr.forEach(elem => {
				th_elem.closest('table').querySelector('tbody').appendChild(elem);
			});
			asc = !asc;
		});
	});
}

table_sort();




