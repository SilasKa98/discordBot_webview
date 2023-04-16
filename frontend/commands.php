<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <title>Commands</title>
</head>
<body>
    <div class="container-fluid">
        <div class="d-flex align-items-start">
            <div class="nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <button class="nav-link active" id="v-pills-faceit-tab" data-bs-toggle="pill" data-bs-target="#v-pills-faceit" type="button" role="tab" aria-controls="v-pills-faceit" aria-selected="true">Faceit Elo Checker</button>
                <button class="nav-link" id="v-pills-lol-tab" data-bs-toggle="pill" data-bs-target="#v-pills-lol" type="button" role="tab" aria-controls="v-pills-lol" aria-selected="false">LoL Elo Checker</button>
            </div>
            <div class="tab-content" id="v-pills-tabContent" style="width: 100%;">

                <div class="tab-pane fade show active" id="v-pills-faceit" role="tabpanel" aria-labelledby="v-pills-faceit-tab" tabindex="0">
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Command</th>
                                <th scope="col">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">1</th>
                                <td>Mark</td>
                            </tr>
                            <tr>
                                <th scope="row">2</th>
                                <td>Jacob</td>
                            </tr>
                            <tr>
                                <th scope="row">3</th>
                                <td colspan="2">Larry the Bird</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="tab-pane fade" id="v-pills-lol" role="tabpanel" aria-labelledby="v-pills-lol-tab" tabindex="0">
                    lol
                </div>

            </div>
        </div>
    </div>
</body>
</html>