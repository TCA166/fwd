//script for handling manage.php

function toggleElement(id){
    element = document.getElementById(id);
    if(element.style.display == "none"){
        element.style.display = "";
    }
    else{
        element.style.display = "none";
    }
}

function resetAddCard(){
    nawierzchniaEl.style.display = "none";
    pasEl.style.display = "none";
    sekcjeEl.style.display = "none";
    sekcjaView.style.display = "none";
    document.getElementById("pointList").innerHTML = "";
    infoTable.parentNode.style.display = "none";
    infoTableHeader.parentNode.style.display = "none";
}

function loadSection(data){
    var list = document.getElementById("pointList");
    data.forEach((e, i) => {
        var elementH = document.createElement("li"); //create list item
        var elementH2 = document.createElement("span"); //put text in span so that value for this point may be stored
        elementH2.value = JSON.stringify(e).replaceAll('"',"'");
        elementH2.innerHTML = "Km " + e["station"].replaceAll('NA',''); //store the display text
        elementH2.setAttribute("onclick", "viewPoint(this);"); //add onclick attr
        elementH2.id = i;
        elementH.appendChild(elementH2);
        list.appendChild(elementH);
    });
}

//Will round the number to two decimal points
function twoDecimal(val){
    return (Math.round(val* 100) / 100).toFixed(2);
}

//Removes all occurences of this class from the page
function removeClass(className){
    var elements = document.getElementsByClassName(className);
    while(elements.length){ 
        elements[0].classList.remove(className);
    }
}
//Called when user clicks on list
function viewPoint(element){
    removeClass("selected")
    //highlight the list item
    element.classList.add("selected");
    data = JSON.parse(element.value.replaceAll("'",'"')); //parse the data
    infoTable = document.getElementById("infoTable"); //get the station drop data table
    infoTableHeader = document.getElementById("infoTableHeader");
    infoTable.parentNode.style.display = "";
    infoTableHeader.parentNode.style.display = "";
    document.getElementById("coords").innerHTML = data["lon"] + "," + data["lat"];
    if(data["lon"] == null || data["lat"] == null){
        document.getElementById("coords").innerHTML += ' <i data-bs-toggle="tooltip" data-bs-title="Wygląda na to, że dane źródłowe nie zawierały koordynatów." class="bi bi-patch-question-fill"></i>'
    }
    document.getElementById("station").innerHTML = data["station"].replaceAll("NA", "") + " km";
    document.getElementById("asphalt").innerHTML = data["asphalt"] + " °C";
    document.getElementById("surface").innerHTML = data["surface"] + " °C";
    document.getElementById("air").innerHTML = data["air"] + " °C";
    document.getElementById("sciDisplay").innerHTML = twoDecimal(data["SCI"]) + "μm";
    document.getElementById("bdiDisplay").innerHTML = twoDecimal(data["BDI"]) + "μm";
    document.getElementById("bciDisplay").innerHTML = twoDecimal(data["BCI"]) + "μm";

    infoTable.innerHTML = ""; //wipe the lower rows
    //Alter the mini table header 
    var minihead = document.getElementById("miniHead");
    minihead.innerHTML = "<th>Naprężenia [kPa]</th>";
    for(var i = 0; i < Object.entries(data["drops"][0]).length - 1; i++){
        var th = document.createElement("th");
        th.innerText = "D" + (i + 1).toString() + " [μm]";
        minihead.appendChild(th);
    }
    //display the drop data
    var i = 0;
    data["drops"].forEach((drop) => { //foreach drop
        if(i >= 0){
            var row = document.createElement("tr"); //create a new row
            Object.entries(drop).forEach(([key, value]) => {
                var td = document.createElement("td");
                td.textContent = twoDecimal(value);
                row.appendChild(td);
            });
            infoTable.appendChild(row);
        }
        i = i + 1;
    });
}

function joinSelectChanged(element, topSelect, bottomSelect){
    if(topSelect.value == 'nul' || bottomSelect.value == 'nul'){
        return;
    }
    if(topSelect.value == bottomSelect.value){
        alert("Musisz wybrać dwie RÓŻNE sekcje");
        element.value = 'nul';
        return;
    }
}

function delClick(){
    var nodeId = null;
    var enabled = $('#tree').treeview('getSelected', nodeId);
    const xhr = new XMLHttpRequest();
    xhr.open("POST", 'deleteNode.php', true);

    //Send the proper header information along with the request
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = () => { // Call a function when the state changes.
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            window.location.reload();
        }
        else{
            console.log(xhr.responseText);
        }
    }
    xhr.send("id=" + enabled[0]["ID"] + "&type=" + enabled[0]["type"]);
}