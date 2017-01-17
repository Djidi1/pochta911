jQuery(function ($) {
    if ($('#map').length) {
        ymaps.ready(init);
    }
});
var myMap, poly_neva_kad,poly_spb_kad;
var order_route = [];

var poly_neva_kad_var = {
    "type": "Polygon",
    "coordinates": [[[30.49511, 59.855337], [30.478631, 59.868459], [30.458718, 59.869841], [30.447045, 59.884338], [30.440179, 59.89814], [30.420952, 59.909866], [30.399666, 59.922278], [30.39486, 59.933307], [30.402413, 59.945021], [30.402413, 59.956043], [30.383187, 59.958405], [30.368081, 59.951862], [30.345421, 59.951862], [30.336495, 59.956684], [30.333062, 59.967701], [30.326195, 59.975618], [30.310403, 59.980091], [30.28843, 59.98078], [30.263024, 59.978715], [30.234185, 59.978715], [30.203057, 59.978371], [30.183831, 59.98422], [30.179711, 60.002683], [30.193444, 60.014831], [30.138512, 60.051203], [30.154992, 60.063218], [30.176278, 60.070769], [30.187951, 60.07969], [30.227776, 60.08655], [30.255242, 60.095467], [30.277901, 60.099239], [30.31086, 60.096839], [30.358925, 60.094095], [30.374718, 60.088608], [30.420724, 60.047769], [30.440636, 60.03987], [30.452996, 60.024753], [30.463296, 60.016161], [30.476342, 60.011004], [30.480462, 59.992434], [30.494195, 59.984522], [30.52784, 59.979016], [30.549126, 59.971101], [30.553246, 59.963872], [30.538827, 59.933561], [30.526467, 59.907017], [30.525094, 59.894254], [30.52784, 59.882867], [30.532647, 59.872512], [30.52372, 59.864113], [30.508614, 59.853752]]]
};
var poly_spb_kad_var = {
    "type": "Polygon",
    "coordinates": [[[30.188008,59.984917],[30.192815,60.005382],[30.206204,60.013805],[30.224744,60.010539],[30.23161,60.013289],[30.221997,60.021195],[30.206891,60.027724],[30.211011,60.034768],[30.237446,60.029614],[30.248776,60.032363],[30.223714,60.05297],[30.250836,60.062754],[30.267316,60.061209],[30.304394,60.069961],[30.360699,60.089345],[30.375462,60.09106],[30.382672,60.082828],[30.386449,60.071677],[30.387479,60.065843],[30.392285,60.060351],[30.401211,60.055888],[30.411168,60.05297],[30.424557,60.050051],[30.43623,60.045415],[30.444127,60.036313],[30.452367,60.026522],[30.458546,60.020851],[30.470219,60.016211],[30.476399,60.011227],[30.480519,60.004522],[30.481206,59.996785],[30.488072,59.991453],[30.493565,59.987669],[30.504552,59.984745],[30.520344,59.982508],[30.54163,59.976658],[30.55399,59.971324],[30.557423,59.966332],[30.555707,59.957381],[30.54678,59.947049],[30.54266,59.936713],[30.538197,59.928787],[30.530301,59.918791],[30.528927,59.903446],[30.529614,59.896893],[30.527898,59.887404],[30.533734,59.876188],[30.534764,59.868247],[30.520001,59.85806],[30.511761,59.851152],[30.503178,59.84977],[30.486699,59.848733],[30.492879,59.84286],[30.504895,59.842169],[30.515538,59.838195],[30.520344,59.832665],[30.515195,59.827307],[30.486355,59.837849],[30.477772,59.850633],[30.46095,59.843205],[30.45271,59.835775],[30.443783,59.827653],[30.435887,59.823504],[30.394688,59.815897],[30.363446,59.81313],[30.35143,59.812265],[30.33289,59.807769],[30.316067,59.809498],[30.290729,59.824714],[30.274593,59.830764],[30.255367,59.829554],[30.234424,59.824195],[30.207301,59.821602],[30.138637,59.824023],[30.138637,59.834047],[30.125247,59.835084],[30.127994,59.849424],[30.129711,59.863455]]]
};


