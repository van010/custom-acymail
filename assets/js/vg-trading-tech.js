
document.addEventListener('DOMContentLoaded', function (){

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

