<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="dist/style.css">
    <script src="htmx.min.js"></script>
</head>

<body>

    <div class="page-container container" id="main-content">
        <h1>Welcome</h1>
        <p>This is an example app to demonstrate that we can now render Camunda Forms externally.</p>
        <!-- Initial content will be dynamically loaded here -->
        <div style="width: 100%;">
            <?php
            // var_dump($_GET);
            if (isset($_GET["pid"])) {

                echo '<form hx-get="rideform.php?pid=' . $_GET['pid'] . '" hx-target="#main-content" method="get">';
            } else {

                echo '<form hx-get="rideform.php" hx-target="#main-content" method="get">';
            }
            ?>
            <button type="submit" class="btn-full-width">Start Process</button>
            </form>
        </div>
 

        <!-- <div id="upload-form">
        </div>
        <button hx-get="uploadform.php" hx-target="#upload-form" type="submit">Add Attachment</button> -->
    </div>
        
      

</body>

</html>