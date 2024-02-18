//Js scripts for index.php
//These make all sorts of things happen, including handling events, but also button clicks etc
//Requires the Leaflet library to work
var maps = []; //variable for map obj storage
var marker; //current marker on map
var infoTable; //HTML element that holds the station info table
var infoTableHeader;
document.onload = onLoad();
var pointsHidden = []; //list necessary for finding the right internal point based on point from map
var StationMode = 1; //Current data display mode
var jsonData; //Currently loaded json data
//stuff for chart highlighting
var selectedPointsCorrds = [];
var startId = null;
var selectedLine;
//Colored icons taken from:
//https://github.com/pointhi/leaflet-color-markers/blob/master/js/leaflet-color-markers.js
var redIcon = new L.Icon({
	iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
	shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
	iconSize: [25, 41],
	iconAnchor: [12, 41],
	popupAnchor: [1, -34],
	shadowSize: [41, 41]
});
var violetIcon = new L.Icon({
	iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-violet.png',
	shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
	iconSize: [25, 41],
	iconAnchor: [12, 41],
	popupAnchor: [1, -34],
	shadowSize: [41, 41]
});
//Is executed on page load
function onLoad(){
    L.Map.addInitHook(function () { //make leaflet update the map array
        maps.push(this);
    });
    infoTable = document.getElementById("infoTable"); //get the station drop data table
    infoTableHeader = document.getElementById("infoTableHeader");
    document.getElementById("sessionSelect").value = ""; //reset the select element
    tooltipReload();
}
//Handles user input into the select box
function selectChanged(select){
    if(select.value == "nul"){
        infoTable.parentNode.style.display = "none";
        infoTableHeader.parentNode.style.display = "none";
        resetLayout();
        return;
    }
    const xhr = new XMLHttpRequest();
    xhr.open("POST", 'mapLoad.php', true);

    //Send the proper header information along with the request
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = () => { // Call a function when the state changes.
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            alert("Wyświetlono: " + select.options[select.selectedIndex].text);
            reloadMap(JSON.parse(xhr.responseText));
        }
    }
    xhr.send("filename=" + select.value);
}
//Creates a new iframe map in the container
function reloadMap(response){
    jsonData = response;
    resetLayout(); //cosmetic reset
    var len = response.length;
    var list = document.getElementById("pointList");
    var points = [];
    response.forEach((element, i) => { //foreach station create
        var coords = L.latLng({lon: element["lon"], lat: element["lat"]}); //create latlong element
        points.push(coords); //save it
        //list creation mechanism
        var elementH = document.createElement("li"); //create list item
        var elementH2 = document.createElement("span"); //put text in span so that value for this point may be stored
        elementH2.value = JSON.stringify(element).replaceAll('"',"'");
        elementH2.innerHTML = "Km " + element["station"].replaceAll('NA',''); //store the display text
        elementH2.setAttribute("onclick", "viewPoint(this);"); //add onclick attr
        elementH2.id = i;
        elementH.appendChild(elementH2);
        pointsHidden.push({'lng':element["lon"], 'lat':element["lat"], 'val':elementH2})//append to list of points
        list.appendChild(elementH);
        //chart rendering
        //assign generic values for the chart box
        var width = (100 * (1/len)).toString() + "%"
        var box = document.createElement("div"); //we are going to create a set of very thin boxes with variable colours
        box.style.width = width;
        box.classList.add("chartElement");
        box.setAttribute("name", i);
        box.setAttribute("id", i);
        box.setAttribute("onmouseover","hovered(" + i.toString() + ")");
        //Decide the numerical values of SCI BDI and BCI
        var SCI = 0;
        var BDI = 0;
        var BCI = 0;
        switch(StationMode){
            case 0: //case we want to display data based on all drops
                SCI = element["SCI"];
                BDI = element["BDI"];
                BCI = element["BCI"];
                break;
            case 1: //case we want to display data based on 70% of drops
                var numDrops = Math.floor(element["drops"].length * 0.75);
                for(var n = element["drops"].length - numDrops; n < element["drops"].length; n++){
                    SCI += element["drops"][n]["SCI"];
                    BDI += element["drops"][n]["BDI"];
                    BCI += element["drops"][n]["BCI"];
                }
                SCI = SCI / numDrops;
                BDI = BDI / numDrops;
                BCI = BCI / numDrops;
            case 2: //case we ignore everything apart from the last drop
                var lastDrop = element["drops"][element["drops"].length - 1];
                SCI = lastDrop["SCI"];
                BDI = lastDrop["BDI"];
                BCI = lastDrop["BCI"];
        }
        //SCI rendering
        if(SCI < 120){
            box.style.backgroundColor = "#00ff00";
        }
        else if(SCI < 160){
            box.style.backgroundColor = "#006600";
        }
        else if(SCI < 200){
            box.style.backgroundColor = "#ff9900";
        }
        else if(SCI < 240){
            box.style.backgroundColor = "#cc3300";
        }
        else{
            box.style.backgroundColor = "#ff0000";
        }
        document.getElementById("SCI").appendChild(box);
        //Now BDI
        box = document.createElement("div"); 
        box.style.width = width;
        box.classList.add("chartElement");
        box.setAttribute("name", i);
        box.setAttribute("onmouseover","hovered(" + i.toString() + ")");
        if(BDI < 90){
            box.style.backgroundColor = "#00ff00";
        }
        else if(BDI < 120){
            box.style.backgroundColor = "#006600";
        }
        else if(BDI < 150){
            box.style.backgroundColor = "#ff9900";
        }
        else if(BDI < 180){
            box.style.backgroundColor = "#cc3300";
        }
        else{
            box.style.backgroundColor = "#ff0000";
        }
        document.getElementById("BDI").appendChild(box);
        //Now BCI
        box = document.createElement("div"); 
        box.style.width = width;
        box.classList.add("chartElement");
        box.setAttribute("name", i);
        box.setAttribute("onmouseover","hovered(" + i.toString() + ")");
        if(BCI < 45){
            box.style.backgroundColor = "#00ff00";
        }
        else if(BCI < 60){
            box.style.backgroundColor = "#006600";
        }
        else if(BCI < 75){
            box.style.backgroundColor = "#ff9900";
        }
        else if(BCI < 90){
            box.style.backgroundColor = "#cc3300";
        }
        else{
            box.style.backgroundColor = "#ff0000";
        }
        document.getElementById("BCI").appendChild(box);
    });
    var mediumElement = response[parseInt(response.length / 2)]; //find the element the map should focus on
    if(checkForAnyNULLCoords(response) == true){
        document.getElementById("BDI").parentElement.parentElement.parentElement.style.width = "82%";
        mapContainer.style.display = "none";
        return;
    }
    //map rendering
    // initialize Leaflet
    var map = L.map('map').setView({lon: mediumElement["lon"], lat: mediumElement["lat"]}, 2);
    map.setZoom(12); //zoom in slightly
    // add the OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
    }).addTo(map);
    // show the scale bar on the lower left corner
    L.control.scale({imperial: false, metric: true}).addTo(map);
    //create a line on map representing the path
    var line = new L.polyline(points, {
        color: 'blue',
        weight: 10,
        opacity: 0.5,
        smoothFactor: 3
  
    });
    line.on('mousedown', clickedLine);
    line.addTo(map);
}
//Is executed on list element click
function viewPoint(element){
    removeClass("selected")
    //highlight the list item
    element.classList.add("selected");
    element.scrollIntoView({block: "center"}); //scroll the list to display the point
    //highlight the chart
    document.getElementsByName(element.id).forEach(selected => {
        selected.classList.add("selected");
    });
    data = JSON.parse(element.value.replaceAll("'",'"')); //parse the data
    if(maps.length > 0){
        if(marker != undefined){
            marker.remove();
        }
        if(containsObject(selectedPointsCorrds, data["lat"], data["lon"])){
            marker = L.marker([data["lat"], data["lon"]], {icon: violetIcon}); //create new marker
        }
        else{
            marker = L.marker({lon: data["lon"], lat: data["lat"]}); //create new marker
        }
        marker.bindPopup(data["station"]).addTo(maps[0]);
    }
    //display the raw data
    infoTable.parentNode.style.display = "";
    infoTableHeader.parentNode.style.display = "";
    document.getElementById("coords").innerHTML = data["lon"] + "," + data["lat"];
    if(data["lon"] == null || data["lat"] == null){
        document.getElementById("coords").innerHTML += ' <i data-bs-toggle="tooltip" data-bs-title="Wygląda na to, że dane źródłowe nie zawierały koordynatów." class="bi bi-patch-question-fill"></i>'
    }
    document.getElementById("station").innerHTML = data["station"].replaceAll("NA", "") + " km";
    document.getElementById("asphalt").innerHTML = data["asphalt"] + " °C";
    document.getElementById("surface").innerHTML = data["surface"] + " °C";
    //document.getElementById("surface2").innerHTML = "T = " + data["surface"] + "°C, ";
    document.getElementById("air").innerHTML = data["air"] + " °C";
    var threshHold = 0;
    //Handle different display for different modes
    switch(StationMode){
        case 0:
            document.getElementById("sciDisplay").innerHTML = twoDecimal(data["SCI"]) + "μm";
            document.getElementById("bdiDisplay").innerHTML = twoDecimal(data["BDI"]) + "μm";
            document.getElementById("bciDisplay").innerHTML = twoDecimal(data["BCI"]) + "μm";
            break;
        case 1:
            var numDrops = Math.floor(data["drops"].length * 0.75);
            if(numDrops == 0){
                numDrops = 1;
            }
            var avgSCI = 0;
            var avgBDI = 0;
            var avgBCI = 0;
            threshHold = data["drops"].length - numDrops
            for(var i = threshHold; i < data["drops"].length; i++){
                avgSCI += data["drops"][i]["SCI"];
                avgBDI += data["drops"][i]["BDI"];
                avgBCI += data["drops"][i]["BCI"];
            }
            avgSCI = avgSCI / numDrops;
            avgBDI = avgBDI / numDrops;
            avgBCI = avgBCI / numDrops;
            document.getElementById("sciDisplay").innerHTML = twoDecimal(avgSCI) + "μm";
            document.getElementById("bdiDisplay").innerHTML = twoDecimal(avgBDI) + "μm";
            document.getElementById("bciDisplay").innerHTML = twoDecimal(avgBCI) + "μm";
            break;
        case 2:
            threshHold = data["drops"].length - 1;
            var lastDrop = data["drops"][threshHold];
            document.getElementById("sciDisplay").innerHTML = twoDecimal(lastDrop["SCI"]) + "μm";
            document.getElementById("bdiDisplay").innerHTML = twoDecimal(lastDrop["BDI"]) + "μm";
            document.getElementById("bciDisplay").innerHTML = twoDecimal(lastDrop["BCI"]) + "μm";
            break;
    }
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
        if(i >= threshHold){
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
    tooltipReload();
}
//called when user hovers a mouse over the chart
function hovered(i){
    viewPoint(document.getElementById(i));
}
//Called when user clicks on line on map
function clickedLine(event){
    var lng = event["latlng"]["lng"];
    var lat = event["latlng"]["lat"];
    var closest; //we have to find the closest point
    var dist = quickDist(pointsHidden[0], lng, lat);
    //in order to save time we will be using approx without sqrt
    //cope and seethe math bois
    for(var i = 0; i < pointsHidden.length; i++){
        var diffloc = quickDist(pointsHidden[i], lng, lat);
        if(diffloc < dist){
            closest = pointsHidden[i]['val'];
            dist = diffloc;
        }
    }
    viewPoint(closest);
}
//cosmetic reset
function resetLayout(){
    //Unhide everything
    hidden = document.getElementsByClassName("hidden");
    while(hidden.length > 0){
        hidden[0].classList.remove("hidden");
    }
    //reset the map container
    var mapContainer = document.getElementById("mapContainer");
    mapContainer.innerHTML = "";
    var newDiv = document.createElement('div');
    newDiv.id = 'map';
    newDiv.style.height = "100%";
    newDiv.style.width = "100%";
    mapContainer.appendChild(newDiv);
    maps.pop(0); //remove map variable from the maps list
    points = [,]; //wipe the points
    //Unselect elements
    removeClass("selected");
    removeClass("highlighted");
    selectedPointsCorrds = [];
    //wipe the chart
    var elements = document.getElementsByClassName("chartElement");
    while(elements.length){ 
        elements[0].parentElement.removeChild(elements[0]);
    }
    //wipe all list items
    var items = document.getElementsByTagName("li");
    var i = 0;
    while(items.length > 3){
        if(items[i].classList.contains("nav-item") != true){
            items[i].parentNode.removeChild(items[i]);
        }
        else{
            i = i + 1;
        }
    }
    //reset chart width
    document.getElementById("BDI").parentElement.parentElement.parentElement.style.width = "";
    mapContainer.style.display = "";
    document.getElementById("selInfo").innerHTML = "";
    document.getElementById("distInfo").innerHTML = "0";
    document.getElementById("surface2").innerHTML = "";
}
//Will round the number to two decimal points
function twoDecimal(val){
    return (Math.round(val* 100) / 100).toFixed(2);
}
//Doesnt actually calculate distance. Just a value TIED to distance. So essencially only good for judging which element is farthest;
function quickDist(element, lng, lat){
    var difflng = lng - element['lng'];
    var difflat = lat - element['lat'];
    return Math.abs(difflng) + Math.abs(difflat);
}
//Invoked by buttons next to the chart
function move(mv){
    var selected = document.getElementsByClassName("selected")
    if(selected.length > 0){
        var id = parseInt(selected[0].id);
        if((id + mv) < 0 || (id + mv) >= jsonData.length){
            return;       
        }
        var element = document.getElementById(id + mv);
        viewPoint(element);
    }
}
//Invoked when the mode change buttons are clicked
function mode(modeNum){
    StationMode = modeNum; //set the correct display mode
    document.getElementById("allBtn").classList.remove("active");
    document.getElementById("70Btn").classList.remove("active");
    document.getElementById("1Btn").classList.remove("active");
    //button display handling
    switch(modeNum){
        case 0:
            document.getElementById("allBtn").classList.add("active");
            break;
        case 1:
            document.getElementById("70Btn").classList.add("active");
            break;
        case 2:
            document.getElementById("1Btn").classList.add("active");
            break;
    }
    //alter the displayed data accordingly
    var selEl = document.getElementsByClassName("selected");
    if(selEl.length > 0){
        var id = parseInt(selEl[0].id);
        if(typeof jsonData !== 'undefined'){
            reloadMap(jsonData);
        }
        var element = document.getElementById(id);
        viewPoint(element);
    }
    
}
//Removes all occurences of this class from the page
function removeClass(className){
    var elements = document.getElementsByClassName(className);
    while(elements.length){ 
        elements[0].classList.remove(className);
    }
}
//Returns true if an array of object contains coords object with matching lat and lon
function containsObject(arrObj, lat, lon){
    for(var i = 0; i < arrObj.length; i++){
        if(arrObj[i].lat == lat && arrObj[i].lng == lon){
            return true;
        }
    }
    return false;
}
//Fired when user presses a mouse button
addEventListener('mousedown', (event) => {
    var target = event.target;
    if(target.classList.contains("chartElement")){
        startId = parseInt(target.getAttribute("name"));
    }
    else{
        startId = null;
    }
});
//Fired when user stops pressing a mouse button
addEventListener('mouseup', (event) => {
    if(startId != null){ //if user wants to highlight something
        var target = event.target;
        if(target.classList.contains("chartElement")){ //if the user successfully selected an endpoint for the highlight
            var endId = parseInt(target.getAttribute("name")); //get the end id of highlight
            //reset previous highlights
            removeClass("highlighted");
            selectedPointsCorrds = [];
            if(selectedLine != undefined){
                selectedLine.remove();
            }
            if(endId > startId){
                for(var i = startId; i <= endId; i++){
                    document.getElementsByName(i).forEach(element => {
                        element.classList.add("highlighted");
                    });
                    var coords = L.latLng({lon: jsonData[i]["lon"], lat: jsonData[i]["lat"]});
                    selectedPointsCorrds.push(coords);
                    document.getElementById(i).classList.add("highlighted");
                }
            }
            else if(endId < startId){
                for(var i = endId; i <= startId; i++){
                    document.getElementsByName(i).forEach(element => {
                        element.classList.add("highlighted");
                    });
                    var coords = L.latLng({lon: jsonData[i]["lon"], lat: jsonData[i]["lat"]});
                    selectedPointsCorrds.push(coords);
                    document.getElementById(i).classList.add("highlighted");
                }
            }
            else{
                document.getElementById("selInfo").innerHTML = "";
                document.getElementById("distInfo").innerHTML = "0";
                return;
            }
            if(maps.length > 0){
                selectedLine = new L.polyline(selectedPointsCorrds, {
                    color: 'purple',
                    weight: 12,
                    opacity: 0.5,
                    smoothFactor: 3
                });
                selectedLine.on('mousedown', clickedLine);
                selectedLine.addTo(maps[0]);
            }
            var dist = parseFloat(jsonData[startId]["station"].slice(0, -2)) - parseFloat(jsonData[endId]["station"].slice(0, -2));
            document.getElementById("distInfo").innerHTML = twoDecimal(Math.abs(dist));
            var selectInfo = document.getElementById("selInfo");
            selectInfo.innerHTML = "od " + jsonData[startId]["station"] + " do " + jsonData[endId]["station"];
        }
        else{
            startId = null;
        }
    }
});
function tooltipReload(){
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
}
function checkForAnyNULLCoords(response){
    var res = false;
    response.forEach(element =>{
        if(element["lon"] == null || element["lat"] == null){
            res = true;
        }
    });
    return res;
}