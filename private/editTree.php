<?php
include('inc.general.php');

$cmd = Util::makeVar( 'cmd' );
$treeID = Util::makeVar( 'treeID' );
$branchID = Util::makeVar( 'branchID' );
$branchContent = Util::makeVar( 'branchContent' );
$forks = Util::makeVar( 'forks' );
$revision = Util::makeVar( 'revision' );

$tree = new DecisionTree( $treeID );
if( !empty( $revision ) ){
	$tree->loadRevision( $revision );
}


?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Interactive Decision Tree - Editor</title>
<link href="../public/css/editor.css" rel="stylesheet" type="text/css" />
<link href="../public/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container">
<h1>Interactive Decision Tree - Editor</h1>
<div id="debug"></div>

<?php
switch( $cmd ){
	case 'edit-tree':
	case 'new-tree':
		showTreeForm( $tree, $revision );
		if( !empty( $tree->treeID ) ){
			$tree->overview();
		}
		break;
	case 'save-tree':
		saveTree( $tree, $revision );
		showTreeForm( $tree, $revision );
		$tree->overview();
		break;
	case 'save-branch':
		saveBranch( $tree );
		$tree->saveData();
		showTreeForm( $tree, $revision );
		$tree->overview();
		break;
	case 'edit-branch':
	case 'new-branch':
		showBranchForm( $tree, $branchID );
		break;
	default:
		showList( $tree );
}
?>
</div>
<script type="text/javascript" src="../public/js/jquery.min.js"></script>
<script type="text/javascript" src="../public/js/editor.js"></script>
</body>
</html>
<?php

function showList( $tree ){
	?>
  <a class="btnCreateNewTree">Create a new decision tree &raquo;</a>
  <?php
	$tree->listAll();

}

function showTreeForm( $tree, $selectedRevision ){
	$treeViewerID = substr( substr( $tree->treeID, 0, strlen( $tree->treeID ) - 4 ), 4 );
	?>
  <p><a href="<?php echo EDITOR_URL; ?>">&laquo; Return to list</a>&nbsp;&nbsp;&nbsp;
  	<a target="_blank" href="<?php echo VIEWER_URL; ?>?<?php echo $treeViewerID; ?>">View tree in new window &raquo;</a></p>
  <form id="tree-editor" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
  <?php
	if( !empty( $tree->revisions ) ){
	?>
  <select id="revisions" name="revision">
  	<option value="">View Revisions</option>
    <?php
		foreach( $tree->revisions as $revision ){
			$selectedHTML = '';
			$revisionParts = split( '\.', $revision );
			$revisionTS = array_pop( $revisionParts );
			$revisionTime = date( 'D M jS, Y g:i a T', $revisionTS );
			if( $revisionTS == $selectedRevision ){
				$selectedHTML = 'selected="true"';
			}
			echo '<option ' . $selectedHTML . ' value="' . $revisionTS . '">' . $revisionTime . '</option>';
		}
		$checkedHTML = '';
		if( !empty( $tree->resetText ) ){ $checkedHTML = 'checked'; }
		?>
  </select>
  <?php
	}
	?>
  <p><label for="title">Title:</label><br />
  	<input type="text" id="title" name="treeTitle" value="<?php echo $tree->title; ?>" /></p>
  <p><label for="description">Description:</label><br />
  	<textarea id="description" name="treeDescription"><?php echo $tree->description; ?></textarea></p>
  <p><label for="disclaimer">Disclaimer:</label><br />
  	<textarea id="disclaimer" name="treeDisclaimer"><?php echo $tree->disclaimer; ?></textarea></p>
  <p><label for="reset">
		<input type="checkbox" id="show-reset" <?php echo $checkedHTML; ?> /> Show reset/home link?</label><br />
  	<input type="text" id="reset-text" name="resetText" placeholder="text for link (e.g. Start again)" disabled value="<?php echo $tree->resetText; ?>" /></p>
  <p><input type="submit" value="Save" />
		<input type="hidden" id="treeID" name="treeID" value="<?php echo $tree->treeID; ?>" />
    <input type="hidden" name="cmd" id="cmd" value="save-tree" /></p>
  </form>
  <?php
}

function saveTree( $tree, $selectedRevision ){
	if( empty( $tree->treeID ) ){
		$tree->createNewTree();
	}
	if( !empty( $selectedRevision ) ){
		$tree->loadRevision( $selectedRevision );
	}
	$tree->title = Util::makeVar('treeTitle');
	$tree->description = Util::makeVar('treeDescription');
	$tree->disclaimer = Util::makeVar('treeDisclaimer');
	$tree->resetText = Util::makeVar('resetText');
	$tree->saveData();
}

function showBranchForm( $tree, $branchID ){
	$branch = $tree->getBranch( $branchID );
	if( empty( $branch ) ){
		// This is a new branch. Add some default data.
		$branch = new Branch();
		$branch->ID = $branchID;
		$branch->forks[$branchID . '.1'] = 'Yes';
		$branch->forks[$branchID . '.2'] = 'No';
	}
	?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="branch-editor">
  <p><label for="content">Question/Decision Text:</label><br />
  	<textarea id="content" name="branchContent"><?php echo $branch->content; ?></textarea>
		<div class="note">Enter a URL above to route users there instead of displaying text (e.g. http://hungry-media.com)</div></p>
  <p id="forks"><label for="fork">Options:</label><br />
  <?php
	foreach( $branch->forks as $forkID => $forkLabel ){
	?>
    <span><input class="fork" type="text" id="fork-<?php echo $forkID; ?>" name="fork-<?php echo $forkID; ?>" value="<?php echo $forkLabel; ?>" /> <a href="#" class="btnRemoveFork">&laquo; Remove</a><br /></span>
  <?php
	}
	?></p>
  <a href="#" id="btnAddFork">Add Another</a>
  <p><input type="submit" value="Save" /> 
  	<input type="hidden" name="cmd" id="cmd" value="save-branch" />
  	<input class="btnCancel" type="button" value="cancel" />
    <input type="hidden" id="branchID" name="branchID" value="<?php echo $branchID; ?>" />
    <input type="hidden" id="treeID" name="treeID" value="<?php echo $tree->treeID; ?>" />
    </p>
  </form>
	<?php

}

function saveBranch( $tree ){
	$branchID = Util::makeVar( 'branchID' );
	$branch = $tree->getBranch( $branchID );
	if( empty( $branch ) ){
		// This is a new branch. Add some default data.
		$branch = new Branch();
		$branch->ID = $branchID;
	}
	$branch->content = Util::makeVar( 'branchContent' );
	// Add forks and branches
	$passedForks = array();
	foreach( $_POST as $formField => $formValue ){
		if( substr( $formField, 0, 5 ) == 'fork-' ){
			// get forkID from field name
			$fieldParts = split( '-', $formField );
			$forkID = str_replace( '_', '.', $fieldParts[1]);
			array_push( $passedForks, $forkID );
			$branch->forks[$forkID] = $formValue;
			$forkBranch = $tree->getBranch( $forkID );
			if( empty( $forkBranch ) ){
				$forkBranch = new Branch();
				$forkBranch->ID = $forkID;
				$tree->saveBranch( $forkBranch );
			}
			unset( $forkBranch );
		}
	}
	// remove any forks from the branch that weren't passed
	foreach( $branch->forks as $forkID => $forkLabel ){
		if( !in_array( $forkID, $passedForks ) ){
			unset( $branch->forks[$forkID] );
			// remove the branches as well
			$tree->removeBranch( $forkID );
		}
	}
	$tree->saveBranch( $branch );	
}

function validateForm( $tree ){

}



?>
