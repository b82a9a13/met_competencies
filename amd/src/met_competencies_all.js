/*
    Function will set all competencies for a peice of evidence to met
    It will only work if this file is included at the bottom in /admin/tool/lp/templates/user_evidence_list_page.mustache
*/
function metAll(id){
    const errorText = $(`#metall_danger_${id}`)[0];
    const successText = $(`#metall_success_${id}`)[0];
    successText.style.display = 'none';
    errorText.style.display = 'none';
    const index = comp.findIndex((innerArray) => innerArray[0] === id);
    if(index !== -1){
        let params = '';
        comp[index][1].forEach(function(item, index){
            switch (index){
                case 0:
                    params += 'c'+index+'='+item;
                    break;
                default:
                    params += '&c'+index+'='+item;
                    break;
            }
        });
        params += (params != '') ? '&t='+comp[index][1].length+'&u='+uid : '&u='+uid;
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './../../../local/met_competencies/classes/inc/met_competencies_all.inc.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['error']){
                    errorText.innerText = text['error'];
                    errorText.style.display = 'block';
                } else if(text['return']){
                    successText.innerText = 'Success';
                    successText.style.display = 'block';
                    $('.user-evidence-competencies').eq(index).find('li').each(function(ind){
                        $(this).find('span')[0].innerText = "(-)";
                    });
                } else {
                    errorText.innerText = 'Submit error';
                    errorText.style.display = 'block';
                }
            } else {
                errorText.innerArray = 'Connection error';
                errorText.style.display = 'block';
            }
        }
        xhr.send(params);
    } else {
        errorText.innerArray = 'Error, contact a admin';
        errorText.style.display = 'block';
    }
}