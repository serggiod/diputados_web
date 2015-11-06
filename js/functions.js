$(window).on('load',function(){
	$.ajaxSetup({cache:false});
	uri = window.document.location.toString();
	prm  = uri.split('/?');
	if(1 in prm){
		id = window.diputado.tools.sanitizeInt(prm[1]);
		if(id === null){
			window.diputado.select();
		} else {
			window.diputado.init(parseInt(id));
		}
	} else {
		window.diputado.select();
	}
});
window.diputado={
	usrId:0,
	usrNm:null,
	usrDir:null,
	appUrl:'/diputados_web',
	legUrl:'/web/home',
	init:function(id){
		window.diputado.usrId = id;
		window.diputado.data();
		window.diputado.comisiones();
		window.diputado.noticia.tres();			
	},
	data:function(){
		$.getJSON(window.diputado.appUrl+'/xhr/home_diputado_data.php?id='+window.diputado.usrId,function(json){
			window.diputado.usrNm  = json.apellido+' '+json.nombre;
			window.diputado.usrDir = json.usrdir;
			$('a#navbar-brand').html(window.diputado.usrNm);
			$('title').html(window.diputado.usrNm+' - Actividad Legislativa');
			$('img#fotografia').attr('alt',json.apellido+' '+json.nombre).attr('src',window.diputado.legUrl+'/img/diputados/'+json.archivo);
			$('span#perfil_nombre').html(window.diputado.usrNm);
			$('span#perfil_bloque').html(json.bloque);
			$('span#perfil_mandato').html(json.desde+' - '+json.hasta);
			$('span#perfil_email').html('<a href="mailto:'+json.email+'" target="__blank">'+json.email+'</a>');
			$('span#perfil_telefono').html(json.telefono);
			
			if(json.paginaweb){
				$('span#perfil_paginaweb').html('<a href="'+json.paginaweb+'" target="__blank">'+json.paginaweb+'</a>');
				$('nav#btn-socials').append('<a href="'+json.paginaweb+'"  target="_blank" class="paginaweb-ico">facebook-ico</a>');
			}

			if(json.facebook){
				$('span#perfil_facebook').html('<a href="'+json.facebook+'" target="__blank">'+json.facebook+'</a>');
				$('nav#btn-socials').append('<a href="'+json.facebook+'" target="_blank" class="facebook-ico">facebook-ico</a>');
			}
			if(json.twitter){
				$('span#perfil_twitter').html('<a href="'+json.twitter+'" target="__blank">'+json.twitter+'</a>');
				$('nav#btn-socials').append('<a href="'+json.twitter+'" target="_blank" class="twitter-ico">twitter-ico</a>');
			}
			if(json.youtube){
				$('span#perfil_youtube').html('<a href="'+json.youtube+'" target="__blank">'+json.youtube+'</a>');
				$('nav#btn-socials').append('<a href="'+json.youtube+'" target="_blank" class="youtube-ico">youtube-ico</a>');
			}
		});
	},
	comisiones:function(){
		$.getJSON(window.diputado.appUrl+'/xhr/home_diputado_comisiones.php?id='+window.diputado.usrId,function(json){
			json.forEach(function(com){
				$('ul#diputado_comisiones').append('<li><strong>'+com.nombre+'</strong>('+com.cargo+')</li>');
			});
		});		
	},
	select:function(){
		$.getJSON(window.diputado.appUrl+'/xhr/index.php',function(json){
			opt = window.diputado.html.option('Seleccione un diputado...','0','class="btn btn-default"');
			json.forEach(function(o){
				opt += window.diputado.html.option(o.apellido+' '+o.nombre,o.id,'onclick="javascript: window.diputado.change('+o.id+');"');
			});
			sel = window.diputado.html.select(null,opt,'class="form-control"');
			cen = window.diputado.html.center(sel);
			$('body').html(cen);
		});
	},
	change:function(id){
		window.document.location = window.diputado.appUrl+'/?'+id;
	}
}
window.diputado.html={
	br:function(){ return '<br />'; },
	hr:function(id){ return '<hr id="'+id+'" />' },
	h1:function(id,val,prm){ return '<h1 id="'+id+'" '+prm+'>'+val+'</h1>'; },
	h2:function(id,val,prm){ return '<h2 id="'+id+'" '+prm+'>'+val+'</h2>'; },
	h3:function(id,val,prm){ return '<h3 id="'+id+'" '+prm+'>'+val+'</h3>'; },
	h4:function(id,val,prm){ return '<h4 id="'+id+'" '+prm+'>'+val+'</h4>'; },
	h5:function(id,val,prm){ return '<h5 id="'+id+'" '+prm+'>'+val+'</h5>'; },
	h6:function(id,val,prm){ return '<h6 id="'+id+'" '+prm+'>'+val+'</h6>'; },
	p:function(id,val,prm){ return '<p id="'+id+'" '+prm+'>'+val+'</p>'; },
	div:function(id,val,prm){ return '<div id="'+id+'" '+prm+'>'+val+'</div>'; },
	table:function(id,val,prm){ return '<table id="'+id+'" '+prm+'>'+val+'</table>'; },
	tr:function(id,val,prm){ return '<tr id="'+id+'" '+prm+'>'+val+'</tr>'; },
	td:function(id,val,prm){ return '<td id="'+id+'" '+prm+'>'+val+'</td>'; },
	ul:function(id,val,prm){ return '<ul id='+id+'" '+prm+'>'+val+'</ul>'; },
	ol:function(id,val,prm){ return '<ol id='+id+'" '+prm+'>'+val+'</ol>'; },
	li:function(id,val,prm){return '<li id='+id+'" '+prm+'>'+val+'</li>'; },
	a:function(id,val,prm){ return '<a id="'+id+'" '+prm+'>'+val+'</a>'; },
	img:function(id,val,prm){ return '<img id="'+id+'" src="'+val+'" '+prm+' />'},
	select:function(id,val,prm){ return '<select id="'+id+'" '+prm+'>'+val+'</select>' },
	option:function(txt,val,prm){ return '<option value="'+val+'" '+prm+'>'+txt+'</option>' },
	center:function(val){ return '<center>'+val+'</center>'}
}
window.diputado.navbar={
	inicio:function(){window.diputado.noticia.tres();},
	proyectos:function(){window.diputado.proyectos.inicio();},
	prensa:function(){window.diputado.prensa.inicio();},
	fotografias:function(){window.diputado.fotografias.inicio();},
	videos:function(){window.diputado.videos.inicio();}
}
window.diputado.noticia={
	tres:function(){
		$.getJSON(window.diputado.appUrl+'/xhr/home_diputado_tres_noticias.php?id='+window.diputado.usrId,function(json){
			$('section#diputado_tres_noticias').html('');
			json.forEach(function(not){
				html = '<div class="col-md-4">'
				html +='<h5 class="text-right">'+not.volanta+'</h6>';
				html +='<h3 class="text-info"><a href="javascript: window.diputado.noticia.leerMas('+not.id+');">'+not.titulo+'</a> <br /><small>'+not.bajada+'</small></h4>';

				if(not.fotografia){html +='<a href="javascript: window.diputado.noticia.leerMas('+not.id+');"><img src="'+window.diputado.appUrl+'/fotografias/'+window.diputado.usrDir+'/'+not.fotografia+'" style="width:90%;" border="0"/></a><br />';}

				html +='<small>('+not.fecha+' - '+not.hora+')</small>';
				html +='<p class="lead">'+not.cabeza+' <a href="javascript: window.diputado.noticia.leerMas('+not.id+');">Leer m&aacute;s</a></p>';
				html +='</div>';
				$('section#diputado_tres_noticias').append(html);
			});
			$("html,body").animate({scrollTop:0},800);
		});
	},
	leerMas:function(id){
		$('#overlay').show();
		$('#cargando').show();
		$.getJSON(window.diputado.appUrl+'/xhr/noticia_leer_mas.php?id='+id,function(json){
			html  = '<h5 class="text-right">'+json.volanta+'</h6>';
			html += '<h3 class="text-info">'+json.titulo+' <br /><small>'+json.bajada+'</small></h4>';
			html +='<small>('+json.fecha+' - '+json.hora+')</small>';
			html +='<p class="lead">'+json.cabeza+'</p>';
			html +='<p class="lead">'+json.cuerpo+'<p><center><a href="javascript: window.diputado.noticia.leerMasHide();" class="btn btn-success btn-medium">Cerrar</a></center></p></p>';

			$.getJSON(window.diputado.appUrl+'/xhr/noticia_leer_mas_fotos.php?id='+id,function(fot){
				fot.forEach(function(img){
					$('#leer_mas_mediabar').append('<img src="'+window.diputado.appUrl+'/fotografias/'+window.diputado.usrDir+'/'+img.archivo+'" width="90%" border="0"/>');
				});
			});

			$.getJSON(window.diputado.appUrl+'/xhr/noticia_leer_mas_videos.php?id='+id,function(vid){
				vid.forEach(function(v){
					$.get(window.diputado.appUrl+'/videos/'+window.diputado.usrDir+'/'+v.archivo+'.html',function(html){
						$('#leer_mas_mediabar').append(html);
					})
				})
			});

			$('#cargando').hide();
			$('#leer_mas').show();
			$('#leer_mas_noticia').html(html);
			$("html,body").animate({scrollTop:0},800);
		});
	},
	leerMasHide:function(){
		$('#leer_mas').hide();
		$('#overlay').hide();
		$('#leer_mas_mediabar').html('');
		$('#leer_mas_noticia').html('');
		$("html,body").animate({scrollTop:0},1600);
	}
}
window.diputado.proyectos={
	inicio:function(){
		this.cofirmados();
	},
	firmados:function(){
		$.getJSON(window.diputado.appUrl+'/xhr/proyectos_firmados.php?id='+window.diputado.usrId,function(json){
			td  = window.diputado.html.td(null,'EXPEDIENTE',null);
			td += window.diputado.html.td(null,'PROYECTO',null);
			td += window.diputado.html.td(null,'TIPO',null);
			td += window.diputado.html.td(null,'FECHA',null);
			td += window.diputado.html.td(null,'ACCIONES',null);
			tr  = window.diputado.html.tr(null,td,null);

			json.forEach(function(reg){
				lk  = window.diputado.html.a(null,reg.expediente,'href="'+window.diputado.legUrl+'/public/detalle_proyecto.php?p='+reg.id+'" target="__black" class="link"');
				td  = window.diputado.html.td(null,lk,null);
				td += window.diputado.html.td(null,reg.proyecto,null);
				td += window.diputado.html.td(null,reg.tipo,null);
				td += window.diputado.html.td(null,reg.fecha,null);
				lk  = window.diputado.html.a(null,'Ver Tr&aacute;mite','href="'+window.diputado.legUrl+'/public/detalle_proyecto.php?p='+reg.id+'" target="__black" class="btn btn-default"');
				lk += window.diputado.html.a(null,'Texto Original','href="'+window.diputado.legUrl+'/public/img/sesiones/ftp/'+reg.expediente+'/'+reg.expediente+'.pdf" target="__blank" class="btn btn-default"');	
				td += window.diputado.html.td(null,lk,null);
				tr += window.diputado.html.tr(null,td,null);
			});

			title = window.diputado.html.h3(null,'Proyectos Firmados',null);
			title += window.diputado.html.hr(null);
			title += window.diputado.html.br();

			opt  = window.diputado.html.li(
				null,
				window.diputado.html.a(null,'Proyectos Cofirmados','href="javascript: window.diputado.proyectos.cofirmados();"'),
				null
			);
			opt  += window.diputado.html.li(
				null,
				window.diputado.html.a(null,'Proyectos Firmados','href="javascript: window.diputado.proyectos.firmados();"'),
				null
			);
			drop = window.diputado.html.ul(null,opt,'class="dropdown-menu"');
			btn_group = window.diputado.html.div(null,
				'<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">Seleccione que proyectos ver...<span class="caret"></span></button>'+drop,
				'class="btn-group"'
			);

			table = window.diputado.html.table(null,tr,'class="table table-striped table-bordered"');
			html  = title+btn_group+table;

			$('section#diputado_tres_noticias').html(html);
			$('html,body').animate({top:0},800);	
		});
	},
	cofirmados:function(){
		$.getJSON(window.diputado.appUrl+'/xhr/proyectos_cofirmados.php?id='+window.diputado.usrId,function(json){
			td  = window.diputado.html.td(null,'EXPEDIENTE',null);
			td += window.diputado.html.td(null,'PROYECTO',null);
			td += window.diputado.html.td(null,'TIPO',null);
			td += window.diputado.html.td(null,'FECHA',null);
			td += window.diputado.html.td(null,'ACCIONES',null);
			tr  = window.diputado.html.tr(null,td,null);

			json.forEach(function(reg){
				lk  = window.diputado.html.a(null,reg.expediente,'href="'+window.diputado.legUrl+'/public/detalle_proyecto.php?p='+reg.id+'" target="__black" class="link"');
				td  = window.diputado.html.td(null,lk,null);
				td += window.diputado.html.td(null,reg.proyecto,null);
				td += window.diputado.html.td(null,reg.tipo,null);
				td += window.diputado.html.td(null,reg.fecha,null);
				lk  = window.diputado.html.a(null,'Ver Tr&aacute;mite','href="'+window.diputado.legUrl+'/public/detalle_proyecto.php?p='+reg.id+'" target="__black" class="btn btn-default"');
				lk += window.diputado.html.a(null,'Texto Original','href="'+window.diputado.legUrl+'/public/img/sesiones/ftp/'+reg.expediente+'/'+reg.expediente+'.pdf" target="__blank" class="btn btn-default"');	
				td += window.diputado.html.td(null,lk,null);
				tr += window.diputado.html.tr(null,td,null);
			});

			title = window.diputado.html.h3(null,'Proyectos Cofirmados',null);
			title += window.diputado.html.hr(null);
			title += window.diputado.html.br();

			opt  = window.diputado.html.li(
				null,
				window.diputado.html.a(null,'Proyectos Cofirmados','href="javascript: window.diputado.proyectos.cofirmados();"'),
				null
			);
			opt  += window.diputado.html.li(
				null,
				window.diputado.html.a(null,'Proyectos Firmados','href="javascript: window.diputado.proyectos.firmados();"'),
				null
			);
			drop = window.diputado.html.ul(null,opt,'class="dropdown-menu"');
			btn_group = window.diputado.html.div(null,
				'<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown">Seleccione que proyectos ver...<span class="caret"></span></button>'+drop,
				'class="btn-group"'
			);

			table = window.diputado.html.table(null,tr,'class="table table-striped table-bordered"');
			html  = title+btn_group+table;

			$('section#diputado_tres_noticias').html(html);
			$('html,body').animate({top:0},800);	
		});
	}
}
window.diputado.prensa={
	inicio:function(){
		this.partes();
	},
	partes:function(){
		$.getJSON(window.diputado.appUrl+'/xhr/prensa_partes.php?id='+window.diputado.usrId,function(json){
			td  = window.diputado.html.td(null,'TITULO',null);
			td += window.diputado.html.td(null,'RESUMEN',null);
			td += window.diputado.html.td(null,'FECHA',null);
			td += window.diputado.html.td(null,'ACCIONES',null);
			tr  = window.diputado.html.tr(null,td,null);

			json.forEach(function(reg){
				lk  = window.diputado.html.a(null,reg.titulo,'href="javascript: window.diputado.noticia.leerMas('+reg.id+');" class="btn btn-link"');
				td  = window.diputado.html.td(null,lk,null);
				td += window.diputado.html.td(null,reg.cabeza,null);
				td += window.diputado.html.td(null,reg.fecha,null);
				lk  = window.diputado.html.a(null,'Leer M&aacute;s','href="javascript: window.diputado.noticia.leerMas('+reg.id+');" class="btn btn-default"');
				td += window.diputado.html.td(null,lk,null);
				tr += window.diputado.html.tr(null,td,null);
			});

			title = window.diputado.html.h3(null,'Partes de Prensa',null);
			title += window.diputado.html.hr(null);
			title += window.diputado.html.br();

			table = window.diputado.html.table(null,tr,'class="table table-striped table-bordered"');

			html  = title+table;
			$('section#diputado_tres_noticias').html(html);
			$('html,body').animate({top:0},800);
		});		
	}
}
window.diputado.fotografias={
	inicio:function(){
		this.fotos();
	},
	fotos:function(){
		$.getJSON(window.diputado.appUrl+'/xhr/fotografias_fotos.php?id='+window.diputado.usrId,function(json){
			td  = window.diputado.html.td(null,'TITULO',null);
			td += window.diputado.html.td(null,'RESUMEN',null);
			td += window.diputado.html.td(null,'FECHA',null);
			td += window.diputado.html.td(null,'ACCIONES',null);
			tr  = window.diputado.html.tr(null,td,null);

			json.forEach(function(reg){
				td  = window.diputado.html.td(null,reg.nombre,null);
				td += window.diputado.html.td(null,reg.descripcion,null);
				td += window.diputado.html.td(null,reg.fecha,null);
				lk  = window.diputado.html.a(null,'Ver Galer&iacute;a','href="javascript: window.diputado.fotografias.showGallery('+reg.id+');" class="btn btn-default"');
				td += window.diputado.html.td(null,lk,null);
				tr += window.diputado.html.tr(null,td,null);
			});

			title = window.diputado.html.h3(null,'Galeria de Fotografias',null);
			title += window.diputado.html.hr(null);
			title += window.diputado.html.br();

			table = window.diputado.html.table(null,tr,'class="table table-striped table-bordered"');

			html  = title+table;

			$('section#diputado_tres_noticias').html(html);
			$('html,body').animate({top:0},800);
		});
	},
	showGallery:function(id){
		$('#overlay').show();
		$('#cargando').show();
		$.getJSON(window.diputado.appUrl+'/xhr/fotografias_fotos_galeria.php?id='+id,function(json){
			imgWidth = window.innerWidth -40;
			window.diputado.fotografias.slider.width=imgWidth;

			json.archivos.forEach(function(fot){
				img  = window.diputado.html.img(null,window.diputado.appUrl+'/fotografias/'+window.diputado.usrDir+'/'+fot.archivo,'style="height:400px;"');
				html = window.diputado.html.div(null,'<center>'+img+'</center>','style="width:'+imgWidth+'px;float:left;"');
				$('#fotos_slider_content').append(html);	
			});

			dsc  = window.diputado.html.h3(null,json.nombre+'<small>  ('+json.fecha+')</small>','class=""');
			dsc += window.diputado.html.p(null,json.descripcion);
			dsc += '<center>';
			dsc += window.diputado.html.a(null,'Cerrar','href="javascript: window.diputado.fotografias.hideGallery()" class="btn btn-success btn-medium"');
			dsc += '</center>';

			width = imgWidth * json.archivos.length;
			$('#fotos_slider_content').css('width',width+'px').css('height','400px').css('left','0px');
			$('#fotos_describe').html(dsc);
			$('div#fotos').show();
			$('#cargando').hide();
			$("html,body").animate({scrollTop:0},800);
		});
	},
	hideGallery:function(){
		$('#fotos_slider_content').html('');
		$('#fotos_describe').html('');
		$('div#fotos').hide();
		$('#overlay').hide();
		$("html,body").animate({scrollTop:0},800);
	},
	slider:{
		width:0,
		moveLeft:function(){
			left = parseInt($('#fotos_slider_content').css('left').replace('px','')) -this.width;
			max_left = $('#fotos_slider_content').width() - this.width;
			if(left < -(max_left)) left=0;
			$('#fotos_slider_content').animate({'left':left},800);
		},
		moveRight:function(){
			left = parseInt($('#fotos_slider_content').css('left').replace('px','')) +this.width;
			max_left = $('#fotos_slider_content').width() - this.width;
			if(left > 0) left= -(max_left);
			$('#fotos_slider_content').animate({'left':left},800);
		}
	}
}
window.diputado.videos={
	inicio:function(){
		this.videos();
	},
	videos:function(){
		$.getJSON(window.diputado.appUrl+'/xhr/videos_videos.php?id='+window.diputado.usrId,function(json){
			td  = window.diputado.html.td(null,'TITULO',null);
			td += window.diputado.html.td(null,'RESUMEN',null);
			td += window.diputado.html.td(null,'FECHA',null);
			td += window.diputado.html.td(null,'ACCIONES',null);
			tr  = window.diputado.html.tr(null,td,null);

			json.forEach(function(reg){
				td  = window.diputado.html.td(null,reg.nombre,null);
				td += window.diputado.html.td(null,reg.descripcion,null);
				td += window.diputado.html.td(null,reg.fecha,null);
				lk  = window.diputado.html.a(null,'Ver Galer&iacute;a','href="javascript: window.diputado.videos.showGallery('+reg.id+');" class="btn btn-default"');
				td += window.diputado.html.td(null,lk,null);
				tr += window.diputado.html.tr(null,td,null);
			});

			title = window.diputado.html.h3(null,'Galeria de Videos',null);
			title += window.diputado.html.hr(null);
			title += window.diputado.html.br();

			table = window.diputado.html.table(null,tr,'class="table table-striped table-bordered"');

			html  = title+table;

			$('section#diputado_tres_noticias').html(html);
			$('html,body').animate({top:0},800);
		});
	},
	showGallery:function(id){
		$('#overlay').show();
		$('#cargando').show();
		$.getJSON(window.diputado.appUrl+'/xhr/videos_videos_galeria.php?id='+id,function(json){
			vidWidth = window.innerWidth -40;
			window.diputado.videos.slider.width=vidWidth;

			json.archivos.forEach(function(vid){
				$.get(window.diputado.appUrl+'/videos/'+window.diputado.usrDir+'/'+vid.archivo+'.html',function(archivo){
					video = archivo.replace('width="100%" height="auto"','width="auto" height="400px"');
					html  = window.diputado.html.div(null,'<center>'+video+'</center>','style="width:'+vidWidth+'px;height:400px;float:left;"');
					//html = window.diputado.html.div(null,'<center>'+html+'</center>','style="display:block;width:'+vidWidth+'px;height:400px;float:left;position:absolute;top:0;"');
					$('#videos_slider_content').append(html);
				});
			});

			dsc  = window.diputado.html.h3(null,json.nombre+'<small> ('+json.fecha+')</small>','class=""');
			dsc += window.diputado.html.p(null,json.descripcion);
			dsc += '<center>';
			dsc += window.diputado.html.a(null,'Cerrar','href="javascript: window.diputado.videos.hideGallery()" class="btn btn-success btn-medium"');
			dsc += '</center>';

			width = vidWidth * json.archivos.length;
			$('#videos_slider_content').css('width',width+'px').css('left','0px');
			$('#videos_describe').html(dsc);
			$('#cargando').hide();
			$('div#videos').show();
			$("html,body").animate({scrollTop:0},800);
		});
	},
	hideGallery:function(){
		$('#videos_slider_content').html('');
		$('#videos_describe').html('');
		$('div#videos').hide();
		$('#overlay').hide();
		$("html,body").animate({scrollTop:0},800);
	},
	slider:{
		width:0,
		moveLeft:function(){
			left = parseInt($('#videos_slider_content').css('left').replace('px','')) -this.width;
			max_left = $('#videos_slider_content').width() - this.width;
			if(left < -(max_left)) left=0;
			$('#videos_slider_content').animate({'left':left},800);
		},
		moveRight:function(){
			left = parseInt($('#videos_slider_content').css('left').replace('px','')) +this.width;
			max_left = $('#videos_slider_content').width() - this.width;
			if(left > 0) left= -(max_left);
			$('#videos_slider_content').animate({'left':left},800);
		}
	}
}
window.diputado.contacto={
	reset:function(){$('#fcontacto').each(function(){this.reset();});},
	submit:function(){
		nombre = window.diputado.tools.sanitizeString($('input#cnombre').val());
		email  = window.diputado.tools.sanitizeEmail($('input#cemail').val());
		asunto = window.diputado.tools.sanitizeString($('input#casunto').val());
		mensaje= window.diputado.tools.sanitizeString($('textarea#cmensaje').val());
		prm = {'id':window.diputado.usrId,'nombre':nombre,'email':email,'asunto':asunto,'mensaje':mensaje};
		$.getJSON(window.diputado.appUrl+'/xhr/email.php',prm,function(json){
			if(json.event){
				alert(json.msg);
				$('input#cnombre').val('');
				$('input#cemail').val('');
				$('input#casunto').val('');
				$('textarea#cmensaje').val('');
			} else {
				alert(json.msg);
			}
		});
	}
}
window.diputado.tools={
	sanitizeInt:function(int){
		regexp  = new RegExp('^\\d+$','igm');
		intr = int.match(regexp);
		if(intr === null){
			return null;
		} else {
			return intr[0];
		}
	},
	sanitizeString:function(str){
		rstring = str;
		return rstring;
	},
	sanitizeEmail:function(email){
		remail = email;
		return remail;
	}
}