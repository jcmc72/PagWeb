<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// A code to show the offline.php page for the demo
if (JRequest::getCmd("tmpl", "index") == "offline") {
    if (is_file(dirname(__FILE__) . DS . "offline.php")) {
        require_once(dirname(__FILE__) . DS . "offline.php");
    } else {
        if (is_file(JPATH_SITE . DS . "templates" . DS . "system" . DS . "offline.php")) {
            require_once(JPATH_SITE . DS . "templates" . DS . "system" . DS . "offline.php");
        }
    }
} else {
	
// Include Variables
include_once(JPATH_ROOT . "/templates/" . $this->template . '/icetools/vars.php');

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	
<?php  if ($this->params->get('responsive_template')) { ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php } ?>
	
    <jdoc:include type="head" />
    

		<?php
			// Include CSS and JS variables 
			include_once(JPATH_ROOT . "/templates/" . $this->template . '/icetools/css.php');
        ?>
        
        
        <!-- ******************MENU FIJO PARTE SUPERIOR**************************** -->
        
      	<style type="text/css">
      	
      	body, html{ margin:0; padding:0}
		.header{ height:55px; width:100%}
		.menu{height:0px; width:100%; background:#393A3C}
		.wrapper{ height:700px; width:100%; padding-top:0px}
		.fixed{position:fixed; top:0}
      	
      	</style>
      
      <script type="text/javascript">
      posicionarMenu();

      $(window).scroll(function() {    
          posicionarMenu();
      });

      function posicionarMenu() {
          var altura_del_header = $('.header').outerHeight(true);
          var altura_del_menu = $('.menu').outerHeight(true);

          if ($(window).scrollTop() >= altura_del_header){
              $('.menu').addClass('fixed');
              $('.wrapper').css('margin-top', (altura_del_menu) + 'px');
          } else {
              $('.menu').removeClass('fixed');
              $('.wrapper').css('margin-top', '0');
          }
      }
      </script>
        <!-- ****************************************************************** -->
          
</head>


<body class="<?php echo $pageclass->get('pageclass_sfx'); ?>">

<?php if ($this->params->get('styleswitcher')) { ?>
<ul id="ice-switcher">  
<li class= "style1"><a href="templates/<?php echo $this->template;?>/css/styles/style-switcher.php?templatestyle=style1"><span>Style 1</span></a></li>  
</ul> 
<?php } ?>

<div class="header">

<center><img src="templates/ice_future/images/cintillo.png" alt="cintillo" width="80%"></center>

</div><!-- /#header --> 

<div class="menu">
    <header id="header">
    
        <div class="container clearfix">
          
           <div id="logo">	
             <p><a href="<?php echo $this->baseurl ?>"><?php echo $logo; ?></a></p>
            </div>
        	 
            <jdoc:include type="modules" name="mainmenu" />
              
              
            <?php if ($this->countModules('search')) { ?>
             <div id="search">
                <jdoc:include type="modules" name="search" />
            </div>
            <?php } ?>
            
            
       </div>   
          

	</header><!-- /#header -->
</div>	<!-- /#MENU --> 

<div class="wrapper">
    <div id="main" class="container clearfix">

<!--*********************************************************-->
<!--POSICIONES DE RUTA DE NAVEGACION Y BOTONES REDES SOCIALES-->

<style type="text/css">
#izquierda{
float:left;
width: 535px;
}

#derecha{
float:right;
width: 160px;
}
</style>

		<div id="izquierda">
		<jdoc:include type="modules" name="breadcrumbs" /> 
		</div>

   		<div id="derecha">
        	<jdoc:include type="modules" name="redessociales" />
		</div>

<br><br>
<!--**************************************************************-->

<style type="text/css">
#icecarousel{
float:left;
width: 890px;
}

#enlaces{
float:right;
width: 260px;
}
</style>

         <div id="icecarousel">
            <jdoc:include type="modules" name="icecarousel" style="xhtml" />
        </div>

	  <div id="enlaces">
            <jdoc:include type="modules" name="enlaces" style="xhtml" />
        </div>
        

