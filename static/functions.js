//gets sections as options
function sessionSelected(select, outSelectList){
    const xhr = new XMLHttpRequest();
    xhr.open("POST", 'getSections.php', true);

    //Send the proper header information along with the request
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = () => { // Call a function when the state changes.
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            var returnValue = JSON.parse(xhr.responseText);
            outSelectList.forEach(e =>{
                var element = e;
                element.parentElement.style = "";
                element.innerHTML = '<option value="nul" selected="selected"></option>';
                returnValue.forEach(o => {
                    element.innerHTML = element.innerHTML + "<option value='" + o['filename'] + "'>" + o['startKM'] + "km-" + o['endKM'] + "km</option>";
                });
            });
            
        }
    }
    xhr.send("id=" + select.value);
}
