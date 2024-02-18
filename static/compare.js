//Js script to make the compare page work
//very similar in function to script.js

document.onload = onLoad();

function onLoad(){
    var topSelect = document.getElementById("topSelect");
    var bottomSelect = document.getElementById("bottomSelect");
}

function resetPage(){
    topSelect.parentElement.classList.add('hidden');
}

function treeChanged(data){
    const xhr = new XMLHttpRequest();
    xhr.open("POST", 'getSections.php', true);

    //Send the proper header information along with the request
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = () => { // Call a function when the state changes.
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            var returnValue = JSON.parse(xhr.responseText);
            topSelect.parentElement.classList.remove('hidden');
            console.log(topSelect.parentElement.classList)
            topSelect.innerHTML = '<option value="nul" selected="selected"></option>';
            returnValue.forEach(o => {
                topSelect.innerHTML = topSelect.innerHTML + "<option value='" + o['filename'] + "'>" + o['startKM'] + "km-" + o['endKM'] + "km | " + o['sesja']['date'] + "</option>";
            });
            bottomSelect.innerHTML = topSelect.innerHTML;
        }
    }
    xhr.send("id=" + data["ID"] + "&mode=1");
}

function requestJSON(filename){
    const xhr = new XMLHttpRequest();
    xhr.open("POST", 'mapLoad.php', true);

    //Send the proper header information along with the request
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = () => { // Call a function when the state changes.
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            return JSON.parse(xhr.responseText);
        }
    }
    xhr.send("filename=" + element.value);
}

function selectChanged(element){
    if(topSelect.value == 'nul' || bottomSelect.value == 'nul'){
        return;
    }
    if(topSelect.value == bottomSelect.value){
        alert("Musisz wybrać dwie RÓŻNE sekcje");
        element.value = 'nul';
        return;
    }
    var bottomData = requestJSON(bottomSelect.value);
    var topData = requestJSON(topSelect.value);
}