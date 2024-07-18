/**
 * @plugin   	vg_trading_tech
 * @developer	vangogh
 * @profile 	https://www.linkedin.com/in/van-hs-0a00511b2/
 * handle api
 */
class vgApiHandling {
	constructor() {
		// todo
		this.baseUrl = Joomla.getOptions('system.paths');
		this.joomlaApi = this.baseUrl.base + '/index.php?option=com_ajax&plugin=vg_trading_tech&format=json&group=system';
	}

	runTest (){
		console.log('vgApiHandling loaded successfully!');
	}

	async sendMailToUsers() {
		console.log('sendMailToUsers');
		const btnSendMail = document.getElementById('vg-send-mail');
		btnSendMail.setAttribute('disabled', true);
		btnSendMail.classList.add('disabled-button');
		const editorContent = getEditorBody().innerHTML ?? '';
		const formData = new FormData();
		formData.append('task', 'sendMail');
		formData.append('mailBody', editorContent);
		formData.append('mailId', currMailId);
		try{
			const response = await fetch(this.joomlaApi, {
				method: 'POST',
				body: formData
			});
			const rawData = await response.json();
			if (!rawData.success) {
				console.log(rawData.message);
			    return ;
			}
			const data = rawData.data[0];
			if (data.code === 200) {
			    showSendMailSuccess(data.message);
				btnSendMail.removeAttribute('disabled');
				btnSendMail.classList.remove('disabled-button');
			}
		}catch(error){
		    console.log(error);
		}
	}

	async updateUsersSendMail($userList) {

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
			const response = await fetch(this.joomlaApi, {
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
			const response = await fetch(this.joomlaApi, {
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