<?php

# NOMBRE: html.
# UTILIDAD: Clase para la abstracción de un documento html solamente en versión 5.
# Cualquier adaptación a una versión anterior de html únicamente para que se complatible con 
# Internet Explorer se deberá utilizar alguna librería javascript independiente de esta clase.
# La clase también contine helpers para incluir tag html.
# AUTOR: Orlando Sergio Dominguez
# EMAIL: serggio@msn.com
# COPYRIGHT: Legislatura de Jujuy.

class html extends main{

	public $lang     = 'en';
	public $charset  = 'uft-8';
	public $title    = 'Una pagina web';

	private $tmpHead = null;
	private $tmpBody = null;
	private $htmlDoc = null;

	# Retorna un tag DOCTYPE, un tag html abierto, y un tag head.
	private function createHead(){
		$head  = '<head>';
		$head .= '<title>'.$this->title.'</title>';
		$head .= '<meta charset="'.$this->charset.'" />';
		$head .= $this->tmpHead;
		$head .= '</head>';
		return $head;
	}

	# Retorna un tag body con tag de cierre html.
	private function createBody(){
		$body  = '<body>';
		$body .= '<noscript>Su navegador no tiene <code>javascript</code> habilitado. Utilice la última version de Firefox o Google Chrome.</noscript>'; 
		$body .= $this->tmpBody;
		$body .= '</body>';
		return $body;
	}

	# Retorno de parametros.
	private function getParams($str=null){
		$prm = null;
		if(strlen($str)){
			$A = explode(';',$str);
			foreach($A as $a){
				$prm .= ' '.str_replace(':','=',$a);
			}
		}
		return $prm;
	}

	# Incluye un tag script para un archivo remoto.
	public function setJs($src=null){
		if($src){
			$this->tmpHead .= '<script type="text/javascript" src="'.$src.'"></script>';
		}
	}

	# Incluye un tag script para un archivo remoto.
	public function setScript($code=null){
		if($code){
			$this->tmpHead .= '<script>'.$code.'</script>';
		}
	}

	# Incluye un tag link para enlazar un archivo css remoto.
	public function setCss($href=null)
	{
		if($href){
			$this->tmpHead .= '<link rel="stylesheet" type="text/css" href="'.$href.'" />';
		}
	}

	# Incluye un tag style para enlazar un archivo css remoto.
	public function setStyle($code=null)
	{
		if($code){
			$this->tmpHead .= '<style>'.$code.'</style>';
		}
	}

	# Incluye un icono.
	public function setIcon($href=null){
		if($href){
			$this->tmpHead .= '<link rel="icon" href="'.$href.'" />';
			$this->tmpHead .= '<link rel="shortcut icon" href="'.$href.'" />';
			$this->tmpHead .= '<link rel="apple-touch-icon" href="'.$href.'" />';			
		}
	}

	# Incluye los tag nacesarios para evitar el cacheo del navegador.
	public function setNotCache()
	{
		$this->tmpHead .= '<meta http-equiv="Expires" content="0" />';
		$this->tmpHead .= '<meta http-equiv="Last-Modified" content="0" />';
		$this->tmpHead .= '<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate" />';
		$this->tmpHead .= '<meta http-equiv="Pragma" content="no-cache" />';
	}

	# Almacena en htmlDoc un documento html5.
	private function createHtmlDoc(){
		$this->htmlDoc  = '<!DOCTYPE html>';
		$this->htmlDoc .= '<html lang="'.$this->lang.'">';
		$this->htmlDoc .= $this->createHead();
		$this->htmlDoc .= $this->createBody();
		$this->htmlDoc .= '</html>';
	}
	
	# Retorna un documento html5.
	public function getHtmlDoc(){
		$this->createHtmlDoc();
		return $this->htmlDoc;
	}

	# Inserta un tag header, parametros: ('contenido html o null','id_del_tag o false').
	public function header($html=null,$id=false,$params=null){
		$header  = '<header';
		if($id): $header .= ' id="'.$id.'"';
		endif;
		if(strlen($params)): $header .= $this->getParams($params);
		endif;
		$header .= '>';
		$header .= $html;
		$header .= '</header>';
		return $header;
	}

