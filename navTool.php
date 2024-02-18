<html>
    <head>
        <title>FWD-Dynatest</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="static/style.css">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script> <!-- Js library for map handling https://switch2osm.org/using-tiles/getting-started-with-leaflet/ -->
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
                        <a href="http://sump-osad.pl/osad_FWD-Dynatest/manage.php" class="nav-link">Zarządzaj</a>
                    </li>
                    <li class="nav-item">
                        <a href="http://sump-osad.pl/" class="nav-link">Powrót</a>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="container-fluid text-black" style="width:100%;height: 85%;" >
            <div class="row" style="height:100%">
                <div class="col-7" style="height:100%">
                    <div id="map" style="width:100%; height: 100%;">
                    </div>
                </div>
                <div class="col-2">
                    <div class="card" style="height:40%; margin-bottom:5%">
                        <div class="card-header">
                            Przybornik
                        </div>
                        <div class="card-body" style="height:auto">
                            <div class="row row-cols-2" style="height:100%">
                                <button class="col btn nohover btn-outline-secondary fs-2" id="pathLayer" onclick="toolSelected(this)"><i class="bi bi-share"></i></button>
                                <button class="col nohover btn btn-outline-secondary fs-2" id="mover" onclick="toolSelected(this)"><i class="bi bi-arrows-move"></i></button>
                                <button class="col btn nohover btn-outline-secondary fs-2" id="deleter" onclick="toolSelected(this)"><i class="bi bi-x-lg"></i></button>
                                <button class="col btn nohover btn-outline-secondary fs-2" id="inserter" onclick="toolSelected(this)"><i class="bi bi-shift"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="card" style="height:58%">
                        <div class="card-header">
                            Znane nawierzchnie
                        </div>
                        <div class="card-body">
                            <ol id="pointList" class="text-start">
                                <?php
                                    include 'functions.php';
                                    getCoordsList();
                                ?>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card" style="height:100%">
                        <div class="card-header">
                            Eksportowanie Trasy
                        </div>
                        <div class="card-body">
                            <div>
                                <span class="fw-bold">Wyeksportuj dane lokalizacyjne do sekcji pomiarowej</span>
                                <label for="sessionSelect">Wybierz kampanię pomiarową:</label>
                                <select id="sessionSelect" class="form-select" style="margin-bottom:10px" onchange="if (this.selectedIndex > -1 && this.value != 'err' && this.value != 'nul') sessionSelected(this, [document.getElementById('sekcjaSelect')])" required>
                                    <option value="nul" selected="selected"></option>
                                    <?php 
                                        getSectionsAsOptions();
                                    ?>
                                </select>
                            </div>
                            <div style="display:none">
                                <label for="sekcjaSelect" >Wybierz sekcje pomiarową z wybranej kampanii:</label>
                                <select id="sekcjaSelect" class="form-select" name="sekcjaSelect" style="margin-bottom:10px" required>
                                    <option value="nul" selected="selected"></option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="exportToSection()">Dodaj</button>
                            <hr/>
                            <span class="fw-bold">Wyeksportuj dane lokalizacyjne do csv</span>
                            <label for="csvRes" >Rozdzielczość csv:</label>
                            <input type="number" class="form-control" min="1" id="csvRes" value="50">
                            <div class="form-text">Co ile metrów mają być zapisane koordynaty z zapisanej trasy</div>
                            <label for="csvRes" >Start km:</label>
                            <input type="number" class="form-control" style="margin-bottom:10px" min="0" id="csvOff" value="0">
                            <button type="button" class="btn btn-primary" onclick="download_csv_file(coords)">Pobierz</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
    <script src="static/functions.js"></script>
    <script src="static/navTool.js"></script>
</html>