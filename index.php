<?php require('_CONFIG.php') ?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo APP_NAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="public/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/decisionTree.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div  class="container">
        <h1><?php echo APP_NAME; ?></h1>
        <div class="welcome toggle">
            <p>This app will help you determine whether or not you can get your criminal 
            conviction expunged under Louisiana law.</p>
            <p>Before we begin, please <a href="#" id = "readDis">read the disclaimer</a>.</p>
        </div>
        <div class = "disclaimer toggle">
            <p>This app does not provide legal advice.  Any information which
            you give here is not confidential and is not protected by attorney-client
            privilege.</p>
            <div class="checkbox">
                <label>
                <input type="checkbox" id="agree"> I agree to this.
                </label>
            </div>
        </div>
        <div class="begin toggle">
            <p>Ok, let's start!</p>
            <button id="start">Begin</button>
        </div>

        <div id="tree-window" class="container" data-source="<?php echo TARGET_TREE; ?>">
            <div id="tree-slider">

            </div>
        </div>
    </div>    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
     <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
     <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/jquery.easing.1.3.js"></script>
    <script type="text/javascript" src="js/jquery.scrollTo-1.3.3-min.js"></script>
    <script type="text/javascript" src="js/decisionTree.js"></script>
  </body>
</html>