	# Inserta un tag footer, parámetros: ('Contenido html o null','id_del_tag o false').
	public function footer($html=null,$id=false,$params=null){
		$footer  = '<footer';
		if($id): $footer .= ' id="'.$id.'"';
		endif;
		if(strlen($params)): $footer .= $this->getParams($params);
		endif;
		$footer .= '>';
		$footer .= $html;
		$footer .= '</footer>';
		return $footer;
	}

	# Inserta un tag nav, parámetros: ('Contenido html o null','id_del_tag o false').
	public function nav($html=null,$id=false,$params=null){
		$nav  = '<nav';
		if($id): $nav .= ' id="'.$id.'"';
		endif;
		if(strlen($params)): $nav .= $this->getParams($params);
		endif;
		$nav .= '>';
		$nav .= $html;
		$nav .= '</nav>';
		return $nav;	
	}

	# Inserta un tag aside, parámetros: ('Contenido html o null','id_del_tag o false').
	public function aside($html=null,$id=false,$params=null){
		$aside  = '<aside';
		if($id): $aside .= ' id="'.$id.'"';
		endif;
		if(strlen($params)): $aside .= $this->getParams($params);
		endif;
		$aside .= '>';
		$aside .= $html;
		$aside .= '</aside>';
		return $aside;
	}

	# Inserta un tag label, parámetros: ('Contenido html o null','id_del_tag o false').
	public function label($html=null,$id=false,$params=null){
		$label  = '<label';
		if($id): $label .= ' id="'.$id.'"';
		endif;
		if(strlen($params)): $label .= $this->getParams($params);
		endif;
		$label .= '>';
		$label .= $html;
		$label .= '</label>';
		return $label;
	}

	# Inserta un tag article, parámetros: ('Contenido html o null','id_del_tag o false').
	public function article($html=null,$id=false,$params=null){
		$article  = '<article';
		if($id): $article .= ' id="'.$id.'"';
		endif;
		if(strlen($params)): $article .= $this->getParams($params);
		endif;
		$article .= '>';
		$article .= $html;
		$article .= '</article>';
		return $article;
	}

	# Inserta un tag section, parámetros: ('Contenido html o null','id_del_tag o false').
	public function section($html=null,$id=false,$params=null){
		$section  = '<section';
		if($id): $section .= ' id="'.$id.'"';
		endif;
		if(strlen($params)): $section .= $this->getParams($params);
		endif;
		$section .= '>';
		$section .= $html;
		$section .= '</section>';
		return $section;
	}

	# Inserta un tag p, parámetros: ('Contenido html o null','id_del_tag o false').
	public function p($html=null,$id=false,$params=null){
		$p  = '<p';
		if($id): $p .= ' id="'.$id.'"';
		endif;
		if(strlen($params)): $p .= $this->getParams($params);
		endif;
		$p .= '>';
		$p .= $html;
		$p .= '</p>';
		return $p;
	}

	# Inserta un tag div, parámetros: ('Contenido html o null','id_del_tag o false').
	public function div($html=null,$id=false,$params=null){
		$div  = '<div';
		if($id): $div .= ' id="'.$id.'"';
		endif;
		if(strlen($params)): $div .= $this->getParams($params);
		endif;
		$div .= '>';
		$div .= $html;
		$div .= '</div>';
		return $div;
	}

	# Inserta un tag font, parámetros: ('Contenido html o null','id_del_tag o false').
	public function font($html=null,$id=false,$params=null){
		$font  = '<font';
		if($id): $font .= ' id="'.$id.'"';
		endif;
		if(strlen($params)): $font .= $this->getParams($params);
		endif;
		$font .= '>';
		$font .= $html;
		$font .= '</font>';
		return $font;
	}

	# Inserta un tag ul, parámetros: ('Contenido html o null','id_del_tag o false').
	public function ul($html=null,$id=false,$params=null){
		$ul  = '<ul';
		if($id): $ul .= ' id="'.$id.'"';
		endif;
		if(strlen($params)): $ul .= $this->getParams($params);
		endif;
		$ul .= '>';
		$ul .= $html;
		$ul .= '</ul>';
		return $ul;
	}

