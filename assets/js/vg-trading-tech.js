
document.addEventListener('DOMContentLoaded', function (){
    vgTradingInit();
})

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
    formData.append('vgTask', 'pagination');
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

function showLoading(element){
    const tblOverlay = document.createElement('div');
	const tblTrading = document.getElementById('tt-position-lists');
	tblOverlay.classList.add('table-overlay');
	tblOverlay.style.display = 'block';
	tblTrading.insertAdjacentElement('afterend', tblOverlay);
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