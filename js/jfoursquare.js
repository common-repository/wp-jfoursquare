jQuery(document).ready(function($){

	$.fn.jFoursqareFader = function() {
		this.each(function() {
			var $this = $(this);
			var rotatetime = $this.data('rotatetime');
			$this.children(':gt(0)').hide();

			setInterval(function() {
				$this.children().eq(0)
				.fadeOut().next().fadeIn().end().appendTo($this);
			}, rotatetime || 10000);
		});
	};

	$('.jfoursquare-feed').jFoursqareFader();

});