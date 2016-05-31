/**
 * @copyright	Copyright (c) 2013 Skyline Software (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

if (!Skyline) {
	var Skyline	= {};
}

Skyline.Slicebox = {
	slideCount:		0,

	addSlide: function(id) {
		jQuery('<li><a class="slide-delete icon-cancel" href="javascript:void(0)"></a><div class="slide-sortable"></div><div class="slide-container"><div class="control-group"><div class="input-append"><input type="text" id="jform_params_slides_' + Skyline.Slicebox.slideCount + '_image" name="jform[params][slides][image][]" placeholder="Image" /><a class="modal btn" rel="{handler: \'iframe\', size: {x: 800, y: 500}}" href="index.php?option=com_media&view=images&tmpl=component&fieldid=jform_params_slides_' + Skyline.Slicebox.slideCount + '_image">Select</a></div></div><div class="control-group"><div class="input-append"><input type="text" name="jform[params][slides][link][]" placeholder="Link" /><input type="hidden" name="jform[params][slides][link_nw][]" /><button type="button" class="chk_link_nw btn" data-toggle="button">New Win</button></div></div><div class="control-group"><input type="text" name="jform[params][slides][title][]" placeholder="Title" /></div><div class="control-group"><textarea rows="3" name="jform[params][slides][text][]" placeholder="Description Text"></textarea></div></div></li>').appendTo('#' + id).hide().show('slide');

		// refresh modal
		$$('a.modal').removeEvents('click');
		SqueezeBox.assign($$('a.modal'), {
			parse: 'rel'
		});

		// refresh buttons
		jQuery('.chk_link_nw').click(function() {
			$this	= jQuery(this);
			$this.siblings('input[type="hidden"]').val($this.hasClass('active') ? 0 : 1);
		});

		jQuery('.slide-delete').click(function() {
			$this	= jQuery(this);
			$this.parent('li').hide('slide', function() {
				jQuery(this).remove();
			});
		})

		Skyline.Slicebox.slideCount++;
	}
};

jQuery(function($) {
	$('.slides-container').sortable({
		'handle':	'.slide-sortable'
	});

	$('.chk_link_nw').click(function() {
		$this	= $(this);
		$this.siblings('input[type="hidden"]').val($this.hasClass('active') ? 0 : 1);
	});

	$('.slide-delete').click(function() {
		$this	= $(this);
		$this.parent('li').hide('slide', function() {
			$(this).remove();
		});
	})
});

function jInsertFieldValue(value, id) {
	document.getElementById(id).value = value;
}