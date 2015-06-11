if (!window.application) {
	window.application = {};
}

var addrLocation, map, infoWindow, locMarkers, locInfoTpl, officeLists;
var maxDist = 5; //miles
var gmapMarkers = [];

if (typeof Number.prototype.toRadians == 'undefined') {
	Number.prototype.toRadians = function() { return this * Math.PI / 180; }
}

function codeAddress(address) {
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode( 
		{ 'address': address }, 
		function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				addrLocation = results[0].geometry.location;
				setMapCenter(addrLocation, 11);
				showMapResults();
			}
		}
	);
}

function latLngDistance(lat1, lng1, lat2, lng2) {
	var r = 6371; // km
	lat1 = new Number(lat1).toRadians();
	lng1 = new Number(lng1).toRadians();
	lat2 = new Number(lat2).toRadians();
	lng2 = new Number(lng2).toRadians();
	var latdiff = (lat2-lat1);
	var lngdiff = (lng2-lng1);
	
	var a = Math.sin(latdiff / 2) * Math.sin(latdiff / 2) +
	        Math.cos(lat1) * Math.cos(lat2) *
	        Math.sin(lngdiff / 2) * Math.sin(lngdiff / 2);
	var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
	return r * c;
}

function kmToMi(km) {
	return km * 0.621371;
}

function plotPoint(mrk, num, gmapMrk) {
	var name = mrk.getAttribute('name');
	var address = mrk.getAttribute('addrln1') + ' ' + mrk.getAttribute('addrln2');
	var html = '<div style="color: #404040;"><b>' + name + ' (' + num + ')</b> <br/>' + address + '</div>';
	gmapMrk.setMap(map);
	bindInfoWindow(gmapMrk, map, infoWindow, html);
}

function addInfo(mrk, num, mid) {
	var tpl = locInfoTpl;
	tpl = tpl.replace('{mid}', mid);
	tpl = tpl.replace(/\{num\}/g, num);
	tpl = tpl.replace('{name}', mrk.getAttribute('name'));
	tpl = tpl.replace('{addrln1}', mrk.getAttribute('addrln1'));
	tpl = tpl.replace('{addrln2}', mrk.getAttribute('addrln2'));
	tpl = tpl.replace('{phone}', mrk.getAttribute('phone'));
	tpl = tpl.replace('{specs}', getMarkerSpecNames(mrk).join(', '));
	for (var i = officeLists.length - 1; i >= 0; --i) {
		if ((i == 0) || (officeLists[i].find('li').length < officeLists[i - 1].find('li').length)) {
			officeLists[i].append(tpl);
			break;
		}
	}
}

function bindInfoWindow(marker, map, infoWindow, html) {
	google.maps.event.addListener(marker, 'click', function() {
		infoWindow.setContent(html);
		infoWindow.open(map, marker);
	});
}

function getMarkerSpecs(mrk) {
	var specs = mrk.getElementsByTagName('specialty');
	var ret = {};
	for (var i = 0; i < specs.length; ++i) {
		ret[specs[i].getAttribute('id')] = 1;
	}
	return ret;
}

function getMarkerSpecNames(mrk) {
	var specs = mrk.getElementsByTagName('specialty');
	var ret = [];
	for (var i = 0; i < specs.length; ++i) {
		ret.push(specs[i].getAttribute('name'));
	}
	return ret;
}

function setSpecBubbles() {
	jQuery('#office-list-left li a').each(function() {
		jQuery(this).bubbletip(jQuery(this).parent().find('div.specialty-bubble'), {
			deltaDirection: 'right',
			offsetTop: -40
		});
	});
	jQuery('#office-list-right li a').each(function() {
		jQuery(this).bubbletip(jQuery(this).parent().find('div.specialty-bubble'), {
			deltaDirection: 'left',
			offsetTop: -40
		});
	});
}

function showMapResults() {
	emptyOfficeLists();
	jQuery('#loading-text').hide();
	var type = jQuery('#fld-specialty').val();
	var num = 1;
	var results = {};
	var distances = [];
	var i, mrk, dist, specs;
	for (i = 0; i < locMarkers.length; ++i) {
		mrk = locMarkers[i];
		specs = getMarkerSpecs(mrk);
		if (type && !specs[type]) {
			continue;
		}
		if (addrLocation) {
			dist = kmToMi(latLngDistance(addrLocation.lat(), addrLocation.lng(), mrk.getAttribute('lat'), mrk.getAttribute('lng')));
			/*if (dist > maxDist) {
				continue;
			}*/
			results['' + dist] = {'marker': mrk, 'gmap': gmapMarkers[i], 'mid': i};
			distances.push(dist);
		}
		else {
			/*if (num > 6) {
				break;
			}*/
			plotPoint(mrk, num, gmapMarkers[i]);
			addInfo(mrk, num, i);
		}
		++num;
	}
	if (distances.length) {
		var res;
		distances.sort(function(a, b){return a-b;});
		for (i = 0; i < distances.length; ++i) {
			if (i > 5) {
				break;
			}
			res = results['' + distances[i]];
			plotPoint(res.marker, i + 1, res.gmap);
			addInfo(res.marker, i + 1, res.mid);
		}
	}
	setSpecBubbles();
}

