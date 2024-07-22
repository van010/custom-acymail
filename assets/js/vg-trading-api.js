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

	async searchPosition(element) {
		let inputField = null;
		if (element.tagName.toLowerCase() === 'input') {
			inputField = element;
		} else {
			inputField = element.previousSibling;
			if (!inputField) return;
		}
		const searchStr = inputField.value.trim();
		const textHandle = new vgTextHandling();
		if (!searchStr) return;
		const data = {};
		if (searchStr.includes('d:')) {
			data.date = searchStr.replace('d:', '');
		} else if (searchStr.includes('db:')) {
			data.date_in = textHandle.replaceMultiple(searchStr, {'db:': '', '_': ' '});
		}
		showLoading();
		const formData = new FormData();
		formData.append('task', 'searchPosition');
		formData.append('data', JSON.stringify(data));
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
				reloadDataTblTrading(data.data.html);
				reloadPagination(data.data.htmlPagination);
			} else {
				alert(data.message);
			}
			hideLoading();
		}catch(error){
		    console.log(error);
		}
	}

	async sendMailToUsers() {
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

	async loadPage(pageNumber, task, element) {
		if (element.classList.contains('active')) {
			return ;
		}
		let pageNum = 0;
		const paginationWrapper = element.parentNode;
		const currPageEl = paginationWrapper.querySelector('li.active');
		if (currPageEl) {
		    const currPage = parseInt(currPageEl.getAttribute('curr-page'));
		}
		const firstPageEl = paginationWrapper.querySelector('li.first');
		if (firstPageEl) {
			const firstPage = parseInt(firstPageEl.getAttribute('curr-page'));
		}
		const lastPageEl = paginationWrapper.querySelector('li.last');
		if (lastPageEl) {
			const lastPage = parseInt(paginationWrapper.querySelector('li.last').getAttribute('curr-page'));
		}
		switch (task) {
			case 'currPage':
				pageNum = pageNumber;
				break;
			case 'prev':
				pageNum = currPage - 1;
				if (currPage - 1 <= firstPage) {
					pageNum = firstPage
				} else {
					pageNum = currPage - 1
				}
				break;
			case 'next':
				if (currPage + 1 >= lastPage) {
				    pageNum = lastPage;
				} else {
					pageNum = currPage + 1;
				}
				break;
			case '':
			default:
				pageNum = 0;
				break;
		}
		console.log(`Load data for page: ${pageNum}`);
		// this.reloadPagination(pageNum, task, firstPage, lastPage);
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
		formData.append('pageNum', pageNum);
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
			hideLoading();
		} catch (error) {
			console.log(error);
		}
		// complete ajax load data for page
		// then load acymailing templates
		// insert data from trading table to this form
	}

	reloadPagination (pageNum, task, firstPage, lastPage) {
		const pageWrapper = document.querySelector('ul.vg-position-pagination');
		if (!pageWrapper) return;
		const allPages = pageWrapper.querySelectorAll('li');
		const currPage = document.querySelector(`li.page-${pageNum}`);
		if (currPage) {
			allPages.forEach(function (el, idx) {
				if (el.className.includes('active')) {
					el.classList.remove('active');
				}
			});
			currPage.classList.add('active');
		} else {
			if (task === 'next') {
			    pageNum += 1;
			} else {
				pageNum -= 1;
			}
			if (pageNum === firstPage || pageNum === lastPage) {
			    return ;
			}
			allPages.forEach(function (el, idx) {
				if ([0, 1, allPages.length - 1, allPages.length - 2].includes(idx)) {
					return;
				}
				const currP = parseInt(el.getAttribute('curr-page'));
				const _class = `page-${pageNum + idx-3}`;
				el.removeAttribute('onclick');
				// el.className = '';
				// el.classList.add(_class);
				// el.classList.add('active');
				el.setAttribute('onclick', `new vgApiHandling.loadPage(${pageNum + idx-3}, "currPage", this)`);
				el.setAttribute('curr-page', pageNum + idx-3);
				el.querySelector('a').innerText = '';
				el.querySelector('a').innerText = pageNum + idx-3;
			});
		}
	}
}