<?php
/**
 * @package XpertScroller
 * @version 3.10-1-GFF3CA2D
 * @author ThemeXpert http://www.themexpert.com
 * @copyright Copyright (C) 2009 - 2011 ThemeXpert
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted accessd');
$index=0;
?>
<!--ThemeXpert: XpertScroller module version 3.10-1-GFF3CA2D Start here-->
<div class="<?php echo $module_id;?> <?php echo $params->get('moduleclass_sfx');?> <?php echo $params->get('scroller_layout');?> clearfix">

    <?php if($params->get('navigator')):?>
    <!-- wrapper for navigator elements -->
    <div class="navi"></div>
    <?php endif;?>


    <a class="prev browse left" <?php echo ($params->get('control','1')) ? '' : 'style="display:none;"';?> ></a>

    <div id="<?php echo $module_id;?>" class="scroller">

        <div class="items">
        <?php for($i = 0; $i<$totalPane; $i++){?>
            <div class="pane">
            <?php for($col=0; $col<(int)$params->get('col_amount'); $col++, $index++) {?>
                <?php if($index>=count($items)) break;?>
                <div class="item">
                    <div class="padding clearfix">

                        <?php if($params->get('image')):?>

                            <?php if( $params->get('image_link') ) :?>
                               <a href="<?php echo $items[$index]->link; ?>" target="<?php echo $params->get('target');?>" >
                            <?php endif; ?>

                                <img class="<?php echo $params->get('image_position');?>" src="<?php echo $items[$index]->image?>" alt="<?php echo $items[$index]->title?>" />
                            <?php if( $params->get('image_link') ) :?>
                                </a>
                            <?php endif; ?>

                        <?php endif;?>

                        <?php if($params->get('title')):?>
                            <h4>
                                <?php if( $params->get('link') ) :?>
                                    <a href="<?php echo $items[$index]->link; ?>" target="<?php echo $params->get('target');?>">
                                <?php endif; ?>

                                    <?php echo $items[$index]->title;?>

                                <?php if( $params->get('link') ) :?>
                                    </a>
                                <?php endif; ?>
                            </h4>
                        <?php endif;?>

                        <?php if($params->get('category')):?>
                            <p class="xs_category">
                                <?php if( $params->get('category_link') ) :?>
                                    <a href="<?php echo $items[$index]->catlink; ?>" target="<?php echo $params->get('target');?>">
                                <?php endif; ?>
                                    <?php echo JText::_('In: ')?>
                                    <?php echo $items[$index]->catname;?>

                                <?php if( $params->get('category_link') ) :?>
                                    </a>
                                <?php endif;?>
                            </p>
                        <?php endif;?>
                        
                        <?php if($params->get('intro')):?>
                            <div class="xs_intro"><?php echo $items[$index]->introtext;?></div>
                        <?php endif;?>

                        <?php if($params->get('readmore')):?>
                            <p class="xs_readmore">
                                <a class="btn" href="<?php echo $items[$index]->link;?>" target="<?php echo $params->get('target');?>">
                                    <?php echo $params->get('readmore_text', 'Readmore..') ;?>
                                </a>
                            </p>
                        <?php endif;?>
                    </div>
                </div>
                <?php if($col == (int)$params->get('col_amount') ){$col=0; break;} ?>
            <?php } ?>
            </div>
        <?php }?>
        </div>
    </div>
    <a class="next browse left" <?php echo ($params->get('control','1')) ? '' : 'style="display:none;"';?> ></a>
</div>
<!--ThemeXpert: XpertScroller module version 3.10-1-GFF3CA2D End Here-->