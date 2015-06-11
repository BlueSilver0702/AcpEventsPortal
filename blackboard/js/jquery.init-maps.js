if (!window.application) {
	window.application = {};
}

window.application.initMaps = function () {
	'use strict';

	if (typeof google !== 'undefined') {
		var mapOptions = {
			zoom: 8,
			center: new google.maps.LatLng(-34.397, 150.644)
		};
		new google.maps.Map(jQuery('.contacts .map').empty()[0], mapOptions);
	}
};