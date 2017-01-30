jQuery(function ($) {
    if ($('#map').length) {
        initMap();
    }
});

var map, directionsService, directionsDisplay;
var poly_spb_kad, poly_VO, poly_Petro, poly_Left, poly_Right;
var order_route = [];

// Санкт-Петербург
var poly_spb_kad_var = [[30.188008,59.984917],[30.192815,60.005382],[30.206204,60.013805],[30.224744,60.010539],[30.23161,60.013289],[30.221997,60.021195],[30.206891,60.027724],[30.211011,60.034768],[30.237446,60.029614],[30.248776,60.032363],[30.223714,60.05297],[30.250836,60.062754],[30.267316,60.061209],[30.304394,60.069961],[30.360699,60.089345],[30.375462,60.09106],[30.382672,60.082828],[30.386449,60.071677],[30.387479,60.065843],[30.392285,60.060351],[30.401211,60.055888],[30.411168,60.05297],[30.424557,60.050051],[30.43623,60.045415],[30.444127,60.036313],[30.452367,60.026522],[30.458546,60.020851],[30.470219,60.016211],[30.476399,60.011227],[30.480519,60.004522],[30.481206,59.996785],[30.488072,59.991453],[30.493565,59.987669],[30.504552,59.984745],[30.520344,59.982508],[30.54163,59.976658],[30.55399,59.971324],[30.557423,59.966332],[30.555707,59.957381],[30.54678,59.947049],[30.54266,59.936713],[30.538197,59.928787],[30.530301,59.918791],[30.528927,59.903446],[30.529614,59.896893],[30.527898,59.887404],[30.533734,59.876188],[30.534764,59.868247],[30.520001,59.85806],[30.511761,59.851152],[30.503178,59.84977],[30.486699,59.848733],[30.492879,59.84286],[30.504895,59.842169],[30.515538,59.838195],[30.520344,59.832665],[30.515195,59.827307],[30.486355,59.837849],[30.477772,59.850633],[30.46095,59.843205],[30.45271,59.835775],[30.443783,59.827653],[30.435887,59.823504],[30.394688,59.815897],[30.363446,59.81313],[30.35143,59.812265],[30.33289,59.807769],[30.316067,59.809498],[30.290729,59.824714],[30.274593,59.830764],[30.255367,59.829554],[30.234424,59.824195],[30.207301,59.821602],[30.138637,59.824023],[30.138637,59.834047],[30.125247,59.835084],[30.127994,59.849424],[30.129711,59.863455],[30.188008,59.984917]];

var poly_VO_var = [[30.259932,59.919203],[30.19573,59.933852],[30.177363,59.944791],[30.209978,59.962715],[30.230921,59.963404],[30.261648,59.956517],[30.282705,59.949543],[30.292834,59.94696],[30.312746,59.945152],[30.309313,59.941707],[30.277413,59.932008]];
var poly_Petro_var = [[30.318068,59.947178],[30.294893,59.947006],[30.224009,59.967573],[30.208732,59.968004],[30.211135,59.978406],[30.250274,59.982277],[30.275336,59.980909],[30.292674,59.982888],[30.320826,59.978672],[30.329974,59.973584],[30.335468,59.965494],[30.337528,59.956628],[30.340617,59.95358]];
var poly_Left_var = [[30.260967,59.91671],[30.268863,59.921537],[30.274528,59.929551],[30.288947,59.934117],[30.30474,59.938941],[30.334781,59.950136],[30.358814,59.951428],[30.36658,59.951686],[30.380313,59.955474],[30.390441,59.957971],[30.402343,59.952117],[30.395476,59.926276],[30.413624,59.910701],[30.438344,59.900009],[30.451218,59.87827],[30.459801,59.871366],[30.477654,59.868173],[30.4907,59.861353],[30.491902,59.852804],[30.467698,59.848832],[30.457055,59.840626],[30.435597,59.823602],[30.411221,59.818934],[30.386502,59.814785],[30.355603,59.814093],[30.341183,59.80951],[30.326077,59.8083],[30.308739,59.81271],[30.281617,59.828961],[30.265481,59.832072],[30.2375,59.825417],[30.207459,59.813228],[30.169522,59.799565],[30.156991,59.795932],[30.139138,59.800516],[29.999564,59.846184],[30.094321,59.884858],[30.211051,59.91935]];
var poly_Right_var = [[30.494196,59.851365],[30.493681,59.859396],[30.491278,59.863108],[30.483038,59.86682],[30.470335,59.870187],[30.461752,59.871646],[30.448191,59.884159],[30.443674,59.895462],[30.433321,59.903485],[30.412722,59.913171],[30.397444,59.927477],[30.404311,59.95478],[30.38663,59.95908],[30.371352,59.951633],[30.344401,59.952451],[30.339766,59.95555],[30.337534,59.959166],[30.333071,59.971129],[30.31848,59.979905],[30.295477,59.983346],[30.27814,59.981453],[30.240717,59.981712],[30.195742,59.976894],[30.157462,59.982572],[29.98005,59.990276],[29.945031,60.028782],[29.951897,60.042523],[30.035668,60.047331],[30.158578,60.0669],[30.190163,60.087829],[30.261574,60.104973],[30.366631,60.096059],[30.384484,60.089201],[30.402729,60.060378],[30.454228,60.041836],[30.48444,60.009535],[30.502293,59.98813],[30.562718,59.973333],[30.537312,59.910633],[30.535252,59.882703],[30.537999,59.864406],[30.506413,59.843527]];


