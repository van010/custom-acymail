/**
 * @plugin   	vg_trading_tech
 * @developer	vangogh
 * @profile 	https://www.linkedin.com/in/van-hs-0a00511b2/
 * handle api
 */
class vgApiHandling {
	constructor() {
		// todo
	}

	runTest (){
		console.log('vgApiHandling loaded successfully!');
	}

	async updateTtSignalMail() {
		const fieldOpenMail = document.getElementById('for-jform_params_load_acym_mail-open');
		const fieldCloseMail = document.getElementById('for-jform_params_load_acym_mail-close');
		if (!fieldCloseMail || !fieldOpenMail) return;

		const closeMailId = fieldCloseMail.value;
		const openMailId = fieldOpenMail.value;
		const formData = new FormData();
		formData.append('task', 'updateTtSignalMail');
		formData.append('mailIds', JSON.stringify({closeMailId: closeMailId, openMailId: openMailId}));
		// formData.append('openMailId', openMailId);
		try {
			const response = await fetch(joomlaApi, {
				method: 'POST',
				body: formData
			});
			const rawData = await response.json();
			if (!rawData.success) {
				return;
			}
			const data = rawData.data[0];
			showUpdateMailMsg(data.message, data.code);
		} catch (error) {
			console.log(error);
		}
	}

	async loadPage(pageNumber, element) {
		const pageWrapper = document.querySelector('ul.vg-position-pagination');
		if (!pageWrapper) return;
		pageWrapper.querySelectorAll('li').forEach(function (el, idx) {
			if (el.className.includes('active')) {
				el.classList.remove('active')
			}
		});
		element.classList.add('active');
		showLoading();
		// load
		const formData = new FormData();
		formData.append('task', 'pagination');
		formData.append('pageNum', pageNumber);
		try {
			const response = await fetch(joomlaApi, {
				method: 'POST',
				body: formData,
			});
			const rawData = await response.json();
			if (!rawData.success) {
				return;
			}
			const data = rawData.data[0];
			reloadDataTblTrading(data.data.html);
			console.log(data);
			hideLoading();
		} catch (error) {
			console.log(error);
		}
		// complete ajax load data for page
		// then load acymailing templates
		// insert data from trading table to this form
	}
}