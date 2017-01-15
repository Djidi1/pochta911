jQuery(function ($) {
    if ($('#map').length) {
        ymaps.ready(init);
    }
});
var myMap, poly_neva_kad;
var order_route = [];

var poly_neva_kad_var = {
    "type": "Polygon",
    "coordinates": [[[30.49511, 59.855337], [30.478631, 59.868459], [30.458718, 59.869841], [30.447045, 59.884338], [30.440179, 59.89814], [30.420952, 59.909866], [30.399666, 59.922278], [30.39486, 59.933307], [30.402413, 59.945021], [30.402413, 59.956043], [30.383187, 59.958405], [30.368081, 59.951862], [30.345421, 59.951862], [30.336495, 59.956684], [30.333062, 59.967701], [30.326195, 59.975618], [30.310403, 59.980091], [30.28843, 59.98078], [30.263024, 59.978715], [30.234185, 59.978715], [30.203057, 59.978371], [30.183831, 59.98422], [30.179711, 60.002683], [30.193444, 60.014831], [30.138512, 60.051203], [30.154992, 60.063218], [30.176278, 60.070769], [30.187951, 60.07969], [30.227776, 60.08655], [30.255242, 60.095467], [30.277901, 60.099239], [30.31086, 60.096839], [30.358925, 60.094095], [30.374718, 60.088608], [30.420724, 60.047769], [30.440636, 60.03987], [30.452996, 60.024753], [30.463296, 60.016161], [30.476342, 60.011004], [30.480462, 59.992434], [30.494195, 59.984522], [30.52784, 59.979016], [30.549126, 59.971101], [30.553246, 59.963872], [30.538827, 59.933561], [30.526467, 59.907017], [30.525094, 59.894254], [30.52784, 59.882867], [30.532647, 59.872512], [30.52372, 59.864113], [30.508614, 59.853752]]]
};

/*function init(){
 myMap = new ymaps.Map("map", {
 center: [59.939784, 30.328228],
 zoom: 10
 });
 }*/

function init() {
    // Создаем карту с добавленными на нее кнопками.
    myMap = new ymaps.Map('map', {
        center: [59.939784, 30.328228],
        zoom: 10,
        controls: []
        // controls: [trafficButton, viaPointButton]
    }, {
        buttonMaxWidth: 300
    });

    // Создаем многоугольник, используя вспомогательный класс Polygon.
    poly_neva_kad = new ymaps.Polygon(poly_neva_kad_var.coordinates, {
        // Описываем свойства геообъекта.
        // Содержимое балуна.
        hintContent: "Зона 1"
    }, {
        // Задаем опции геообъекта.
        // Цвет заливки.
        fillColor: '#00FF0088',
        // Ширина обводки.
        strokeWidth: 5
    });
    poly_neva_kad.options.set('visible', true);
    // Добавляем многоугольник на карту.
    myMap.geoObjects.add(poly_neva_kad);


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
        var cost_km = 25;
        for (var i = 0; i < routes.getPaths().getLength(); i++) {
            way = routes.getPaths().get(i);
            var distance = Math.ceil(parseFloat(way.properties.get("distance").value) / 1000);
            // Расчитываем стоимость по маршруту и выполняем перерасчет
            var cost_route = $('.cost_route').eq(i).get();
            $(cost_route).val(cost_km * distance);
            re_calc(cost_route);
            moveList += 'Участок №' + (1 + i) + ':' + distance + 'км.';
            moveList += '</br>';
        }
        // Выводим маршрутный лист.
        $('#viewContainer').html(moveList);
    }, function (error) {
        alert('Возникла ошибка: ' + error.message);
    });
    $(".calc_route").prop('disabled', false);

}

function iLog(text) {
    console.log(text);
}