function init() {
    // Создаем карту с добавленными на нее кнопками.
    myMap = new ymaps.Map('map', {
        center: [30.328228,59.939784],
        zoom: 10,
        controls: []
        // controls: [trafficButton, viaPointButton]
    }, {
        buttonMaxWidth: 300
    });


    // До Невы
    poly_neva_kad = new ymaps.Polygon(poly_neva_kad_var.coordinates, { hintContent: "Зона до невы"  }, {
        fillColor: '#00FF0050',
        strokeWidth: 3
    });
    poly_neva_kad.options.set('visible', true);
    myMap.geoObjects.add(poly_neva_kad);

    // СПб
    poly_spb_kad = new ymaps.Polygon(poly_neva_kad_var.coordinates, { hintContent: "Зона до невы"  }, {
        fillColor: '#00FF0050',
        strokeWidth: 3
    });
    poly_spb_kad.options.set('visible', true);
    myMap.geoObjects.add(poly_spb_kad);


    var order_id = $('#order_id').val();
    if (order_id > 0) {
        calc_route();
    }


}
function calc_route() {
    $(".calc_route").prop('disabled', true);
    var route = [];
    // Начальная точка маршрута
    route.push($("SELECT.store_address option:selected").html());
    var i = 0;
    $('.spb-streets').each(function () {
        // Адреса доставки
        var route_address = $(this).val();
        var route_to_house = $(this).parent().find('.to_house').val();
        var route_to_corpus = $(this).parent().find('.to_corpus').val();
        if (route_address != '') {
            // route.push('Санкт-Петербург, ' + route_address + ((route_to_house != '')?(', '+route_to_house):'') + ((route_to_corpus != '')?(', '+route_to_corpus):''));
            route.push('' + route_address + ((route_to_house != '') ? (', ' + route_to_house) : '') + ((route_to_corpus != '') ? (', ' + route_to_corpus) : ''));
            i++;
        }
    });
    if (i == 0) {
        bootbox.alert('Необходимо ввести хотя бы один адрес доставки.');
        return false;
    }
    show_route(route);
}


function show_route(route_addresses) {
    // Удаляем старые маршруты
    $.each(order_route, function () {
        myMap.geoObjects.remove(this);
    });
    ymaps.route(route_addresses, {
        multiRoute: true,
        wayPointDraggable: true,
        mapStateAutoApply: true,
        avoidTrafficJams: true,
        results: 1
    }).done(function (route) {
        order_route.push(route);
        // myMap.geoObjects.add(route);

        var routes = route.getRoutes().get(0);

        // Объединим в выборку все сегменты маршрута.
        var pathsObjects = ymaps.geoQuery(routes.getPaths().get(0)),
            edges = [];

        // Переберем все сегменты и разобьем их на отрезки.
        pathsObjects.each(function (path) {
            var coordinates = path.geometry.getCoordinates();
            for (var i = 1, l = coordinates.length; i < l; i++) {
                edges.push({
                    type: 'LineString',
                    coordinates: [coordinates[i], coordinates[i - 1]]
                });
            }
        });

        // Создадим новую выборку, содержащую:
        // - отрезки, описываюшие маршрут;
        // - начальную и конечную точки;
        // - промежуточные точки.
        var routeObjects = ymaps.geoQuery(edges)
                .add(route.getWayPoints())
                .add(route.getViaPoints())
                .setOptions('strokeWidth', 1)
                .addToMap(myMap),
            // Найдем все объекты, попадающие внутрь МКАД.
            objectsInZone_1 = routeObjects.searchInside(poly_neva_kad),
            // Найдем объекты, пересекающие МКАД.
            boundaryObjects = routeObjects.searchIntersect(poly_neva_kad);
        // Раскрасим в разные цвета объекты внутри, снаружи и пересекающие МКАД.
        boundaryObjects.setOptions({
            strokeColor: '#06ff00'
            // preset: 'islands#greenIcon'
        });
        objectsInZone_1.setOptions({
            strokeColor: '#ff0005'
            // preset: 'islands#redIcon'
        });
        // Объекты за пределами МКАД получим исключением полученных выборок из
        // исходной.
        routeObjects.remove(objectsInZone_1).remove(boundaryObjects).setOptions({
            strokeColor: '#0010ff'
            // preset: 'islands#blueIcon'
        });


        var moveList = '',
            way;
        // Получаем массив путей.
        // var routes = route.getRoutes().get(0);
        for (var i = 0; i < routes.getPaths().getLength(); i++) {
            way = routes.getPaths().get(i);
            var distance = Math.ceil(parseFloat(way.properties.get("distance").value) / 1000);
            // Расчитываем стоимость по маршруту и выполняем перерасчет
            var cost_km = getRoutePrice(distance);
            var cost_route = $('.cost_route').eq(i).get();
            $(cost_route).val(cost_km * distance);
            re_calc(cost_route);
            moveList += 'Участок №' + (1 + i) + ':' + distance + 'км. (' + cost_km + ' р/км)';
            moveList += '</br>';
        }
        // Выводим маршрутный лист.
        $('#viewContainer').html(moveList);
    }, function (error) {
        alert('Возникла ошибка: ' + error.message);
    });
    $(".calc_route").prop('disabled', false);

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
    return cost_res;
}
function iLog(text) {
    console.log(text);
}