	# Inserta un tag ol, parámetros: ('Contenido html o noll','id_del_tag o false').
	public function ol($html=null,$id=false,$params=null){
		$ol  = '<ol';
		if($id): $ol .= ' id="'.$id.'"';
		endif;
		if(strlen($params)): $ol .= $this->getParams($params);
		endif;
		$ol .= '>';
		$ol .= $html;
		$ol .= '</ol>';
		return $ol;
	}

	# Inserta un tag ol, parámetros: ('Contenido html o noll','id_del_tag o false').
	public function li($html=null,$id=false,$params=null){
		$li  = '<li';
		if($id): $li .= ' id="'.$id.'"';
		endif;
		if(strlen($params)): $li .= $this->getParams($params);
		endif;
		$li .= '>';
		$li .= $html;
		$li .= '</li>';
		return $li;
	}	

	# Inserta un tag a, parámetros: ('Contenido html o null','href del enlace','id_del_tag o false','target o false').
	public function a($html=null,$href=false,$target=false,$id=false,$params=null){
		$a  = '<a';
		if($id): $a .= ' id="'.$id.'"';
		endif;
		if($href): $a .= ' href="'.$href.'"';
		endif;
		if($target): $a .= ' target="'.$target.'"';
		endif;
		if(strlen($params)): $a .= $this->getParams($params);
		endif;
		$a .= '>';
		$a .= $html;
		$a .= '</a>';
		return $a;
	}

	# Inserta un tag img, parámetros: ('url del recurso','id_del_tag o false').
	public function img($src=null,$id=false,$params=null){
		$img  = '<img';
		if($id): $img .= ' id="'.$id.'"';
		endif;
		if(strlen($params)): $img .= $this->getParams($params);
		endif;
		$img .= ' src="'.$src.'"';
		$img .= ' />';
		return $img;
	}

	# Inserta un tag input, parámetros: ('typo de recurso','id_del_tag o false','valor del recurso o false').
	public function input($type=null,$id=false,$value=false,$params=null){
		$input  = '<input';
		$input .= ' type="'.$type.'"';
		if($id): $input .= ' id="'.$id.'" name="'.$id.'"';
		endif;
		if($value): $input .= ' value="'.$value.'"';
		endif;
		if(strlen($params)): $input .= $this->getParams($params);
		endif;
		$input .= ' />';
		return $input;
	}

	# Inserta un tag select, parámetros: ('options del recurso','id_del_tag o false').
	public function select($options=null,$id=false,$params=null){
		$select  = '<select';
		if($id): $select .= ' id="'.$id.'" name="'.$id.'"';
		endif;
		if(strlen($params)): $select .= $this->getParams($params);
		endif;
		$select .= '>';
		$select .= $options;
		$select .= '</select>';
		return $select;
	}

	# Inserta un tag option, parámetros: ('text del recurso','value o false').
	public function option($text=null,$value=false,$params=null){
		$option  = '<option';
		if($value): $option .= ' value="'.$value.'"';
		endif;
		if(strlen($params)): $option .= $this->getParams($params);
		endif;
		$option .= '>';
		$option .= $text;
		$option .= '</option>';
		return $option;
	}

	# Inserta un tag textarea, parámetros: ('id del recurso','value o false').
	public function textarea($id=null,$value=false,$params=null){
		$textarea  = '<textarea id="'.$id.'" name="'.$id.'"';
		if(strlen($params)): $textarea .= $this->getParams($params);
		endif;
		$textarea .= '>';
		$textarea .= $value;
		$textarea .= '</textarea>';
		return $textarea;
	}

	# Inserta un tag h1, parámetros: ('id del recurso','value o false').
	public function h1($value=false,$id=null,$params=null){
		$h1  = '<h1 id="'.$id.'"';
		if(strlen($params)): $h1 .= $this->getParams($params);
		endif;
		$h1 .= '>';
		$h1 .= $value;
		$h1 .= '</h1>';
		return $h1;
	}

	# Inserta un tag h2, parámetros: ('id del recurso','value o false').
	public function h2($value=false,$id=null,$params=null){
		$h2  = '<h2 id="'.$id.'"';
		if(strlen($params)): $h2 .= $this->getParams($params);
		endif;
		$h2 .= '>';
		$h2 .= $value;
		$h2 .= '</h2>';
		return $h2;
	}

