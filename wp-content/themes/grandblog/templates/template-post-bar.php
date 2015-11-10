<?php
	//Check if display post info bar
	$tg_blog_display_bar = kirki_get_option('tg_blog_display_bar');

	if(!empty($tg_blog_display_bar))
	{
		if(has_post_thumbnail(get_the_ID(), 'large'))
		{
		    $image_id = get_post_thumbnail_id(get_the_ID());
		    $image_thumb = wp_get_attachment_image_src($image_id, 'thumbnail', true);
		}
?>
<div id="post_info_bar">
	<div id="post_indicator"></div>
	<div class="standard_wrapper">
		<?php
		    if(isset($image_thumb[0]) && !empty($image_thumb[0]))
		    {
		?>
		<div class="post_info_thumb"><img src="<?php echo esc_url($image_thumb[0]); ?>" alt="" class=""/></div>
		<?php
		    }
		?>
		<div class="post_info">
			<div class="post_info_label"><?php esc_html_e( 'You are reading', 'grandblog-translation' ); ?></div>
			<div class="post_info_title"><h6><?php the_title(); ?></h6></div>
		</div>

<?php /*
		<a id="post_info_comment" href="#comments"><i class="fa fa-comment"></i><?php comments_number(__('No Comment', 'grandblog-translation'), __('1 Comment', 'grandblog-translation'), __('% Comments', 'grandblog-translation')); ?></a>
*/ ?>
		<a id="post_info_share" href="javascript:;"><i class="fa fa-share-alt"></i><?php esc_html_e( 'Share', 'grandblog-translation' ); ?></a>

	</div>
</div>
<?php
	}
?>
