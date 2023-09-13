const macbtn = $('#mac_btn')[0];
macbtn.addEventListener('click', ()=>{
    const errorText = $('#mac_error')[0];
    const successText = $('#mac_success')[0];
    successText.style.display = 'none';
    errorText.style.display = 'none';
    let type = '';
    if(macbtn.innerText.includes('Disable')){
        type = 'd';
    } else if(macbtn.innerText.includes('Enable')){
        type = 'e';
    }
    if(type != ''){
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './classes/inc/met_competencies_all_enabled.inc.php', false);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function(){
            if(this.status == 200){
                const text = JSON.parse(this.responseText);
                if(text['error']){
                    errorText.innerText = text['error'];
                    errorText.style.display = 'block';
                } else if(text['return']){
                    switch (type){
                        case 'd':
                            macbtn.innerText = macbtn.innerText.replace('Disable', 'Enable');
                            break;
                        case 'e':
                            macbtn.innerText = macbtn.innerText.replace('Enable', 'Disable');
                            break;
                    }
                    successText.innerText = 'Success';
                    successText.style.display = 'block';
                } else {
                    errorText.innerText = 'Submit error';
                    errorText.style.display = 'block';
                }
            } else {
                errorText.innerText = 'Connection error';
                errorText.style.display = 'block';
            }
        }
        xhr.send(`t=${type}`);
    } else {
        errorText.innerText = 'Error, Contact a admin';
        errorText.style.display = 'block';
    }
});