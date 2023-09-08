/*
    Function will set all competencies for a peice of evidence to met
    It will only work if this file is included at the bottom in /admin/tool/lp/templates/user_evidence_list_page.mustache
*/
function metAll(id){
    const errorText = $(`#metall_danger_${id}`)[0];
    errorText.style.display = 'none';
    let params = '';
    comp[id - 1].forEach(function(item, index){
        switch (index){
            case 0:
                params += 'c'+index+'='+item;
                break;
            default:
                params += '&c'+index+'='+item;
                break;
        }
    });
    params += (params != '') ? '&t='+comp[id - 1].length+'&u='+uid : '&u='+uid;
    const xhr = new XMLHttpRequest();
    xhr.open('POST', './../../../local/met_competencies/classes/inc/met_competencies_all.inc.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        if(this.status == 200){
            const text = JSON.parse(this.responseText);
            if(text['error']){
                errorText.innerText = text['error'];
                errorText.style.display = 'block';
            }
        } else {

        }
    }
    xhr.send(params);
}