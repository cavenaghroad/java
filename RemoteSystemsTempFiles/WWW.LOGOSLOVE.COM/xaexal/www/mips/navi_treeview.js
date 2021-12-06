$(document).ready(function(){
	$.ajaxSetup({
		url:'dbwork.php', 
		cache:false,
		async:true,
		type:'POST'
	});

	win_resize();
	setLog(-1);

//	buildTree();
});
var prevTitle;

function buildTree() {
	$.ajax({
		data: 'optype=buildTree',
		beforeSend: function(){
			wlog('optype=buildTree');
		},
		success: function(xml){
			$(xml).find('crlf').each(function(){
				var enterprise = $(this).find('enterprise').text();
				var name_ko = $(this).find('name_kor').text();
				$('#tbl_list_bar').parent().append('<td valign=top>'+name_ko+'<input type=text value='+enterprise+' /><br><div id='+enterprise+' name='+enterprise+' /></td>');
				$('#'+enterprise).dynatree({
					title:name_ko,
					autoCollapse:true
					,onDblClick: function(node,event){
						prevTitle = node.data.title;
						editNode(node);
						return false;
					}
					,onClick: function(node, event) {
						if( event.shiftKey ){
							editNode(node);
							return false;
						}
					}
					,dnd:{
						onDragStart: function(node){
							return true;
						},
						onDragEnter: function(node,sourceNode) {
							if( node.parent !== sourceNode.parent ) return false;
							return ['before','after'];
						},
						onDrop: function(node,sourceNode,hitMode,ui,draggable) {
//							logMsg('tree.onDrop(%o,%o,%s)',node,sourceNode,hitMode);
							sourceNode.move(node,hitMode);
						}
					}
				});
				getTree(enterprise);

			});
		},
		complete: function(){}
	});
}

function editNode(node){
//prevTitle = node.data.title,

tree = node.tree;
// Disable dynatree mouse- and key handling
tree.$widget.unbind();
// Replace node with <input>
$(".dynatree-title", node.span).html("<input id='editNode' value='" + prevTitle + "'>");
// Focus <input> and bind keyboard handler
$("input#editNode")
	.focus()
	.keydown(function(event){
		switch( event.which ) {
		case 27: // [esc]
			// discard changes on [esc]
			$("input#editNode").val(prevTitle);
			$(this).blur();
			break;
		case 13: // [enter]
			alert(prevTitle+','+node.data.title);
			// simulate blur to accept new value
			$(this).blur();
			break;
		}
	})
	.click(function(event){
		return false;
	})
	.blur(function(event){
		alert(prevTitle+','+node.data.title);
		// Accept new value, when user leaves <input>
		var title = $("input#editNode").val();
		node.setTitle(title);
		// Re-enable mouse and keyboard handlling
		tree.$widget.bind();
		node.focus();
	});
}

function getTree(tree_id){
	$.ajax({
		data : 'optype=menu&lingua='+$.cookie('lingua')+'&enterprise='+tree_id,
		beforeSend: function(){
			wlog('optype=menu&lingua='+$.cookie('lingua')+'&enterprise='+tree_id);
		},
		success: function(xml){
			var bFirst = false;
			var nCurrent = 0;
			var node = '';
			var rootNode = $('#'+tree_id).dynatree('getRoot');
			var tree = $('#'+tree_id).dynatree('getTree');
			$(xml).find('crlf').each( function(){
				var rowid = $(this).find('rowid').text();
				var menuname = $(this).find('menuname').text();
				var seq_no = $(this).find('seq_no').text();
				var menu_id = $(this).find('menu_id').text();
				var parent_id = $(this).find('parent_id').text();
				var screenurl = $(this).find('screenurl').text();
				var access_level = $(this).find('access_level').text();
				var active = $(this).find('active').text();
				
				var tag, class_id;
				if( parent_id == '' ) {
					rootNode.addChild({	title:menuname,
									key:menu_id,
									isFolder:false,
									href:screenurl+'.php',
									activate:true,
									expand:true,
									rowid:rowid,seq_no:seq_no,screenurl:screenurl,access_level:access_level,active:active
								});
				} else {
					var node = tree.getNodeByKey(parent_id);
					console.log('node ['+node+']');
					node.addChild({
						title:menuname, key:menu_id,isFolder:false,href:screenurl+'.php', activate:true, expand:true, 
						rowid:rowid,seq_no:seq_no,screenurl:screenurl,access_level:access_level,active:active
					});
				}
			});
		},
		error: function(){
		},
		complete: function(){
		}
	});
}

function fSpecialPage(_e,_p){
	this._e = _e; this._p = _e;
	
}