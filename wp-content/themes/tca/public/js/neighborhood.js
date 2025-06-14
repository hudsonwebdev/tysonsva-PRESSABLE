/******/ (() => { // webpackBootstrap
/*!********************************!*\
  !*** ./src/js/neighborhood.js ***!
  \********************************/
jQuery(document).ready(function ($) {
  const url = window.location.href;
  function getLastPartOfUrl(url) {
    const urlParts = url.split('/'); // Split the URL by "/"

    // Get the last part of the URL, ignoring any trailing slash
    let lastPart = urlParts[urlParts.length - 1];

    // If the last part is empty (indicating a trailing slash), get the second-to-last part
    if (lastPart === '') {
      lastPart = urlParts[urlParts.length - 2];
    }
    return lastPart; // Return the last part or the final slug
  }
  var currenturl = getLastPartOfUrl(url);
  $('#neighborhoodSelect option').each(function () {
    var optionurl = getLastPartOfUrl($(this).val());
    if (optionurl == currenturl) {
      $(this).prop('selected', true);
    }
  });

  // Submit the form when the selection changes
  $('#neighborhoodSelect').change(function () {
    var selectedValue = $(this).val(); // Get the selected option value
    $('#neighborhoodForm').attr('action', selectedValue); // Update the form action to the selected value
    $('#neighborhoodForm').submit(); // Submit the form to the selected URL
  });
  if ($('#neighborhood-map').length) {
    var activeid = '';
    var initialDesktop = [-77.227, 38.926];
    var initialMobile = [-77.227, 38.94];
    mapboxgl.accessToken = 'pk.eyJ1IjoidGNhc29mdHdhcmUiLCJhIjoiY2xzdWd6MHVpMTFxajJ2cjA4cnh6cmZ5cCJ9.e9dAUpCID8DqXU5WD0QOxw';
    var map = new mapboxgl.Map({
      container: 'neighborhood-map',
      // container ID
      style: 'mapbox://styles/tcasoftware/cm7unqmeq01px01qo3jv83ql2',
      // style URL
      center: initialDesktop,
      // starting position [lng, lat]
      zoom: 13.3,
      // starting zoom
      dragPan: false
    });
    map.scrollZoom.disable();
    $(window).smartresize(function () {
      if (document.documentElement.clientWidth > 768) {
        map.flyTo({
          center: initialDesktop,
          zoom: 13.2
        });
      } else {
        map.flyTo({
          center: initialMobile,
          zoom: 12.3
        });
      }
    });
    if (document.documentElement.clientWidth > 768) {
      map.flyTo({
        center: initialDesktop,
        zoom: 13.2
      });
    } else {
      map.flyTo({
        center: initialMobile,
        zoom: 12.3
      });
    }
    map.on('load', function () {
      var layers = map.getStyle().layers;
      var firstSymbolId;
      for (var i = 0; i < layers.length; i++) {
        if (layers[i].type === 'symbol') {
          firstSymbolId = layers[i].id;
          break;
        }
      }
      $.getJSON('/wp-json/neighborhood/geojson', function (data) {
        var neighborhoodgeojson = JSON.parse(data);
        storeDZ = [];
        var count = 0;
        neighborhoodgeojson.features.forEach(function (shape) {
          var thisid = shape.properties.id.toString();
          var opacity = 0;
          if (shape.properties.slug == currenturl) {
            activeid = thisid;
            opacity = 0;
          }
          count++;
          storeDZ[thisid] = {
            'id': thisid,
            'type': 'fill',
            'source': {
              'type': 'geojson',
              'data': {
                'properties': shape.properties,
                'type': 'Feature',
                'geometry': {
                  'type': 'Polygon',
                  'coordinates': shape.coords
                }
              }
            },
            'layout': {},
            'paint': {
              'fill-color': '#00B852',
              'fill-opacity': opacity
            }
          };
          map.addLayer(storeDZ[thisid], firstSymbolId);
          thisidline = thisid + 'line';
          storeDZ[thisidline] = {
            'id': thisidline,
            'type': 'line',
            'source': {
              'type': 'geojson',
              'data': {
                'properties': shape.properties,
                'type': 'Feature',
                'geometry': {
                  'type': 'Polygon',
                  'coordinates': shape.coords
                }
              }
            },
            'layout': {},
            'paint': {
              'line-color': '#ffffff',
              //shape.properties.stroke,
              'line-width': 2,
              'line-dasharray': [1, 2]
            }
          };
          map.addLayer(storeDZ[thisidline], firstSymbolId);
          map.on('click', thisid, function (e) {
            window.location.href = '/discover-tysons/neighborhood-guide/' + storeDZ[thisid].source.data.properties.slug;

            /*
                   	activeid = thisid;
            
               		for (var key in storeDZ) {
                     
                         if($.isNumeric(key)){
                               var fillColor = storeDZ[key].paint['fill-color'];
                               map.setPaintProperty(key, 'fill-opacity', 0);
                            
                         }
                     
                     }
                      var fc = storeDZ[thisid].paint['fill-color']
              
                      $('.neighborhood-cards').css('background-color',fc);
              		 
              		 map.setPaintProperty(thisid, 'fill-opacity', 0.8);
               	
            */
          });
          map.on('mouseenter', thisid, function (e) {
            map.setPaintProperty(thisid, 'fill-opacity', 0.8);
          });
          map.on('mouseleave', thisid, function (e) {
            if (thisid != activeid) {
              map.setPaintProperty(thisid, 'fill-opacity', 0);
            }
          });
        });
      });
    });
  }
});
/******/ })()
;
//# sourceMappingURL=neighborhood.js.map