/*
// Нева
var poly_neva_1_var = [[30.508322,59.843532],[30.495275,59.848542],[30.493731,59.8602],[30.484289,59.866761],[30.476908,59.869],[30.461287,59.871417],[30.448927,59.881945],[30.444292,59.895252],[30.436224,59.902496],[30.419229,59.910347],[30.402833,59.921368],[30.395909,59.929635],[30.398312,59.934976],[30.401746,59.938939],[30.404149,59.948037],[30.40535,59.954065],[30.400544,59.957508],[30.385094,59.959144],[30.37943,59.956217],[30.379344,59.956475],[30.372048,59.95329],[30.364838,59.951998],[30.356299,59.952383],[30.339304,59.953588],[30.322138,59.948594],[30.315615,59.945925],[30.30583,59.941111],[30.297076,59.938183],[30.287806,59.93534],[30.277678,59.933273],[30.271498,59.929568],[30.267893,59.924657],[30.265662,59.919572],[30.260397,59.917774],[30.245529,59.920357],[30.218406,59.928112],[30.205532,59.930008],[30.207935,59.919064],[30.260607,59.916149],[30.268668,59.920506],[30.274326,59.929687],[30.281272,59.932577],[30.28856,59.933744],[30.323713,59.946178],[30.345475,59.951031],[30.364797,59.950664],[30.372883,59.952253],[30.385796,59.957324],[30.39536,59.956111],[30.402522,59.95266],[30.399582,59.940142],[30.391643,59.929311],[30.396363,59.923413],[30.414558,59.910924],[30.434126,59.902061],[30.442364,59.893816],[30.447337,59.881808],[30.460372,59.869952],[30.476831,59.8679],[30.484342,59.86483],[30.491468,59.859468],[30.490958,59.851332],[30.493198,59.845335],[30.508322,59.843532]];
// Малая Нева
var poly_neva_2_var = [[30.310546,59.945109],[30.293594,59.946277],[30.278059,59.950152],[30.272136,59.952262],[30.261751,59.956065],[30.252052,59.95826],[30.239263,59.95882],[30.232397,59.962435],[30.206905,59.961876],[30.208536,59.969019],[30.225702,59.96704],[30.241924,59.961252],[30.264025,59.957001],[30.274046,59.953585],[30.28441,59.950512],[30.293873,59.947165],[30.315599,59.946567],[30.310546,59.945109]];

// Большая Нева
var poly_neva_3_var = [[30.339957,59.952684],[30.335966,59.95764],[30.335708,59.961815],[30.335107,59.965603],[30.331417,59.970422],[30.326181,59.976129],[30.319443,59.978817],[30.304809,59.981334],[30.291892,59.982323],[30.275026,59.980903],[30.266786,59.982065],[30.260091,59.982452],[30.245071,59.981764],[30.230394,59.979957],[30.218978,59.978107],[30.20413,59.976],[30.203615,59.977118],[30.237175,59.981204],[30.24962,59.982452],[30.254942,59.982624],[30.261794,59.982688],[30.267959,59.982237],[30.275341,59.981334],[30.28461,59.982839],[30.294137,59.983592],[30.304523,59.982452],[30.321088,59.979484],[30.328126,59.976086],[30.333362,59.971654],[30.336538,59.965803],[30.337217,59.960535],[30.340177,59.955237],[30.345883,59.951401],[30.339957,59.952684]];
*/
function initMap() { //30.328228,59.939784
    var spb = {lat: 59.939784, lng: 30.328228};

    map = new google.maps.Map(document.getElementById('map'), {
        center: spb,
        scrollwheel: true,
        zoom: 10
    });

    directionsDisplay = new google.maps.DirectionsRenderer({
        map: map,
        polylineOptions: {
            strokeColor: "#000099",
            strokeOpacity: 0.5
        }
    });
    directionsService = new google.maps.DirectionsService();


    poly_spb_kad = CratePoly(poly_spb_kad_var, map, '#00FF00');
    poly_VO = CratePoly(poly_VO_var, map, '#ffe708');
    poly_Petro = CratePoly(poly_Petro_var, map, '#d300d6');
    poly_Left = CratePoly(poly_Left_var, map, '#622c09');
    poly_Right = CratePoly(poly_Right_var, map, '#86ffe4');
    // poly_neva_1 = CratePoly(poly_neva_1_var, map, '#0000FF');
    // poly_neva_2 = CratePoly(poly_neva_2_var, map, '#0000FF');
    // poly_neva_3 = CratePoly(poly_neva_3_var, map, '#0000FF');

    calc_route();
}

