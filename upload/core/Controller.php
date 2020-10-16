<?php
/*
 * Base Controller
 * Loads the models and views
 */
class Controller
{
    // Load model
    public function model($model)
    {
        // Require model file
        require_once 'models/' . $model . '.php';

        // Instatiate model
        return new $model();
    }

    // Load View (we add data with array $data[] )
    public function view($file, $data = [], $inc = false)
    {
        global $config, $THEME, $LANGUAGE;
        // Check for view file
        if (file_exists('views/' . $file . '.php')) {
            if ($inc) {
                require "themes/" . ($_SESSION['stylesheet'] ?: $config['default_theme']) . "/header.php";
                require "themes/" . ($_SESSION['stylesheet'] ?: $config['default_theme']) . "/topnavbar.php";
                require_once "views/" . $file . ".php";
                require "themes/" . ($_SESSION['stylesheet'] ?: $config['default_theme']) . "/footer.php";
            } else {
                require_once "views/" . $file . ".php";
            }
        } else {
            // View does not exist
            die('View does not exist');
        }
    }
}
