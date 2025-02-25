<?php
############################################################
# PODCAST GENERATOR
#
# Created by Alberto Betella and Emil Engler
# http://www.podcastgenerator.net
#
# This is Free Software released under the GNU/GPL License.
############################################################
require 'checkLogin.php';
require '../core/include_admin.php';

if (isset($_GET['upload'])) {
    checkToken();
    // Check mime type
    if (mime_content_type($_FILES['file']['tmp_name']) != "image/jpeg") {
        $error = _('Image is not a JPEG');
        goto error;
    }

    $imagesize = getimagesize($_FILES['file']['tmp_name']);
    // Verify if image is a square
    if ($imagesize[0] / $imagesize[1] != 1) {
        $error = _('Image is not quadratic');
        goto error;
    }

    // Now everything is cool and the file can uploaded
    if (!move_uploaded_file($_FILES['file']['tmp_name'], $config['absoluteurl'] . $config['img_dir'] . 'itunes_image.jpg')) {
        $error = _('File was not uploaded');
        goto error;
    } else {
        // Wait a few seconds so the upload can finish
        sleep(3);
        header('Location: store_cover.php');
        die();
    }
    error:
}
?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('Store Cover') ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="<?= $config['url'] ?>favicon.ico">
</head>

<body>
    <?php
    include 'js.php';
    include 'navbar.php';
    ?>
    <br>
    <div class="container">
        <h1><?= _('Change Cover') ?></h1>
        <p><?= _('The cover art will be displayed in the podcast readers.') ?></p>
        <?php if (isset($error)) { ?>
            <strong><p style="color: red;"><?= $error ?></p></strong>
        <?php } ?>
        <h3><?= _('Current Cover') ?></h3>
        <img src="<?= $config['url'] . $config['img_dir'] ?>itunes_image.jpg" style="max-height: 350px; max-width: 350px;">
        <hr>
        <h3><?= _('Upload new cover') ?></h3>
        <form action="store_cover.php?upload=1" method="POST" enctype="multipart/form-data">
            <?= _('Select file') ?>:<br>
            <input type="file" name="file"><br><br>
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="submit" value="<?= _('Upload') ?>" class="btn btn-success">
        </form>
    </div>
</body>

</html>
