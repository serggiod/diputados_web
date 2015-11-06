var app_url = 'http://localhost/diputados_web/docs';
$(window).on('load',function(){
	
	/* Defininición visual de la interface. */
	// header
	$('body header')
		.css('border-top','15px solid #241f1c')
		.css('color','#ffffff')
		.css('background-color','#483e37')
		.css('height','70px')
		.css('border-bottom','10px solid #6c5d53')
	;
	$('body header img')
		.css('width','50px')
		.css('height','50px')
		.css('position','absolute')
		.css('margin-top','10px')
		.css('margin-left','10px')
	;
	$('body header h1')
		.css('font-size','1.8em')
		.css('padding-left','70px')
		.css('padding-top','0.5%')
		.css('text-shadow','#333333 0.1em 0.1em 1px')
	;
	$('body header h2')
		.css('font-size','1.5em')
		.css('padding-left','70px')
		.css('text-shadow','#333333 0.1em 0.1em 1px')
	;
	
	// content
	$('body span')
		.css('width','100%')
		.css('height','325px')
		.css('display','table')
	;
	$('body span aside')
		.css('width','25%')
		.css('background','#ac9d93')
		.css('border-left','20px solid #6c5d53')
		.css('display','table-cell')
	;
	$('body span aside ul')
		.css('width','100%')
	;
	$('body span aside ul li')
		.css('height','20px')
		.css('list-style','none')
		.css('cursor','pointer')
		.css('color','#333')
		.css('font-family','0.5em')
		.css('line-height','20px')
		.css('display','block')
		.css('background-color','#c8beb7')
		.css('padding-left','15px')
		.css('margin-bottom','3px')
		.css('border-left','7px solid #241f1c')
		.css('border-bottom','1px solid #241f1c')
	;
	$('body span section')
		.css('width','auto')
		.css('background-color','#e3dedb')
		.css('border-right','20px solid #6c5d53')
		.css('display','table-cell')
	;
	
	//Footer
	$('body footer')
		.css('color','#ffffff')
		.css('text-align','center')
		.css('font-size','0.8em')
		.css('font-style','bold')
		.css('background','#6c5d53')
		.css('padding-top','5px')
		.css('padding-bottom','5px')
	;
	
	// Cargando.
	$('#cargando')
		.css('position','absolute')
		.css('top','50%')
		.css('left','50%')
		.css('margin-top','-125px')
		.css('margin-left','-160px')
		.css('display','none')
	;
	
	/* Acciones para el menú. */
	$('body span aside ul li')
		.click(function(){
			$('#html').fadeOut(400);
			$('#cargando').show();
			$.ajax({
				url:app_url+'/html-content/html-'+this.id+'.html',
				success:function(rta){$('#html').html(rta).fadeIn(400);$('#cargando').hide();}
			});
		})
	;
});