function CratePoly(poly_arr,map, color){
    var points = ArrayToCoords(poly_arr);
    var poly = new google.maps.Polygon({
        paths: points,
        strokeColor: color,
        strokeOpacity: 0.05,
        strokeWeight: 2,
        fillColor: color,
        fillOpacity: 0.05
    });
    poly.setMap(map);
    return poly;
}

function ArrayToCoords(arr){
    var points = [];
    for (var i = 0; i < arr.length; i++) {
        points.push({
            lat: arr[i][1],
            lng: arr[i][0]
        });
    }
    return points;
}

function calc_route() {
    // Удаляем старые маршруты
    $.each(order_route, function () {
        this.setMap(null);
    });
    var way_points = [];
    var i = 0;
    $('.spb-streets').each(function () {
        // Адреса доставки
        var route_address = $(this).val();
        var route_to_house = $(this).parent().find('.to_house').val();
        var route_to_corpus = $(this).parent().find('.to_corpus').val();
        if (route_address != '') {
            way_points.push({
                location: ('' + route_address + ((route_to_house != '') ? (', ' + route_to_house) : '') + ((route_to_corpus != '') ? (', ' + route_to_corpus) : '')) + ', Ленинградская обл.',
                stopover: true
            });
            i++;
        }
    });

    if (i == 0) {
        bootbox.alert('Необходимо ввести хотя бы один адрес доставки.');
        return false;
    }
    // Начальная точка маршрута
    var origin_point = ($("SELECT.store_address").val() != 0)?$("SELECT.store_address option:selected").text():$('INPUT.store_address_new').val();
    // Конечная точка маршрута
    // var destination_point = route[route.length-1].location;
    // Исклюаем конечную точку маршрута из промежуточных
    var destination_point = way_points.pop();

    directionsService.route({
        origin: origin_point,
        destination: destination_point.location,
        waypoints: way_points,
        region: 'ru',
        optimizeWaypoints: true,
        avoidHighways: true,
        avoidTolls: true,
        travelMode: google.maps.TravelMode.DRIVING
    }, function(response, status) {
        if (status === google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);
            var route = response.routes[0];
            var summaryPanel = document.getElementById('viewContainer');
            summaryPanel.innerHTML = '';
            // For each route, display summary information.
            for (var i = 0; i < route.legs.length; i++) {
                var distanceInSPb = 0;
                var distanceOutSideSPb = 0;
                var neva_cross = false;
                var neva_path = [];
                for (var k = 0; k < route.legs[i].steps.length; k++) {
                    var outside_path = [];
                    var inside_path = [];
                    for (var z = 0; z < route.legs[i].steps[k].lat_lngs.length; z++){
                        var way_latlangs = route.legs[i].steps[k].lat_lngs[z];
                        if (google.maps.geometry.poly.containsLocation(way_latlangs, poly_spb_kad)){
                            inside_path.push(way_latlangs);
                        }else{
                            outside_path.push(way_latlangs);
                        }
                        if (google.maps.geometry.poly.containsLocation(way_latlangs, poly_VO)){
                            neva_path.push('VO');
                        }
                        if (google.maps.geometry.poly.containsLocation(way_latlangs, poly_Petro)){
                            neva_path.push('Petro');
                        }
                        if (google.maps.geometry.poly.containsLocation(way_latlangs, poly_Left)){
                            neva_path.push('Left');
                        }
                        if (google.maps.geometry.poly.containsLocation(way_latlangs, poly_Right)){
                            neva_path.push('Right');
                        }
                    }
                    if (outside_path.length > 0) {
                        var polyline_out = new google.maps.Polyline({ map: map, path: outside_path,
                            strokeColor: "#d60005",strokeOpacity: 0.8, strokeWeight: 3 });
                        distanceOutSideSPb += google.maps.geometry.spherical.computeLength(polyline_out.getPath());
                        // Записываем маршрут в массив для очистки
                        order_route.push(polyline_out);
                    }
                    if (inside_path.length > 0) {
                        var polyline_in = new google.maps.Polyline({ map: map, path: inside_path,
                            strokeColor: "#04d622",strokeOpacity: 0.8, strokeWeight: 2 });
                        distanceInSPb += google.maps.geometry.spherical.computeLength(polyline_in.getPath());
                        // Записываем маршрут в массив для очистки
                        order_route.push(polyline_in);
                    }
                }
                // iLog(neva_path.getUnique());
                // iLog(neva_path.getUnique().length);
                // Если мы сменили за путь несколько районов, то мы пересекали Неву
                if (neva_path.getUnique().length > 1){
                    neva_cross = true;
                }
                var moveList = '';
                var cost_km = 0;
                var cost_km_out = 0;
                var cost_Neva = 0;
                if (distanceInSPb > 0) {
                    cost_km = getRoutePrice(MetersToKilo(distanceInSPb));
                    moveList += ' по городу: ' + MetersToKilo(distanceInSPb) + 'км. (' + cost_km + ' р.);' + '<br/>';
                }
                 if (neva_cross) {
                    cost_Neva = $('input#km_neva').val();
                    moveList += ' пересечение Невы: ' + cost_Neva + ' р.;' + '<br/>';
                }
                if (distanceOutSideSPb > 0) {
                    cost_km_out = getOutKADprice(MetersToKilo(distanceOutSideSPb));
                    moveList += ' за городом: ' + MetersToKilo(distanceOutSideSPb) + 'км. (' + cost_km_out + ' р.) ' + '<br/>';
                }

                // Устанавливаем стоимость по маршруту и выполняем перерасчет
                var cost_route = $('.cost_route').eq(i).get();
                $(cost_route).val(parseFloat(cost_km)+parseFloat(cost_km_out)+parseFloat(cost_Neva));
                re_calc(cost_route);

                var routeSegment = i + 1;
                summaryPanel.innerHTML += '<b>Участок: ' + routeSegment +
                    '</b><br>';
                // summaryPanel.innerHTML += 'От: ' + route.legs[i].start_address + ',<br>';
                // summaryPanel.innerHTML += 'До: ' + route.legs[i].end_address + '<br>';
                summaryPanel.innerHTML += moveList + '<br>';
            }
        } else {
            window.alert('Ошибка построения маршрута: ' + status);
        }
    });
}

function MetersToKilo(number){
    return Math.ceil(number / 100)/10;
}

function getRoutePrice(km_route){
    var cost_res = 0;
    $('input.km_cost').each(function(){
        var km_from = $(this).attr('km_from');
        var km_to = $(this).attr('km_to');
        var km_cost = $(this).val();
        if (km_route >= km_from && km_route < km_to ){
            cost_res = km_cost;
        }
    });
    return Math.ceil(cost_res);
}

function getOutKADprice(dist){
    var cost_km_kad = $('input#km_kad').val();
    return dist*cost_km_kad;
}

Array.prototype.getUnique = function(){
    var u = {}, a = [];
    for(var i = 0, l = this.length; i < l; ++i){
        if(u.hasOwnProperty(this[i])) {
            continue;
        }
        a.push(this[i]);
        u[this[i]] = 1;
    }
    return a;
};

function iLog(text) {
    console.log(text);
}
