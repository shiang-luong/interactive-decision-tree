var treeData, windowWidth, sliderWidth, slideTime, branches ;

$(document).ready(function (){

    windowWidth = $('#tree-window').show().outerWidth(false);
    sliderWidth = 0;
    slideTime = 300;
    var branches = [];
    var options = {};
    var thisURL = String(document.location);
    var urlParts = thisURL.split('?');
    loadData( urlParts[1] );

	$('#tree-reset').click(function (event) {
        event.preventDefault();
        $('#tree-window').scrollTo( 0 + 'px', {
            axis:'x',
            duration: slideTime,
            easing:'easeInOutExpo',
            onAfter: function(){
                $('.tree-content-box:gt(0)').remove();
            } //onAfter
        }); //scrollTo
    }); //click

});

function debug(str) {
	$('#debug').append( str + '<br />' );
}

function loadData(id) {
	$.ajax({
		type: 'GET',
		url: 'xml/tree' + id + '.xml',
		dataType: 'xml',
		success: function(xml){
			buildNodes(xml, id );
		}
	});
}

function TreeBranch() {
	this.id = '';
	this.content = '';
	this.forkIDs = [];
	this.forkLabels = [];
}

function buildNodes(xmlData, id) {
	var maxDepth = 0;
    branches = [];
	treeData = xmlData;
	$(xmlData).find('branch').each(
		function(){
			var branch = new TreeBranch();
			branch.id = $(this).attr('id');
			branch.content = $(this).find('content').text();
			$(this).find('fork').each(
				function(){
					branch.forkIDs.push( $(this).attr('target') );
					branch.forkLabels.push( $(this).text() );
				}
			);
			branches.push( branch );
			var branchDepthParts = branch.id.split('.');
			if( branchDepthParts.length > maxDepth ){
				maxDepth = branchDepthParts.length;
			}
        });
	sliderWidth = windowWidth * maxDepth;
	$('#tree-slider').width( sliderWidth );
	var resetText = $(xmlData).find('resetText').text();
	$('#tree-reset').html( resetText );

    //New code to show description and disclaimer
    $('.app-title, title').text($(xmlData).find('title').text());
	$('#tree-slider').append('<div class="info-wrapper"><span class="lead">' + $(xmlData).find('description').text() + '</span></div>' );
    $('.info-wrapper').width($('#tree-window').outerWidth() - 100);
    var existingUser;
    if (typeof $.cookie('idt-user') !== 'undefined'){ //this "user" has has done a tree before
        existingUser = $.cookie('idt-user');
    } else {
        existingUser = '';
    }

    if ($(xmlData).find('disclaimer').length){
        $('.info-wrapper').append('<br /><br /><button type="button" class="btn btn-warning show-disclaimer">Please Read the Disclaimer</button>');

        $('#tree-window .show-disclaimer').on('click', function (event) {
            event.preventDefault();
            $('.info-wrapper').html('<span class="lead">' + $(xmlData).find('disclaimer').text() + '</span>' )
            .append( '<div class="checkbox"> <label> <input type="checkbox" id="agree"> I agree.</label> </div>');
            $('#tree-window #agree').on('change', function (event) {
                $('.info-wrapper').remove();
                $.post('private/backend.php', {'action': 'log', 'existing_user': existingUser, 'tree_id': id}, function(data) {
                    var resp = $.parseJSON(data);
                    $.cookie('idt-user',resp.userid,{ expires: 365 });
                    $.cookie('idt-sess-id',resp.sessid);
                    showBranch(1);
                });
            });
        });
    } else {
        $('.info-wrapper').append('<br /><br /><button type="button" class="btn btn-primary begin-tree">Begin</button>');
        $('#tree-window .begin-tree').on('click', function (event) {
            event.preventDefault();
            $('.info-wrapper').remove();
            $.post('private/backend.php', {'action':'log','existing_user':existingUser, 'tree_id': id}, function(data) {
                var resp = $.parseJSON(data);
                $.cookie('idt-user',resp.userid,{ expires: 365 });
                $.cookie('idt-sess-id',resp.sessid);
                showBranch(1);
            });
        });
    }
}

function resetActionLinks(){
	$('.decision-links a').unbind( 'click' );
	$('a.back-link').unbind( 'click' );

	$('.decision-links a').click( function(e){
		if( !$(this).attr('href') ){
            //JM track here
			showBranch( $(this).attr('id') );
		}
	});
	$('a.back-link').click( function(){
		$('#tree-window').scrollTo( '-=' + windowWidth + 'px', { axis:'x', duration:slideTime, easing:'easeInOutExpo' } );
		$(this).parent().fadeOut( slideTime, function(){
			$(this).remove();
		});
	});
}

function showBranch( id ){
    var currentBranch;
	for(var i = 0; i < branches.length; i++ ){
		if( branches[i].id == id ){
			currentBranch = branches[i];
			break;
		}
	}
	var decisionLinksHTML = '<div class="decision-links">';
	for(var d = 0; d < currentBranch.forkIDs.length; d++ ){
		var link = '';
		var forkContent = $(treeData).find('branch[id="' + currentBranch.forkIDs[d] + '"]').find('content').text();
		if( forkContent.indexOf('http://') == 0 || forkContent.indexOf('https://') == 0 ){
			link = 'href="' + forkContent + '"';
		}
		decisionLinksHTML += '<a ' + link + ' id="' + currentBranch.forkIDs[d] + '">' + currentBranch.forkLabels[d] + '</a>';
	}
	decisionLinksHTML += '</div>';
	var branchHTML = '<div id="branch-' + currentBranch.id + '" class="tree-content-box"><div class="content">' + currentBranch.content + '</div>' + decisionLinksHTML;
	if( currentBranch.id !== 1 ){
		branchHTML += '<a class="back-link">&laquo; Back</a>';
	}
	branchHTML += '</div>';
	$('#tree-slider').append( branchHTML );
	resetActionLinks();
	if(currentBranch.id != 1 ){
		$('#tree-window').scrollTo( '+=' + windowWidth + 'px', { axis:'x', duration:slideTime, easing:'easeInOutExpo' } );
	}
	// add last-child class for IE
	$('.decision-links a:last').addClass( 'last-child' );
}

