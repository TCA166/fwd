<html>
    <head>
        <title>FWD-Dynatest</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="static/style.css">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <!--tree view stuff -->
        <script src="static/jquery-3.6.3.min.js"></script>
        <script src="static/bootstrap-treeview.js"></script>
        <link rel="stylesheet" href="static/bootstrap-treeview.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
        <!-- Bootsrap stuff -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    </head>
    <body style="width:100%; height: 100%;" class="bg-dark">
        <nav class="navbar navbar-expand-lg bg-secondary">
            <div class="container-fluid">
                <a href="http://sump-osad.pl/osad_FWD-Dynatest/" class="navbar-brand">FWD-Dynatest</a>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="http://sump-osad.pl/osad_FWD-Dynatest/compare.php" class="nav-link">Porównywanie</a>
                    </li>
                    <li class="nav-item">
                        <span class="nav-link disabled">Zarządzaj</span>
                    </li>
                    <li class="nav-item">
                        <a href="http://sump-osad.pl/" class="nav-link">Powrót</a>
                    </li>
                </ul>
            </div>
        </nav>
        <?php include "functions.php" ?>
        <main class="container-fluid text-white" style="width:100%; height: 80%;" >
            <div class="card text-center text-dark" style="margin-bottom:1%">
                <div class="card-header" onclick="toggleElement('campaignBox')">
                    Zarządzanie kampaniami
                </div>
                <div class="card-body row " id="campaignBox">
                    <div class="col card scrollableContainer" style="max-width:30%;padding-left:0;padding-right:0;overflow-y:auto;">
                        <div id="campaignTree"></div>
                    </div>
                    <div class="col card" style="padding-left:0;padding-right:0;margin-left:calc(var(--bs-gutter-x) * .5);width:60%;text">
                        <div id="campaignInput" style="display:none;padding:10px">
                            <form method="post" enctype="multipart/form-data">
                                <select id="sessionSelect" name="sessionSelect" class="form-select disabledOption" style="margin-bottom:10px">
                                    <option selected="selected" id="campaignIDopt"></option>
                                </select>
                                <hr>
                                <label for="sessionSelect">Dodaj plik FWD jako sekcję do kampanii:</label>
                                <input type="file" class="form-control" name="FWD" accept=".FWD," style="margin-bottom:10px"/>
                                <button type="submit" formaction="./fwdParse.php" class="btn btn-primary">Dodaj</button>
                                <hr>
                                <label for="bottomSelect">Usuń kampanię:</label><br>
                                <button type="submit" formaction="./delete.php" class="btn btn-danger">Usuń</button>
                            </form>
                        </div>
                        <div id="sectionInput" style="display: none;padding:10px">
                            <form method="post" enctype="multipart/form-data">
                                <select id="sekcjaSelect" class="form-select disabledOption" name="sekcjaSelect" style="margin-bottom:10px" required>
                                    <option selected="selected" id="sekcjaIDopt"></option>
                                </select>
                                <hr>
                                <label for="bottomSelect">Wybierz sekcje do przyłączenia:</label>
                                <select id="bottomSelect" class="form-select" style="margin-bottom:10px" onchange="if (this.selectedIndex > -1 && this.value != 'err' && this.value != 'nul') joinSelectChanged(this, document.getElementById('sekcjaSelect'), this);">
                                    <option value="nul" selected="selected"></option>
                                </select>
                                <button type="submit" formaction="./join.php" class="btn btn-primary">Dodaj</button>
                                <hr>
                                <label for="bottomSelect">Wybierz dane lokalizacyjne:</label> <a href="http://sump-osad.pl/osad_FWD-Dynatest/navTool.php" style="display:inline-block" role="button"><i class="bi bi-tools"></i></a>
                                <input type="file" class="form-control" name="csv" accept=".csv," required style="margin-bottom:10px"/>
                                <div class="form-text">Zgodny z <a href="static/DK12 Kalisz-gr.woj_GPS.csv" >formatem GDDKIA</a> albo <a href="static/9jhaf09G.csv" >formatem narzędzia</a></div>
                                <button type="submit" formaction="./addLoc.php" class="btn btn-primary">Dodaj</button>
                            </form>
                        </div>
                        <div id="noneInput" style="padding:10px">
                            <form  action="./createNewCampaign.php" method="post" enctype="multipart/form-data" >
                                <label for="name" class="form-label">Nazwa nowej kampanii</label>
                                <input type="text" class="form-control" id="name" name="name">
                                <label for="date" class="form-label">Data przeprowadzenia</label>
                                <input type="date" class="form-control" id="date" name="date" style="margin-bottom:10px">
                                <button type="submit" class="btn btn-success">Utwórz</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card text-dark text-center">
                <div class="card-header" onclick="toggleElement('treeBox')">
                    Zarządzaj strukturą drzewiastą
                </div>
                <div class="card-body row" style="height:50vh;" id="treeBox">
                    <div class="col card scrollableContainer" style="max-width:30%;padding-left:0;padding-right:0;overflow-y:auto;">
                        <div id="tree"></div>
                    </div>
                    <div class="col card" style="padding-left:0;padding-right:0;margin-left:calc(var(--bs-gutter-x) * .5);height:100%">
                        <form id="nawierchniaAdd" action="addNode.php" style="margin:15px" method="post">
                            <input id="type" name="type" value="0" style="display:none">
                            <span class="text-start fw-bold">Dodaj nową nawierzchnię</span>
                            <div class="mb-3">
                                <label class="form-label">Nazwa</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Długość</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" step="0.01" id="endKM" name="endKM">
                                    <span class="input-group-text">km</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Koordynaty startu</label>
                                <input type="text" class="form-control" id="startCoords" name="startCoords">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Koordynaty końca</label>
                                <input type="text" class="form-control" id="endCoords" name="endCoords">
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary">Dodaj</button>
                        </form>
                        <form id="pasAdd" action="addNode.php" style="margin:15px;display:none"  method="post">
                            <button class="btn btn-danger btn-sm position-absolute top-0 start-0" type="button" onclick="delClick()">
                                <i class="bi bi-trash"></i>
                            </button>
                            <input id="type" name="type" value="1" style="display:none">
                            <span class="text-start fw-bold">Dodaj nowy pas</span>
                            <div class="mb-3">
                                <label class="form-label">Nawierzchnia</label>
                                <select id="nawierzchniaID" name="nawierzchniaID" class="form-control disabledOption" readonly>
                                    <option value="" id="nawierchniaIDopt"></option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kierunek</label>
                                <select id="dir" name="dir" class="form-control">
                                    <option value="1">W kierunku końca(+)</option>
                                    <option value="0">W kierunku początku(-)</option>
                                </select>
                                <div class="form-text">Początek i koniec pasa to określenia umowne, relatywne i zależne od koordynatów końca i początku nawierzchni.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pozycja</label>
                                <input type="number" min="1" class="form-control" id="pos" name="pos">
                                <div class="form-text">Pasy są numerowane od środka nawierzchni. Czyli pierwszy pas od środka nawierzchni w kierunku końca nawierzchni ma pozycję 1+ i symetryczny pas ma pozycję 1-</div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Dodaj</button>
                        </form>
                        <form id="sekcjaAdd" action="addNode.php" style="margin:15px;display:none" method="post">
                            <button class="btn btn-danger btn-sm position-absolute top-0 start-0" type="button" onclick="delClick()">
                                <i class="bi bi-trash"></i>
                            </button>
                            <input id="type" name="type" value="2" style="display:none">
                            <span class="text-start fw-bold">Przyporządkuj sekcje do pasu</span>
                            <div class="mb-3">
                                <label class="form-label">Pas</label>
                                <select id="pasID" name="pasID" class="form-control disabledOption" readonly>
                                    <option value="" id="pasIDopt"></option>
                                </select>
                            </div>
                            <div>
                                <label for="sessionSelect">Wybierz kampanię pomiarową:</label>
                                <select id="sessionSelect" name="sessionSelect" class="form-select" style="margin-bottom:10px" onchange="if (this.selectedIndex > -1 && this.value != 'err' && this.value != 'nul') sessionSelected(this, [document.getElementById('sekcjaSelect2')])">
                                    <option value="nul" selected="selected"></option>
                                    <?php 
                                        getSectionsAsOptions();
                                    ?>
                                </select>
                            </div>
                            <div style="display:none">
                                <label for="sekcjaSelect2">Wybierz sekcje pomiarową z wybranej kampanii:</label>
                                <select id="sekcjaSelect2" name="sekcjaSelect2" class="form-select" style="margin-bottom:10px">
                                    <option value="nul" selected="selected"></option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Dodaj</button>
                        </form>
                        <div id="sekcjaView" style="display:none;height:100%;margin-right:0;margin-left:0;" class="row">
                            <div class="col scrollableContainer" style="max-height:auto;max-width:15%;padding-left:0;padding-right:0;border-right: 1px solid  var(--bs-border-color-translucent);">
                                <button class="btn btn-danger btn-sm position-absolute top-0 start-0" type="button" onclick="delClick()">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                                <ol id="pointList" class="text-start" style="margin-left: 40px;">
                                </ol>
                            </div>
                            <div class="col" style="height:100%;max-width:85%">
                                <div class="row" style="height:30%;border-bottom: 1px solid  var(--bs-border-color-translucent)">
                                    <table style="width:100%;display:none" class="tableBordered">
                                        <thead id="infoTableHeader">
                                            <tr>
                                                <th>Koordynaty (WGS84)</th>
                                                <th>Kilometraż</th>
                                                <th>T MMA</th>
                                                <th>T Powierzchni</th>
                                                <th>T Powietrza</th>
                                                <th>SCI</th>
                                                <th>BDI</th>
                                                <th>BCI</th>
                                            </tr>
                                            <tr>
                                                <td id="coords"></td>
                                                <td id="station"></td>
                                                <td id="asphalt"></td>
                                                <td id="surface"></td>
                                                <td id="air"></td>
                                                <td id="sciDisplay"></td>
                                                <td id="bdiDisplay"></td>
                                                <td id="bciDisplay"></td>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="row" style="height:70%">
                                    <table style="width:100%;display:none" class="tableBordered">
                                        <thead>
                                            <tr id="miniHead">
                                                <th>Naprężenia [kPa]</th>
                                            </tr>
                                        </thead>
                                        <tbody id="infoTable">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer>

        </footer>
    </body>
    <script src="static/functions.js"></script>
    <script src="static/manage.js"></script>
    <script>
        var nawierzchniaEl = document.getElementById("nawierchniaAdd");
        var pasEl = document.getElementById("pasAdd");
        var sekcjeEl = document.getElementById("sekcjaAdd");
        var sekcjaView = document.getElementById("sekcjaView");
        treeData = JSON.parse('<?php include 'createTree.php'; echo createTree(0)?>');
        $('#tree').treeview({
            data: treeData, 
            onNodeSelected: function(event, data) {
                switch(data["type"]){
                    case 0:
                        nawierzchniaEl.style.display = "none";
                        pasEl.style.display = "";
                        sekcjeEl.style.display = "none";
                        sekcjaView.style.display = "none";
                        var nawierzchniaOpt = document.getElementById("nawierchniaIDopt");
                        nawierzchniaOpt.innerHTML = data["text"];
                        nawierzchniaOpt.value = data["ID"];
                        break;
                    case 1:
                        nawierzchniaEl.style.display = "none";
                        pasEl.style.display = "none";
                        sekcjeEl.style.display = "";
                        sekcjaView.style.display = "none";
                        var pasOpt = document.getElementById("pasIDopt");
                        pasOpt.innerHTML = data["text"];
                        pasOpt.value = data["ID"];
                        break;
                    case 2:
                        nawierzchniaEl.style.display = "none";
                        pasEl.style.display = "none";
                        sekcjeEl.style.display = "none";
                        sekcjaView.style.display = "";
                        const xhr = new XMLHttpRequest();
                        xhr.open("POST", 'mapLoad.php', true);

                        //Send the proper header information along with the request
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                        xhr.onreadystatechange = () => { // Call a function when the state changes.
                            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                                loadSection(JSON.parse(xhr.responseText));
                            }
                        }
                        xhr.send("filename=" + data["ID"]);
                        break;
                }
            },
            onNodeUnselected: function(event, data){
                resetAddCard();
                nawierzchniaEl.style.display = "";
            },
            collapseIcon:"bi bi-chevron-double-down",
            expandIcon:"bi bi-chevron-double-right",
            levels:1,
            selectedIcon:""
        });
        campaignData = JSON.parse('<?php echo createCampaignTree()?>');
        campaignEl = document.getElementById("campaignInput");
        sectionEl = document.getElementById("sectionInput");
        noneEl = document.getElementById("noneInput");
        selected = null;
        $('#campaignTree').treeview({
            data: campaignData, 
            onNodeSelected: function(event, data) {
                switch(data["type"]){
                    case 0:
                        campaignEl.style.display = "";
                        sectionEl.style.display = "none";
                        noneEl.style.display = "none";
                        campaignOption = document.getElementById("campaignIDopt");
                        campaignOption.value = data["ID"];
                        campaignOption.innerHTML = data["text"];
                        selected = data["nodeId"];
                        break;
                    case 1:
                        campaignEl.style.display = "none";
                        sectionEl.style.display = "";
                        noneEl.style.display = "none";
                        sekcjaOption = document.getElementById("sekcjaIDopt");
                        sekcjaOption.value = data["ID"];
                        sekcjaOption.innerHTML = data["text"];
                        joinOption = document.getElementById("bottomSelect");
                        campaignData[selected]["nodes"].forEach(e => {
                            if(data["ID"] != e["ID"]){
                                newOption = document.createElement("option");
                                newOption.value = e["ID"];
                                newOption.innerHTML = e["text"];
                                joinOption.appendChild(newOption);
                            }
                        });
                        break;
                }
            },
            onNodeUnselected: function(event, data){
                campaignEl.style.display = "none";
                sectionEl.style.display = "none";
                noneEl.style.display = "";
            },
            collapseIcon:"bi bi-chevron-double-down",
            expandIcon:"bi bi-chevron-double-right",
            levels:1,
            selectedIcon:""
        });
    </script>
</html>