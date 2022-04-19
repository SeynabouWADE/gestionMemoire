<?php

	echo $data;
	
?>
<script>

if($('.card-title').html()=="Se connecter"){
	
	$('#block0login').removeClass(),
	$('#block0password').removeClass(),

	$('#block0login').addClass('col-md-7'),
	$('.card-text').addClass('col-sm-9 pl-5'),
	$('#block0password').addClass('col-md-7'),
	
	$('#block0login').css('margin-left', '20%'),
	$('#block0password').css('margin-left', '20%'),
	$('#block0login').append('<br>'),
	$('.stats').css('margin-left', '20%'),
	$('.text-right').hide(),
	$(".navbar-minimize").parent('div').attr('id','div_entete'),
	$('#div_entete').hide(),
	$('.card').css({'width':'50%','margin-left':'25%'}),
	$('.card-header').css({'margin-left': '20%'})
}
</script>