	# Inserta un tag h3, parámetros: ('id del recurso','value o false').
	public function h3($value=false,$id=null,$params=null){
		$h3  = '<h3 id="'.$id.'"';
		if(strlen($params)): $h3 .= $this->getParams($params);
		endif;
		$h3 .= '>';
		$h3 .= $value;
		$h3 .= '</h3>';
		return $h3;
	}

	# Inserta un tag h4, parámetros: ('id del recurso','value o false').
	public function h4($value=false,$id=null,$params=null){
		$h4  = '<h4 id="'.$id.'"';
		if(strlen($params)): $h4 .= $this->getParams($params);
		endif;
		$h4 .= '>';
		$h4 .= $value;
		$h4 .= '</h4>';
		return $h4;
	}

	# Inserta un tag h5, parámetros: ('id del recurso','value o false').
	public function h5($value=false,$id=null,$params=null){
		$h5  = '<h5 id="'.$id.'"';
		if(strlen($params)): $h5 .= $this->getParams($params);
		endif;
		$h5 .= '>';
		$h5 .= $value;
		$h5 .= '</h5>';
		return $h5;
	}

	# Inserta un tag h6, parámetros: ('id del recurso','value o false').
	public function h6($value=false,$id=null,$params=null){
		$h6  = '<h6 id="'.$id.'"';
		if(strlen($params)): $h6 .= $this->getParams($params);
		endif;
		$h6 .= '>';
		$h6 .= $value;
		$h6 .= '</h6>';
		return $h6;
	}

	# Inserta un tag canvas, parámetros: ('id del recurso','parametros separados por ;').
	public function canvas($id=null,$params=null){
		$canvas  = '<canvas id="'.$id.'"';
		if(strlen($params)): $canvas .= $this->getParams($params);
		endif;
		$canvas .= '>';
		$canvas .= 'Su navegador no soporta el objeto <code>canvas</code>. Utilice la última version de Firefox o Google Chrome.';
		$canvas .= '</canvas>';
		return $canvas;
	}

	# Inserta un tag svg, parámetros: ('id del recurso','parametros separados por ;').
	public function svg($id=null,$params=null){
		$svg  = '<svg id="'.$id.'"';
		if(strlen($params)): $svg .= $this->getParams($params);
		endif;
		$svg .= '>';
		$svg .= 'Su navegador no soporta el objeto <code>svg</code>. Utilice la última version de Firefox o Google Chrome.';
		$svg .= '</svg>';
		return $svg;
	}

	# Inserta un tag br, parámetros: ('url del recurso','id_del_tag o false').
	public function br(){
		$br  = '<br />';
		return $br;
	}

	# Inserta un tag hr, parámetros: ('url del recurso','id_del_tag o false').
	public function hr(){
		$hr  = '<hr />';
		return $hr;
	}

	# Hack html5 in older version of IE.
	public function html54IE(){
		$this->tmpHead .= '<!--[if lt IE 9] >';
		$this->tmpHead .= '<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>';
		$this->tmpHead .= '< ![endif]-->';
	}

	# Interta un tag el el curpo del documento html, parámetros ('tag a insertar','contenido html','id_del_tag');
	public function tag($tag=null,$html=null,$id=false,$params=null){
		$arrH = array('h1','h2','h3','h4','h5','h6','textarea','select','img','li','ol','ul','font','div','p','section','article','aside','nav','footer','header');
		$arr5 = array('canvas','svg');
		if(method_exists($this,$tag)){
			if(in_array($tag,$arrH)) $this->tmpBody .= $this->$tag($html,$id,$params);
			if(in_array($tag,$arr5)){
				$id = $html;
				$this->tmpBody .= $this->$tag($id,$params);
			}
			if($tag=='a')  $this->tmpBody .= $this->a($html,$id,null,null,null,$params);
			//if($tag=='input') $this->tmpBody .= $this->input();
			if($tag=='br') $this->tmpBody .= $this->br();
			if($tag=='hr') $this->tmpBody .= $this->hr();
		}
	}

}