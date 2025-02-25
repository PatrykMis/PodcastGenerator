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

if (isset($_GET['edit'])) {
    checkToken();
    foreach ($_POST as $key => $value) {
        if ($key == 'custom_tags') {
            // need to handle custom_tags specially
            $custom_tags = $value;
            if (isWellFormedXml($custom_tags)) {
                // only set the value if it's well-formed XML
                saveCustomFeedTags($custom_tags);
            } elseif ($config['customtagsenabled'] == 'yes') {
                // only error if custom tags feature is enabled
                $error = _('Custom tags are not well-formed');
            }
        } else {
            updateConfig('../config.php', $key, $value);
        }
    }

    if (!isset($error)) {
        header('Location: podcast_details.php');
        die();
    }
    header('Location: podcast_details.php');
    die();
} else {
    generateRSS();
    pingServices();
}

$custom_tags = getCustomFeedTags();

?>
<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($config['podcast_title']); ?> - <?= _('Podcast Details') ?></title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../core/bootstrap/style.css">
    <style>
        .txt {
            width: 100%;
        }
    </style>
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
        <h1><?= _('Change Podcast Details') ?></h1>
        <form action="podcast_details.php?edit=1" method="POST">
            <?= _('Podcast Title') ?>:<br>
            <input type="text" name="podcast_title" value="<?= htmlspecialchars($config['podcast_title']) ?>" class="txt"><br>
            <?= _('Podcast Subtitle or Slogan') ?>:<br>
            <input type="text" name="podcast_subtitle" value="<?= htmlspecialchars($config['podcast_subtitle']) ?>" class="txt"><br>
            <?= _('Podcast Description') ?>:<br>
            <input type="text" name="podcast_description" value="<?= htmlspecialchars($config['podcast_description']) ?>" class="txt"><br>
            <?= _('Copyright Notice') ?>:<br>
            <input type="text" name="copyright" value="<?= htmlspecialchars($config['copyright']) ?>" class="txt"><br>
            <?= _('Author Name') ?>:<br>
            <input type="text" name="author_name" value="<?= htmlspecialchars($config['author_name']) ?>" class="txt"><br>
            <?= _('Author E-Mail Address') ?>:<br>
            <input type="text" name="author_email" value="<?= htmlspecialchars($config['author_email']) ?>" class="txt"><br>
            <?= _('Feed Language'); ?>: (<?= _('Main language of your podcast') ?>)<br>
            <select name="feed_language">
                <option value="af">Afrikanns</option>
                <option value="sq">Albanian</option>
                <option value="ar">Arabic</option>
                <option value="hy">Armenian</option>
                <option value="eu">Basque</option>
                <option value="bn">Bengali</option>
                <option value="bg">Bulgarian</option>
                <option value="ca">Catalan</option>
                <option value="km">Cambodian</option>
                <option value="zh">Chinese (Mandarin)</option>
                <option value="hr">Croation</option>
                <option value="cs">Czech</option>
                <option value="da">Danish</option>
                <option value="nl">Dutch</option>
                <option value="en" selected>English</option>
                <option value="eo">Esperanto</option>
                <option value="et">Estonian</option>
                <option value="fj">Fiji</option>
                <option value="fi">Finnish</option>
                <option value="fr">French</option>
                <option value="ka">Georgian</option>
                <option value="de">German</option>
                <option value="el">Greek</option>
                <option value="gu">Gujarati</option>
                <option value="he">Hebrew</option>
                <option value="hi">Hindi</option>
                <option value="hu">Hungarian</option>
                <option value="is">Icelandic</option>
                <option value="id">Indonesian</option>
                <option value="ga">Irish</option>
                <option value="it">Italian</option>
                <option value="ja">Japanese</option>
                <option value="jw">Javanese</option>
                <option value="ko">Korean</option>
                <option value="la">Latin</option>
                <option value="lv">Latvian</option>
                <option value="lt">Lithuanian</option>
                <option value="mk">Macedonian</option>
                <option value="ms">Malay</option>
                <option value="ml">Malayalam</option>
                <option value="mt">Maltese</option>
                <option value="mi">Maori</option>
                <option value="mr">Marathi</option>
                <option value="mn">Mongolian</option>
                <option value="ne">Nepali</option>
                <option value="no">Norwegian</option>
                <option value="fa">Persian</option>
                <option value="pl">Polish</option>
                <option value="pt">Portuguese</option>
                <option value="pa">Punjabi</option>
                <option value="qu">Quechua</option>
                <option value="ro">Romanian</option>
                <option value="ru">Russian</option>
                <option value="sm">Samoan</option>
                <option value="sr">Serbian</option>
                <option value="sk">Slovak</option>
                <option value="sl">Slovenian</option>
                <option value="es">Spanish</option>
                <option value="sw">Swahili</option>
                <option value="sv">Swedish </option>
                <option value="ta">Tamil</option>
                <option value="tt">Tatar</option>
                <option value="te">Telugu</option>
                <option value="th">Thai</option>
                <option value="bo">Tibetan</option>
                <option value="to">Tonga</option>
                <option value="tr">Turkish</option>
                <option value="uk">Ukranian</option>
                <option value="ur">Urdu</option>
                <option value="uz">Uzbek</option>
                <option value="vi">Vietnamese</option>
                <option value="cy">Welsh</option>
                <option value="xh">Xhosa</option>
            </select><br>
            <?= _('Explicit Podcast') ?>:<br>
            <input type="radio" name="explicit_podcast" value="yes" <?= $config['explicit_podcast'] == 'yes' ? 'checked' : '' ?>> <?= _('Yes'); ?> <input type="radio" name="explicit_podcast" value="no" <?= $config['explicit_podcast'] == 'no' ? 'checked' : '' ?>> <?= _('No') ?><br>
            <br>
<?php if ($config['customtagsenabled'] == 'yes') { ?>
            <?= _('Custom Feed Tags') ?>:<br>
            <textarea name="custom_tags" style="width:100%;"><?= htmlspecialchars($custom_tags) ?></textarea>
            <br>
<?php } ?>
            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
            <input type="submit" value="<?= _("Submit") ?>" class="btn btn-success">
        </form>
    </div>
</body>

</html>
