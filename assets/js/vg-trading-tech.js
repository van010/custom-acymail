
document.addEventListener('DOMContentLoaded', function (){
    vgTradingInit();
})

var $ = jQuery;
const baseUrl = Joomla.getOptions('system.paths');
const joomlaApi = baseUrl.base + '/index.php?option=com_ajax&plugin=vg_trading_tech&format=json&group=system';

function selectAllPositionAttrs(element, task){
    const fieldPositions = document.getElementById(element.getAttribute('data-for').trim());
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
    const acymTemplatePreview = document.getElementById('jform_params_preview_acym_mail_templates-lbl');
    trading_data_label.parentElement.remove();
    acymTemplatePreview.parentElement.remove();
}

function triggerSearchPosition(element){
    console.log(element.value);
}

function triggerUpdateTtSignalMail(el, mailId, preview=true){
    const openMailField = document.getElementById(`for-${el.id}`);
    const closeMailPreview = document.getElementById(`close-mail-jform_params_load_acym_mail-${mailId}`);
    const openMailPreview = document.getElementById(`open-mail-jform_params_load_acym_mail-${mailId}`);
    var allMailId = el.getAttribute('data-id');

    if (openMailField) {
        openMailField.value = mailId;
    }

    if (preview) {
        let task = 'close';
        if (el.id.includes('-open')) {
            task = 'open';
        }
        allMailId = JSON.parse(allMailId);
        allMailId = allMailId.filter(function (number) {
            return number != mailId;
        })

        if (task === 'close') {
            closeMailPreview.style.display = 'block';
        } else {
            openMailPreview.style.display = 'block';
        }
        for (let i = 0; i < allMailId.length; i++) {
            if (task === 'close') {
                var closePreview = document.getElementById(`close-mail-jform_params_load_acym_mail-${allMailId[i]}`);
                if (!closePreview) return;
                closePreview.style.display = 'none';
            }
            if (task === 'open') {
                var openPreview = document.getElementById(`open-mail-jform_params_load_acym_mail-${allMailId[i]}`);
                if (!openPreview) return;
                openPreview.style.display = 'none';
            }
        }
    }
    loadMailContentIntoEditor(mailId);
}

//==========================================
// ajax handling
//==========================================

function loadMailContentIntoEditor(mailId){
    const editorIframe = document.getElementById('jform_params_preview_acym_mail_templates__ifr');
    if (!editorIframe) return;
    const docIfr = editorIframe.contentDocument || editorIframe.contentWindow.document;
    const bodyContent = docIfr.querySelector('body#tinymce');
    // id = open-mail-jform_params_acym_temps_preview-9
    const currentMailPreview = document.getElementById(`open-mail-jform_params_acym_temps_preview-${mailId}`);
    if (currentMailPreview) {
        bodyContent.innerHTML = currentMailPreview.innerHTML;
    }
}

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