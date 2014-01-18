<?php
session_start();
if (!$_SESSION['isLoggedIn']){
    header('Location: login.php');
}

require('../_CONFIG.php');
require('../_THEME.php');
require('db.php');


?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Interactive Decision Tree - Admin</title>
<link href="../public/css/editor.css" rel="stylesheet" type="text/css" />
<link href="../public/bower_components/bootstrap/dist/css/<?php echo BOOTSTRAP_THEME; ?>" rel="stylesheet">
<link href="../public/bower_components/DataTables/media/css/dataTables.bootstrap.css" rel="stylesheet">
</head>

<body>
<nav class="navbar navbar-default" role="navigation">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="editTree.php">Interactive Decision Tree - Admin</a>
  </div>

  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <ul class="nav navbar-nav navbar-right">
      <li><a href="editTree.php">Trees</a></li>
      <li><a href="theme_chooser.php">Themes</a></li>
      <li><a class="active" href="referral_sources.php">Referral Sources</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div><!-- /.navbar-collapse -->
</nav>
<div class="container">
<div class="row">
    <div class="notify alert">
        <div class="notify-text"></div>
    </div>
</div>
<h1>Manage Referral Sources</h1>
<div class = "ref-container">
<p> <button class="btn btn-primary ref-add">Add</button></p>
    <div class="table-responsive">

        <table id="refTable" class="table table-hover">
            <thead><tr><th>Name</th><th>Address</th><th>City</th><th>Email</th><th>Phone</th></tr></thead>
            <tbody>
    <?php 
        $q = $dbh->prepare('SELECT * from referrals ORDER BY id asc');
        $q->execute();
        $rows = $q->fetchALL(PDO::FETCH_ASSOC);
        foreach ($rows as $r) {extract($r)
    ?>
            <tr data-id="<?php echo $r['id'] ?>">
            <td><?php echo $r['name'];?></td>
            <td><?php echo $r['address'];?></td>
            <td><?php echo $r['city'];?></td>
            <td><?php echo $r['email'];?></td>
            <td><?php echo $r['phone'];?></td>
            </tr>
        <?php
        }
        ?>
            </tbody>
        </table>
    </div>
</div>
</div>
<script type="text/javascript" src="../public/js/jquery.min.js"></script>
<script type="text/javascript" src="../public/js/referralSources.js"></script>
<script type="text/javascript" src="../public/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="../public/js/additional-methods.js"></script>
<script type="text/javascript" src="../public/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../public/bower_components/bootstrap/js/tooltip.js"></script>
<script type="text/javascript" src="../public/bower_components/bootstrap/js/popover.js"></script>
<script type="text/javascript" src="../public/bower_components/handlebars/handlebars.min.js"></script>
<script type="text/javascript" src="../public/bower_components/DataTables/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="../public/bower_components/DataTables/media/js/dataTables.bootstrap.js"></script>

<script id="view-template" type="text/x-handlebars-template">

<div class="center-block">
    <button class="btn btn-primary" onClick="window.location.reload();">&#171; Back</button>
        <dl class="dl-horizontal">
            <dt>Name</dt>
            <dd>{{name}}<dd>
            <dt>Email</dt>
            <dd>{{email}}<dd>
            <dt>Phone</dt>
            <dd>{{phone}}<dd>
            <dt>Address</dt>
            <dd>{{address}}<dd>
            <dt>City</dt>
            <dd>{{city}}<dd>
            <dt>State</dt>
            <dd>{{state}}<dd>
            <dt>Zip</dt>
            <dd>{{zip}}<dd>
            <dt>Status</dt>
            <dd>{{status}}<dd>
            <button class="btn btn-default ref-edit" data-id="{{id}}">Edit</button>
            <button class="btn btn-default ref-delete" data-id="{{id}}">Delete</button>
        </dl>
