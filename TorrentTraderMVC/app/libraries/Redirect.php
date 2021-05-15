<?php
class Redirect
{
    public $url;

    public function __construct()
    {}

    private function __clone()
    {}

    public static function to($url)
    {
        if (!headers_sent()) {
            header("Location: " . $url, true, 302);
            exit();
        } else {
            echo '<script type="text/javascript">';
            echo 'window.location.href="' . $url . '";';
            echo '</script>';
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="0; url=' . $url . '" />';
            echo '</noscript>';
            exit();
        }
    }

    public static function autolink($al_url, $al_msg)
    {
        Style::header("info");
        Style::begin("Info");
        echo "\n<meta http-equiv=\"refresh\" content=\"3; url=$al_url\">\n";
        ?>
        <div class="alert alert-info">
        <b><?php echo $al_msg; ?></b>&nbsp;
        <b>Redirecting ...</b><br>
        <b>[ <a href='<?php echo $al_url; ?>'>link</a> ]</b>&nbsp;
        </div>
        <?php
        Style::end();
        Style::footer();
        exit;
    }

}