<?php

/*
	Begin Create Shortcode Generator Options
*/

add_action('admin_menu', 'grandblog_shortcode_generator');

function grandblog_shortcode_generator() {
  
  global $grandblog_page_postmetas;
	if ( function_exists('add_meta_box') && isset($grandblog_page_postmetas) && count($grandblog_page_postmetas) > 0 ) {  
		add_meta_box( 'shortcode_metabox', 'Shortcode Options', 'grandblog_shortcode_generator_options', 'page', 'normal', 'high' );
		add_meta_box( 'shortcode_metabox', 'Shortcode Options', 'grandblog_shortcode_generator_options', 'post', 'normal', 'high' );  
	}
}

function grandblog_shortcode_generator_options() {

  	$plugin_url = get_template_directory_uri().'/plugins/shortcode_generator';
  	
  	//Enqueue colorpicker CSS
  	wp_enqueue_style("grandblog-colorpicker", get_template_directory_uri()."/functions/colorpicker/css/colorpicker.css", false, THEMEVERSION, "all");
  	
  	//Enqueue colorpicker javascript
  	wp_enqueue_script("grandblog-colorpicker", get_template_directory_uri()."/functions/colorpicker/js/colorpicker.js", false, THEMEVERSION);
	wp_enqueue_script("grandblog-eye", get_template_directory_uri()."/functions/colorpicker/js/eye.js", false, THEMEVERSION);
	wp_enqueue_script("grandblog-utils", get_template_directory_uri()."/functions/colorpicker/js/utils.js", false, THEMEVERSION);
  	
  	//Get all galleries
  	$args = array(
	    'numberposts' => -1,
	    'post_type' => array('galleries'),
	);
	
	$galleries_arr = get_posts($args);
	$galleries_select = array();
	$galleries_select[''] = '';
	
	foreach($galleries_arr as $gallery)
	{
		$galleries_select[$gallery->ID] = $gallery->post_title;
	}

	//Begin shortcode array
	$shortcodes = array(
		'dropcap' => array(
			'name' => 'Dropcap',
			'attr' => array(),
			'desc' => array(),
			'content' => TRUE,
		),
		'quote' => array(
			'name' => 'Quote Text',
			'attr' => array(),
			'desc' => array(),
			'content' => TRUE,
		),
		'one_half' => array(
			'name' => 'One Half Column',
			'attr' => array(),
			'desc' => array(),
			'content' => TRUE,
			'repeat' => 1,
		),
		'one_half_last' => array(
			'name' => 'One Half Last Column',
			'attr' => array(),
			'desc' => array(),
			'content' => TRUE,
			'repeat' => 1,
		),
		'one_third' => array(
			'name' => 'One Third Column',
			'attr' => array(),
			'desc' => array(),
			'content' => TRUE,
			'repeat' => 2,
		),
		'one_third_last' => array(
			'name' => 'One Third Last Column',
			'attr' => array(),
			'desc' => array(),
			'content' => TRUE,
		),
		'two_third' => array(
			'name' => 'Two Third Column',
			'attr' => array(),
			'desc' => array(),
			'content' => TRUE,
		),
		'two_third_last' => array(
			'name' => 'Two Third Last Column',
			'attr' => array(),
			'desc' => array(),
			'content' => TRUE,
		),
		'one_fourth' => array(
			'name' => 'One Fourth Column',
			'attr' => array(),
			'desc' => array(),
			'content' => TRUE,
			'repeat' => 3,
		),
		'one_fifth' => array(
			'name' => 'One Fifth Column',
			'attr' => array(),
			'desc' => array(),
			'content' => TRUE,
			'repeat' => 4,
		),
		'one_sixth' => array(
			'name' => 'One Sixth Column',
			'attr' => array(),
			'desc' => array(),
			'content' => TRUE,
			'repeat' => 5,
		),
		'one_half_bg' => array(
			'name' => 'One Half Column With Background',
			'attr' => array(
				'bgcolor' => 'colorpicker',
				'fontcolor' => 'colorpicker',
				'padding' => 'text',
			),
			'title' => array(
				'bgcolor' => 'Background Color',
				'fontcolor' => 'Font Color',
				'padding' => 'Padding (Optional)',
			),
			'desc' => array(
				'bgcolor' => 'Select background color',
				'fontcolor' => 'Select font color',
				'padding' => 'Enter padding for this content (in px)',
			),
			'content' => TRUE,
		),
		'one_third_bg' => array(
			'name' => 'One Third Column With Background',
			'attr' => array(
				'bgcolor' => 'colorpicker',
				'fontcolor' => 'colorpicker',
				'padding' => 'text',
			),
			'title' => array(
				'bgcolor' => 'Background Color',
				'fontcolor' => 'Font Color',
				'padding' => 'Padding (Optional)',
			),
			'desc' => array(
				'bgcolor' => 'Select background color',
				'fontcolor' => 'Select font color',
				'padding' => 'Enter padding for this content (in px)',
			),
			'content' => TRUE,
		),
		'two_third_bg' => array(
			'name' => 'Two Third Column With Background',
			'attr' => array(
				'bgcolor' => 'colorpicker',
				'fontcolor' => 'colorpicker',
				'padding' => 'text',
			),
			'title' => array(
				'bgcolor' => 'Background Color',
				'fontcolor' => 'Font Color',
				'padding' => 'Padding (Optional)',
			),
			'desc' => array(
				'bgcolor' => 'Select background color',
				'fontcolor' => 'Select font color',
				'padding' => 'Enter padding for this content (in px)',
			),
			'content' => TRUE,
		),
		'one_fourth_bg' => array(
			'name' => 'One Fourth Column With Background',
			'attr' => array(
				'bgcolor' => 'colorpicker',
				'fontcolor' => 'colorpicker',
				'padding' => 'text',
			),
			'title' => array(
				'bgcolor' => 'Background Color',
				'fontcolor' => 'Font Color',
				'padding' => 'Padding (Optional)',
			),
			'desc' => array(
				'bgcolor' => 'Select background color',
				'fontcolor' => 'Select font color',
				'padding' => 'Enter padding for this content (in px)',
			),
			'content' => TRUE,
		),
		'googlefont' => array(
			'name' => 'Google Font',
			'attr' => array(
				'font' => 'text',
				'fontsize' => 'text',
				'style' => 'text',
			),
			'title' => array(
				'font' => 'Font Name',
				'fontsize' => 'Font Size',
				'style' => 'Custom CSS style ex. font-style:italic; (Optional)',
			),
			'desc' => array(
				'font' => 'Enter Google Web Font Name you want to use',
				'fontsize' => 'Enter font size in pixels',
				'style' => 'Enter custom CSS styling code',
			),
			'content' => TRUE,
		),
		'tg_gallery_slider' => array(
			'name' => 'Gallery Slider',
			'attr' => array(
				'gallery_id' => 'select',
			),
			'title' => array(
				'gallery_id' => 'Gallery',
			),
			'desc' => array(
				'gallery_id' => 'Select gallery you want to display its images',
			),
			'options' => array(
				'gallery_id' => $galleries_select
			),
			'content' => FALSE,
		),
		'tg_social_icons' => array(
			'name' => 'Social Icons',
			'attr' => array(
				'style' => 'select',
				'size' => 'text',
			),
			'title' => array(
				'style' => 'Color Style',
				'size' => 'Icon Size',
			),
			'desc' => array(
				'style' => 'Select color style for social icons',
				'size' => 'Enter icon size between small and large',
			),
			'options' => array(
				'style' => array(
					'dark' => 'Dark',
					'light' => 'Light',
				)
			),
			'content' => FALSE,
		),
		'tg_divider' => array(
			'name' => 'Divider',
			'attr' => array(
				'style' => 'select',
			),
			'title' => array(
				'style' => 'Style',
			),
			'desc' => array(
				'style' => 'Select HR divider style',
			),
			'options' => array(
				'style' => array(
					'normal' => 'Normal',
					'thick' => 'Thick',
					'dotted' => 'Dotted',
					'dashed' => 'Dashed',
					'faded' => 'Faded',
					'totop' => 'Go To Top',
				)
			),
			'content' => FALSE,
		),
		'tg_lightbox' => array(
			'name' => 'Media Lightbox',
			'attr' => array(
				'type' => 'select',
				'src' => 'text',
				'href' => 'text',
				'vimeo_id' => 'text',
				'youtube_id' => 'text',
			),
			'title' => array(
				'type' => 'Content Type',
				'src' => 'Image URL',
				'href' => 'Link URL',
				'vimeo_id' => 'Vimeo Video ID',
				'youtube_id' => 'Youtube Video ID',
			),
			'desc' => array(
				'type' => 'Select ligthbox content type',
				'src' => 'Enter lightbox preview image URL',
				'href' => 'If you selected "Image". Enter full image URL here',
				'vimeo_id' => 'If you selected "Vimeo". Enter Vimeo video ID here ex. 82095744',
				'youtube_id' => 'If you selected "Youtube". Enter Youtube video ID here ex. hT_nvWreIhg',
			),
			'content' => TRUE,
			'options' => array(
				'type' => array(
					'image' => 'Image',
					'vimeo' => 'Vimeo',
					'youtube' => 'Youtube',
				)
			),
			'content' => FALSE,
		),
		'tg_youtube' => array(
			'name' => 'Youtube Video',
			'attr' => array(
				'width' => 'text',
				'height' => 'text',
				'video_id' => 'text',
			),
			'title' => array(
				'width' => 'Width',
				'height' => 'Height',
				'video_id' => 'Youtube Video ID',
			),
			'desc' => array(
				'width' => 'Enter video width in pixels',
				'height' => 'Enter video height in pixels',
				'video_id' => 'Enter Youtube video ID here ex. hT_nvWreIhg',
			),
			'content' => FALSE,
		),
		'tg_vimeo' => array(
			'name' => 'Vimeo Video',
			'attr' => array(
				'width' => 'text',
				'height' => 'text',
				'video_id' => 'text',
			),
			'title' => array(
				'width' => 'Width',
				'height' => 'Height',
				'video_id' => 'Vimeo Video ID',
			),
			'desc' => array(
				'width' => 'Enter video width in pixels',
				'height' => 'Enter video height in pixels',
				'video_id' => 'Enter Vimeo video ID here ex. 82095744',
			),
			'content' => FALSE,
		),
	);
	
	grandblog_aasort($shortcodes,"name");

?>
<script>
function nl2br (str, is_xhtml) {   
	var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
	return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}

jQuery(document).ready(function(){ 
	jQuery('#shortcode_select').change(function() {
  		var target = jQuery(this).val();
  		jQuery('.rm_section').hide()
  		jQuery('#div_'+target).fadeIn()
	});	
	
	jQuery('.color_picker').each(function()
	{	
	    var inputID = jQuery(this).attr('id');
	    
	    jQuery(this).ColorPicker({
	    	color: jQuery(this).val(),
	    	onShow: function (colpkr) {
	    		jQuery(colpkr).fadeIn(200);
	    		return false;
	    	},
	    	onHide: function (colpkr) {
	    		jQuery(colpkr).fadeOut(200);
	    		return false;
	    	},
	    	onChange: function (hsb, hex, rgb, el) {
	    		jQuery('#'+inputID).val('#' + hex);
	    		jQuery('#'+inputID+'_bg').css('backgroundColor', '#' + hex);
	    	}
	    });	
	    
	    jQuery(this).css('float', 'left');
	});
	
	jQuery('.code_area').click(function() { 
		document.getElementById(jQuery(this).attr('id')).focus();
    	document.getElementById(jQuery(this).attr('id')).select();
	});
	
	jQuery('.shortcode_button').click(function() { 
		var target = jQuery(this).attr('id');
		var gen_shortcode = '';
  		gen_shortcode+= '['+target;
  		
  		if(jQuery('#'+target+'_attr_wrapper .attr').length > 0)
  		{
  			jQuery('#'+target+'_attr_wrapper .attr').each(function() {
				gen_shortcode+= ' '+jQuery(this).data('attr')+'="'+jQuery(this).val()+'"';
			});
		}
		
		gen_shortcode+= ']';
		
		if(jQuery('#'+target+'_content').length > 0)
  		{
  			gen_shortcode+= jQuery('#'+target+'_content').val()+'[/'+target+']';
  			gen_shortcode+= '\n';
  			
  			var repeat = jQuery('#'+target+'_content_repeat').val();
  			for (count=1;count<=repeat;count=count+1)
			{
				if(count<repeat)
				{
					gen_shortcode+= '['+target+']';
					gen_shortcode+= jQuery('#'+target+'_content').val()+'[/'+target+']';
					gen_shortcode+= '\n';
				}
				else
				{
					gen_shortcode+= '['+target+'_last]';
					gen_shortcode+= jQuery('#'+target+'_content').val()+'[/'+target+'_last]';
					gen_shortcode+= '\n';
				}
			}
  		}
  		jQuery('#'+target+'_code').val(gen_shortcode);
  		jQuery('#pp-insert-to-post').attr('rel', '#'+target+'_code');
  		
  		jQuery("#"+target+"-pp-insert-to-post").click(function() { 
			var current_id = jQuery(this).attr('rel');
			var current_code = jQuery('#'+target+'_code').val();
			
			tinyMCE.activeEditor.selection.setContent(nl2br(current_code));
		});
	});
});
</script>

	<div style="padding:20px 10px 10px 10px">
	<?php
		if(!empty($shortcodes))
		{
	?>
			<strong>Select Shortcode</strong><hr class="pp_widget_hr">
			<div class="pp_widget_description">Please select short code from list below then enter short code attributes and click "Generate Shortcode".</div>
			<br/>
			<select id="shortcode_select">
				<option value="">(no short code selected)</option>
			
	<?php
			foreach($shortcodes as $shortcode_name => $shortcode)
			{
				$shortcode_key = $shortcode_name;
				
				if(isset($shortcodes[$shortcode_name]['name']))
				{
					$shortcode_name = $shortcodes[$shortcode_name]['name'];
				}
	?>
	
			<option value="<?php echo esc_attr($shortcode_key); ?>"><?php echo esc_html($shortcode_name); ?></option>
	
	<?php
			}
	?>
			</select>
	<?php
		}
	?>
	
	<br/><br/>
	
	<?php
		if(!empty($shortcodes))
		{
			foreach($shortcodes as $shortcode_name => $shortcode)
			{
	?>
	
			<div id="div_<?php echo esc_attr($shortcode_name); ?>" class="rm_section" style="display:none">
				<div style="width:47%;float:left">
			
				<div class="rm_title">
					<h3><?php echo ucfirst($shortcode_name); ?></h3>
					<div class="clearfix"></div>
				</div>
				
				<div class="rm_text" style="padding: 10px 0 20px 0">
			
				<?php
					if(isset($shortcode['attr']) && !empty($shortcode['attr']))
					{
				?>
						
						<div id="<?php echo esc_attr($shortcode_name); ?>_attr_wrapper">
						
				<?php
						foreach($shortcode['attr'] as $attr => $type)
						{
				?>
				
							<?php echo '<strong>'.$shortcode['title'][$attr].'</strong>: '.$shortcode['desc'][$attr]; ?><br/><br/>
							
							<?php
								switch($type)
								{
									case 'text':
							?>
							
									<input type="text" id="<?php echo esc_attr($shortcode_name); ?>_<?php echo esc_attr($attr); ?>" style="width:100%" class="attr" data-attr="<?php echo esc_attr($attr); ?>"/>
							
							<?php
									break;
									
									case 'textarea':
							?>
							
									<textarea id="<?php echo esc_attr($shortcode_name); ?>_<?php echo esc_attr($attr); ?>" style="width:100%" class="attr" data-attr="<?php echo esc_attr($attr); ?>"></textarea>
							
							<?php
									break;
									
									case 'colorpicker':
							?>
							
									<input type="text" id="<?php echo esc_attr($shortcode_name); ?>_<?php echo esc_attr($attr); ?>" style="width:90%" class="attr color_picker" data-attr="<?php echo esc_attr($attr); ?>" readonly/>
									
									<div id="<?php echo esc_attr($shortcode_name); ?>_<?php echo esc_attr($attr); ?>_bg" class="colorpicker_bg" onclick="jQuery('#<?php echo esc_js($shortcode_name); ?>_text').click()" style="background-image: url(<?php echo get_template_directory_uri(); ?>/functions/images/trigger.png);margin-top:3px">&nbsp;</div><br/>
							
							<?php
									break;
									
									case 'select':
							?>
							
									<select id="<?php echo esc_attr($shortcode_name); ?>_<?php echo esc_attr($attr); ?>" style="width:100%" class="attr" data-attr="<?php echo esc_attr($attr); ?>">
									
										<?php
											if(isset($shortcode['options'][$attr]) && !empty($shortcode['options'][$attr]))
											{
												foreach($shortcode['options'][$attr] as $select_key => $option)
												{
										?>
										
													<option value="<?php echo esc_attr($select_key); ?>"><?php echo esc_html($option); ?></option>
										
										<?php	
												}
											}
										?>							
									
									</select>
							
							<?php
									break;
								}
							?>
							
							<br/><br/>
				
				<?php
						} //end attr foreach
				?>
				
						</div>
				
				<?php
					}
				?>
				
				<?php
					if(isset($shortcode['content']) && $shortcode['content'])
					{
						if(isset($shortcode['content_text']))
						{
							$content_text = $shortcode['content_text'];
						}
						else
						{
							$content_text = 'Your Content';
						}
				?>
				
				<strong><?php echo esc_html($content_text); ?>:</strong><br/><br/>
				<?php if(isset($shortcode['repeat'])) { ?>
					<input type="hidden" id="<?php echo esc_attr($shortcode_name); ?>_content_repeat" value="<?php echo esc_attr($shortcode['repeat']); ?>"/>
				<?php } ?>
				<textarea id="<?php echo esc_attr($shortcode_name); ?>_content" style="width:100%;height:70px" rows="3" wrap="off"></textarea><br/><br/>
				
				<?php
					}
				?>
				
				</div>
				
				</div>
				
				<div style="width:47%;float:right">
				
				<strong>Shortcode:</strong><br/><br/>
				<textarea id="<?php echo esc_attr($shortcode_name); ?>_code" style="width:100%;height:200px;word-wrap: break-word;" rows="3" readonly="readonly" class="code_area" wrap="off"></textarea>
				
				<br/><br/>
				<input type="button" id="<?php echo esc_attr($shortcode_name); ?>" value="Generate Shortcode" class="button shortcode_button button-primary"/>
				</div>
				
				<br style="clear:both"/>
			</div>
	
	<?php
			} //end shortcode foreach
		}
	?>
	
</div>

<?php

}

/*
	End Create Shortcode Generator Options
*/

?>