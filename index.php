<?php

$dbh = new PDO('mysql:host=localhost;dbname=meine_nachbarin_nervt', 'root', '');

if (isset($_POST['molestations']) && !empty($_POST['molestations'])) {
    $stmt = $dbh->prepare("INSERT INTO entries (datetime) VALUES (?)")->execute([(new DateTime())->format(DateTime::ATOM)]);
    $entryId =  $dbh->lastInsertId();

    $stmt = $dbh->prepare("INSERT INTO rel_entries_molestations (entry_id, molestation_id) VALUES (?,?)");
    $dbh->beginTransaction();
    foreach ($_POST['molestations'] as $molestation) {
        $stmt->execute([$entryId, $molestation]);
    }
    $dbh->commit();
}

?>
<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap CSS -->
        <link href="assets/css/styles.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

        <title>Meine Nachbarin nervt!</title>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-sm">
                    <h1>Meine Nachbarin nervt!</h1>
                    <p>Vivamus placerat odio sed ullamcorper gravida. Nam ac ullamcorper risus. Ut eu mattis ex, id venenatis lacus. Donec maximus nisi a sagittis semper. In vehicula bibendum tincidunt. Pellentesque malesuada pretium ex sed rutrum. In lobortis nisl velit, et egestas nisi congue non. Sed faucibus molestie ipsum a imperdiet. Ut ullamcorper felis consequat sapien semper sagittis efficitur ut justo. </p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                    <h2>Raport-Eintrage hinzufügen</h2>
                    <form action="index.php" method="post">
                        <h3>Art der Belästigung</h3>
                        <?php foreach ($dbh->query("SELECT * from molestations") as $molestation) { ?>
                            <div class="mb-3 form-check">
                                <input class="form-check-input" type="checkbox" name="molestations[]" value="<?php echo $molestation['id']; ?>" id="molestation-<?php echo $molestation['id']; ?>">
                                <label class="form-check-label" for="molestation-<?php echo $molestation['id']; ?>">
                                    <?php echo $molestation['molestation']; ?>
                                </label>
                            </div>
                        <?php } ?>
                        <button type="submit" class="btn btn-primary">Speichern</button>
                    </form>
                </div>
                <div class="col-sm">
                    <h2>Einträge</h2>
                    <ul class="list-group">
                        <?php foreach ($dbh->query("SELECT * from entries ORDER BY datetime DESC") as $entry) { ?>
                            <li class="list-group-item">
                                <?php echo $entry['datetime']; ?>
                                <ul>
                                    <?php foreach ($dbh->query("SELECT * from rel_entries_molestations LEFT JOIN molestations ON rel_entries_molestations.molestation_id = molestations.id WHERE rel_entries_molestations.entry_id = " . $entry['id']) as $molestation) { ?>
                                        <li><?php echo $molestation['molestation']; ?></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>



        <!-- Optional JavaScript; choose one of the two! -->

        <!-- Option 1: Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>

        <!-- Option 2: Separate Popper and Bootstrap JS -->
        <!--
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js" integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG" crossorigin="anonymous"></script>
        -->
    </body>
</html>
<?php

$dbh = null;

?>