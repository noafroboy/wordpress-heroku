<?php header("content-type: application/x-javascript"); ?>
<?php
$absolute_path = __FILE__;
$path_to_file = explode( 'wp-content', $absolute_path );
$path_to_wp = $path_to_file[0];
require_once( $path_to_wp.'/wp-load.php' );

$current_query = '';
$items = 1;
$columns = 2;

if(isset($_GET['current_query']))
{
	$current_query = $_GET['current_query'];
}

if(isset($_GET['items']))
{
	$items = $_GET['items'];
}

if(isset($_GET['items_ini']))
{
	$items_ini = $_GET['items_ini'];
}

if(isset($_GET['columns']))
{
	$columns = $_GET['columns'];
}

if(isset($_GET['action']))
{
	$action = $_GET['action'];
}
?>
function loadInfinitePost()	
{
	if(jQuery('#infinite_loading_status').val() == 0)
	{
		var currentOffset = parseInt(jQuery('#infinite_loading_offset').val());
		jQuery('#infinite_loading').addClass('visible');
	
		jQuery.ajax({
	        url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
	        type:'POST',
	        data: 'action=<?php echo esc_js($action); ?>&current_query=<?php echo stripslashes($current_query); ?>&items=<?php echo esc_js($items); ?>&items_ini=<?php echo esc_js($items_ini); ?>&offset='+currentOffset+'&columns=<?php echo esc_js($columns); ?>&tg_security='+tgAjax.ajax_nonce, 
	        success: function(html)
	        {
	        	jQuery('#infinite_loading_offset').val(parseInt(currentOffset+<?php echo esc_js($items_ini); ?>));
	        	jQuery('#infinite_scroll_wrapper').append(html);
				jQuery('#infinite_loading').removeClass('visible');
	        }
	    });
	}
}

jQuery(window).load(function(){ 
	jQuery(document).ajaxStart(function() {
	  	jQuery('#infinite_loading_status').val(1);
	});
	
	jQuery(document).ajaxStop(function() {
	  	jQuery('#infinite_loading_status').val(0);
	});

	if (jQuery(document).height() <= jQuery(window).height())
	{
        var currentOffset = parseInt(jQuery('#infinite_loading_offset').val());
		var total = parseInt(jQuery('#infinite_loading_total').val());
		
		if (currentOffset > total)
	    {
	        return false;
	    }
	    else
	    {
	        loadInfinitePost();
	    }
    }

	jQuery(window).on('scroll', function() {
		var currentOffset = parseInt(jQuery('#infinite_loading_offset').val());
		var total = parseInt(jQuery('#infinite_loading_total').val());
		
		wrapperHeight = jQuery('#infinite_scroll_wrapper').height()+jQuery('#infinite_scroll_wrapper').offset().top;
	
	    if(jQuery(window).scrollTop() >= parseInt(jQuery(document).height() - wrapperHeight))
	    {
	    	if (currentOffset >= total)
	    	{
	    		return false;
	    	}
	    	else
	    	{
	    		loadInfinitePost();
	    	}
	    }
	});
});