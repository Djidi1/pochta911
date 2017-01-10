jQuery(function ($) {
    if ($('#map').length) {
        ymaps.ready(init);
    }
});
var myMap;
var order_route = [];

/*function init(){
    myMap = new ymaps.Map("map", {
        center: [59.939784, 30.328228],
        zoom: 10
    });
}*/

function init () {
    // Создаем карту с добавленными на нее кнопками.
    myMap = new ymaps.Map('map', {
        center: [59.939784, 30.328228],
        zoom: 10,
        controls: []
        // controls: [trafficButton, viaPointButton]
    }, {
        buttonMaxWidth: 300
    });

    var order_id = $('#order_id').val();
    if (order_id > 0){
        calc_route();
    }

}
function calc_route(){
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
            route.push('' + route_address + ((route_to_house != '')?(', '+route_to_house):'') + ((route_to_corpus != '')?(', '+route_to_corpus):''));
            i++;
        }
    });
    if (i == 0){
        bootbox.alert('Необходимо ввести хотя бы один адрес доставки.');
        return false;
    }
    show_route(route);
}


function show_route(route_addresses){
    // Удаляем старые маршруты
    $.each(order_route,function () {
        myMap.geoObjects.remove(this);
    });
    ymaps.route(route_addresses, {
        multiRoute: true,
        wayPointDraggable: true
    }).done(function (route) {
        order_route.push(route);
        myMap.geoObjects.add(route);

        var moveList = '',
            way;
        // Получаем массив путей.
        var routes = route.getRoutes().get(0);
        var cost_km = 25;
        for (var i = 0; i < routes.getPaths().getLength(); i++) {
            way = routes.getPaths().get(i);
            var distance = Math.ceil(parseFloat(way.properties.get("distance").value) / 1000);
            // Расчитываем стоимость по маршруту и выполняем перерасчет
            var cost_route = $('.cost_route').eq(i).get();
            $(cost_route).val(cost_km*distance);
            re_calc(cost_route);
            moveList += 'Участок №'+(1+i)+':' + distance + 'км.';
            moveList += '</br>';
        }
        // Выводим маршрутный лист.
        $('#viewContainer').html(moveList);
    }, function (error) {
        alert('Возникла ошибка: ' + error.message);
    });
    $(".calc_route").prop('disabled', false);

    // var multiRoute = new ymaps.multiRouter.MultiRoute({
    //     // Описание опорных точек мультимаршрута.
    //     referencePoints: route,
    //     // Параметры маршрутизации.
    //     params: {
    //         // Ограничение на максимальное количество маршрутов, возвращаемое маршрутизатором.
    //         results: 2
    //     }
    // }, {
    //     // Автоматически устанавливать границы карты так, чтобы маршрут был виден целиком.
    //     wayPointDraggable: true,
    //     boundsAutoApply: true
    // });

/*    // Создаем кнопки для управления мультимаршрутом.
    var trafficButton = new ymaps.control.Button({
            data: { content: "Учитывать пробки" },
            options: { selectOnClick: true }
        }),
        viaPointButton = new ymaps.control.Button({
            data: { content: "Добавить транзитную точку" },
            options: { selectOnClick: true }
        });

    // Объявляем обработчики для кнопок.
    trafficButton.events.add('select', function () {

        multiRoute.model.setParams({ avoidTrafficJams: true }, true);
    });

    trafficButton.events.add('deselect', function () {
        multiRoute.model.setParams({ avoidTrafficJams: false }, true);
    });

    viaPointButton.events.add('select', function () {
        var referencePoints = multiRoute.model.getReferencePoints();
        referencePoints.splice(1, 0, "Санкт-Петербург, наб. Фонтанки, 123");

        multiRoute.model.setReferencePoints(referencePoints, [1]);
    });

    viaPointButton.events.add('deselect', function () {
        var referencePoints = multiRoute.model.getReferencePoints();
        referencePoints.splice(1, 1);
        multiRoute.model.setReferencePoints(referencePoints, []);
    });
    */
    // Добавляем мультимаршрут на карту.
    // myMap.geoObjects.add(multiRoute);
    /*

    // Подписываемся на события модели мультимаршрута.
    multiRoute.model.events.add("requestsend", function (event) {
        iLog('Отправлены данные на просчет, ожидаем ответ...');
    });
    multiRoute.model.events.add("requestsuccess", function (event) {
        iLog('Просчет получен.');
        iLog('Общая длина маршрута составляет: '+multiRoute.getRoutes().get(0).properties.get('distance').text); // ВЕСЬ маршрут   .value

        // ждем, пока закончит просчет всего маршрута
        for(var i=0; i<route.length; i++) {
         //   iLog('длина '+i+' составляет: '+multiRoute.getRoutes().get(0).getSegments().get(i).properties.get('distance').text);
            var way = multiRoute.getWayPoints();
            console.log(way);
            console.log(way.get(i));
            console.log(way.get(i).properties);
            // var test1 = way.getLength();
            // var test2 = way.get(0).properties.get('distance').text;
            // var distance = segments[i].getLength();
            // console.log(distance);
            // console.log(multiRoute.getWayPoints().get(i).properties.get('distance'));
        }
        console.log(multiRoute.getRoutes().get(0).model);
    });
    */
}

function iLog(text) {
    console.log(text);
}
