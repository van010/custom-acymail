
document.addEventListener('DOMContentLoaded', function (){
    vgTradingInit();
})

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