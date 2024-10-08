/**
 * @plugin   	vg_trading_tech
 * @developer	vangogh
 * @profile 	https://www.linkedin.com/in/van-hs-0a00511b2/
 * handle general behavior
 */
document.addEventListener('DOMContentLoaded', function (){
    vgTradingInit();
    const inputMailId = document.getElementById('for-jform_params_load_acym_mail-open');
    if (inputMailId) {
        currMailId = inputMailId.value;
    }
});

var $ = jQuery;
let insertedShortCode = false;
let currMailId = null;

function vgTradingInit(){
    const trading_data_label = document.getElementById('jform_params_load_positions-lbl');
    const acymTemplatePreview = document.getElementById('jform_params_preview_acym_mail_templates-lbl');
    trading_data_label.parentElement.remove();
    acymTemplatePreview.parentElement.remove();
    const scripts = [handleTextPath, handleApiPath, handleInsertValues];
    for (var i = 0; i < scripts.length; i++) {
        loadScript(scripts[i], function (){
            console.log('Load script success!');
        });
    }
    copyShortCode();
    hideSidebarSetting();
    setTradingTblStyle();
}

function setTradingTblStyle(){
    const tradingTbl = document.getElementById('tbl-trading-data');
    if (!tradingTbl) return;
    const tradingTblWrapper = tradingTbl.parentNode;
    tradingTblWrapper.style.overflow = 'auto';
}

function loadScript(url, callback){
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = url;
    // Bind the event to the callback function.
    // 'onreadystatechange' for IE compatibility
    script.onload = callback;
    script.onreadystatechange = function () {
        if (this.readyState === 'complete' || this.readyState === 'loaded') {
            callback();
        }
    };
    document.head.appendChild(script);
}

function hideSidebarSetting() {
    const idHide = 'jform_params_hide_sidebar0';
    const idShow = 'jform_params_hide_sidebar1';

    const mainTab = document.getElementById('general');
    if (!mainTab) return;

    const columnWrapper = mainTab.querySelector('div');
    if (!columnWrapper) return;

    const colSetting = columnWrapper.querySelector('div.col-lg-3');
    const tradingContent = columnWrapper.querySelector('div.col-lg-9');
    if (!colSetting || !tradingContent) return;

    const labelHideSidebar = document.querySelector(`label[for="${idHide}"]`);
    const labelShowSidebar = document.querySelector(`label[for="${idShow}"]`);
    if (!labelHideSidebar || !labelShowSidebar) return;

    const hideSidebar = () => {
        colSetting.classList.remove('col-lg-3');
        colSetting.style.display = 'none';
        tradingContent.classList.remove('col-lg-9');
        tradingContent.classList.add('col-lg-12');
    };

    const showSidebar = () => {
        tradingContent.classList.remove('col-lg-12');
        tradingContent.classList.add('col-lg-9');
        colSetting.classList.add('col-lg-3');
        colSetting.style.display = 'block';
    };

    if (document.getElementById(idHide).getAttribute('checked') === 'checked') {
        hideSidebar();
    } else {
        showSidebar();
    }

    labelHideSidebar.addEventListener('click', hideSidebar);
    labelShowSidebar.addEventListener('click', showSidebar);
}

//==========================================
// start user behavior
//==========================================
class vgUserBehaviors {
    constructor() {
        // todo
    }
}
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