<!--****************************************************************-->
        <?php if ($this->countModules('promo1 + promo2 + promo3 + promo4')) { ?>
        <section id="promo" class="row">
        
            <div class="<?php echo $promospan;?>">	
                <jdoc:include type="modules" name="promo1" style="xhtml" />
            </div> 
            
            <div class="<?php echo $promospan;?>">	
                <jdoc:include type="modules" name="promo2" style="xhtml" />
            </div> 
            
            <div class="<?php echo $promospan;?>">	
                <jdoc:include type="modules" name="promo3" style="xhtml" />
            </div> 
            
            <div class="<?php echo $promospan;?>">	
                <jdoc:include type="modules" name="promo4" style="xhtml" />
            </div> 
           
        </section> 
        
        <hr />   
        <?php } ?>
         
            
        <div id="columns" class="row">
        
        	
            <?php if ($this->countModules('left')) { ?>
        	<div id="left-col" class="span3">
            
            	<jdoc:include type="modules" name="left" style="xhtml" />
            
            </div>
            <?php } ?>
           
      <div id="middle-col" class="<?php echo $colspan;?>">
        
                <section id="content">
                    
                  <jdoc:include type="message" />
		  <jdoc:include type="component" />
               
               
                </section><!-- /#content --> 
          
            </div>
            
            <?php if ($this->countModules('right')) { ?>
            <div id="right-col" class="span3">
            
                <jdoc:include type="modules" name="right" style="xhtml" />
            
            </div>	
            <?php } ?>
    
    
        </div>
    
    
    </div><!-- /#main --> 
    


    <?php if ($this->countModules('marketing')) { ?>
    <section id="marketing">
    	
        <div id="marketing_inside">
        
             <div class="container clearfix">
             
                 <jdoc:include type="modules" name="marketing" />
    
    		</div>
            
        </div>
        
    </section><!-- /#marking --> 
     <?php } ?>

    <footer id="footer">
    
        <div class="container clearfix">
        
        	<?php if ($this->countModules('footer1 + footer2 + footer3 + footer4')) { ?>
            <div id="footermods" class="row">
       
            	<div class="<?php echo $footerspan;?>">	
                	<jdoc:include type="modules" name="footer1" style="xhtml" />
                </div> 
                
                <div class="<?php echo $footerspan;?>">	
                	<jdoc:include type="modules" name="footer2" style="xhtml" />
                </div> 
                
                <div class="<?php echo $footerspan;?>">	
                	<jdoc:include type="modules" name="footer3" style="xhtml" />
                </div> 
                
                <div class="<?php echo $footerspan;?>">	
                	<jdoc:include type="modules" name="footer4" style="xhtml" />
                </div> 
                
            </div>
            
            <hr />   
            <?php } ?> 
             
         
            <div id="copyright_area">
            	
                <?php if($this->params->get('icelogo')) { ?>
                	<center><img src="templates/ice_future/images/piepagina.png" alt="IceTheme" width="100%"></center>
                <?php } ?> 
                
                <p id="copyright">&copy; <?php echo $sitename; ?> <?php echo date('Y');?></p>
                
                <?php if ($this->countModules('copyrightmenu')) { ?>
                <div id="copyrightmenu">
                    <jdoc:include type="modules" name="copyrightmenu" />
                </div>
                <?php } ?> 
                
                
                <?php if ($this->params->get('social_fb') or  $this->params->get('social_tw')) { ?>
                <div id="ice_social">
                	
                    <?php if($this->params->get('social_tw')) { ?>
                    <div id="social_tw">
                        <?php echo $social_tw; ?>
                    </div>
                     <?php } ?>  
                     
                     <?php if($this->params->get('social_fb')) { ?>
                    <div id="social_fb">
                        <?php echo $social_fb; ?>
                    </div> 
                    <?php } ?>   
                      
                    
                </div>
                <?php } ?> 
                
            </div>
        
        </div>

		
		<jdoc:include type="modules" name="debug" style="none"/>             

    </footer>
  
  </div>  <!-- /#wrapper -->  
  
<?php if ($this->params->get('styleswitcher')) { ?> 
<script type="text/javascript">  
jQuery.fn.styleSwitcher = function(){
	jQuery(this).click(function(){
		loadStyleSheet(this);
		return false;
	});
	function loadStyleSheet(obj) {
		jQuery('body').append('<div id="overlay" />');
		jQuery('body').css({height:'100%'});
		jQuery('#overlay')
			.fadeIn(500,function(){
				/* change the default style */
				jQuery.get( obj.href+'&js',function(data){
					jQuery('#stylesheet').attr('href','<?php echo $this->baseurl ?>/templates/<?php echo $this->template;?>/css/styles/' + data + '.css');
					cssDummy.check(function(){
						jQuery('#overlay').fadeOut(1000,function(){
							jQuery(this).remove();
						});	
					});
				});
			
				/* change the responsive style */
				jQuery.get( obj.href+'&js',function(data){
					jQuery('#stylesheet-responsive').attr('href','<?php echo $this->baseurl ?>/templates/<?php echo $this->template;?>/css/styles/' + data + '_responsive.css');
					
					cssDummy.check(function(){
						jQuery('#overlay').fadeOut(1000,function(){
							jQuery(this).remove();
						});	
					});
				});
			});
	}
	var cssDummy = {
		init: function(){
			jQuery('<div id="dummy-element" style="display:none" />').appendTo('body');
		},
		check: function(callback) {
			if (jQuery('#dummy-element').width()==2) callback();
			else setTimeout(function(){cssDummy.check(callback)}, 200);
		}
	}
	cssDummy.init();
}
	jQuery('#iceMenu_496 a').styleSwitcher(); 
	jQuery('#iceMenu_497 a').styleSwitcher(); 
	jQuery('#iceMenu_498 a').styleSwitcher(); 
	jQuery('#iceMenu_499 a').styleSwitcher(); 
	jQuery('#iceMenu_500 a').styleSwitcher(); 
	jQuery('#iceMenu_501 a').styleSwitcher(); 
	
	jQuery('#ice-switcher a').styleSwitcher(); 	
	
	
		/* Control the active class to styleswitcher */
		jQuery(function() {
		jQuery('#ice-switcher a').click(function(e) {
			e.preventDefault();
			var $this = jQuery(this);
			$this.closest('ul').find('.active').removeClass('active');
			$this.parent().addClass('active');
		});
		
		jQuery(document).ready(function(){
			jQuery('#ice-switcher li.<?php echo $templatestyle ?>').addClass('active');
		});
		
	});

</script>  
<?php } ?>


<?php if ($this->params->get('go2top')) { ?>
<div id="gotop" class="">
	<a href="#" class="scrollup"><?php echo JText::_('TPL_TPL_FIELD_SCROLL'); ?></a>
</div>
<?php } ?>  

</body>
</html>
<?php } ?> 
