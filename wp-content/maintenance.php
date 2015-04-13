<?php
$protocol = $_SERVER["SERVER_PROTOCOL"];
if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol )
        $protocol = 'HTTP/1.0';
header( "$protocol 503 Service Unavailable", true, 503 );
header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Kennett Design Under Maintenance</title>
</head>
<body>
    <h1>Coming soon...</h1>
    <img src="http://kennett-design.com/wp-content/kd_logo_cropped.jpg">
    <h2>In the mean time... You can find us on Facebook!<h2>
    <a href="http://www.facebook.com/KennettDesign"><img src="http://kennett-design.com/wp-content/f_logo.png"></></a>
</body>
</html>
<?php die(); ?>