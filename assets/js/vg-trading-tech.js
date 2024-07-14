
document.addEventListener('DOMContentLoaded', function (){
    vgTradingInit();
})

var $ = jQuery;
const baseUrl = Joomla.getOptions('system.paths');
const joomlaApi = baseUrl.base + '/index.php?option=com_ajax&plugin=vg_trading_tech&format=json&group=system';

function selectAllPositionAttrs(element, task){
    const fieldPositions = document.getElementById('jform_params_select_positions');
    if (!fieldPositions) return;
    fieldPositions.querySelectorAll('input').forEach(function (el, idx){
        if (task) {
            el.setAttribute('checked', true);
        } else {
            el.removeAttribute('checked');
        }
    });
}

function changePosition(tagName, element){
    console.log(tagName);
}

function vgTradingInit(){
    const trading_data_label = document.getElementById('jform_params_load_positions-lbl');
    trading_data_label.parentElement.remove();
}

function triggerSearchPosition(element){
    console.log(element.value);
}

function triggerUpdateTtSignalMail(el, mailId){
    const openMailField = document.getElementById(`for-${el.id}`);
    openMailField.value = mailId;
}

//==========================================
// ajax handling
//==========================================
async function loadPage(pageNumber, element){
    const pageWrapper = document.querySelector('ul.vg-position-pagination');
    if (!pageWrapper) return;
    pageWrapper.querySelectorAll('li').forEach(function (el, idx){
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
    try{
        const response = await fetch(joomlaApi, {
            method: 'POST',
            body: formData,
        });
        const rawData = await response.json();
        if (!rawData.success) {
            return ;
        }
        const data = rawData.data[0];
        reloadDataTblTrading(data.data.html);
        console.log(data);
        hideLoading();
    }catch(error){
        console.log(error);
    }
    // complete ajax load data for page
    // then load acymailing templates
    // insert data from trading table to this form
}

async function updateTtSignalMail(){
    const fieldOpenMail = document.getElementById('for-jform_params_load_acym_mail-open');
    const fieldCloseMail = document.getElementById('for-jform_params_load_acym_mail-close');
    if (!fieldCloseMail || !fieldOpenMail) return;

    const closeMailId = fieldCloseMail.value;
    const openMailId = fieldOpenMail.value;
    const formData = new FormData();
    formData.append('task', 'updateTtSignalMail');
    formData.append('mailIds', JSON.stringify({closeMailId: closeMailId, openMailId: openMailId}));
    // formData.append('openMailId', openMailId);
    try{
        const response = await fetch(joomlaApi, {
            method: 'POST',
            body: formData
        });
        const rawData = await response.json();
        if (!rawData.success) {
            return ;
        }
        const data = rawData.data[0];
        showUpdateMailMsg(data.message, data.code);
    }catch(error){
        console.log(error);
    }
}

//==========================================
// end handling ajax
//==========================================

function showLoading(element){
    const tblOverlay = document.createElement('div');
	const tblTrading = document.getElementById('tt-position-lists');
	tblOverlay.classList.add('table-overlay');
	tblOverlay.style.display = 'block';
	tblTrading.insertAdjacentElement('afterend', tblOverlay);
}

function showUpdateMailMsg(text, code){
    const msg = $('p.update-mail-msg');
    msg.text(text)
    if (code !== 200) {
        msg.removeClass('msg-success');
        msg.addClass('msg-fail');
    } else {
        msg.removeClass('msg-fail');
        msg.addClass('msg-success');
    }
    msg.css('display', 'block');
    msg.fadeOut(2500, 'swing');
}

function hideLoading(){
    const tblOverlay = document.querySelector('div.table-overlay');
    // tblOverlay.style.display = 'block';
    tblOverlay.remove();
}

function reloadDataTblTrading(htmlData){
    const tbl_trading_wrapper = document.getElementById('tbl-trading-data');
    tbl_trading_wrapper.querySelector('table').remove();
    tbl_trading_wrapper.innerHTML = htmlData;
}