function triggerUpdateTtSignalMail(el, mailId, preview=true){
    currMailId = mailId;
    const openMailField = document.getElementById(`for-${el.id}`);
    const closeMailPreview = document.getElementById(`close-mail-${mailId}`);
    console.log(mailId);
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
                var closePreview = document.getElementById(`close-mail-${allMailId[i]}`);
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

function changePosition(tagName, mapKeys, allDataMapKeys, insertPositionBy, element){
    const editor = tinymce.get("acym_mail_preview_editor");
    if (!editor) {
        console.log('Editor not found!');
        return ;
    }

    const htmlData = parsePositionData(mapKeys);
    let textHandling = new vgTextHandling();
    let insertValues = new VgInsertValues();
    const htmlEditor = getEditorBody();
    const rawContent = htmlEditor.innerHTML;
    const shortCode = textHandling.extractStrings(rawContent);
    const data = {
        'short_code': shortCode, // get array of appeared shortCode inside {} from JCE editor: ['netPosition', 'avgBuy', 'productName',...]
        'raw_content' : rawContent, // html string content from JCE editor
        'map_keys': mapKeys, // assoc data between tt_positions and tt_instruments filtered by option: Select Trading Attributes
                            // {accountId: {val: 123, key: 'Account Id'}, avgBuy: {val: 220, key: 'Average Buy'},...};
        'all_data_map_keys': allDataMapKeys, // all assoc data between tt_positions and tt_instruments
                            // {accountId: {val: 123, key: 'Account Id'}, avgBuy: {val: 220, key: 'Average Buy'},...};
    };

    switch (insertPositionBy) {
        case 'insert_multiple_by_pointer':
            insertValues.insertMultipleByPointer(editor, 'pointer', htmlData);
            break;
        case 'insert_multiple_by_text_selected':
            insertValues.insertMultipleByPointer(editor, 'text_selected', htmlData);
            break;
        case 'insert_one_by_shortcode_value':
            insertValues.insertOneByShortCode(editor, data, insertPositionBy);
            break;
        case 'insert_one_by_shortcode_key_value':
            insertValues.insertOneByShortCode(editor, data, insertPositionBy);
            break;
        case '':
        default:
            console.log(`Task ${insertPositionBy} not found!`);
            return ;
    }
}
//==========================================
// end user behavior
//==========================================

//==========================================
// start communicating with JCE Editor
//==========================================

function parsePositionData(mapKeys){
    if (typeof mapKeys === 'string') {
        mapKeys = JSON.parse(mapKeys);
    }
    const mapKeysValue = Object.values(mapKeys);
    const dataTradingWrapper = document.createElement('div');
    dataTradingWrapper.id = 'data-trading-wrapper';
    let content = '';
    for (const property in mapKeysValue) {
        var colName = mapKeysValue[property].key;
        var colVal = mapKeysValue[property].val;
        var pClass = Object.keys(mapKeys)[property];
        if (Object.keys(mapKeys)[property] === 'instrument_link') {
            content += `${colName}: <a class="${pClass}" style="color: #007cd2" href="${colVal}">${colVal}</a>`;
        } else {
            content += `<p class="${pClass}">${colName}: ${colVal}</p>`;
        }
    }
    dataTradingWrapper.innerHTML = content;
    return dataTradingWrapper;
}

function loadMailContentIntoEditor(mailId){
    const bodyContent = getEditorBody();
    if (!bodyContent) return ;
    // id = open-mail-jform_params_acym_temps_preview-9
    const currentMailPreview = document.getElementById(`open-mail-jform_params_acym_temps_preview-${mailId}`);
    if (currentMailPreview) {
        bodyContent.innerHTML = currentMailPreview.innerHTML;
    }
}

function getEditorBody(){
    const editorIframe = document.getElementById('acym_mail_preview_editor_ifr');
    if (!editorIframe) return;
    const docIfr = editorIframe.contentDocument || editorIframe.contentWindow.document;
    return docIfr.querySelector('body#tinymce');
}
//==========================================
// end communicating with JCE Editor
//==========================================

function showLoading(element){
    const tblOverlay = document.createElement('div');
	const tblTrading = document.getElementById('tt-position-lists');
    if (!tblTrading) return ;
	tblOverlay.classList.add('table-overlay');
	tblOverlay.style.display = 'block';
	tblTrading.insertAdjacentElement('afterend', tblOverlay);
}

function showUpdateMailMsg(class_, text, code){
    let msg = null;
    msg = $(`.${class_}`);
    if ($(`.${class_}`).length === 0) {
        msg = $(`#${class_}`);
    }

    if (!msg) {
        console.log(`No tag p.${class_} found!`);
        return;
    }
    msg.html(text)
    if (code !== 200) {
        msg.removeClass('msg-success');
        msg.addClass('msg-fail');
        msg.css('color', 'red');
    } else {
        msg.removeClass('msg-fail');
        msg.addClass('msg-success');
    }
    msg.css('display', 'block');
    msg.fadeOut(9000, 'swing');
}

function showSendMailSuccess(htmlText){
    const existedNotify = document.getElementById('send-mail-success');
    if (existedNotify) {
        existedNotify.remove();
    }
    const notifyMail = document.createElement('div');
    notifyMail.id = 'send-mail-success';
    notifyMail.innerHTML = htmlText
    document.querySelector('.select-acym-templates').insertAdjacentElement('afterend', notifyMail);
}

function hideLoading(){
    const tblOverlay = document.querySelector('div.table-overlay');
    // tblOverlay.style.display = 'block';
    if (!tblOverlay) return;
    tblOverlay.remove();
}

function reloadDataTblTrading(htmlData){
    const tbl_trading_wrapper = document.getElementById('tbl-trading-data');
    tbl_trading_wrapper.innerHTML = htmlData;
}

function reloadPagination(htmlPagination){
    const paginationWrapper = document.getElementById('tbl-trading-pagination');
    paginationWrapper.innerHTML = htmlPagination;
}

function copyShortCode(){
    const labelsWrapper = document.querySelector('div.label-shortcode');
    if (!labelsWrapper) return;
    let allShortcode = '';
    labelsWrapper.querySelectorAll('label').forEach(function (el, idx){
        if (idx === 0) {
            return ;
        }
        var shortcodeText = el.innerText.trim();
        shortcodeText = `{${shortcodeText}}`;
        allShortcode += `${shortcodeText}<br>`;
        el.addEventListener('click', function (e){
            navigator.clipboard.writeText(shortcodeText);
            alert(`Copied ${shortcodeText} to clipboard!`);
        });
    })
    const sampleAllShortcode = document.createElement("p");
    sampleAllShortcode.id = 'all-sample-shortcode';
    sampleAllShortcode.innerHTML = allShortcode;
    labelsWrapper.insertAdjacentElement('afterend', sampleAllShortcode);
    sampleAllShortcode.addEventListener('click', function (){
        navigator.clipboard.writeText(allShortcode);
        alert(`Copied ${allShortcode} to clipboard!`);
    })
}