
window.onload = function() {
	$ = jQuery;

	/**
	* This code here rewrites the href to the "preview" blog post link in the editor
	*/
	var siteUrl = $('.ooga').data('site-url'),
		homeUrl = $('.ooga').data('home-url'),
		$previewAnchor = $('a.preview.button');

	var previewUrl = $previewAnchor.attr('href');
	if (previewUrl) {
		previewUrl = previewUrl.replace(homeUrl, siteUrl);
		$previewAnchor.attr('href', previewUrl);		
	}
}
