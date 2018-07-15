<!DOCTYPE html>
<HTML>
<HEAD>
 <meta charset="UTF-8">
 <title>Music Score Writer by Chris B</title>
 <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Merienda" />
 <link rel="stylesheet" href="css/css_reset.css" />
 <meta name="keywords"    content="Svg JavaScript , Music scores, Music score editor, unicode in svg">
 <meta name="description" content="Tool for writing easily beautiful music scores">
 <meta name="author"      content="Chris B">
 
<style>
body,html{width:100%;height:100%;}
@font-face {
    font-family: 'Bravura';
    font-family: 'Bravura',Sans-Serif;
	
    src: url("bravura/woff/Bravura.woff"),
	     url("bravura/otf/Bravura.otf"),
	     url("bravura/eot/Bravura.eot"),
		 url("bravura/ttf_conv/Bravura.ttf");
	
}


circle { 
  stroke-width       : 1;
  stroke             : #c00;
  fill               : rgba(255,255,255,0.1);
}
g circle {	
  stroke-width       : 0;
  stroke             : #c00;
  fill               : rgba(255,255,255,0.1);
}
circle:hover {
  fill               : #c00;
  cursor             : pointer;
}
g circle:hover {	

  cursor              : auto;
  fill                : rgba(255,255,255,0.1);
}

body {
     
  background-color    : rgb(14, 19, 31);
  font-family         : 'Bravura';
}

#wrapper {
  min-height          : 900px;
  background-color    : rgba(220, 252, 252, 0.4);
  text-align          : center;
  display             : inline-block;
  width               : 100%;
  height              : 100%;   
  background-color    : rgb(14, 19, 31);
}

#title_wrapper {     
  background-image    : url('notes.png');
  background-repeat   : repeat-x;
  min-height          : 130px;
  height              : auto;
}

#title, #subtitle { 
  margin              : auto;
  text-align          : center;
  padding-top         : 45px;
  color               : #ffffff;
  font-family         : Merienda;
  font-size           : 35px;
  color               : #E7F3F6;
  text-shadow         : 10px 10px 60px #32BDDC;    
  max-width           : 1000px;
}

#title { 
  padding-top         : 90px; 

}

#subtitle {
	
  padding-top         : 35px;
}

#musicpad {
  overflow             : hidden;
  box-shadow           : none;
  background-color     : white;
  width                : 100%;
  max-width            : 1000px;
  height               : 100%;
  position             : static;
  padding              : 0px;
  margin               : auto;
  font-family          : arial;
  font-size            : 15px;
  text-align           : center;
  margin-top           : 50px;
}

#musicpad svg {
  -webkit-touch-callout: none;
  -webkit-user-select: none;
  -khtml-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
   user-select: none;
}

#menuX {
  background-color: white;
  padding-top: 10px;
  max-width: 80%;
  text-align: center;    
  margin: auto;
  line-height: 30px;
  margin-top: 30px;
}	
#menuX  span:hover {
  text-decoration: underline;
}



#musicpad svg.musicSymbol {
  fill: black;
}

#musicpad svg.musicSymbol text:hover {
    /*fill               : blue;*/
  cursor: move;
}
#musicpad .edit_area_svg .musicSymbol text:hover{	
  cursor: pointer;
}
#musicpad .thrash_bin:hover{
  fill: red;
}

@media print {
  #title,#subtitle,#title_wrapper,#menuX,#menuX span,.palettes,.thrash_bin {
    display: none;
	fill: rgba(255,255,255,0);
  }
}


</style>

<script>
/*Copyright ©  All Rights Reserved. Designed and fully created By Chris B*/

