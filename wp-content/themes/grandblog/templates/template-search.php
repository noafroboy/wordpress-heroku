<?php 
if(is_search())
{
?>
<div class="search_form_wrapper">
    <?php esc_html_e( "Search results for", 'grandblog-translation' ); ?> <strong><?php the_search_query(); ?></strong>. <?php esc_html_e( "If you didn't find what you were looking for, try a new search.", 'grandblog-translation' ); ?><br/><br/>
    
    <form class="searchform" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
    	<input style="width:100%" type="text" class="field searchform-s" name="s" value="<?php the_search_query(); ?>" title="<?php esc_html_e( 'Type to search and hit enter...', 'grandblog-translation' ); ?>">
    </form>
</div>
<?php
}
?>