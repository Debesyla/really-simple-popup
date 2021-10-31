jQuery(function ($) {
	
	if ($.cookie('rs_popup') != 'set') {
		// show popup
		$('#rs-popup').show();
		$('body').toggleClass('rs-popup-open');

		// set cookie if not previous set
		var date = new Date();
		var days = 3650;
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		$.cookie('rs_popup', 'set', {expires: date});
	};
	
	$('#rs-popup .rs__popup-close').on('click', function() {
		$('#rs-popup').hide();
		$('body').toggleClass('rs-popup-open');
	});
	
	document.onkeydown = function(evt) {
		evt = evt || window.event;
		var isEscape = false;
		if ("key" in evt) {
			isEscape = (evt.key === "Escape" || evt.key === "Esc");
		} else {
			isEscape = (evt.keyCode === 27);
		}
		if (isEscape) {
			$('#rs-popup').hide();
		}
	};

});

