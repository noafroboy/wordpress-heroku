
window.onload = function() {
	$ = jQuery;
	var siteUrl = $('.ooga').data('site-url'),
		homeUrl = $('.ooga').data('home-url'),
		$previewAnchor = $('a.preview.button');

	var previewUrl = $previewAnchor.attr('href');

	if (previewUrl) {
		previewUrl = previewUrl.replace(homeUrl, siteUrl);
		$previewAnchor.attr('href', previewUrl);		
	}
}
