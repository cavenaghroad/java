$(document)
.ready(function(){
})
.on('click','#btnWrite',function(e,u){
	var filelist='';
	var imgid=$('#imglist img');
	imgid.each(function(ndx){
		if(filelist!='') filelist+=',';
		filelist+=$(this).attr('id');
	});

	$.ajax({
		url:'_housing.php',async:false,type:'post',datatype:'json', data:{
			_type:$('#_type').val(),optype:$('#optype').val(),title:$('#title').val(),content:$('#content').val(),rowid:$('#rowid').val(),img:filelist,	// <= Add the image file information.
			housetype:$('#housetype').val(),price:$('#price').val(),deposit:$('#deposit').val(),
			roomtype:$('#roomtype').val(),loc_type:$('#loc_type').val(),location:$('#location').val(),
			housesize:$('#housesize').val(),sizeunit:$('#sizeunit').val(),staytype:$('input[name=staytype]:checked').val(),
			howlong:$('#howlong').val(),expected_move:$('#expected_move').val(),num_room:$('#num_room').val(),
			pub_include:$('input[name=pub]:checked').val(),cookable:$('input[name=cookable]:checked').val()
		},
		beforeSend:function(){
			var result='';

			if(!generalFields()) return false;
			
			if($('#loc_type').val()=='address'){
			
			} else {
				if(isNaN($('#location').val())){
					result='Postcode should be a number.';
					return false;
				}
			}
			if(isNaN($('#num_room').val())){
				result='방 갯수를 입력하십시오.';
				return false;
			}
			if($.trim($('#housesize').val())!='' && isNaN($('#housesize').val())){
				result='주택 크기를 입력하십시오.';
				return false;
			}
			if(isNaN($('#price').val())){
				result='가격(렌트비)를 입력하십시오.';
				return false;
			}
			if($.trim($('#deposit').val())!='' && isNaN($('#deposit').val())){
				result='보증금을 입력하십시오.';
				return false;
			}			
		},
		success:function(json){
			if(json['msg']!=''){
				result=alert(json['msg']); 
				return false;
			}
			alert('게시물이 등록됐습니다.1');
			document.location='post.php?_type='+$('#_type').val()+'&rowid='+json['rowid'];
		}
	});
	return false;
})
.on('click','#btnDelete',function(e,u){
	if(!confirm('정말로 삭제하시겠습니까?')) return false;
	$.post('_housing.php',{
		optype:'delete',rowid:$('#rowid').val()
	},function(json){
		if(json['msg']!=''){
			alert(json['msg']); return false;
		}
		alert('삭제되었습니다.');
	},'json');
	return false;
})
;

var w_total=0;