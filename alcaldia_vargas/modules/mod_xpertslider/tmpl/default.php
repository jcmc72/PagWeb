<?php
/**
 * @package Xpert Slider
 * @version 1.4
 * @author ThemeXpert http://www.themexpert.com
 * @copyright Copyright (C) 2009 - 2011 ThemeXpert
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
// no direct access
defined( '_JEXEC' ) or die('Restricted access');
?>
<!--Themexpert (http://www.themexpert.com) Xpert Slider module - 1.4 Start-->
<div class="xslider_container">
    <div class="xslider_wrap xslider_azure_skin" id="<?php echo $module_id?>">
        <?php foreach($items as $item) :?>
            <div <?php echo XEFXpertSliderHelper::getDataAttribute($item, $params);?>>
                
                <div class="xslider_caption <?php echo $params->get('caption_animation','moveFromLeft')?>">

                    <?php if($params->get('title')):?>
                        <h3 class="xslider_title"><?php echo $item->title?></h3>
                    <?php endif;?>
                    <?php if($params->get('intro')):?>
                        <?php echo $item->introtext; ?>
                    <?php endif;?>
                </div>
                
            </div>
        <?php endforeach;?>
    </div>
</div>
<!--Themexpert (http://www.themexpert.com) Xpert Slider module - 1.4 End-->