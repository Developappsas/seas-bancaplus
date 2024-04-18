$(document).ready(function(){
	//$('#menu').superfish();
	activa_cb();
	if($('#tabgescom').length!=0)		{	$('#tabgescom').attc(	{"googleOptions":{"legend":"none","is3D":true,"width":200, "height":140,"chartArea":{"top":0, "height":180 }}, "title": ''  });		}
	if($('#tabproduc').length!=0)		{	$('#tabproduc').attc(	{"googleOptions":{"legend":"none","is3D":true,"width":200, "height":140,"chartArea":{"top":0, "height":180 }}, "title": ''  });		}
	if($('#tabgescombig').length!=0)	{	$('#tabgescombig').attc({"googleOptions":{"legend":"none","is3D":true,"width":370, "height":270,"backgroundColor":'#e7f2f8', "chartArea":{"top":0, "height":300 }}, 	"title": '',       });		}
	if($('#tabproducbig').length!=0)	{	$('#tabproducbig').attc({"googleOptions":{"legend":"none","is3D":true,"width":370, "height":270,"backgroundColor":'#e7f2f8', "chartArea":{"top":0, "height":300 }}, 	"title": '',       });		}
	
	//cambio a compact header
	$('.logo').click(function(){	
		$('#encabezado').removeClass('header'); $('#encabezado').addClass('header2');			
		$('#piepagina').removeClass('footer');  $('#piepagina').addClass('footer2');			
		
	});
});

function activa_cb()
{	$('input[tipo="slide"]').each(function()	{	
			if($(this).is(':checked'))	
			{	var eidi=$(this).attr('id');
				$('label[for="'+eidi+'"]').addClass('sele'); 
			}
			$(this).click(function(){
				var eidi=$(this).attr('id');
				if($(this).is(':checked'))	{	$('label[for="'+eidi+'"]').addClass('sele');	 }	
				else 						{	$('label[for="'+eidi+'"]').removeClass('sele');	 }
				
			});
	});
}

function verzona(me)
{	if($('#'+me).is(':visible'))	{	$('#'+me).slideUp(200);	}
	else							{	$('.zona_flota').hide();	$('#'+me).slideDown(200);	}
}