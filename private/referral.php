<?php

require('../_CONFIG.php');

if(!isset($_COOKIE['idt-sess-id'])){
    die('There was an error loading customized referrals for you');
};

try {
        $dbh = new PDO("mysql:host=" . DBHOST . ";dbname=" . DATABASE_NAME , DBUSERNAME, DBPASSWD);
    }
catch(PDOException $e)
    {
        echo $e->getMessage();
    }

if (isset($_GET['geo_range'])){
    $geo_range = $_GET['geo_range'];
} else {
    $geo_range = 105600; //105600 feet = 20miles
}

if (isset($_GET['zip'])){
    $zip = $_GET['zip'];
} else {
    $zip = false;
}

//Get session data
$q = $dbh->prepare("SELECT * from sessions WHERE id = ?");
$q->bindParam(1, $_COOKIE['idt-sess-id']);
$q->execute();
$sess_info = $q->fetch(PDO::FETCH_ASSOC);

//Get user geo data
$geo = $dbh->prepare("SELECT * from users where user_id = ?");
$geo->bindParam(1, $sess_info['user_id']);
$geo->execute();
$user_geo = $geo->fetch(PDO::FETCH_ASSOC);

//Get referrals for this tree
$refs = $dbh->prepare("SELECT `referrals_assoc_tree`.`referral_id`,`referrals`.*,                                                                                           
`referrals_assoc_tree`.`assoc_tree`  FROM referrals_assoc_tree , `referrals`                                                                                                                 
 WHERE(( referrals_assoc_tree.assoc_tree = '1374682207') AND ( referrals.id = referral_id) AND (referrals.status = 'active'))");
$refs->bindParam(1,$sess_info['tree_id']);
$refs->execute();
$ref_data = $refs->fetchAll(PDO::FETCH_ASSOC);

/*
Find referrals that are near the user
*/

//If user has provided zip, use that; otherwise, use ip geolocation previously stored in db
if ($zip){
    $geo_val = $zip;
} else {
    $geo_val = $user_geo['loc'];
}

$nearby = array();

foreach ($ref_data as $ref) {
    $ch = curl_init("http://maps.googleapis.com/maps/api/distancematrix/json?sensor=false&units=imperial&origins=" . $geo_val . "&destinations=" . $ref['loc'] );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  /* return the data */
    $result = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($result,true);

    //Get distance in feet between user and referral: 20 miles is 105600 feet;
    $distance = $json['rows'][0]['elements'][0]['distance']['value'];
    if ($distance < $geo_range){
        $nearby[] = $ref;
    }
}

?>

<h3>Nearby Referrals</h3>
<form id="refChooser" class="form-inline" role="form" >
    <div class="form-group">
        <span>Showing referrals within </span>
    </div>
    <div class="form-group">
        <select  name="geo_range" class="form-control" id="geoRange">
            <option value="528000">100</option>
            <option value="264000">50</option>
            <option value="105600" selected=selected>20</option>
            <option value="52800">10</option>
            <option value="26400">5</option>
            <option value="10560">2</option>
        </select>
    </div>
    <div class="form-group">
        <span> miles of zip code </span>
    </div>
    <div class="form-group">
        <input type="text" class="form-control" name = "zip" id="zipCode" value="<?php echo $user_geo['zip']; ?>">
    </div>
  <button id="refSubmit"  class="btn btn-default">Change</button>
</form>
<table class="table">
<?php
if (empty($nearby)){

    echo "<p>Sorry, we couldn't find any referrals near you.</p>";

} else {

    foreach ($nearby as $n) { extract($n) ?>
        <tr><td><a class="addTooltip click-through" title="Visit website" href="<?php echo $url; ?>" target="_new"  data-id="<?php echo $id; ?>">
        <?php echo $n['name']; ?></a></td><td><?php echo $n['address']; ?>, <?php echo $city; ?></td>
        <td><span class="glyphicon glyphicon-earphone click-through addTooltip" data-id="<?php echo $id; ?>"  title="Click for phone number"></span>
        <span class="phone-hide"><?php echo $phone; ?></span></td></tr> 
<?php
    }
}
?>
</table>