function MusicPad(targetEl, MenuWrapper) {

  'use strict';
	   
  var self = this;

  this.Helpers = self.Helpers();  
	
  this.SVGViewBox = { 
    X: 0,
    Y: 0,
    W: 1000,
    H: 1000 
  };
  
  this.TargetSymbol = { 
	el: "",
	startPos: { x: 0, y: 0 }, 
	scalable: false,
	movable: false,
	beamObje: null,
	dragPoint: { x : 0 , y : 0 },
        Reset: function () { 
	  var k = this;
	  k.el = "",
	  k.startPos.x = k.startPos.y = k.dragPoint.x = k.dragPoint.y = 0,
	  k.scalable = k.movable = false,
	  k.beamObj = null; 
	}
  };
  
  this.MainSvgPad = {   
    SvgPoint: '',
    MouseCoord: { x: 0, y: 0 },
    El: '',  
    El_tag: 'svg',	
    Attributes: [ 
	  { prop: 'style', val: "/*width:100%;height:100%;*/display: block;position: absolute;top: 0;left: 0;width: 100%;" },
	  { prop: 'viewBox', val: self.SVGViewBox.X + ' ' + self.SVGViewBox.Y + ' ' + self.SVGViewBox.W + ' ' + self.SVGViewBox.H }
    ],				 
    Events: {
      mouseleave: function(evt) { 
        self.ResetSymbol(evt); 
      },
      mousemove: function(evt) { 
        evt.preventDefault();
        self.MainSvgPad.MouseCoord = self.cursorPoint(evt,self.MainSvgPad.El); 
        self.MoveSvgIn();
        self.ScaleSvg();
      },
      mouseup: function(evt) { 
        self.ResetSymbol(evt); 
      }
    },	
    Children: {    
	  EditArea: { 
	    el: '', 
		El_tag: 'svg', 
		Attributes: [ 
		  { prop: 'class', val: 'edit_area_svg' },
		  { prop: 'x', val: self.SVGViewBox.X },
 		  { prop: 'y', val: self.SVGViewBox.Y },
		  { prop: 'width', val: self.SVGViewBox.W },
		  { prop: 'height', val: self.SVGViewBox.H },
		  { prop: 'viewBox', val: self.SVGViewBox.X + ' ' + self.SVGViewBox.Y + ' ' + self.SVGViewBox.W + ' ' + self.SVGViewBox.H }																										
	    ] 
	  }, 
	  Palette: { 
	    el: '', 
		El_tag: 'svg', 
		Attributes: [ 
		  { prop: 'class', val: 'palettes' },
		  { prop: 'x', val: self.SVGViewBox.X },
 		  { prop: 'y', val: self.SVGViewBox.Y },
		  { prop: 'width', val: self.SVGViewBox.W },
		  { prop: 'height', val: self.SVGViewBox.H },
		  { prop: 'viewBox', val: self.SVGViewBox.X + ' ' + self.SVGViewBox.Y + ' ' + self.SVGViewBox.W + ' ' + self.SVGViewBox.H }																										
		] 
	   }, 
	   Stuffs: { 
	     el: '', 
		El_tag: 'svg', 
		Attributes: [ 
		  { prop: 'class', val: 'stuffs' },
		  { prop: 'x', val: self.SVGViewBox.X },
 		  { prop: 'y', val: self.SVGViewBox.Y },
		  { prop: 'width', val: self.SVGViewBox.W },
		  { prop: 'height', val: self.SVGViewBox.H },
		  { prop: 'viewBox', val: self.SVGViewBox.X + ' ' + self.SVGViewBox.Y + ' ' + self.SVGViewBox.W + ' ' + self.SVGViewBox.H }																									
		] 
	  }, 
	  Thrash: { 
	    el: '', 
	    El_tag: 'path', 
	    Attributes: [ 
              { prop: 'd', val: "M902.00,481 C902.00,481 901.50,481 901.5,482 C901.5,483 902.00,483 902.00,483 L919.99,483 C919.99,483 920.5,483 920.5,482 C920.5,481 919.99,481 919.99,481 L902.00,481 L902.00,481 Z M902.47,484 L919.47,484 L917.47,502 L904.47,502 L902.47,484 Z M909.47,480 C908.47,480 908.47,481 908.47,481 L913.47,481 C913.47,481 913.47,480 912.47,480 L909.47,480 L909.47,480 Z M907.47,500 L908.47,500 L907.47,483.96 L906.47,483.96 L907.47,500 Z M914.47,483.96 L913.47,500 L914.47,500 L915.47,483.96 L914.47,483.96 L914.47,483.96 Z M910.47,483.96 L910.47,500 L911.47,500 L911.47,483.96 L910.47,483.96 L910.47,483.96 Z"},
              { prop: 'class', val: 'thrash_bin' },
	      { prop: 'transform', val: 'translate(60, -479)'  }
	    ],
          Events: {
          mouseover: function(evt) { 
		    if (self.TargetSymbol.beamObje != null) {
		      self.BeamDestroy(self.TargetSymbol.beamObje);
			  self.TargetSymbol.beamObje = null;
			  return;
		    }
            var m = self.TargetSymbol.el; 
		    if (m != '' && m.parentNode === self.MainSvgPad.Children.EditArea.el) {
		      self.getSymbol(self.TargetSymbol.el).DeleteIt();
			  self.TargetSymbol.Reset();
		    } 
		  }
	    }				 
	} 
    },	
    Create: function(targetEl) {								  
	  var viewBox = self.SVGViewBox;
	  var child, a, wa;  
	  self.MainSvgPad.El = self.Helpers.createSvgEl({ 
	    svg_type: self.MainSvgPad.El_tag, 
		attribute_Arr: self.MainSvgPad.Attributes
	  });
	  targetEl.appendChild(self.MainSvgPad.El);
	  
      for (a in self.MainSvgPad.Children) {														  
	      child = self.Helpers.createSvgEl({
 		    svg_type: self.MainSvgPad.Children[a].El_tag , 
		    attribute_Arr: self.MainSvgPad.Children[a].Attributes
	      }); 
																													 
		  self.MainSvgPad.El.appendChild(child);
		  self.MainSvgPad.Children[a].el = child;
																			   
		  if (self.MainSvgPad.Children[a].hasOwnProperty('Events')) {
		    for (wa in self.MainSvgPad.Children[a].Events) {
		      child.addEventListener(wa, self.MainSvgPad.Children[a].Events[wa], false);
		    }
		  }
	  }
	  self.MainSvgPad.SvgPoint  = self.MainSvgPad.El.createSVGPoint();								                                         
	},	
	SetEvents: function () {								  
	  self.MainSvgPad.El.addEventListener('mousemove', this.Events.mousemove, false );
	  self.MainSvgPad.El.addEventListener('mouseup', this.Events.mouseup, false );
	  self.MainSvgPad.El.addEventListener('mouseleave', this.Events.mouseleave, false );
	}                                                     
																		                		                            
		
  };
  
  this.Palette = {   
    category: {
	  notes: [ 
	    { name: 'double_whole'       , svg: { vb: { w: 23, h: 20, x: 0, y: 0 } } , text: { x: 0, y: 10, unicode: '&#x1D15C;' }                           }, 
	    { name: 'whole'              , svg: { vb: { w: 17, h: 18, x: 0, y: 0 } } , text: { x: 0, y: 10, unicode: '&#x1D15D;' }                           }, 
	    { name: 'half'               , svg: { vb: { w: 17, h: 40, x: 0, y: 0 } } , text: { x: 3, y: 33, unicode: '&#x1D15E;' }                           }, 
	    { name: 'quarter'            , svg: { vb: { w: 17, h: 40, x: 0, y: 0 } } , text: { x: 3, y: 33, unicode: '&#x1D15F;' }                           }, 
	    { name: 'eighth'             , svg: { vb: { w: 25, h: 40, x: 0, y: 0 } } , text: { x: 3, y: 33, unicode: '&#x1D160;' }                           }, 
	    { name: 'sixteenth'          , svg: { vb: { w: 25, h: 40, x: 0, y: 0 } } , text: { x: 3, y: 33, unicode: '&#x1D161;' }                           }, 
	    { name: 'thirty_2'           , svg: { vb: { w: 25, h: 45, x: 0, y: 0 } } , text: { x: 3, y: 37, unicode: '&#x1D162;' }                           }, 
	    { name: 'sixty_4'            , svg: { vb: { w: 25, h: 50, x: 0, y: 0 } } , text: { x: 3, y: 42, unicode: '&#x1D163;' }                           }, 
	    { name: 'rev_half'           , svg: { vb: { w: 17, h: 40, x: 0, y: 0 } } , text: { x: 3, y: 33, unicode: '&#x1D15E;', tr: 'rotate(180 8.5 20)'   } }, 
	    { name: 'rev_quarter'        , svg: { vb: { w: 17, h: 40, x: 0, y: 0 } } , text: { x: 3, y: 33, unicode: '&#x1D15F;', tr: 'rotate(180 8.5 20)'   } }, 
	    { name: 'rev_eighth'         , svg: { vb: { w: 24, h: 40, x: 0, y: 0 } } , text: { x: 3, y: 33, unicode: '&#x1D160;', tr: 'rotate(180 12 20)'    } }, 
	    { name: 'rev_sixteenth'      , svg: { vb: { w: 25, h: 40, x: 0, y: 0 } } , text: { x: 3, y: 33, unicode: '&#x1D161;', tr: 'rotate(180 12.5 20)'  } }, 
	    { name: 'rev_thirty_2'       , svg: { vb: { w: 25, h: 45, x: 0, y: 0 } } , text: { x: 3, y: 37, unicode: '&#x1D162;', tr: 'rotate(180 12.5 22.5)'} }, 
	    { name: 'rev_sixty_4'        , svg: { vb: { w: 25, h: 50, x: 0, y: 0 } } , text: { x: 3, y: 42, unicode: '&#x1D163;', tr: 'rotate(180 12.5 25)'  } }
	  ],
	  accidentals: [ 
	    { name: 'double_sharp'       , svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 7.5, unicode: '&#x1D12A;' } } , 
	    { name: 'double_flat'        , svg: { vb: { w: 20, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 18 , unicode: '&#x1D12B;' } } , 
	    { name: 'flat_up'            , svg: { vb: { w: 15, h: 26, x: 0, y: 0 } } , text: { x: 3, y: 19 , unicode: '&#x1D12C;' } } , 
	    { name: 'flat_down'          , svg: { vb: { w: 15, h: 27, x: 0, y: 0 } } , text: { x: 3, y: 14 , unicode: '&#x1D12D;' } } , 
	    { name: 'natural_up'         , svg: { vb: { w: 15, h: 30, x: 0, y: 0 } } , text: { x: 3, y: 18 , unicode: '&#x1D12E;' } } ,
	    { name: 'natural_down'       , svg: { vb: { w: 15, h: 30, x: 0, y: 0 } } , text: { x: 3, y: 11 , unicode: '&#x1D12F;' } } , 
	    { name: 'sharp_up'           , svg: { vb: { w: 15, h: 30, x: 0, y: 0 } } , text: { x: 3, y: 18 , unicode: '&#x1D130;' } } , 
	    { name: 'sharp_down'         , svg: { vb: { w: 15, h: 30, x: 0, y: 0 } } , text: { x: 3, y: 11 , unicode: '&#x1D131;' } } , 
	    { name: 'quarter_sharp'      , svg: { vb: { w: 20, h: 35, x: 0, y: 0 } } , text: { x: 3, y: 22 , unicode: '&#x1D132;' } } ,
		{ name: 'quarter_flat'       , svg: { vb: { w: 20, h: 30, x: 0, y: 0 } } , text: { x: 3, y: 23 , unicode: '&#x1D133;' } } 
	  ],
	  dynamics: [ 
		{ name: 'rinforzando'        , svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 12, unicode: '&#x1D18C;'                  } } , 
		{ name: 'subito'             , svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 12, unicode: '&#x1D18D;'                  } } , 
		{ name: 'zed'                , svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 12, unicode: '&#x1D18E;'                  } } , 
		{ name: 'piano'              , svg: { vb: { w: 20, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 12, unicode: '&#x1D18F;'                  } } , 
		{ name: 'pianissimo'         , svg: { vb: { w: 32, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 12, unicode: '&#x1D18F;&#x1D18F;'         } } , 
		{ name: 'pianississimo'      , svg: { vb: { w: 44, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 12, unicode: '&#x1D18F;&#x1D18F;&#x1D18F;'} } , 
		{ name: 'mezzo_piano'        , svg: { vb: { w: 32, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 12, unicode: '&#x1D190;&#x1D18F;'         } } , 
		{ name: 'mezzo'              , svg: { vb: { w: 20, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 12, unicode: '&#x1D190;'                  } } ,                
		{ name: 'mezzo_forte'        , svg: { vb: { w: 32, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 18, unicode: '&#x1D190;&#x1D191;'         } } , 
		{ name: 'forte'              , svg: { vb: { w: 20, h: 25, x: 0, y: 0 } } , text: { x: 6, y: 18, unicode: '&#x1D191;'                  } } , 
		{ name: 'fortissimo'         , svg: { vb: { w: 35, h: 25, x: 0, y: 0 } } , text: { x: 6, y: 18, unicode: '&#x1D191;&#x1D191;'         } } , 
		{ name: 'fortississimo'      , svg: { vb: { w: 48, h: 25, x: 0, y: 0 } } , text: { x: 6, y: 18, unicode: '&#x1D191;&#x1D191;&#x1D191;'} } , 
		{ name: 'crescendo'          , svg: { vb: { w: 28, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 16, unicode: '&#x1D192;'                  } } , 
		{ name: 'decrescendo'        , svg: { vb: { w: 28, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 16, unicode: '&#x1D193;'                  } } 
	  ],
	  ornaments: [                                                           
		{ name: 'grace'              , svg: { vb: { w: 17, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 20, unicode: '&#x1D195;' } } , 
		{ name: 'grace_slash'        , svg: { vb: { w: 17, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 20, unicode: '&#x1D194;' } } , 
		{ name: 'trill'              , svg: { vb: { w: 25, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 20, unicode: '&#x1D196;' } } , 
		{ name: 'turn'               , svg: { vb: { w: 25, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 15, unicode: '&#x1D197;' } } , 
		{ name: 'turn_invert'        , svg: { vb: { w: 25, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 15, unicode: '&#x1D198;' } } , 
		{ name: 'turn_slash'         , svg: { vb: { w: 25, h: 22, x: 0, y: 0 } } , text: { x: 3, y: 16, unicode: '&#x1D199;' } } , 
		{ name: 'turn_up'            , svg: { vb: { w: 17, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 20, unicode: '&#x1D19A;' } } , 
		{ name: 'stroke_one'         , svg: { vb: { w: 15, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 20, unicode: '&#x1D19B;' } } , 
		{ name: 'stroke_two'         , svg: { vb: { w: 18, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 16, unicode: '&#x1D19C;' } } ,
		{ name: 'stroke_three'       , svg: { vb: { w: 23, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 16, unicode: '&#x1D19D;' } } , 
		{ name: 'stroke_six'         , svg: { vb: { w: 23, h: 20, x: 0, y: 0 } } , text: { x: 15, y: 15 , unicode: '&#x1D19E;' } } 
	  ],
	  articulation: [ 
		{ name: 'accent'             , svg: { vb: { w: 20, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 15, unicode: '&#x1D17B;' } } , 
		{ name: 'staccato'           , svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 5, y: 10, unicode: '&#x1D17C;' } } , 
		{ name: 'tenuto'             , svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 8 , unicode: '&#x1D17D;' } } ,
		{ name: 'staccatissimo'      , svg: { vb: { w: 13, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 14, unicode: '&#x1D17E;' } } , 
		{ name: 'marcato'            , svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 12, unicode: '&#x1D17F;' } } ,
		{ name: 'marcato_staccato'   , svg: { vb: { w: 15, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 20, unicode: '&#x1D180;' } } ,
		{ name: 'accent_staccato'    , svg: { vb: { w: 20, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 20, unicode: '&#x1D181;' } } ,
		{ name: 'loure'              , svg: { vb: { w: 20, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 17, unicode: '&#x1D182;' } } ,
		{ name: 'arpeggiato_up'      , svg: { vb: { w: 16, h: 55, x: 0, y: 0 } } , text: { x: 3, y: 53, unicode: '&#x1D183;' } } ,
		{ name: 'arpeggiato_down'    , svg: { vb: { w: 16, h: 55, x: 0, y: 0 } } , text: { x: 3, y: 55, unicode: '&#x1D184;' } } ,
		{ name: 'doit'               , svg: { vb: { w: 20, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 15, unicode: '&#x1D185;' } } , 
		{ name: 'rip'                , svg: { vb: { w: 18, h: 18, x: 0, y: 0 } } , text: { x: 3, y: 15, unicode: '&#x1D186;' } } ,
		{ name: 'flip'               , svg: { vb: { w: 18, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 15, unicode: '&#x1D187;' } } , 
		{ name: 'smear'              , svg: { vb: { w: 20, h: 18, x: 0, y: 0 } } , text: { x: 3, y: 12, unicode: '&#x1D188;' } } ,
		{ name: 'bend'               , svg: { vb: { w: 22, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 15, unicode: '&#x1D189;' } } ,
		{ name: 'double_tongue'      , svg: { vb: { w: 22, h: 22, x: 0, y: 0 } } , text: { x: 3, y: 15, unicode: '&#x1D18A;' } } , 
		{ name: 'triple_tongue'      , svg: { vb: { w: 25, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 15, unicode: '&#x1D18B;' } }  
	  ],
	  lines: [
        { name: 'single_bar'         , svg: { vb: { w: 10, h: 45, x: 0, y: 0 } } , text: { x: 3, y: 40, unicode: '&#x1D100;' }  } , 
		{ name: 'double_bar'         , svg: { vb: { w: 13, h: 45, x: 0, y: 0 } } , text: { x: 3, y: 40, unicode: '&#x1D101;' }  } , 
		{ name: 'final_bar'          , svg: { vb: { w: 15, h: 45, x: 0, y: 0 } } , text: { x: 3, y: 40, unicode: '&#x1D102;' }  } , 
		{ name: 'rev_final_bar'      , svg: { vb: { w: 15, h: 45, x: 0, y: 0 } } , text: { x: 3, y: 40, unicode: '&#x1D103;' }  } , 
		{ name: 'dashed_bar'         , svg: { vb: { w: 10, h: 45, x: 0, y: 0 } } , text: { x: 3, y: 40, unicode: '&#x1D104;' }  } , 
		{ name: 'short_bar'          , svg: { vb: { w: 10, h: 30, x: 0, y: 0 } } , text: { x: 3, y: 40, unicode: '&#x1D105;' }  } , 
		{ name: 'left_repeat'        , svg: { vb: { w: 20, h: 45, x: 0, y: 0 } } , text: { x: 3, y: 40, unicode: '&#x1D106;' }  } ,
		{ name: 'right_repeat'       , svg: { vb: { w: 20, h: 45, x: 0, y: 0 } } , text: { x: 3, y: 40, unicode: '&#x1D107;' }  } , 
		{ name: 'brace'              , svg: { vb: { w: 20, h: 45, x: 0, y: 0 } } , text: { x: 3, y: 40, unicode: '&#x1D114;' }  } ,
		{ name: 'bracket'            , svg: { vb: { w: 20, h: 56, x: 0, y: 0 } } , text: { x: 3, y: 45, unicode: '&#x1D115;' }  } , 
		{ name: 'one_line'           , svg: { vb: { w: 20, h: 50, x: 0, y: 0 } } , text: { x: 0, y: 45, unicode: '&#x1D116;' }  } , 
		{ name: 'two_line'           , svg: { vb: { w: 20, h: 50, x: 0, y: 0 } } , text: { x: 0, y: 45, unicode: '&#x1D117;' }  } , 
		{ name: 'three_line'         , svg: { vb: { w: 20, h: 50, x: 0, y: 0 } } , text: { x: 0, y: 45, unicode: '&#x1D118;' }  } , 
		{ name: 'four_line'          , svg: { vb: { w: 20, h: 50, x: 0, y: 0 } } , text: { x: 0, y: 45, unicode: '&#x1D119;' }  } , 
		{ name: 'five_line'          , svg: { vb: { w: 20, h: 50, x: 0, y: 0 } } , text: { x: 0, y: 45, unicode: '&#x1D11A;' }  } 
	  ],
	  clefs: [ 
        { name: 'g_clef'             , svg: { vb: { w: 30, h: 75, x: 0, y: 0 } } , text: { x: 3, y: 45, unicode: '&#x1D11E;'           } } , 
        { name: 'g_clef_alto'        , svg: { vb: { w: 30, h: 75, x: 0, y: 0 } } , text: { x: 3, y: 50, unicode: '&#x1D11F;'           } } , 
        { name: 'g_clef_bassa'       , svg: { vb: { w: 30, h: 75, x: 0, y: 0 } } , text: { x: 3, y: 40, unicode: '&#x1D120;'           } } , 
        { name: 'c_clef'             , svg: { vb: { w: 30, h: 50, x: 0, y: 0 } } , text: { x: 3, y: 25, unicode: '&#x1D121;'           } } , 
        { name: 'f_clef'             , svg: { vb: { w: 30, h: 40, x: 0, y: 0 } } , text: { x: 3, y: 15, unicode: '&#x1D122;'           } } , 
        { name: 'f_clef_alta'        , svg: { vb: { w: 30, h: 45, x: 0, y: 0 } } , text: { x: 3, y: 20, unicode: '&#x1D124;'           } } ,
        { name: 'f_clef_bassa'       , svg: { vb: { w: 30, h: 70, x: 0, y: 0 } } , text: { x: 3, y: 45, unicode: '&#x1D123'            } } , 
        { name: 'natural_clef'       , svg: { vb: { w: 42, h: 45, x: 0, y: 0 } } , text: { x: 3, y: 23, unicode: '&#x1D125; &#x1D126;' } } ,
        { name: 't'                  , svg: { vb: { w: 20, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 17, letter: 'T'}                   } ,
        { name: 'a'                  , svg: { vb: { w: 20, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 17, letter: 'A'}                   } ,
        { name: 'b'                  , svg: { vb: { w: 20, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 17, letter: 'B'}                   }
	  ],
	  times: [ 
		{ name: 'common_time'        , svg: { vb: { w: 22, h: 30, x: 0, y: 0 } } , text: { x: 3, y: 15, unicode: '&#x1D134;' } }, 
		{ name: 'cut_time'           , svg: { vb: { w: 22, h: 30, x: 0, y: 0 } } , text: { x: 3, y: 15, unicode: '&#x1D135;' } } 
	  ],
	  repetition: [
        { name: 'dal_segno'          , svg: { vb: { w: 45, h: 35, x: 0, y: 0 } } , text: { x: 3, y: 25, unicode: '&#x1D109;' } }, 
		{ name: 'da_capo'            , svg: { vb: { w: 45, h: 35, x: 0, y: 0 } } , text: { x: 3, y: 25, unicode: '&#x1D10A;' } }, 
		{ name: 'segno'              , svg: { vb: { w: 25, h: 35, x: 0, y: 0 } } , text: { x: 3, y: 30, unicode: '&#x1D10B;' } }, 
		{ name: 'coda'               , svg: { vb: { w: 40, h: 45, x: 0, y: 0 } } , text: { x: 3, y: 35, unicode: '&#x1D10C;' } }, 
		{ name: 'figure_one'         , svg: { vb: { w: 25, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 13, unicode: '&#x1D10D;' } }, 
		{ name: 'figure_two'         , svg: { vb: { w: 25, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 15, unicode: '&#x1D10E;' } }, 
		{ name: 'figure_three'       , svg: { vb: { w: 35, h: 30, x: 0, y: 0 } } , text: { x: 3, y: 15, unicode: '&#x1D10F;' } }  
	  ],
	  octaves: [ 
		{ name: 'ottava_alta'        , svg: { vb: { w: 38, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 20, unicode: '&#x1D136;' } }, 
		{ name: 'ottava_bassa'       , svg: { vb: { w: 38, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 20, unicode: '&#x1D137;' } },
		{ name: 'quindicesima_alta'  , svg: { vb: { w: 52, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 20, unicode: '&#x1D138;' } }, 
		{ name: 'quindicesima_bassa' , svg: { vb: { w: 52, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 20, unicode: '&#x1D139;' } }
	  ],
	  rests: [ 
		{ name: 'fermata'            , svg: { vb: { w: 30, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 20, unicode: '&#x1D110;' } }, 
		{ name: 'fermata_below'      , svg: { vb: { w: 30, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 10, unicode: '&#x1D111;' } }, 
		{ name: 'breath'             , svg: { vb: { w: 13, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 10, unicode: '&#x1D112;' } }, 
		{ name: 'caesura'            , svg: { vb: { w: 20, h: 25, x: 0, y: 0 } } , text: { x: 3, y: 22, unicode: '&#x1D113;' } },
		{ name: 'multi_rest'         , svg: { vb: { w: 10, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 15, unicode: '&#x1D13A;' } },
		{ name: 'whole_rest'         , svg: { vb: { w: 17, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 10, unicode: '&#x1D13B;' } }, 
		{ name: 'half_rest'          , svg: { vb: { w: 17, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 10, unicode: '&#x1D13C;' } }, 
		{ name: 'quarter_rest'       , svg: { vb: { w: 17, h: 30, x: 0, y: 0 } } , text: { x: 3, y: 15, unicode: '&#x1D13D;' } }, 
		{ name: 'eighth_rest'        , svg: { vb: { w: 16, h: 20, x: 0, y: 0 } } , text: { x: 3, y: 7 , unicode: '&#x1D13E;' } }, 
		{ name: 'sixteenth_rest'     , svg: { vb: { w: 16, h: 30, x: 0, y: 0 } } , text: { x: 3, y: 8 , unicode: '&#x1D13F;' } },
		{ name: 'thirty_second_rest' , svg: { vb: { w: 18, h: 38, x: 0, y: 0 } } , text: { x: 3, y: 17, unicode: '&#x1D140;' } }, 
		{ name: 'sixty_fourth_rest'  , svg: { vb: { w: 20, h: 45, x: 0, y: 0 } } , text: { x: 3, y: 17, unicode: '&#x1D141;' } },
		{ name: '128_rest'           , svg: { vb: { w: 23, h: 55, x: 0, y: 0 } } , text: { x: 3, y: 25, unicode: '&#x1D142;' } }
	  ],
	  numbers: [
        { name: 'zero' , svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 13 ,letter: '0' } },
	    { name: 'one'  , svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 13 ,letter: '1' } },
	    { name: 'two'  , svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 13 ,letter: '2' } },
	    { name: 'three', svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 13 ,letter: '3' } },
	    { name: 'four' , svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 13 ,letter: '4' } },
	    { name: 'five' , svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 13 ,letter: '5' } },
	    { name: 'six'  , svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 13 ,letter: '6' } },
	    { name: 'seven', svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 13 ,letter: '7' } },
	    { name: 'eight', svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 13 ,letter: '8' } },
	    { name: 'nine' , svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 13 ,letter: '9' } },
	    { name: 'equal', svg: { vb: { w: 15, h: 15, x: 0, y: 0 } } , text: { x: 3, y: 13 ,letter: '=' } }
	  ]

	}
						  
  };
  
  this.UnitStuffs  = function(parentSvg) {
		         
    var Ypos,stuffs = new Array(), multi = 9.0, Distance = 80; 					 
    var StuffWrapper = self.MainSvgPad.Children.Stuffs.el; parentSvg.insertBefore(StuffWrapper, parentSvg.firstChild); 
																 
	function createStuff() {
	  var Stuff, newLine;
      if (stuffs.length == 0) { 
	    Ypos = Distance; 
	  } else { 
	    Ypos = stuffs[stuffs.length-1].getAttribute('y'); 
	  }					  
      Ypos = parseInt(Ypos,10) + Distance;					  
      Stuff = self.Helpers.createSvgEl({  
	    svg_type: 'svg' ,
		attribute_Arr: [ 
		  { prop: 'class', val: 'stuff' }, 
		  { prop: 'y', val: Ypos } ,
		  { prop: 'viewBox', val: self.SVGViewBox.X + ' ' + self.SVGViewBox.Y + ' ' + self.SVGViewBox.W + ' ' + self.SVGViewBox.H }
		]
      }); 	   
	  parentSvg.insertBefore(Stuff, parentSvg.firstChild);                 	                                						  
      for (var i = 0; i < 5; i++) {                				 
          newLine = self.Helpers.createSvgEl({
	      svg_type: 'line', 
          attribute_Arr: [ 
			   { prop: 'class', val: 'stuff_line' } ,  
               { prop: 'x1', val: 0 } ,  
               { prop: 'y1', val: 1 + i * multi } ,
               { prop: 'x2', val: self.SVGViewBox.H } ,
               { prop: 'y2', val: 1 + i * multi } ,	
               { prop: 'style', val: 'stroke:rgba(0,0,0,0.8);stroke-width:1.3;' } ,
			   { prop: 'viewBox', val: self.SVGViewBox.X + ' ' + self.SVGViewBox.Y + ' ' + self.SVGViewBox.W + ' ' + self.SVGViewBox.H }                 																							  																								   
           ]
        });		   
		 Stuff.appendChild(newLine);						   
      }	   
	  StuffWrapper.appendChild( Stuff );
	  stuffs.push( Stuff );
      return Stuff;									  
	} 
	
    for (var r = 0; r < 10; r++) {
	  createStuff();
	}
    stuffs.length = 0;       			  
  };
  
  this.AddSymbol = function(parentEl, HTMLObject, x, y) { 		
    var svgVB = HTMLObject.svg.vb; 
	var svgVBw = svgVB.w || 0,svgVBh = svgVB.h || 0,svgVBx = svgVB.x || 0,svgVBy = svgVB.y || 0;		                          								 
	var refSvg = self.Helpers.createSvgEl({ 
	  svg_type: 'svg' , 
	  attribute_Arr: [ 
	    { prop: 'class', val: 'musicSymbol ' + HTMLObject.name } ,  
		{ prop: 'x', val: x } ,  
		{ prop: 'y', val: y } ,
		{ prop: 'width', val: svgVBw } ,
		{ prop: 'height', val: svgVBh } , 
		{ prop: 'viewBox', val: svgVBx + ' ' + svgVBy + ' ' + svgVBw + ' ' + svgVBh }   																								   
	  ]
	}); 
	parentEl.appendChild( refSvg );	
														  
    var rect = self.Helpers.createSvgEl({  
	  svg_type: 'rect', 
	  attribute_Arr: [ 
	    { prop: 'x', val: '0'  } ,  
	    { prop: 'y', val: '0'  } ,
	    { prop: 'width', val: '100%' } ,
	    { prop: 'height', val: '100%' } ,
        { prop: 'style', val: 'fill:rgba(1,1,1,0.01);' } , 
	    { prop: 'class', val: 'border_like' },
        { prop: 'viewBox', val: self.SVGViewBox.X + ' ' + self.SVGViewBox.Y + ' ' + self.SVGViewBox.W + ' ' + self.SVGViewBox.H }																							 
	  ]
	}); 
	refSvg.appendChild( rect );


	var refSvgText = self.Helpers.createSvgEl({  
	  svg_type: "text" , 
	  attribute_Arr: [ 
	    { prop: 'x', val: HTMLObject.text.x      } ,  
		{ prop: 'y', val: HTMLObject.text.y      } ,
		{ prop: 'style', val: "font-size :35px;font-family: 'Bravura';"  } , 
	  ]
	}); 
	
	refSvg.appendChild(refSvgText);	
														  
	if (HTMLObject.text.hasOwnProperty('tr')) { 
	  refSvgText.setAttribute('transform', HTMLObject.text.tr); 
	}	
	if (HTMLObject.text.hasOwnProperty('letter')) {
								  
	  var textNode = document.createTextNode(HTMLObject.text.letter);
      refSvgText.appendChild(textNode);
											 
      var n = HTMLObject.name;	
	  
	  if (
	    n == 'zero' 
		|| n == 'one' 
		|| n == 'two' 
		|| n == 'three' 
		|| n == 'four' 
		|| n =='five' 
		|| n=='six' 
		|| n =='seven' 
		|| n=='eight' 
		|| n == 'nine' 
		|| n == 'equal'
	  ) {											
	    refSvgText.setAttribute('style',"font-size :13px;fill: #000000;font-family: 'arial';font-weight:bold;");
	  }	else {
	    refSvgText.setAttribute('style',"font-size :20px;fill: #000000;font-family: 'arial';font-weight:bold;");
	  }
	  
	} else {
	  var textNode = document.createTextNode(entity(HTMLObject.text.unicode));
      refSvgText.appendChild(textNode);								  
	}

    function entity(str, mode) {
      var str = (str) ? str : '';
      var mode = (mode) ? mode : 'string';
                                                      
      var e=document.createElement("div");
      e.innerHTML=str;
                                                      
      if (mode=='numeric') {
        return'&#'+e.innerHTML.charCodeAt(0)+';';
      } else if (mode=='utf16') {
        var un=e.innerHTML.charCodeAt(0).toString(16);
        while(un.length<4) un="0"+un;
         return"\\u"+un;
      } else return e.innerHTML;
    }								  
	return refSvg;
							  
  };
  
  this.getSymbol = function(symbol) {
			 
    var getSymbol = this;

	this.DeleteIt = function () {								
	  symbol.parentNode.removeChild(symbol);
	  getSymbol.Symbol_KillEvents(symbol);													  
	},
	this.ReturnDefaultPos  = function () {								
	  symbol.setAttribute("x", self.TargetSymbol.startPos.x);
	  symbol.setAttribute("y", self.TargetSymbol.startPos.y);								
	}
	this.Symbol_AddEvents  = function () {								
	  self.Helpers.addEvent(symbol, 'mousedown', getSymbol.Symbol_LeftClick, false);
	  self.Helpers.addEvent(symbol, 'mouseup', getSymbol.Symbol_MouseUp, false);
	  self.Helpers.addEvent(symbol, 'mouseover', getSymbol.Symbol_MouseOver, false);
	  self.Helpers.addEvent(symbol, 'mouseout', getSymbol.Symbol_MouseOut, false);
	}
	this.Symbol_KillEvents = function () {								
	  self.Helpers.removeEvent(symbol, 'mousedown', getSymbol.Symbol_LeftClick, false);
	  self.Helpers.removeEvent(symbol, 'mouseup', getSymbol.Symbol_MouseUp, false);
	  self.Helpers.removeEvent(symbol, 'mouseover', getSymbol.Symbol_MouseOver, false);
	  self.Helpers.removeEvent(symbol, 'mouseout', getSymbol.Symbol_MouseOut, false);																
	}
	this.Symbol_MouseOver  = function(e) {								
	  e.stopPropagation(); 
	  e.preventDefault();
	  self.UnHoverBeams();										                 
	  symbol.getElementsByTagName('rect')[0].setAttribute('style', 'fill:rgba(114, 197, 236,0.5);' );									  
	}	
	this.Symbol_MouseOut = function(e) {								
	  e.stopPropagation();
	  e.preventDefault();
	  if (!self.Helpers.hasClass(symbol, 'numbers')) { 
		symbol.getElementsByTagName('rect')[0].setAttribute('style', 'fill:rgba(1,1,1,0.01);' );
	  } else {
	    symbol.getElementsByTagName('rect')[0].setAttribute('style', 'fill:rgba(255,255,255,1);' );  															  
	  }	
	}								
	this.Symbol_LeftClick = function(e) {	
  	  e.preventDefault();
	  e = e || window.event;
																			   
      if (e.which) {
        if (e.which === 1) { 																					
		  Fire();
		} else if  (e.which === 3) {  
		  Fire();
		}
      } else if (e.button) {
        if (e.button != 2) { 
          Fire();
     	} else if  (e.button === 2) {
		  Fire();
		}
	  }
	  
	  function Fire() {																			   
	    self.TargetSymbol.el = symbol;
	    self.TargetSymbol.movable = true;
	    self.TargetSymbol.scalable = false;
	    self.TargetSymbol.startPos.x = parseInt(symbol.getAttribute('x'),10);
	    self.TargetSymbol.startPos.y = parseInt(symbol.getAttribute('y'),10);
	    self.TargetSymbol.dragPoint.x = self.MainSvgPad.MouseCoord.x;
	    self.TargetSymbol.dragPoint.y = self.MainSvgPad.MouseCoord.y;
	  }
																						
	}
								
	this.Symbol_MouseUp = function(e) { 								                                           
	  e.stopPropagation();
	  e.preventDefault();
											
	  var editSpot = self.MainSvgPad.Children.EditArea.el;
      self.TargetSymbol.el = '';
																			 
	  if (parseInt(symbol.getAttribute('y'), 10) > 100) {
																			   
        if (symbol.parentNode != editSpot) {
		  if (self.Helpers.hasClass(symbol, 'quarter')) { 

		    var asa = self.BeamSymbol(editSpot, 'quarter', 1, symbol.getAttribute("x"), symbol.getAttribute("y"));
																			  
			self.getSymbol( symbol ).ReturnDefaultPos();
			self.TargetSymbol.Reset();
			return;
		  } else if (self.Helpers.hasClass(symbol, 'rev_quarter' )) {
			var asa = self.BeamSymbol(editSpot, 'rev_quarter', 1 ,symbol.getAttribute('x') ,symbol.getAttribute('y'));
			self.getSymbol(symbol).ReturnDefaultPos(); 
			self.TargetSymbol.Reset();
			return;
		  }
																				  
          var newPawn = symbol.cloneNode(true);
		  newPawn.setAttributeNS(null, 'x', self.TargetSymbol.startPos.x);
		  newPawn.setAttributeNS(null, 'y', self.TargetSymbol.startPos.y);  
		  newPawn.getElementsByTagName('rect')[0].setAttribute('style', 'fill:rgba(1,1,1,0.1);' );
																				   
		  self.getSymbol( newPawn ).Symbol_AddEvents();

		  symbol.parentNode.appendChild(newPawn);
          editSpot.appendChild(symbol);
		}
      } else { 
		self.getSymbol( symbol ).ReturnDefaultPos();
	  }
									
	  self.TargetSymbol.Reset();									   
                                                                             								
	}
	return this;
  };
  
  this.UnitMainSvg = function(targetEl) {		                    
    self.MainSvgPad.Create(targetEl);
	self.MainSvgPad.SetEvents();
	return self.MainSvgPad.El;
  };
  
  this.UnitPaletteMenu = function () {
		
    var Palettes = self.MainSvgPad.Children.Palette.el;
    var palettemenu =  MenuWrapper;
								   
	for (var o in self.Palette.category) {
		
	  (function(o_prop) { 
	  
        var menuLink = document.createElement('span');
		menuLink.setAttribute('style', 'margin:0px;padding:1%;vertical-align:middle;cursor:pointer;text-transform:capitalize;display:inline-block;font-size:17px;');
		menuLink.innerHTML = o_prop;
		
		palettemenu.appendChild(menuLink);
										   

		var paletteSVG =  self.UnitPalette( Palettes , self.Palette.category[o_prop] , o_prop );									
			paletteSVG.setAttribute('visibility', 'hidden');
			menuLink.addEventListener('click' ,function(e) {    
			  var all_palettes = Palettes.childNodes;
			  for (var m = 0; m < all_palettes.length; m++) {
				all_palettes[m].setAttribute('visibility', 'hidden');
			  }
			  paletteSVG.setAttribute('visibility', 'visible');
			});
			
	  })(o)  
	  
	}
    Palettes.getElementsByTagName('svg')[0].setAttribute('visibility', 'visible');	
  };
  
  this.UnitPalette = function(palleteFather, objectArray, clname) {
	  
    var svgPaletteNode = self.Helpers.createSvgEl({ 
	  svg_type: 'svg', 
	  attribute_Arr:[ 
	    { prop: 'class', val: 'palette ' + clname  },  
	    { prop: 'x', val: "0" },
	    { prop: 'y', val: "0" },
	    { prop: 'width', val: self.SVGViewBox.W    },
	    { prop: 'viewBox', val: self.SVGViewBox.X + ' ' + self.SVGViewBox.Y + ' ' + self.SVGViewBox.W + ' ' +self.SVGViewBox.H }
	  ]
	}); 
	palleteFather.appendChild(svgPaletteNode);								   
						
	var previous = null,Sunit = objectArray,StartXPoing = 40;

    for (var k = 0; k < Sunit.length; k++) {
		
      (function(idx){ 	  
        if (previous != null) { 
		  var xX = parseInt( previous.getAttribute('width') , 10 ) + parseInt( previous.getAttribute('x') , 10 )+2; 
		} else { var xX = 0; }
        if (xX > idx*15) { 
		  xX = xX;
		} else { 
		  xX = idx*15 
		};
		
		var refSvg = self.AddSymbol(svgPaletteNode, Sunit[idx], xX + StartXPoing, 30); 
		self.Helpers.addClass(refSvg, clname);  
		self.getSymbol(refSvg).Symbol_AddEvents();
		previous =  refSvg;									   
	  })(k)
	  
    }
		
	return svgPaletteNode;
  };
  
  this.ResetSymbol = function(e) {
    e.stopPropagation();
	e.preventDefault();
		
	if (self.TargetSymbol.el != '' && self.TargetSymbol.movable) { 
	  var SvgEl = self.TargetSymbol.el;
	  SvgEl.setAttribute('x', self.TargetSymbol.startPos.x);
	  SvgEl.setAttribute('y', self.TargetSymbol.startPos.y);
	  self.TargetSymbol.Reset();
	}
		
  };
  
  this.UnHoverBeams = function() {		
     for (var ty = 0; ty < self.Beams.length; ty ++) {
	   for (var ta = 0; ta < 4; ta++ ) {
	     self.Beams[ty].TreeObject.Points['circle' + ta].Line.Circle.el.setAttribute('r', 0);
		 self.Beams[ty].TreeObject.Points['circle' + ta].Line.Circle.Line.Circle.el.setAttribute('r', 0);
		 self.Beams[ty].TreeObject.Points['circle' + ta].el.setAttribute('r', 0);
		 self.Beams[ty].TreeObject.Points['circle' + ta].Line.Circle.Line.el.setAttribute('style', 'stroke:black;stroke-width:3;');
		}
	 }		
  };
  
  this.cursorPoint  = function(evt, svgMain) {
	  var pt = self.MainSvgPad.SvgPoint;
	  pt.x = evt.clientX; pt.y = evt.clientY;
      return pt.matrixTransform(svgMain.getScreenCTM().inverse());
  };
  
  this.MoveSvgIn   = function() {  	
	  if (self.TargetSymbol.el != '' && self.TargetSymbol.movable) { 
	    var E = self.TargetSymbol.el;
		E.setAttribute('x', self.MainSvgPad.MouseCoord.x + self.TargetSymbol.startPos.x - self.TargetSymbol.dragPoint.x );
		E.setAttribute('y', self.MainSvgPad.MouseCoord.y + self.TargetSymbol.startPos.y - self.TargetSymbol.dragPoint.y );
	  }	  
  };
  
  this.ScaleSvg   = function() {
	  if (self.TargetSymbol.el != '' && self.TargetSymbol.scalable) {
	    var SvgEl = self.TargetSymbol.el;
		var w = self.loc.x -self.StartX + self.StartW ;
		var h = self.loc.y -self.StartY + self.StartH ;
		if( w > 0 ){ SvgEl.setAttribute('width', w); }
		if( h > 0 ){ SvgEl.setAttribute('height', h); }
	  }	  				
  };
  
  this.BeamDestroy = function(beamObj) { 
	
	  var c1,l1,c2,l2,c3,bo,dis,index,Anchor,BeLen = self.Beams.length, FullTree = beamObj, beamObj = beamObj.TreeObject;
	  for ( var m = 0; m < BeLen; m++) { 
	    if (self.Beams[m] === FullTree) { 
	      index = m;
		  if (beamObj.BeamObj.FullTree) {  
		    for ( var v = 0; v < 4; v++) {	
		      Anchor = beamObj.BeamObj.FullTree.Points['circle' + v].Line.Circle.Line.Circle.el;
			  beamObj.BeamObj.FullTree.Points['circle' + v].Line.Circle.Line.Circle.DeMove( beamObj.BeamObj.FullTree , v );
			  Anchor.setAttribute('style', 'fill:yellow;');
			  self.Helpers.removeClass(Anchor, 'beam');
		    }
		    beamObj.BeamWith = false;
		  }
	    }
	    if (self.Beams[m].TreeObject.BeamObj.FullTree === FullTree.TreeObject) {
	      self.Beams[m].TreeObject.BeamWith = false;
	      bo = self.Beams[m].TreeObject.BeamObj , dis = self.Beams[m].TreeObject.Points;
	      dis.circle0.Line.Circle.el.setAttribute('style', 'fill:green;');
	      dis.circle1.Line.Circle.el.setAttribute('style', 'fill:green;');
	      dis.circle2.Line.Circle.el.setAttribute('style', 'fill:green;');
	      dis.circle3.Line.Circle.el.setAttribute('style', 'fill:green;');
	      bo.FullTree = bo.g0 = bo.g1 = bo.g2  = bo.g3 =  bo.SubTree0 = bo.SubTree1 = bo.SubTree2 = bo.SubTree3 = null;
	    }
	  }
	  
	  self.Helpers.removeEvent(beamObj.el, 'mousedown', beamObj, false);
	  self.Helpers.removeEvent(beamObj.el, 'mouseup', beamObj, false);
	  self.Helpers.removeEvent(beamObj.el, 'mouseover', beamObj, false);
	  self.Helpers.removeEvent(beamObj.el, 'mouseout', beamObj, false);
	  beamObj.el.parentNode.removeChild(beamObj.el);
	  beamObj.g.parentNode.removeChild(beamObj.g);
	  
	  for (var b = 0; b < 4; b++ ) {																	  
	    c1 = beamObj.Points['circle' + b].el;
	    l1 = beamObj.Points['circle' + b].Line.el;
	    c2 = beamObj.Points['circle' + b].Line.Circle.el;
	    l2 = beamObj.Points['circle' + b].Line.Circle.Line.el;
	    c3 = beamObj.Points['circle' + b].Line.Circle.Line.Circle.el;
	    self.Helpers.removeEvent(c2, 'mousedown', beamObj, false);
	    self.Helpers.removeEvent(c3, 'mouseup', beamObj, false);
	    self.Helpers.removeEvent(c2, 'mouseover', beamObj, false);
	    self.Helpers.removeEvent(c3, 'mouseout', beamObj, false);
	    c1.parentNode.removeChild(c1);
	    l1.parentNode.removeChild(l1);
	    c2.parentNode.removeChild(c2);
	    l2.parentNode.removeChild(l2);
	    c3.parentNode.removeChild(c3);
	  }
	  self.Beams.splice(index, 1);

  };
  
  this.Beams = [];
  
  this.BeamSymbol = function(edit_area, SymbolPath, Direction, MouseXcor, MouseYcor) {
		
	  var EditArea = edit_area;

	  function TranceIntel_Beam(par, D, path_x, path_y, lineSpace) { 
	    var Vect,symbTyp,DaStyle,mul = 5;
		var pathObj = this;
									  
        self.Beams.push( this );

        this.g = self.Helpers.createSvgEl({ svg_type: 'g' });  
		EditArea.insertBefore( this.g,EditArea.firstChild);
                                     					  										
		if (SymbolPath === 'quarter') {
		  symbTyp = ' -0.5,29.6 c -1.20,-0.8 -3.6,-1.20 -6,-0 -2.80,1.20 -4.80,4 -3.6,6 0.8,2 4,2.40 7.2,1.20 0,-0 0,-0 0.4,-0 2,-1.20 3.6,-3.2 3.2,-5.2 l 0,-31.6 z';
		  path_x = path_x + 14;
		  path_y = path_y + 1;
		  Vect = 1;
		  DaStyle = 'stroke-width:0.1;fill:#000000;stroke:black;cursor:pointer;';
		} else {
		  symbTyp = ' -0.4,-0 0,-32.4 c 0,-1.6 0.8,-2.40 2,-3.6 1.6,-1.20 3.2,-1.6 5.2,-1.6 2.40,-0 3.6,1.20 3.6,2.80 -0,3.2 -4.80,6 -7.60,6 -0.8,-0 -2.40,-0.4 -2.80,-0.8 z';
		  symbTyp = ' l-0.4,0 l 0,-32.40 c 0,-1.6 0.8,-2.40 2,-3.6 1.6,-1.20 3.2,-1.6 5.2,-1.6 2.40,-0 3.6,1.20 3.6,2.80 -0,3.2 -4.80,6 -7.60,6 -0.8,-0 -2.40,-0.4 -2.80,-0.8 z';
		  path_x  = path_x + 4;
		  path_y  = path_y + 41;
		  Vect    = -1;
		  DaStyle = 'stroke-width:0.9;fill:#000000;stroke:black;cursor:pointer;'
		}
										
										
		this.TreeObject = {
 		  BeamObj: { 
		    FullTree: null, 
			g0: null, 
			g1: null, 
			g2: null, 
			g3: null, 
			SubTree0: null, 
			SubTree1: null, 
			SubTree2: null, 
	        SubTree3: null
		  },
		  BeamWith: false,
		  g: this.g,
		  path: null,
		  el: null,
		  x: path_x,
		  y: path_y,
		  D_n: " -0.5,29.6 c -1.20,-0.8 -3.6,-1.20 -6,-0 -2.80,1.20 -4.80,4 -3.6,6 0.8,2 4,2.40 7.2,1.20 0,-0 0,-0 0.4,-0 2,-1.20 3.6,-3.2 3.2,-5.2 l 0,-31.6 z",														   //D_r      : " -0.4,-0 0,-32.4 c 0,-1.6 0.8,-2.40 2,-3.6 1.6,-1.20 3.2,-1.6 5.2,-1.6 2.40,-0 3.6,1.20 3.6,2.80 -0,3.2 -4.80,6 -7.60,6 -0.8,-0 -2.40,-0.4 -2.80,-0.8 z",
		  D_r: " l-0.4,0 l 0,-32.40 c 0,-1.6 0.8,-2.40 2,-3.6 1.6,-1.20 3.2,-1.6 5.2,-1.6 2.40,-0 3.6,1.20 3.6,2.80 -0,3.2 -4.80,6 -7.60,6 -0.8,-0 -2.40,-0.4 -2.80,-0.8 z",
		  D_tale: symbTyp ,
		  style: DaStyle,
		  par: this.g,
		  Move: function(X, Y, Tree, g) {
														   
            var uX = this.x + X;	
			var uY = this.y + Y;	
																	  
            this.el.setAttribute('d', 'm ' + uX + ',' + uY + this.D_tale);
																	  
            for (var b = 0; b < 4; b++) {																	  
			  var lok = Tree.Points['circle' + b].el; 
			  lok.setAttribute('cx', Tree.Points['circle' + b].cx + X);
			  lok.setAttribute('cy', Tree.Points['circle' + b].cy + Y); 
			  var lak = Tree.Points['circle' + b].Line.el;
			  lak.setAttribute('x1', Tree.Points['circle' + b].Line.x1 + X);
			  lak.setAttribute('y1', Tree.Points['circle' + b].Line.y1 + Y);
		    }
			for (var mo = 0; mo < 4; mo++) {
			  Tree.Points['circle' + mo].Line.Circle.Move(X, Y, Tree);
			  if (!self.Helpers.hasClass(Tree.Points['circle' + mo].Line.Circle.Line.Circle.el, 'beam')) {
			    Tree.Points['circle' + mo].Line.Circle.Line.Circle.Move(X, Y, Tree, mo);
			  }
			}																	  
		  },
		  Update: function(X, Y, Tree, g) {
														   
            var uX = this.x + X;	
			var uY = this.y + Y;													   
            this.x = uX; 
			this.y = uY;
            for (var b = 0; b < 4; b++) {
			  Tree.Points['circle' + b].cx = Tree.Points['circle' + b].cx + X;
			  Tree.Points['circle' + b].cy = Tree.Points['circle' + b].cy + Y;
			  Tree.Points['circle' + b].Line.x1 = Tree.Points['circle' + b].Line.x1 + X; 
			  Tree.Points['circle' + b].Line.y1 = Tree.Points['circle' + b].Line.y1 + Y;
			}
																	
			Tree.Points['circle' + 0 ].Line.Circle.Update(X, Y, Tree);
			for (var mo = 0; mo < 4; mo++) {
			  if (!self.Helpers.hasClass(Tree.Points['circle' + mo].Line.Circle.Line.Circle.el, 'beam')) {
			    Tree.Points['circle' + mo].Line.Circle.Line.Circle.Update(X, Y, Tree, mo);
			  }
			}																	  
		  },			
		  Points: {}
		}
                                     
        Create(this.TreeObject);
										
		for (var gi =0 ; gi < 4; gi++ ) { 

		  this.TreeObject.Points['circle' + gi] = { 
										 
		    cx: path_x    ,
			cy: path_y +gi*mul*Vect,
			el: null   ,
			par: this.g ,
			Line: {  
			  x1: path_x ,
			  y1: path_y +gi*mul*Vect,
			  x2: path_x    ,
			  y2: path_y +gi*mul*Vect,
			  el: null ,
			  style: 'stroke:black;stroke-width:1;',
			  par: EditArea ,
              Circle: {
			    cx: path_x    ,
			    cy: path_y + gi*mul*Vect,
			    el: null ,
			    par: EditArea ,
			    drag: true ,
			    style: 'fill:green;',
			    Move : function(X, Y, Tree, g) { 
																									     
				  for (var m=0;m<4;m++) {																											     
				    var circle = Tree.Points['circle' + m].Line.Circle; 
				    circle.el.setAttribute('cx', circle.cx + X);
				    circle.el.setAttribute('cy', circle.cy + Y);
				    var line = Tree.Points['circle' + m].Line;
				    line.el.setAttribute('x2', line.x2 + X);
				    line.el.setAttribute('y2', line.y2 + Y);
				    var line = Tree.Points['circle' + m].Line.Circle.Line;  
				    line.el.setAttribute('x1', line.x1 + X);
				    line.el.setAttribute('y1', line.y1 + Y);																												  
				  }
				
				  if (Tree.BeamWith != false) { 
				    for (var mu = 0; mu < 4; mu++) {
				      if (Tree.BeamObj['SubTree' + mu]) {
					    Tree.BeamObj['SubTree' + mu].Move(X, Y, Tree.BeamObj.FullTree, Tree.BeamObj["g" + mu]);
					  }                                                                                                      
				    }
				  }
				  
			    },
				Update: function(X, Y, Tree, g) {	
																									        
				  for (var m = 0; m < 4; m++) {																											    
				    var circle = Tree.Points['circle' + m].Line.Circle; 
					circle.cx = circle.cx + X;
					circle.cy = circle.cy + Y;
					var line = Tree.Points['circle' + m].Line;
					line.x2 = line.x2 + X;
					line.y2 = line.y2 + Y;
					var line = Tree.Points['circle' + m].Line.Circle.Line;  
					line.x1 = line.x1 + X;
					line.y1 = line.y1 + Y;																												 
				  }
				  if (Tree.BeamWith != false) { 
					for (var mu = 0; mu < 4; mu++) {
					  if (Tree.BeamObj['SubTree' + mu]) {
					    Tree.BeamObj['SubTree' + mu].Update(X, Y, Tree.BeamObj.FullTree, Tree.BeamObj['g' + mu]);
					  }                                                                                                                   
					}
				  }
				  
				}, 
				Line: {
				  x1: path_x    ,
				  y1: path_y +gi*mul*Vect,
				  x2: path_x    ,
				  y2: path_y +gi*mul*Vect,
				  el: null,
				  par: EditArea ,
				  style: 'stroke:black;stroke-width:3;',
				  Circle: {
				    cx: path_x,
					cy: path_y +gi*mul*Vect,
					el: null,
					par: EditArea,
					stop: true,
					style: 'fill:yellow;',
					Move: function(X, Y, Tree, g) { 
					  this.el.setAttribute('cx', this.cx + X);
                      this.el.setAttribute('cy', this.cy + Y);
					  var line = Tree.Points['circle' + g].Line.Circle.Line.el;  
					  line.setAttribute('x2', this.cx + X);
					  line.setAttribute('y2', this.cy + Y);			   
					},
					DeMove: function(Tree, g) {
					  var xa = Tree.Points['circle' + g].Line.Circle;
					  this.el.setAttribute('cx', xa.cx);
                      this.el.setAttribute('cy', xa.cy);
					  var line = Tree.Points['circle' + g].Line.Circle.Line;  
					  line.el.setAttribute('x2', xa.cx);
					  line.el.setAttribute('y2', xa.cy);
					  this.cx = line.x2 = xa.cx,this.cy = line.y2 = xa.cy;
					},
					Update: function(X, Y, Tree, g) {	
					  this.cx = this.cx + X , this.cy = this.cy + Y;
                      var line = Tree.Points['circle' + g].Line.Circle.Line;
					  line.x2 = this.cx + X ,line.y2 = this.cy + Y;
				    }
				  }
			    }
			  } 
			}
										 
		  }
		  WalkObject(this.TreeObject.Points['circle' + gi]);
		  self.Helpers.addEvent(this.TreeObject.Points['circle' + gi].Line.Circle.el, 'mousedown', this, false);
		  self.Helpers.addEvent(this.TreeObject.Points['circle' + gi].Line.Circle.el, 'mouseup', this, false);
		  self.Helpers.addEvent(this.TreeObject.Points['circle' + gi].Line.Circle.Line.Circle.el, 'mousedown', this, false);
		  self.Helpers.addEvent(this.TreeObject.Points['circle' + gi].Line.Circle.Line.Circle.el, 'mouseup', this, false);									  										   
		 }
									   
		 function Create(obj) {									   
		   if (obj.hasOwnProperty('path')) {
		     obj.el = self.Helpers.createSvgEl({ 
			   svg_type: 'path' , 
			   attribute_Arr: [
			     { prop: 'd', val:'m ' + path_x + ',' + path_y + obj.D_tale } 
			   ] 
			 });
			 obj.path = obj.el;
		   }									   
		   if (obj.hasOwnProperty('cx')) {
		     obj.el = self.Helpers.createSvgEl({ 
			   svg_type: 'circle' , 
			   attribute_Arr: [
			     { prop: 'class', val: '' },
				 { prop: "cx", val:obj.cx },
				 { prop: "cy", val:obj.cy },
				 { prop: "r", val:4 }
			   ] 
			 });
		   }
           if (obj.hasOwnProperty('x1')) {
		     obj.el = self.Helpers.createSvgEl( { svg_type : 'line' , attribute_Arr :[{prop:'x1',val:obj.x1},{prop:"y1",val:obj.y1},{prop:"x2",val:obj.x2},{prop:"y2",val:obj.y2}]  });
		   }
		   if (obj.hasOwnProperty('style')) {
		     obj.el.setAttribute('style', obj.style);
		   }
           obj.par.appendChild( obj.el );											 
		 }
		 function WalkObject(obj) { 									    
		   Create(obj);
		   if (obj.hasOwnProperty('Circle') || obj.hasOwnProperty('Line')) {
     	     var nest = obj.Circle || obj.Line;
			 WalkObject(nest);
		   }											   
		 }
									   

		self.Helpers.addEvent(this.TreeObject.path, 'mousedown', this, false);
		self.Helpers.addEvent(this.TreeObject.path, 'mouseup', this, false);
		self.Helpers.addEvent(this.TreeObject.path, 'mouseover', this, false);
		self.Helpers.addEvent(this.TreeObject.path, 'mouseout', this, false);
		return this;		   
	  }//end of trance
					
	  TranceIntel_Beam.prototype.handleEvent = function(e) {
        switch (e.type) {						      
          case 'mousedown': this.mousedown(e); break;
          case 'mouseup': this.mouseup(e); break;
          case 'mouseover': this.mouseover(e); break;
          case 'mouseout': this.mouseout(e); break;								
        }
      }																									  

	  var MouseEv = { 
	    TargetEl: null,
		MousePress: null,
		ResetIt: function () { 
		  this.MousePress = 
		  this.TargetEl = 
		  this.g = 
		  this.TreeObj = 
		  this.WholeTree = 
		  this.Collision = 
		  this.CollisionObj = null;
		},
		g: null,
		TreeObj: null,
		WholeTree: null,
		Collision: null,
		CollisionObj: null
	  } 
	  
   	  TranceIntel_Beam.prototype.mouseover = function(e) {				   
        e.stopPropagation(); 
	    e.preventDefault();
		FoundInTree(this.TreeObject, e.target); 
        self.UnHoverBeams();
							  
        var treeRoads = MouseEv.TreeObj;    
		treeRoads.g.parentNode.appendChild(treeRoads.g); 
		for (var la = 0; la < 4; la++) {								  
		  treeRoads.Points["circle"+la].el.setAttribute("r", 4);
		  treeRoads.Points["circle"+la].Line.Circle.el.setAttribute("r", 4);
		  treeRoads.Points["circle"+la].Line.Circle.Line.Circle.el.setAttribute("r", 4);
		  treeRoads.Points["circle"+la].el.parentNode.appendChild(treeRoads.Points["circle" + la].el ); 
		  treeRoads.Points["circle"+la].Line.Circle.el.parentNode.appendChild(treeRoads.Points["circle" + la].Line.Circle.el); 
		  treeRoads.Points["circle"+la].Line.Circle.Line.Circle.el.parentNode.appendChild(treeRoads.Points["circle" + la].Line.Circle.Line.Circle.el); 									
		}								
      }
   	  TranceIntel_Beam.prototype.mouseout = function(e) { return;				   
        e.stopPropagation();
		e.preventDefault();
		FoundInTree(this.TreeObject, e.target ); 
        var treeRoads = MouseEv.TreeObj;
		for( var la = 0; la < 4; la++ ){
		  treeRoads.Points['circle' + la].Line.Circle.el.setAttribute('r', 0);
		  treeRoads.Points['circle' + la].Line.Circle.Line.Circle.el.setAttribute('r', 0);
	    }		  						        				
      }				   
   	  TranceIntel_Beam.prototype.mousedown = function(e) {
				   
        e.stopPropagation();
		e.preventDefault();
        FoundInTree( this.TreeObject , e.target ); 
        MouseEv.TargetEl = e.target;
        MouseEv.MousePress = self.cursorPoint( e , EditArea.parentNode );
        MouseEv.WholeTree = this.TreeObject;

		self.Helpers.addEvent(EditArea.parentNode, 'mousemove', Tracking, false);
		self.Helpers.addEvent(EditArea.parentNode, 'mouseup', ResetPos, false);
		self.Helpers.addEvent(EditArea.parentNode, 'mouseleave', ResetPos, false);							  
		self.TargetSymbol.beamObje = this; 
      }
				   
   	  TranceIntel_Beam.prototype.mouseup = function(e) { 
				             
        e.stopPropagation();
		e.preventDefault(); // an afairesoume auto tote ginetai trigger kai to geniko mouseup
		var mEl,Tr; 
  		var mouseCoords = self.cursorPoint(e, EditArea.parentNode);
		var MoveX = mouseCoords.x - MouseEv.MousePress.x;
		var MoveY = mouseCoords.y - MouseEv.MousePress.y;
		if (MouseEv.Collision) {   // EDW NA DIORTHWTHEI GIA TO KOLLISION
		  MoveX = parseInt(MouseEv.Collision.getAttribute('cx'),10) - MouseEv.TreeObj.cx;
		  MoveY = parseInt(MouseEv.Collision.getAttribute('cy'),10) - MouseEv.TreeObj.cy;
          MouseEv.TreeObj.Move(  MoveX ,  MoveY , MouseEv.WholeTree , MouseEv.g );
        }
		if (!MouseEv.TreeObj.hasOwnProperty('Line') && !MouseEv.TreeObj.hasOwnProperty('path')) {
		  mEl = MouseEv.TreeObj.el,Tr = MouseEv.WholeTree;
		  
		  if ( parseInt(mEl.getAttribute('cx'),10) < Tr.Points['circle' + MouseEv.g].Line.Circle.cx + 15 && 
			   parseInt(mEl.getAttribute('cx'),10) > Tr.Points['circle' + MouseEv.g].Line.Circle.cx - 15 &&
			   parseInt(mEl.getAttribute('cy'),10) < Tr.Points['circle' + MouseEv.g].Line.Circle.cy + 15 && 
			   parseInt(mEl.getAttribute('cy'),10) > Tr.Points['circle' + MouseEv.g].Line.Circle.cy - 15  
		  ) { 
									
			MoveX =  Tr.Points['circle'+ MouseEv.g].Line.Circle.cx - MouseEv.TreeObj.cx;
			MoveY =  Tr.Points['circle'+ MouseEv.g].Line.Circle.cy - MouseEv.TreeObj.cy;										
			MouseEv.TreeObj.Move(MoveX, MoveY, MouseEv.WholeTree, MouseEv.g);
		  }
		}
		if (MouseEv.TreeObj.hasOwnProperty('Line')) { 
		  MoveX = 0;
		  if (MouseEv.WholeTree.D_tale === MouseEv.WholeTree.D_n ) { 
		    MouseEv.TreeObj.Update( MoveX ,  MoveY , MouseEv.WholeTree , MouseEv.g );
		  } else if( MouseEv.WholeTree.D_tale === MouseEv.WholeTree.D_r  ) {
		    MouseEv.TreeObj.Update( MoveX ,  MoveY , MouseEv.WholeTree , MouseEv.g );
		  } 
								//----- MouseEv.TreeObj.Update( 0 ,  MoveY , MouseEv.WholeTree , MouseEv.g );
		} else {     
	      MouseEv.TreeObj.Update(  MoveX ,  MoveY , MouseEv.WholeTree , MouseEv.g ); 
		}	 
        MouseEv.ResetIt();
        					  
		self.Helpers.removeEvent(EditArea.parentNode, 'mousemove', Tracking, false);
		self.Helpers.removeEvent(EditArea.parentNode, 'mouseup', ResetPos, false);
		self.Helpers.removeEvent(EditArea.parentNode, 'mouseleave', ResetPos, false);
		
		self.TargetSymbol.beamObje = null;
	  }
	  
	  function ResetPos(e) { 
	    alert('mouseUp Not In the path');				   
		if (MouseEv.TargetEl != null && e.target!= MouseEv.TargetEl) {
	  	  var mouseCoords = self.cursorPoint(e, EditArea.parentNode);
		  var MoveX = mouseCoords.x - MouseEv.MousePress.x;
		  var MoveY = mouseCoords.y - MouseEv.MousePress.y;
		  MoveX = 0;
		  MoveY = 0;								 
	      MouseEv.TreeObj.Move(MoveX, MoveY, MouseEv.WholeTree, MouseEv.g);
	      MouseEv.ResetIt();
	      EditArea.parentNode.removeEventListener('mousemove', Tracking, false);										 
	      EditArea.parentNode.removeEventListener('mouseup', ResetPos, false);
	      EditArea.parentNode.removeEventListener('mouseleave', ResetPos, false);										 
	      self.TargetSymbol.beamObje = null;
		}
	  }
	  function Tracking(e) {
        var mouseCoords = self.cursorPoint(e, EditArea.parentNode);
		var MoveX = mouseCoords.x - MouseEv.MousePress.x;
		var MoveY = mouseCoords.y - MouseEv.MousePress.y;
		if (MouseEv.TreeObj.hasOwnProperty('Line')) {
		  MoveX = 0; 
		  if (MouseEv.WholeTree.D_tale === MouseEv.WholeTree.D_n) { 
		    MouseEv.TreeObj.Move( MoveX ,  MoveY , MouseEv.WholeTree , MouseEv.g );
		  } else if(MouseEv.WholeTree.D_tale === MouseEv.WholeTree.D_r) {
		    MouseEv.TreeObj.Move( MoveX ,  MoveY , MouseEv.WholeTree , MouseEv.g );
		  } 
		} else {   
		  MouseEv.TreeObj.Move( MoveX ,  MoveY , MouseEv.WholeTree , MouseEv.g );
		}	 
		Collision(MouseEv.g);		   
	  }
				   
	  function FoundInTree(tree, elem) {				   
	    for (var la = 0; la < 4; la++) {
		  if (tree.el == elem ){ MouseEv.g = 'a';  MouseEv.TreeObj = tree;break;}
		  if (tree.Points['circle' + la].Line.Circle.el == elem ) { 
		    MouseEv.g = la;   
			MouseEv.TreeObj = tree.Points['circle'+la].Line.Circle;
			break;
		  }
		  if (tree.Points['circle' + la].Line.Circle.Line.Circle.el == elem ) { 
		    MouseEv.g = la;   
			MouseEv.TreeObj = tree.Points['circle'+la].Line.Circle.Line.Circle;
			break;
		  }
		}					   
	  }
	  function Collision(f) { 				   
	   if (self.Beams.length > 1 && MouseEv.TreeObj.hasOwnProperty('stop')) {							
	     var cicle_x = parseInt(MouseEv.TargetEl.getAttribute('cx'), 10);
		 var cicle_y = parseInt(MouseEv.TargetEl.getAttribute('cy'), 10);
							
	     for (var ty = 0; ty < self.Beams.length; ty ++) {
								  
		   var other = self.Beams[ty].TreeObject.Points['circle' + f].Line.Circle.el;
									   
		   if (other!=MouseEv.TargetEl && MouseEv.WholeTree!= self.Beams[ty].TreeObject) { 
		     if (
			   cicle_x > parseInt(other.getAttribute('cx'), 10) - 20 
			   && cicle_x < parseInt(other.getAttribute('cx'), 10) + 20 
			   && cicle_y > parseInt(other.getAttribute('cy'), 10) - 10 
			   && cicle_y < parseInt(other.getAttribute('cy'), 10) + 10
		     ) {
		     						        
			   if (self.Beams[ty].TreeObject.BeamObj['g' + f] === null ) {
			     self.Beams[ty].TreeObject.BeamWith = true;
			     self.Beams[ty].TreeObject.BeamObj.FullTree = MouseEv.WholeTree; 
			     self.Beams[ty].TreeObject.BeamObj['g' + f] = f;
			     self.Beams[ty].TreeObject.BeamObj['SubTree' + f] = MouseEv.TreeObj;
			     MouseEv.Collision = other;													 
			     MouseEv.TargetEl.setAttribute('style', 'fill:black;');
			     self.Helpers.addClass(MouseEv.TargetEl, 'beam' );  
			     other.setAttribute('style', 'fill:black;');
			     other.setAttribute('r', '4');											   
			   } else if(self.Beams[ty].TreeObject.BeamWith && self.Beams[ty].TreeObject.BeamObj.FullTree != MouseEv.WholeTree ) {
			     alert('this symbol is already combined with another.You can chain only from Left to Right');
			   }
			   break;
			 
		     } else {
									         
		       if (self.Beams[ty].TreeObject.BeamWith && self.Beams[ty].TreeObject.BeamObj.FullTree == MouseEv.WholeTree) {
			     self.Beams[ty].TreeObject.BeamObj['g' + f] = null;
			     self.Beams[ty].TreeObject.BeamObj['SubTree' + f] = null;
			     MouseEv.Collision = null;
			     MouseEv.TargetEl.setAttribute('style' ,'fill:yellow;');
			     self.Helpers.removeClass(MouseEv.TargetEl, 'beam' );
			     other.setAttribute('style', 'fill:green;');
			     other.setAttribute('r', '0');
			     if (
				   self.Beams[ty].TreeObject.BeamObj['g0'] === null 
				   && self.Beams[ ty ].TreeObject.BeamObj['g1'] === null  
				   && self.Beams[ ty ].TreeObject.BeamObj['g2'] === null 
				   && self.Beams[ ty ].TreeObject.BeamObj['g3'] === null 
			     ) {
				   self.Beams[ty].TreeObject.BeamWith = false;
				   self.Beams[ty].TreeObject.BeamObj.FullTree = null;												   
			     }
			   }
										
		     }
		   }
									   
		 }//end FOR
								  
	    }
				   
	  }
				   
	  var lineSpace = 10; 
	  var D = '';
	  var Path_x = parseInt( MouseXcor ,10 );
	  var Path_y = parseInt( MouseYcor ,10 );
				
   	  var NoteObject = new TranceIntel_Beam(EditArea, D, Path_x, Path_y, lineSpace);
				
	  return NoteObject;
				   
  }
		
  var mainS = self.UnitMainSvg(targetEl); 

  self.UnitPaletteMenu();
  self.UnitStuffs(mainS);												
}					  


MusicPad.prototype.Helpers = function () { 
		
  var MainClass = this.MainClass;
		
  var Helpers = {
    createSvgEl: function(svg_obj) {				   
	  var svgEl =  document.createElementNS('http://www.w3.org/2000/svg', svg_obj.svg_type);								  
	  if (
	    typeof(svg_obj.attribute_Arr) != 'undefined' 
	    && svg_obj.attribute_Arr != '' 
		&& svg_obj.attribute_Arr.constructor === Array 
		&& svg_obj.attribute_Arr.length > 0
	  ) {									  
	     var Len = svg_obj.attribute_Arr.length;											  
		 for ( var u = 0; u < Len; u++ ) {											  
	       svgEl.setAttributeNS(null, svg_obj.attribute_Arr[u].prop, svg_obj.attribute_Arr[u].val);												 
		 }
	  }									  
	  return svgEl;
	},
    trim: function(str) {
      return str.trim ? str.trim() : str.replace(/^\s+|\s+$/g,'');
    },
    hasClass: function(el, cn) {
      return (' ' + el.getAttribute('class') + ' ').indexOf(' ' + cn + ' ') !== -1;
    },
    addClass: function(el, cn) {
      if (!Helpers.hasClass(el, cn)) { 
	    var cla;
		if( el.getAttribute('class')==='' || el.getAttribute('class')=== null) { 
		  cla = cn; 
		} else { 
		  cla = el.getAttribute('class') +' '+cn; 
		}
		el.setAttribute( 'class' , cla );
      }
    },
    removeClass: function(el, cn) { 
      var newClass = Helpers.trim((' ' + el.getAttribute('class') + ' ').replace(' ' + cn + ' ', ' ')); 
	  el.setAttribute('class', newClass );
    },
	addEvent: function(el, e, callback, capture) {
      var hasEventListeners = !!window.addEventListener;      	
      if (hasEventListeners) {
        el.addEventListener(e, callback, !!capture);
      } else {
        el.attachEvent('on' + e, callback);
      }
    },
    removeEvent: function(el, e, callback, capture) {                                      
      var hasEventListeners = !!window.removeEventListener;     	                                       
      if (hasEventListeners) {
        el.removeEventListener(e, callback, !!capture);
      } else {
        el.detachEvent('on' + e, callback);
      }
    },
	resizePath: function(d, multiplier) {
	  var resizedD = d.replace(/(\d+\.?\d{0,9}|\.\d{1,9})/g, function(match) {
        return parseInt(match,10)*multiplier;
      });   
	  return resizedD;			   
				   
	}
				   
  }	   
  return Helpers;		   
				   
}




var MusicEditor;
onload = function () {  
  MusicEditor = new MusicPad(document.getElementById('musicpad'), document.getElementById('menuX')); 			
}

</SCRIPT>
</HEAD>

<BODY>
  <div id="wrapper" oncontextmenu="/*return false;*/" style="height:auto;">
    <div id="title_wrapper" >
      <div id="title">Music Score Writer</div>	 
    </div> 
    <div id="subtitle" style = "font-size:15px;color:#E7F3F6;text-align:right;    max-width: 80%;">Chain Quarters from left to right </div>
    <div id="menuX"></div>
    <div id="musicpad" style = "
	  		   display: inline-block;
                 position: relative;
                 width: 80%;
                 padding-bottom: 70%; 
                 vertical-align: top; max-width:none;height:auto;margin-top:0px;">
    </div>
  </div>         
</BODY>
</HTML>		 
