//Js script for navTool.php
document.onload = onLoad();
var map; //the map object from leaflet
var mode = null; //the current tool being used
var points = []; //array of marker objects
var coords = [];
var polyLine;

function onPointClick(e, marker){
    if(mode == 2){
        var index = points.indexOf(marker)
        points.splice(index, 1);
        coords.splice(index, 1);
        marker.remove();
        polyLine.setLatLngs(coords);
    }
}

function onPointDrag(e, marker){
    var index = points.indexOf(marker);
    coords[index] = e.latlng;
    polyLine.setLatLngs(coords);
}

function createPoint(latlng){
    var marker = L.marker(latlng, {draggable: true, autoPan: true}).addTo(map);
    marker.on('click', function(ev1) {
        onPointClick(ev1, marker);
    });
    marker.on('drag', function(ev3){
        onPointDrag(ev3, marker);
    });
    return marker;
}

function onLineClick(e){
    if(mode == 3){
        var coord;
        var min = e.latlng.distanceTo(coords[0]);
        coords.forEach(c => {
            var dist = e.latlng.distanceTo(c);
            if(dist < min){
                min = dist;
                coord = c;
            }
        });
        var index = coords.indexOf(coord);
        coords.splice(index, 0, e.latlng);
        points.splice(index, 0, createPoint(e.latlng));
        polyLine.setLatLngs(coords);
    }
}

function onMapClick(e){
    if(mode == 0){
        points.push(createPoint(e.latlng));
        coords.push(e.latlng);
        polyLine.addLatLng(e.latlng);
    }
}

//Is executed on page load
function onLoad(){
    // initialize Leaflet
    map = L.map('map').setView({lon:16.931992, lat:52.409538}, 2);
    map.setZoom(12); //zoom in slightly
    // add the OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
    }).addTo(map);
    polyLine = L.polyline([]).addTo(map);
    polyLine.on('click', function(e){
        onLineClick(e);
    });
    // show the scale bar on the lower left corner
    L.control.scale({imperial: false, metric: true}).addTo(map);
    map.on('click', function(e){
        onMapClick(e);
    });
}

//Removes all occurences of this class from the page
function removeClass(className){
    var elements = document.getElementsByClassName(className);
    while(elements.length){ 
        elements[0].classList.remove(className);
    }
}

function toolSelected(el){
    var active = el.classList.contains("active");
    removeClass("active");
    points.forEach(p => {
        p.dragging.disable();
    });
    if(!active){
        el.classList.add("active");
        switch(el.id){
            case "pathLayer":
                mode = 0;
                break;
            case "mover":
                mode = 1;
                points.forEach(p => {
                    p.dragging.enable();
                });
                break;
            case "deleter":
                mode = 2;
                break;
            case "inserter":
                mode = 3;
                break;
        }
    }
    else{
        mode = null;
    }
    
}

function interpolateCoordArray(data, dist){
    result = []
    for(let i = 0; i < data.length - 1; i++){
        result.push(data[i]);
        var num = data[i].distanceTo(data[i + 1]) / dist;
        var index = i;
        partLat = (data[i + 1].lat - data[i].lat) / num;
        partLng = (data[i + 1].lng - data[i].lng) / num;
        for(let j = 1; j < num + 1; j++){
            coord = L.latLng(data[i].lat + partLat * j, data[i].lng + partLng * j);
            result.push(coord);
            index++;
        }
    }
    return result;
}

//create a user-defined function to download CSV file   
function download_csv_file() { 
    var csv = 'lat;lng;km\n';
    coords = interpolateCoordArray(coords, parseInt(document.getElementById('csvRes').value));
    //console.log(coords)
    var km = parseInt(document.getElementById('csvOff').value);
    //merge the data with CSV  
    for(let i = 0; i < coords.length; i++){
        csv += coords[i].lat + ';' + coords[i].lng + ';' + km + '\n';  
        if(i + 1 < coords.length){
            km += coords[i].distanceTo(coords[i + 1]) / 1000; 
        }
    } 
   
    //display the created CSV data on the web browser   
    //document.write(csv);  
    
    //console.log(csv)

    var hiddenElement = document.createElement('a');  
    hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csv);  
    hiddenElement.target = '_blank';  
      
    //provide the name for the CSV file to be downloaded  
    //hiddenElement.download = 'locFile.csv';  
    hiddenElement.click();  
}  

function exportToSection(){
    const xhr = new XMLHttpRequest();
    xhr.open("POST", 'mapLoad.php', true);

    //Send the proper header information along with the request
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = () => { // Call a function when the state changes.
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            var sectionJson = JSON.parse(xhr.responseText);
            var reso = (parseFloat(sectionJson[0]["station"].replace("NA", "")) - parseFloat(sectionJson[1]["station"].replace("NA", ""))) * 1000;
            reso = parseInt(reso);
            if(reso < 0){
                reso = 0 - reso;
            }
            coords = interpolateCoordArray(coords, reso);
            for(let i = 0; i < sectionJson.length; i++){
                if(sectionJson[i]["lat"] == null && sectionJson[i]["lon"] == null){
                    sectionJson[i]["lat"] = coords[i].lat;
                    sectionJson[i]["lon"] = coords[i].lng;
                }
            }
            const xhr2 = new XMLHttpRequest();
            xhr2.open("POST", 'replaceJson.php', true);
            xhr2.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr2.send("filename=" + document.getElementById("sekcjaSelect").value + "&json=" + JSON.stringify(sectionJson));
        }
    }
    xhr.send("filename=" + document.getElementById("sekcjaSelect").value);
}

function jumpTo(coord){
    if(coord == ""){
        return;
    }
    coord = coord.split("x");
    map.panTo(new L.LatLng(coord[0], coord[1]));
}

