<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_GET['gallery']) && !empty($_GET['category'])) {
    $gallery = htmlspecialchars($_GET['gallery']);
    $category = htmlspecialchars($_GET['category']);
} else {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['visitor']) && !isset($_SESSION['admin'])) {
    $redirect = urlencode("../gallery.php?category=$category&gallery=$gallery");
    header("Location: auth/login.php?role=visitor&redirect=$redirect");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Wink</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Photo Gallery">
    <meta name="keywords" content="PHP, Photography">
    <meta name="author" content="Fenimore">
    <link rel="icon" href="favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
</head>
<body>
<div class="container">
    <div class="row text-center">
        <?php
        $spaces = array("-", "_");
        $galleryTitle = str_replace($spaces, " ", $gallery);
        echo '<h1 class="title"><a href="index.php">' . ucfirst($galleryTitle) . '</a></h1>';

        echo '<div class="col-md-1">';
        echo '<a href="index.php" class="nav-control arrow"><span class="glyphicon glyphicon-home" aria-hidden="true"></span></a><br>';
        echo '<a href="zip.php?category=' . $category . '&gallery=' . $gallery . '" class="nav-control"><span title="Download" class="glyphicon glyphicon-cloud-download" aria-hidden="true"></span></a><br><br>';
        echo '<a href="#" onclick="prev()" class="nav-control"><span class="glyphicon glyphicon-chevron-left arrow" aria-hidden="true"></span></a><br><br>';
        echo '<a href="#" onclick="next()" class="nav-control"><span class="glyphicon glyphicon-chevron-right arrow" aria-hidden="true"></span></a><br><br>';
        echo '</div>';

        $path = 'media/' . $category . '/' . $gallery . '/';
        $images = glob($path . "*.{[jJ][pP][gG],gif,jpeg,svg,bmp,png}", GLOB_BRACE);
        $thumbnails = glob($path . 'thumbnails/' . "*.{[jJ][pP][gG],gif,jpeg,svg,bmp,png}", GLOB_BRACE);

        $src = 'view.php?category=' . $category . '&gallery=' . $gallery . '&index=';
        $size = sizeof($images);

        echo '<div class="col-md-11">';
        echo '<img src="#" id="image" alt="Gallery Image" class="img-responsive center-block">';
        echo '<br><div class="image-title"></div>';
        echo '</div>';

        echo '<div class="col-md-1 text-right" style="z-index:100">';
        echo '<img src="loading.gif" style="display:none;" id="loading" alt="Loading..." width="auto" height="auto">';
        echo '</div>';
        echo '</div>';

        echo '<div id="slider" class="row">';
        foreach ($thumbnails as $key => $val) {
            if ($key % 6 === 0 && $key !== 0) {
                echo '</div><div class="row" style="margin-top:5%;">';
            }
            echo '<div class="col-md-2">';
            echo '<img src="' . $val . '" class="thmb img-responsive center-block" id="thumbnail-' . $key . '" alt="Thumbnail ' . $key . '" onclick="getImage(' . $key . ')">';
            echo '</div>';
        }
        echo '</div>';
        ?>
</div>

<?php include("footer.php"); ?>

<script type="text/javascript">
    var index = 0;
    var size = <?php echo json_encode($size); ?>;

    function next() {
        index = checkBounds(index, size, 1);
        getImage(index);
    }

    function prev() {
        index = checkBounds(index, size, -1);
        getImage(index);
    }

    function checkBounds(idx, sze, inc) {
        idx += inc;
        if (idx >= sze) return 0;
        if (idx < 0) return sze - 1;
        return idx;
    }

    function getImage(idx) {
        var loading = document.getElementById("loading");
        loading.style.display = "block";
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                document.getElementById("image").src = this.responseText;
                loading.style.display = "none";
                index = idx;
                var thumbs = document.getElementsByClassName("thmb");
                for (var i = 0; i < thumbs.length; i++) {
                    thumbs[i].style.boxShadow = "";
                }
                if (idx >= 0 && idx < thumbs.length) {
                    thumbs[idx].style.boxShadow = "inset 0 0 1em black, 0 0 1em white";
                }
            }
        };
        xmlhttp.open("GET", "<?php echo $src ?>" + idx, true);
        xmlhttp.send();
    }

    document.addEventListener("keydown", function (e) {
        if (e.keyCode === 37) prev();
        if (e.keyCode === 39) next();
    });

    getImage(0);
</script>
</body>
</html>
