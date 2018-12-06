<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=ABQIAAAARVX9GWAQfeAbMRKpg38tgBQFKq6KJXeqPzztW-ljyG2taL5EwhQ-d1EKv6N4vGjoeKmdl1pTnz41UA&amp;hl=ru"
type="text/javascript"></script>

<script type="text/javascript">
//<![CDATA[

var map = null;
var geocoder = null;
var markerOptions = null;

function initialize() {
	if (GBrowserIsCompatible()) {
		map = new GMap2(document.getElementById("map"));
		geocoder = new GClientGeocoder();

		map.addControl(new GLargeMapControl());
		var bottomLeft = new GControlPosition(G_ANCHOR_BOTTOM_LEFT, new GSize(10,50));
		map.addControl(new GScaleControl(), bottomLeft);
		map.addControl(new GMapTypeControl());
		map.removeMapType(G_HYBRID_MAP);
		map.addControl(new GOverviewMapControl());
		map.enableScrollWheelZoom();

		/*GDownloadUrl("http://dmitry-ponomarev.ru/main/geo.php", function(data) {
			var xml = GXml.parse(data);
			var markers = xml.documentElement.getElementsByTagName("marker");
			for (var i = 0; i < markers.length; i++) {
				var name = markers[i].getAttribute("name");
				var address = markers[i].getAttribute("address");
				var type = markers[i].getAttribute("type");
				var point = new GLatLng(parseFloat(markers[i].getAttribute("lat")),
				parseFloat(markers[i].getAttribute("lng")));
				var marker = createMarker(point, name, address, type);
				map.addOverlay(marker);
			}
		});*/

		var point = new GLatLng(59.866345, 30.321924);
		map.setCenter(point, 12);

		var tinyIcon = new GIcon();
		tinyIcon.image = "http://dmitry-ponomarev.ru/img/map/yu.gif";
		tinyIcon.iconSize = new GSize(30, 24);
		tinyIcon.iconAnchor = new GPoint(6, 20);
		tinyIcon.infoWindowAnchor = new GPoint(5, 1);

		var iconBlue = new GIcon();
		iconBlue.image = 'http://labs.google.com/ridefinder/images/mm_20_blue.png';
		iconBlue.shadow = 'http://labs.google.com/ridefinder/images/mm_20_shadow.png';
		iconBlue.iconSize = new GSize(12, 20);
		iconBlue.shadowSize = new GSize(22, 20);
		iconBlue.iconAnchor = new GPoint(6, 20);
		iconBlue.infoWindowAnchor = new GPoint(5, 1);

		var iconRed = new GIcon();
		iconRed.image = 'http://labs.google.com/ridefinder/images/mm_20_red.png';
		iconRed.shadow = 'http://labs.google.com/ridefinder/images/mm_20_shadow.png';
		iconRed.iconSize = new GSize(12, 20);
		iconRed.shadowSize = new GSize(22, 20);
		iconRed.iconAnchor = new GPoint(6, 20);
		iconRed.infoWindowAnchor = new GPoint(5, 1);

		var customIcons = [];
		customIcons["restaurant"] = iconBlue;
		customIcons["bar"] = iconRed;

		markerOptions = {draggable: true, icon:tinyIcon};

		GEvent.addListener(map, "click", function(overlay,point) {
			if(point) {
				// GLog.writeHtml("Центр в: " + map.getCenter().toString() + " Щелчок в: " + point.toString());
				GLog.writeHtml(point.lat().toString()+','+point.lng().toString());
			}
		});

		var marker = new GMarker(new GLatLng(59.866345, 30.321924), markerOptions);

		GEvent.addListener(marker, "dragstart", function() {
			map.closeInfoWindow();
		});

		GEvent.addListener(marker, "dragend", function() {
			GLog.writeHtml("Координаты: " + marker.getLatLng().toString());
			marker.openInfoWindowHtml("Координаты: " + marker.getLatLng().toString());
		});
		map.addOverlay(marker);
		marker.openInfoWindowHtml("Центр: <b>" + point.toString() + '</b><br />Перетащите маркер в то место где хотите определить координаты!<br />Щелкните на карте для определения координаты в месте щелчка!');

	}
}

function createMarker(point, name, address, type) {
	var marker = new GMarker(point, customIcons[type]);
	var html = "<b>" + name + "</b> <br/>" + address;
	GEvent.addListener(marker, 'click', function() {
		marker.openInfoWindowHtml(html);
	});
	return marker;
}

function showAddress(address) {
	if (geocoder) {
		geocoder.getLatLng(
		address,
		function(point) {
			if (!point) {
				alert(address + " Не найден");
			} else {
				map.setCenter(point);
				var marker = new GMarker(point, markerOptions);

				GEvent.addListener(marker, "dragstart", function() {
					map.closeInfoWindow();
				});

				GEvent.addListener(marker, "dragend", function() {
					GLog.writeHtml("Координаты: " + marker.getLatLng().toString());
					marker.openInfoWindowHtml("Координаты: " + marker.getLatLng().toString());
				});

				map.clearOverlays();
				map.addOverlay(marker);
				marker.openInfoWindowHtml("Центр: <b>" + map.getCenter().toString() + '</b><br />Перетащите маркер в то место где хотите определить координаты!<br />Щелкните на карте для определения координаты в месте щелчка!');
				GLog.writeHtml("Центр: " + map.getCenter().toString());
			}
		}
		);
	}
}

//]]>
</script>