</div>
</script>
<script id="update-template" type="text/x-handlebars-template">
<form class="form-horizontal" role="form">
  <div class="form-group">
    <label for="refName" class="col-sm-2 control-label">Name</label>
    <div class="col-sm-10">
      <input type="text" name = "name" class="form-control" id="refName" value="{{name}}" class="required" required>
    </div>
  </div>
  <div class="form-group">
    <label for="refEmail" class="col-sm-2 control-label">Email</label>
    <div class="col-sm-10">
      <input type="email" name = "email" class="form-control" id="refEmail" value="{{email}}" class="required email">
    </div>
  </div>
  <div class="form-group">
    <label for="refPhone" class="col-sm-2 control-label">Phone</label>
    <div class="col-sm-10">
      <input type="tel" name = "phone" class="form-control" id="refPhone" value="{{phone}}" class="required phoneUS">
    </div>
  </div>
  <div class="form-group">
    <label for="refAddress" class="col-sm-2 control-label">Address</label>
    <div class="col-sm-6">
      <textarea name = "address" class="form-control" id="refaddress"  class="required">{{address}}</textarea>
    </div>
  </div>
  <div class="form-group">
    <label for="refCity" class="col-sm-2 control-label">City</label>
    <div class="col-sm-10">
      <input type="text" name = "city" class="form-control" id="refCity" value="{{city}}" class="required">
    </div>
  </div>
  <div class="form-group">
    <label for="refState" class="col-sm-2 control-label">State</label>
    <div class="col-sm-10">
      <input type="text" name = "state" class="form-control" id="refState" value="{{state}}" class="required">
    </div>
  </div>
  <div class="form-group">
    <label for="refZip" class="col-sm-2 control-label">Zip</label>
    <div class="col-sm-10">
      <input type="text" name = "zip" class="form-control" id="refZip" value="{{zip}}" class="required">
    </div>
  </div>
  <div class="form-group">
    <label for="refStatus" class="col-sm-2 control-label">Status</label>
    <div class="col-sm-10">
      <select name = "Status" class="form-control" id="refStatus">
        <option value="active" selected=selected>Active</option>
        <option value="inactive">Inactive</option>
    </div>
  </div>
  <input type="hidden" name="id" value="{{id}}">
  <input type="hidden" name="action" value="update">
  <br />
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-default ref-update">Save</button>
      <button type="submit" class="btn btn-default ref-cancel-update" data-id="{{id}}" >Cancel</button>
    </div>
  </div>
</form>
</script>
<script id="add-template" type="text/x-handlebars-template">

<form class="form-horizontal" role="form">
  <div class="form-group">
    <label for="refName" class="col-sm-2 control-label">Name</label>
    <div class="col-sm-10">
      <input type="text" name = "name" class="form-control" id="refName" placeholder="Name" class="required" required>
    </div>
  </div>
  <div class="form-group">
    <label for="refEmail" class="col-sm-2 control-label">Email</label>
    <div class="col-sm-10">
      <input type="email" name = "email" class="form-control" id="refEmail" placeholder="Email" class="required email">
    </div>
  </div>
  <div class="form-group">
    <label for="refPhone" class="col-sm-2 control-label">Phone</label>
    <div class="col-sm-10">
      <input type="tel" name = "phone" class="form-control" id="refPhone" placeholder="Phone" class="required phoneUS">
    </div>
  </div>
  <div class="form-group">
    <label for="refAddress" class="col-sm-2 control-label">Address</label>
    <div class="col-sm-6">
      <textarea name = "address" class="form-control" id="refaddress"  class="required"></textarea>
    </div>
  </div>
  <div class="form-group">
    <label for="refCity" class="col-sm-2 control-label">City</label>
    <div class="col-sm-10">
      <input type="text" name = "city" class="form-control" id="refCity" placeholder="City" class="required">
    </div>
  </div>
  <div class="form-group">
    <label for="refState" class="col-sm-2 control-label">State</label>
    <div class="col-sm-10">
      <input type="text" name = "state" class="form-control" id="refState" placeholder="State" class="required">
    </div>
  </div>
  <div class="form-group">
    <label for="refZip" class="col-sm-2 control-label">Zip</label>
    <div class="col-sm-10">
      <input type="text" name = "zip" class="form-control" id="refZip" placeholder="Zip" class="required">
    </div>
  </div>
  <div class="form-group">
    <label for="refStatus" class="col-sm-2 control-label">Status</label>
    <div class="col-sm-10">
      <select name = "Status" class="form-control" id="refStatus">
        <option value="active" selected=selected>Active</option>
        <option value="inactive">Inactive</option>
    </div>
  </div>
  <input type="hidden" name="action" value="create">
  <br />
  <div class="form-group">
    <button type="submit" class="btn btn-default">Add</button>
    <button type="submit" class="btn btn-default" onClick="window.location.reload();">Cancel</button>
  </div>
</form>
</script>
<script id="deleted-template" type="text/x-handlebars-template">
<div class="center-block">
    <button class="btn btn-primary" onClick="window.location.reload();">Show All</button>
</div>
</script>
</body>
</html>
