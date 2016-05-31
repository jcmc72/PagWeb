<?php
/*------------------------------------------------------------------------
# "joombig featured content slideshow" Joomla module
# Copyright (C) 2013 All rights reserved by joombig.com
# License: GNU General Public License version 2 or later; see LICENSE.txt
# Author: joombig.com
# Website: http://www.joombig.com
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access'); // no direct access 
?>
<script>
	var style_view = <?php echo $style_view;?>;
	var scale_moduleWidth = <?php echo $moduleWidth;?>;
	var scale_moduleHeight = <?php echo $moduleHeight;?>;
	var call_autoplay = <?php echo $autoplay;?>;
	var call_pausetime = <?php echo $pausetime;?>;
	var auto_load = <?php echo $auto_load;?>;
</script>
<?php if($enable_jQuery == 1){?>
	<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/modules/mod_joombig_featured_content_slideshow/js/jquery-1.8.2.js"></script>
<?php }?>
<?php if($enable_jQuery_ui == 1){?>
	<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/modules/mod_joombig_featured_content_slideshow/js/jquery-ui-1.9.0.custom.min.js"></script>
<?php }?>
<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/modules/mod_joombig_featured_content_slideshow/js/jquery-ui-tabs-rotate.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $mosConfig_live_site; ?>/modules/mod_joombig_featured_content_slideshow/js/style-featured-content-slideshow.css" />

<div class="main-featured-content-slideshow" id="main-featured-content-slideshow">
    <div id="featured-content-slideshow1">
        <ul class="ui-tabs-nav">
			<?php for ($loop = 1; $loop <= $tabNumber; $loop += 1) { 
			?>
				<li class="ui-tabs-nav-item" id="nav-fragment-<?php echo $loop;?>">
					<a href="#fragment-<?php echo $loop;?>">
						<img src="<?php echo $mosConfig_live_site; ?>/<?php echo $imagethumb[$loop];?>"/>
						<?php if($style_view == 0){?>
							<span><?php echo $title[$loop]; ?></span>
						<?php }?>
					</a>
				</li>
			<?php
			}  
			?>
        </ul>
        <!-- First Content -->
		<?php for ($loop2 = 1; $loop2 <= $tabNumber; $loop2 += 1) 
		{ 
					if ($loop2 == 1)
					{?>
						<div id="fragment-1" class="ui-tabs-panel">
							<a href="<?php echo $readmorelink[$loop2]; ?>">
								<img src="<?php echo $mosConfig_live_site; ?>/<?php echo $image[$loop2];?>"/>
							</a>
							<?php if(($display_title ==1)&&($display_des ==1)){?>
								<div class="info-feature-content-slideshow">
									<h2><a href="<?php echo $readmorelink[$loop2]; ?>"><?php echo $title[$loop2]; ?></a></h2>
									<?php if($display_readmore == 1){?>
										<p><?php echo $info[$loop2]; ?>...<a href="<?php echo $readmorelink[$loop2]; ?>"><?php echo $readmoretext[$loop2];?></a></p>
									<?php } else {?>
										<p><?php echo $info[$loop2]; ?></p>
									<?php }?>
								</div>
							<?php } else {?>
									<?php if($display_title ==1){?>
										<div class="info-feature-content-slideshow">
											<h2><a href="<?php echo $readmorelink[$loop2]; ?>"><?php echo $title[$loop2]; ?></a></h2>
										</div>
									<?php }?>
									<?php if($display_des ==1){?>
										<div class="info-feature-content-slideshow">
											<?php if($display_readmore == 1){?>
												<p><?php echo $info[$loop2]; ?>...<a href="<?php echo $readmorelink[$loop2]; ?>"><?php echo $readmoretext[$loop2];?></a></p>
											<?php } else {?>
												<p><?php echo $info[$loop2]; ?></p>
											<?php }?>
										</div>
									<?php }?>
							<?php }?>
						</div>
					<?php
					}
					else
					{
					?>
						<div id="fragment-<?php echo $loop2;?>" class="ui-tabs-panel">
							<a href="<?php echo $readmorelink[$loop2]; ?>">
								<img src="<?php echo $mosConfig_live_site; ?>/<?php echo $image[$loop2];?>"/>
							</a>
							<?php if(($display_title ==1)&&($display_des ==1)){?>
								<div class="info-feature-content-slideshow">
									<h2><a href="<?php echo $readmorelink[$loop2]; ?>"><?php echo $title[$loop2]; ?></a></h2>
									<?php if($display_readmore == 1){?>
										<p><?php echo $info[$loop2]; ?>...<a href="<?php echo $readmorelink[$loop2]; ?>"><?php echo $readmoretext[$loop2];?></a></p>
									<?php } else {?>
										<p><?php echo $info[$loop2]; ?></p>
									<?php }?>
								</div>
							<?php } else {?>
									<?php if($display_title ==1){?>
										<div class="info-feature-content-slideshow">
											<h2><a href="<?php echo $readmorelink[$loop2]; ?>"><?php echo $title[$loop2]; ?></a></h2>
										</div>
									<?php }?>
									<?php if($display_des ==1){?>
										<div class="info-feature-content-slideshow">
											<?php if($display_readmore == 1){?>
												<p><?php echo $info[$loop2]; ?>...<a href="<?php echo $readmorelink[$loop2]; ?>"><?php echo $readmoretext[$loop2];?></a></p>
											<?php } else {?>
												<p><?php echo $info[$loop2]; ?></p>
											<?php }?>
										</div>
									<?php }?>
							<?php }?>
						</div>
					<?php
					}
					?>
			<?php
		}
			?>
    </div>
</div>
<script type="text/javascript" src="<?php echo $mosConfig_live_site; ?>/modules/mod_joombig_featured_content_slideshow/js/responsive.js"></script>
