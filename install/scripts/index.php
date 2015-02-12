<?

if (count($_GET) == 0)
    header("Location: http://" . $_SERVER['HTTP_HOST'] . "/en/home.html", true, 301);

include_once("../core/start.php");

?>