function setMapCenter(LatLng, zm) {
	zm = (zm ? zm : 9);
	if (map) {
		map.setCenter(LatLng);
		map.setZoom(zm);
	}
	else {
		map = new google.maps.Map(document.getElementById('office-gmap'), {
			zoom: zm,
			mapTypeId: 'roadmap',
			center: LatLng
		});
	}
}

function clearAllMarkers() {
	infoWindow.close();
	if (gmapMarkers.length) {
		for (var i = 0; i < gmapMarkers.length; ++i) {
			if (gmapMarkers[i].setMap) {
				gmapMarkers[i].setMap(null);
			}
		}
	}
}

function emptyOfficeLists() {
	jQuery('section.contacts div.list-holder li a').each(function() {
		//jQuery.fn.removeBubbletip(jQuery(this).attr('data-bubbletip_tips'));
		jQuery(this).removeBubbletip();
	});
	for (var i = 0; i < officeLists.length; ++i) {
		officeLists[i].empty();
	}
}

window.application.searchAjax = function () {
	'use strict';

	var form = jQuery('.contacts form.search'),
		text = form.find('.type .text'),
		holder = form.siblings('.list-holder'),
		input = form.find('#fld-specialty'),
		links = jQuery('[data-search]'),
		items = links.closest('li'),
		activeClass = 'active',
		loader = holder.find('.loader'),
		url = form.attr('action');
	infoWindow = new google.maps.InfoWindow;
	locInfoTpl = jQuery('#list-item-tpl').html();
	officeLists = [
		jQuery('#office-list-left'),
		jQuery('#office-list-right')
	];
	

	if (!form.length) {
		return;
	}
	
	//load the offices and save their info
	jQuery.ajax({
		type: 'GET',
		url: wpThemeRoot + 'locations.xml',
		dataType: 'xml',
		success: function(data) {
			locMarkers = data.documentElement.getElementsByTagName('marker');
			if (locMarkers.length) {
				for (var i = 0; i < locMarkers.length; ++i) {
					gmapMarkers[i] = new google.maps.Marker({
						map: null,
						position: new google.maps.LatLng(parseFloat(locMarkers[i].getAttribute("lat")), parseFloat(locMarkers[i].getAttribute("lng")))
					});
				}
				//setMapCenter(new google.maps.LatLng(locMarkers[0].getAttribute('lat'), locMarkers[0].getAttribute('lng')));
				setMapCenter(new google.maps.LatLng(40.736210, -73.911231));
				showMapResults();
			}
		}
	});

	jQuery(document).on('click', 'div.container ul.list li', function() {
		var mid = jQuery(this).attr('data-mid');
		setMapCenter(new google.maps.LatLng(parseFloat(locMarkers[mid].getAttribute("lat")), parseFloat(locMarkers[mid].getAttribute("lng"))), 11);
		new google.maps.event.trigger(gmapMarkers[mid], 'click');
		window.scrollTo(0, 150);
	});
	
	links.on({
		'click': function (event) {
			var curText = jQuery.trim(jQuery(this).text());
			event.preventDefault();
			items.removeClass(activeClass).has(this).addClass(activeClass);
			input.val(jQuery(this).attr('data-sid'));
			text.text(curText);
		}
	});

	form.on({
		'submit': function (e) {
			e.preventDefault();
			e.stopPropagation();
			jQuery('#loading-text').show();
			
			//if the locations havent finished loading, try again in 1 second
			if (!locMarkers) {
				window.setTimeout(function() {
					jQuery('#frm-office-search').trigger('submit');
				}, 1000);
				return false;
			}
			
			clearAllMarkers();
			var loc = jQuery('#fld-location').val();
			addrLocation = null;
			if (loc) {
				codeAddress(loc);
			}
			else {
				//setMapCenter(new google.maps.LatLng(locMarkers[0].getAttribute('lat'), locMarkers[0].getAttribute('lng')));
				setMapCenter(new google.maps.LatLng(40.736210, -73.911231));
				showMapResults();
			}
			
			return false;
		}
	});
};