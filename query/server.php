<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name=viewport content="width=device-width, initial-scale=1">
        <title>Main</title>
        <script src="../semantic/jquery-2.1.4.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <script src="../semantic/semantic.js"></script>
        <script src="../semantic/components/dropdown.js"></script>
        <link href="../semantic/semantic.css" rel="stylesheet" />
        <link href="../semantic/components/dropdown.css" rel="stylesheet" />
    </head>
    <body>
        
        <div class="container"> <br><br>
  <div class="ui form segment"><h3>RCON Status</h3>
            <div class="table-responsive">
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    function createConnection($ip, $port) {
        $fp = fsockopen("udp://" . $ip, $port, $errno, $errstr);
        if (!$fp) {
            return null;
        }
        return $fp;
    }
    
    function printheadrow ($list = array()) {
        foreach ($list as $elem) {
                print "<th>$elem</th>";
        }
    }


function printrow ($list = array(), $class) {
    print "<tr>\n";
    foreach ($list as $elem) {
        print "<td>$elem</td>";
    }
    print "</tr>\n";
}

function stripcolors ($quoi) {
    $result = $quoi;
    $result = preg_replace ("/\^x....../i", "", $result); // remove OSP's colors (^Xrrggbb)
    $result = preg_replace ("/\^./", "", $result); // remove Q3 colors (^2) or OSP control (^N, ^B etc..)
    $result = preg_replace ("/</", "&lt;", $result); // convert < into &lt; for proper display
    return $result;
}
function rcon ($ip, $port, $rcon_pass, $command) {
    $fp = fsockopen("udp://$ip",$port, $errno, $errstr, 2);
    socket_set_timeout($fp,2);

    if (!$fp)    {
        echo "$errstr ($errno)<br>\n";
    } else {
        $query = "\xFF\xFF\xFF\xFFrcon \"" . $rcon_pass . "\" " . $command;
        fwrite($fp,$query);
    }
    $data = '';
    while ($d = fread ($fp, 10000)) {
        $data .= $d;
    }
    fclose ($fp);
    $data = preg_replace ("/....print\n/", "", $data);
    $data = stripcolors ($data);
    return $data;
}
rconstatus("185.34.216.28", "20100", "itschristmas");
function rconstatus ($ip, $port, $rcon_pass) {
    $result = rcon ($ip, $port, $rcon_pass, "status");
    $result = explode ("\n", $result);

    # ok, let's deal with the following :
    #
    # map: q3wcp9
    # num score ping name            lastmsg address               qport rate
    # --- ----- ---- --------------- ------- --------------------- ----- -----
    #   1    19   33 l33t^n1ck       33 62.212.106.216:27960   5294 25000

    print "<table class=\"table table-hover table-bordered\">\n";
    print "<thead>";
    printheadrow (array("ID", "Current score", "Ping", "Name", "IP", "Rate"));
    print "</thead>\n";
    array_shift($result); // 1st line : map q3wcp9
    array_shift($result); // 2nd line : col headers
    array_shift($result); // 3rd line : -- ------ ----
    array_pop($result);
    array_pop($result); // two empty lines at the end, go figure.
    foreach ($result as $line) {
        $player = $line;
        preg_match_all("/^\s*(\d+)\s*(\d+)\s*(\d+)(.*?)(\d*)\s*(\S*)\s*(\d*)\s*(\d*)\s*$/", $player, $out);
        $num = $out[1][0];
        $score = $out[2][0];
        $ping = $out[3][0];
        $name = trim($out[4][0]);
        //$lastmsg = $out[5][0];
        $address = $out[6][0];
        if ($address != 'bot') {
            $addressip = preg_replace ("/:.*$/", "", $address);
            $address = "$address";
        }
        //$qport = $out[7][0];
        $rate = $out[8][0];
        printrow (array ($num, $score, $ping, $name, $address, $rate), "");
    }
    print "\n</table>\n";
}


?>
            </div>
      </div>
<script>
jQuery('ul.nav li.dropdown').hover(function() {
 jQuery(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn();
}, function() {
 jQuery(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut();
});
</script>
    </body>
</html>