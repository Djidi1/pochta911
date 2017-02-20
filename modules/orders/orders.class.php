<?php

class ordersModel extends module_model {
	public function __construct($modName) {
		parent::__construct ( $modName );
	}

	public function get_assoc_array($sql){
		$this->query ( $sql );
		$items = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
			if (isset($row['to'])) $row['to'] = str_replace('г. Санкт-Петербург,','',$row['to']);
			if (isset($row['ready'])) $row['ready'] = substr($row['ready'],0,5);
			if (isset($row['to_time'])) $row['to_time'] = substr($row['to_time'],0,5);
			if (isset($row['to_time_end'])) $row['to_time_end'] = substr($row['to_time_end'],0,5);
			if (isset($row['to_time_ready'])) $row['to_time_ready'] = substr($row['to_time_ready'],0,5);
			$items[] = $row;
		}
		return $items;
	}

	public function exportToExcel($titles,$orders){
        require_once CORE_ROOT . 'classes/PHPExcel.php';
        // Instantiate a new PHPExcel object
        $objPHPExcel = new PHPExcel();

        $style = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'wrap' => true
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '333333'),
                'size'  => 10,
                'name'  => 'Verdana'
            )
        );
        $objPHPExcel->setActiveSheetIndex(0);
// Записываем заголовки со второй строки
        $colCount = 'A';
        foreach ($titles as $col_value) {
            $cell = $colCount.'1';
            $objPHPExcel->getActiveSheet()->SetCellValue($cell, $col_value);
            $objPHPExcel->getActiveSheet()->getStyle($colCount."1")->applyFromArray($style);
            $objPHPExcel->getActiveSheet()->getColumnDimension($colCount)->setAutoSize(true);
            $colCount++;
        }
//        $objPHPExcel->getActiveSheet()->getStyle("A1:".$colCount."1")->applyFromArray($style);
// Записываем данные со второй строки
        $rowCount = 2;
        foreach($orders as $order){
            $colCount = 'A';
            foreach ($order as $col_value) {
                $cell = $colCount.$rowCount;
                $objPHPExcel->getActiveSheet()->SetCellValue($cell, $col_value);
//                $objPHPExcel->getActiveSheet()->getColumnDimension($colCount)->setAutoSize(true);
                $colCount++;
            }
            $rowCount++;
        }
        $file_name = 'orders'.date('_Y_m_d').'.xlsx';
// Instantiate a Writer to create an OfficeOpenXML Excel .xlsx file
//        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
// Write the Excel file to filename some_excel_file.xlsx in the current directory
//        $objWriter->save('orders'.date('_Y_m_d').'.xlsx');
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }


    public function getClientTitle($user_id){
		$sql = 'SELECT
				  title
				FROM users
				WHERE id = '.$user_id;
		return $this->get_assoc_array($sql);
	}

	public function getStores($user_id) {
		$sql = 'SELECT
				  id,
				  address
				FROM users_address
				WHERE user_id = '.$user_id;
		return $this->get_assoc_array($sql);
	}
	public function getStatuses() {
		$sql = 'SELECT
				  id,
				  status
				FROM orders_status ';
		return $this->get_assoc_array($sql);
	}
	public function getCarCouriers() {
		$sql = 'SELECT
				  id,
				  fio, car_number
				FROM cars_couriers WHERE isBan != 1';
		return $this->get_assoc_array($sql);
	}
	public function getCouriers() {
		$sql = 'SELECT
				  id,
				  fio
				FROM couriers WHERE isBan != 1';
		return $this->get_assoc_array($sql);
	}

	public function getRoutes($order_id) {
		$sql = 'SELECT r.id id_route, `to`,to_house,to_corpus,to_appart,
					  to_fio,to_phone,to_coord,from_coord,lenght,cost_route,cost_tovar,cost_car,
					  `to_time`,`to_time_end`,r.`comment`, s.status, s.id status_id, r.pay_type, 
					  r.to_time_ready
				FROM orders_routes r
				LEFT JOIN orders_status s ON s.id = r.id_status
				WHERE id_order = '.$order_id;
		return $this->get_assoc_array($sql);
	}
    public function getPrices() {
        $sql = 'SELECT id, km_from, km_to, km_cost FROM routes_price r';
        return $this->get_assoc_array($sql);
    }
    public function getPayTypes() {
        $sql = 'SELECT id, pay_type FROM orders_pay_types opt';
        return $this->get_assoc_array($sql);
    }
    public function getAddPrices() {
        $sql = 'SELECT id, type, cost_route FROM routes_add_price r';
        return $this->get_assoc_array($sql);
    }
	public function getSpbStreets(){
		$sql = 'SELECT id, street_name name FROM spb_streets';
		return $this->get_assoc_array($sql);
	}

	public function getOrderRoute($order_route_id) {
	    $sql = "SELECT id id_route, id_order, `to`, to_house, to_corpus, to_appart, to_fio, to_phone, to_coord, 
                      from_coord, lenght, cost_route,cost_tovar,cost_car, to_date, to_time, comment, id_status, dk 
                FROM orders_routes
                WHERE id = $order_route_id";
	    $row = $this->get_assoc_array($sql);
	    return $row[0];
    }


    public function getOrderInfo($order_id) {
        $sql = 'SELECT o.id,
                       ua.address `from`,
                       u.inkass_proc,
					   o.ready
				FROM orders o
				LEFT JOIN users_address ua ON ua.id = o.id_address
				LEFT JOIN users u ON u.id = o.id_user
				WHERE o.id = '.$order_id;
        $items = $this->get_assoc_array($sql);
        return isset($items[0])?$items[0]:array();
    }

    public function getOrderRoutesInfo($order_id) {
        $sql = 'SELECT r.id,
                       r.to_time,
                       r.to_time_end,
                       r.to_time_ready,
                       CONCAT(r.`to`,\', д.\',r.`to_house`,\', корп.\',r.`to_corpus`,\', кв.\',r.`to_appart`) to_addr,
                       r.to_fio,
                       r.to_phone,
                       r.pay_type,
					   r.cost_route,
					   r.cost_tovar,
					   r.id_status,
					   s.status
				FROM orders o
				LEFT JOIN orders_routes r ON o.id = r.id_order
				LEFT JOIN orders_status s ON r.id_status = s.id
				WHERE o.id = '.$order_id;
        $items = $this->get_assoc_array($sql);
        return $items;
    }

	public function getOrder($order_id) {
		$sql = 'SELECT o.id,
					   o.id_user,
					   o.id_address,
					   o.address_new,
					   o.id_car,
					   o.id_courier,
					   o.ready,
					   o.date,
					   o.cost,
					   o.comment,
					   o.dk,
					   u.name,
					   u.title,
					   u.phone,
					   cc.fio fio_car,
					   cc.car_number,
					   c.fio fio_courier
				FROM orders o
				LEFT JOIN cars_couriers cc ON cc.id = o.id_car
				LEFT JOIN couriers c ON c.id = o.id_car
				LEFT JOIN users u ON o.id_user = u.id
				WHERE o.id = '.$order_id;
		$this->query ( $sql );
		$items = array ();
		// один заказ
		while ( ($row = $this->fetchRowA ()) !== false ) {
			$row['date'] = $this->dateToRuFormat($row['date']);
			$items = $row;
		}
		return $items;
	}

	public function getChatIdByOrderRoute($order_route_id) {
		$sql = 'SELECT u.phone_mess
				FROM orders o
				LEFT JOIN orders_routes r ON o.id = r.id_order
				LEFT JOIN users u ON o.id_user = u.id
				WHERE r.id = '.$order_route_id;
		$row = $this->get_assoc_array($sql);
		return $row[0]['phone_mess'];
	}

    public function getChatIdByOrder($order_id) {
        $sql = 'SELECT u.phone_mess
				FROM orders o
				LEFT JOIN users u ON o.id_user = u.id
				WHERE o.id = '.$order_id;
        $row = $this->get_assoc_array($sql);
        return $row[0]['phone_mess'];
    }

	public function getChatIdByCourier($courier_id) {
		$sql = "SELECT c.telegram
				FROM couriers c
				WHERE c.id = $courier_id";
		$row = $this->get_assoc_array($sql);
		return $row[0]['telegram'];
	}

    public function getChatIdByCarCourier($courier_id) {
        $sql = "SELECT c.telegram
				FROM cars_couriers c
				WHERE c.id = $courier_id";
        $row = $this->get_assoc_array($sql);
        return $row[0]['telegram'];
    }

    public function getStatusName($status_id) {
        $sql = 'SELECT s.status
				FROM orders_status s
				WHERE s.id = '.$status_id;
        $row = $this->get_assoc_array($sql);
        return $row[0]['status'];
    }
    public function getOrdersListExcel($from, $to, $user_id = 0) {
        $sql = 'SELECT o.id, 
                       o.date, 
                       r.to_time_ready, 
                       r.to_time,
                       r.to_time_end,
                       a.address `from`,
                       u.title,
                       concat(r.`to`, \', \', r.to_house, \'-\', r.to_corpus, \'-\', r.to_appart) `to`,
                       r.to_phone,
                       r.to_fio,
                       s.status,
					   cc.fio fio_car,
					   r.cost_route,
					   r.cost_tovar,
					   (r.cost_route + r.cost_tovar + (r.cost_tovar)*(u.inkass_proc/100)) inkass,
					   ((r.cost_tovar)*(u.inkass_proc/100)) inkas_proc,
					   r.cost_car money_car,
					   (r.cost_route - r.cost_car) money_comp,
                       o.comment
                  FROM orders o
                LEFT JOIN orders_routes r ON r.id_order = o.id
				LEFT JOIN orders_status s ON s.id = r.id_status
                LEFT JOIN users_address a ON a.id = o.id_address
				LEFT JOIN cars_couriers cc ON cc.id = o.id_car
				LEFT JOIN couriers c ON c.id = o.id_car
                LEFT JOIN users u ON u.id = o.id_user
                  WHERE o.date BETWEEN \''.$this->dmy_to_mydate($from).'\' AND \''.$this->dmy_to_mydate($to).' 23:59:59\'
                  AND (o.id_user = \''.$user_id.'\' or \''.$user_id.'\' = 0)
                  and o.isBan = 0
                ORDER BY o.date, r.to_time_ready, o.id desc
                LIMIT 0,1000';
        $orders = $this->get_assoc_array($sql);
        $result_orders = array();
        foreach ($orders as $key => $order) {
            $order['to_time'] = $order['to_time'].'-'.$order['to_time_end'];
            unset($order['to_time_end']);
            $result_orders[] = $order;
        }
        return $result_orders;
    }
    /*
	public function getOrdersList($from, $to) {
		$sql = 'SELECT o.id, o.comment, o.cost, o.ready, a.address `from`, a.comment addr_comment,
					   u.name, u.title, o.dk, o.id_user,
					   o.id_courier, o.id_car,
					   cc.fio fio_car,
					   cc.car_number,
					   c.fio fio_courier
                  FROM orders o
                LEFT JOIN users_address a ON a.id = o.id_address
				LEFT JOIN cars_couriers cc ON cc.id = o.id_car
				LEFT JOIN couriers c ON c.id = o.id_car
                LEFT JOIN users u ON u.id = o.id_user
                  WHERE o.date BETWEEN \''.$this->dmy_to_mydate($from).'\' AND \''.$this->dmy_to_mydate($to).' 23:59:59\'
                  and o.isBan = 0
                ORDER BY o.id desc
                LIMIT 0,1000';
		$orders = $this->get_assoc_array($sql);
		foreach ($orders as $key => $order) {
			$route = $this->getRoutes($order['id']);
			$orders[$key]['route'] = $route;
		}
		return $orders;
	}
*/
	public function getLogistList($from, $to, $user_id = 0) {
		$sql = 'SELECT o.id, 
                       ua.address,
                       ua.comment addr_comment, 
                       u.name,
                       u.title, 
                       o.ready,
                       o.date, 
                       o.comment, 
                       u.inkass_proc, 
                       o.id_car,
					   cc.fio fio_car,
					   cc.car_number,
					   c.fio fio_courier,
					   o.car_accept
			  FROM orders o
			  LEFT JOIN users_address ua ON o.id_address = ua.id
			  LEFT JOIN cars_couriers cc ON cc.id = o.id_car
			  LEFT JOIN couriers c ON c.id = o.id_car
			  LEFT JOIN users u ON u.id = o.id_user
                  WHERE o.date BETWEEN \''.$this->dmy_to_mydate($from).'\' AND \''.$this->dmy_to_mydate($to).' 23:59:59\'
                  AND (o.id_user = \''.$user_id.'\' or \''.$user_id.'\' = 0)
                LIMIT 0,1000';
		$orders = $this->get_assoc_array($sql);
		foreach ($orders as $key => $order) {
			$route = $this->getRoutes($order['id']);
			$orders[$key]['route'] = $route;
		}
		return $orders;
	}
	
	function dateToRuFormat($date) {
		$date = $this->mydate_to_dmy( $date );
		setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
		$date = strftime("%a, %d.%m.%Y", strtotime($date));
		return iconv('windows-1251','Utf-8', $date);
	}
	


	public function orderInsert($id_user, $params) {
		$sql = "
		INSERT INTO orders (id_user, ready, `date`, comment, id_address, address_new, dk)
		VALUES ($id_user,'".$params['ready']."','".$this->dmy_to_mydate($params['date'])."','".$params['order_comment']."','".$params['store_id']."','".$params['store_new']."',NOW());
		";
		$this->query($sql);

		$order_id = $this->insertID();
		$this->update_routes($order_id,$params);

		return $order_id;
	}

	public function orderUpdate($params) {
		$sql = "
		UPDATE orders SET
		`ready` = '".$params['ready']."',
		`date` = '".$this->dmy_to_mydate($params['date'])."',
		`id_address` = '".$params['store_id']."',
		`address_new` = '".$params['store_new']."',
		`comment` = '".$params['order_comment']."',
		`dk` = NOW()
		WHERE id = ".$params['order_id']."
		";
		$this->query($sql);

		$this->update_routes($params['order_id'],$params);

		return $params['order_id'];
	}

	public function updOrderStatus($user_id, $order_route_id, $new_status, $stat_comment){
		$sql = "UPDATE orders_routes SET id_status = $new_status, `dk` = NOW()	WHERE id = $order_route_id ";
		$this->query($sql);

		$sql = "INSERT INTO order_status_history (user_id, order_route_id, new_status, comment, dk)
				VALUES ($user_id, $order_route_id, $new_status, '$stat_comment', NOW())";
		$this->query($sql);
	}

	public function updOrderRouteCourier($user_id, $order_id, $new_courier, $new_car_courier, $courier_comment){
		$sql = "UPDATE orders SET id_courier = $new_courier, id_car = $new_car_courier, `dk` = NOW() WHERE id = ".$order_id." ";
		$this->query($sql);

		$sql = "INSERT INTO order_courier_history (user_id, order_route_id, new_courier, new_car, comment, dk)
				VALUES ($user_id, $order_id, $new_courier, $new_car_courier, '$courier_comment', NOW())";
		$this->query($sql);

        // При назначении курьера, статус заказа должен меняться на исполняется
        $sql = "SELECT id FROM orders_routes WHERE id_order = '$order_id'";
        $routes = $this->get_assoc_array($sql);

        foreach ($routes as $route){
            $this->updOrderStatus($user_id, $route['id'], '3', 'Назначен курьер');
        }

	}

	public function update_routes($order_id,$params){
		if (is_array($params ['to'])) {
			$sql = 'DELETE FROM orders_routes WHERE id_order = '.$order_id.';';
			$this->query ( $sql );
            $sql_values = '';
			foreach ($params ['to'] as $key => $item) {
                $params ['status'][$key] = $params ['status'][$key] == 0 ? 1 : $params ['status'][$key];
				$sql_values .= ($key > 0)?',':'';
                $sql_values .= ' (\''.$order_id.'\',\''.$params ['to'][$key].'\',\''.$params ['to_house'][$key].'\',\''.$params ['to_corpus'][$key].'\',
							\''.$params ['to_appart'][$key].'\',\''.$params ['to_fio'][$key].'\',\''.$params ['to_phone'][$key].'\',
							\''.$params ['cost_route'][$key].'\',\''.$params ['cost_tovar'][$key].'\',\''.$params ['cost_car'][$key].'\',
							\''.$params ['to_time'][$key].'\',\''.$params ['to_time_end'][$key].'\',\''.$params ['comment'][$key].'\',
							\''.$params ['to_time_ready'][$key].'\',\''.$params ['pay_type'][$key].'\',\''.$params ['status'][$key].'\'	)';
			}
			if ($sql_values != '') {
                $sql = "INSERT INTO orders_routes (id_order,`to`,`to_house`,`to_corpus`,`to_appart`,`to_fio`,`to_phone`,`cost_route`,`cost_tovar`,`cost_car`,`to_time`,`to_time_end`,`comment`, `to_time_ready`, `pay_type`, `id_status`) VALUES $sql_values";
                $this->query($sql);
            }
		}
	}

	public function orderBan($id) {
		$sql = "UPDATE `orders`
				SET `isBan` = 1
                WHERE `id` = $id";
		return $this->query ( $sql);
	}

function mydate_to_dmy($date) {
	return date ( 'd.m.Y', strtotime ( substr ( $date, 0, 20 ) ) );
}

function dmy_to_mydate($date) {
	return date ( 'Y-m-d', strtotime (  $date ) );
}

public function getNewsList($limCount) {
      $page = 1;
      $limStart = ($page - 1) * $limCount;      
      $sql = 'SELECT n.*, DATE_FORMAT(`time`, \'%%d.%%m.%%Y\') as time, 
			    	(SELECT COUNT(*) FROM news) as news_count
			     FROM news n
				ORDER BY n.`time` DESC';
      if ($limCount > 0) $sql.= ' LIMIT '.$limStart.','.$limCount;
      $this->query($sql);
      $collect = Array();
      while($row = $this->fetchRowA()) {      	
      	$collect[]=$row;
      }      
      return $collect;
    }

function GetInTranslit($string) {
	$replace=array(
			"'"=>"",
			"`"=>"",
			"а"=>"a","А"=>"a",
			"б"=>"b","Б"=>"b",
			"в"=>"v","В"=>"v",
			"г"=>"g","Г"=>"g",
			"д"=>"d","Д"=>"d",
			"е"=>"e","Е"=>"e",
			"ж"=>"zh","Ж"=>"zh",
			"з"=>"z","З"=>"z",
			"и"=>"i","И"=>"i",
			"й"=>"y","Й"=>"y",
			"к"=>"k","К"=>"k",
			"л"=>"l","Л"=>"l",
			"м"=>"m","М"=>"m",
			"н"=>"n","Н"=>"n",
			"о"=>"o","О"=>"o",
			"п"=>"p","П"=>"p",
			"р"=>"r","Р"=>"r",
			"с"=>"s","С"=>"s",
			"т"=>"t","Т"=>"t",
			"у"=>"u","У"=>"u",
			"ф"=>"f","Ф"=>"f",
			"х"=>"kh","Х"=>"kh",
			"ц"=>"tc","Ц"=>"tc",
			"ч"=>"ch","Ч"=>"ch",
			"ш"=>"sh","Ш"=>"sh",
			"щ"=>"shch","Щ"=>"shch",
			"ъ"=>"","Ъ"=>"",
			"ы"=>"y","Ы"=>"y",
			"ь"=>"","Ь"=>"",
			"э"=>"e","Э"=>"e",
			"ю"=>"iu","Ю"=>"iu",
			"я"=>"ia","Я"=>"ia",
			"і"=>"i","І"=>"i",
			"ї"=>"yi","Ї"=>"yi",
			"є"=>"e","Є"=>"e"
	);
	return $str=iconv("UTF-8","UTF-8//IGNORE",strtr($string,$replace));
}

}

class ordersProcess extends module_process {
	public function __construct($modName) {
		global $values, $User, $LOG, $System;
		parent::__construct ( $modName );
		$this->Vals = $values;
		$this->System = $System;
		if (! $modName)
			unset ( $this );
		$this->modName = $modName;
		$this->User = $User;
		$this->Log = $LOG;
		$this->action = false;
		/*
		 * actionDefault - Действие по умолчанию. Должно браться из БД!!!
		 */
		$this->actionDefault = '';
		$this->actionsColl = new actionColl ();
		$this->nModel = new ordersModel ( $modName );
		$sysMod = $this->nModel->getSysMod ();
		$this->sysMod = $sysMod;
		$this->mod_id = $sysMod->id;
		$this->nView = new ordersView ( $this->modName, $this->sysMod );
		$this->regAction ( 'view', 'Главная страница заказов', ACTION_GROUP );
		$this->regAction ( 'LogistList', 'Для логистов', ACTION_GROUP );
		$this->regAction ( 'order', 'Заявка', ACTION_GROUP );
		$this->regAction ( 'orderUpdate', 'Редактирование заявки', ACTION_GROUP );
		$this->regAction ( 'orderBan', 'Удаление заявки', ACTION_GROUP );
		$this->regAction ( 'chg_status', 'Изменение статуса заявки', ACTION_GROUP );
		$this->regAction ( 'chg_courier', 'Изменение курьера', ACTION_GROUP );
		$this->regAction ( 'get_data', 'Получение интерактивных данных', ACTION_GROUP );
		$this->regAction ( 'excel', 'Экспорт в Excel', ACTION_GROUP );

		if (DEBUG == 0) {
			$this->registerActions ( 1 );
		}
		if (DEBUG == 1) {
			$this->registerActions ( 0 );
		}
	}
	
	
	public function update($_action = false) {
		if ($_action)
			$this->action = $_action;
		if ($this->action)
			$action = $this->action;
		else
			$action = $this->checkAction ();
		if (! $action) {
			$this->Vals->URLparams ( $this->sysMod->defQueryString );
			$action = $this->actionDefault;
		}
		$user_id = $this->User->getUserID ();

		if ($user_id > 0) {
			$this->User->nView->viewLoginParams ( 'Доставка', '', $user_id, array (), array (), $this->User->getRight ( 'admin', 'view' ) );
		}

        $user_right = $this->User->getRight ( $this->modName, $action );
        if ($user_right == 0 && ! $_action) {
            $this->User->nView->viewLoginParams ( '', '', $user_id, array (), array () );
            $this->nView->viewMessage ( 'У вас нет прав на работу с этим модулем.','' );
            $this->updated = true;
            return;
        }


		if ($action == 'get_data'){
			$type_data = $this->Vals->getVal ( 'get_data', 'GET', 'string' );
			if ($type_data == 'spbStreets'){
				$items = $this->nModel->getSpbStreets();
				echo json_encode($items);
				exit();
			}
		}

        if ($action == 'excel'){
            $sub_action = $this->Vals->getVal ( 'sub_action', 'POST', 'string' );
            $logist = $this->Vals->getVal ( 'logist', 'GET', 'string' );
            list($from, $to) = $this->get_post_date('all');
            if ($sub_action == 'excel') {
                if ($logist == 1) {
                    $titles = array('номер заказа', 'дата', 'время готовности', 'время доставки', 'адрес приема', 'компания', 'адрес доставки', 'телефон', 'ФИО получателя', 'статус заказа', 'курьер', 'стоимость доставки', 'стоимость цветов', 'инкассация', '% инкас.', 'заработок курьера', 'заработок компании', 'примечания');
                    $orders = $this->nModel->getOrdersListExcel($from, $to);
                }else{
                    $titles = array('номер заказа', 'дата', 'время готовности', 'время доставки', 'адрес приема', 'компания', 'адрес доставки', 'телефон', 'ФИО получателя', 'статус заказа', 'курьер', 'стоимость доставки', 'стоимость цветов', 'инкассация', '% инкас.', 'заработок курьера', 'заработок компании', 'примечания');
                    $orders = $this->nModel->getOrdersListExcel($from, $to, $user_id);
                }
                $this->nModel->exportToExcel($titles,$orders);
                stop($orders);
            }
            $this->nView->viewExcelDialog($from, $to, $logist);
        }
		
		if ($action == 'order') {
			$order_id = $this->Vals->getVal ( 'order', 'GET', 'integer' );
			$is_single = $this->Vals->getVal ( 'single', 'GET', 'integer' );
			$without_menu = $this->Vals->getVal ( 'without_menu', 'GET', 'integer' );
			$order = $this->nModel->getOrder($order_id);
			$routes = $this->nModel->getRoutes($order_id);
			$pay_types = $this->nModel->getPayTypes();
            $statuses = $this->nModel->getStatuses();
			$prices = $this->nModel->getPrices();
			$timer = $this->getTimeForSelect();
            $add_prices = $this->nModel->getAddPrices();
			$stores = $this->nModel->getStores(isset($order['id_user'])?$order['id_user']:$user_id);
			$client_title = $this->nModel->getClientTitle(isset($order['id_user'])?$order['id_user']:$user_id);
			$this->nView->viewOrderEdit ( $order, $stores, $routes, $pay_types, $statuses, $timer, $prices, $add_prices, $client_title, $without_menu, $is_single );
		}

		if ($action == 'orderBan') {
			$order_id = $this->Vals->getVal ( 'orderBan', 'GET', 'integer' );
			$this->nModel->orderBan($order_id);
			$this->nView->viewMessage('Заказ успешно удален.', 'Сообщение');
			header ( "Location:/orders/" );
		}

		if ($action == 'orderUpdate') {
			$params['order_id'] = $this->Vals->getVal ( 'order_id', 'POST', 'integer' );
			$params['id_user'] = $this->Vals->getVal ( 'id_user', 'POST', 'integer' );
			$params['store_id'] = $this->Vals->getVal ( 'store_id', 'POST', 'integer' );
			$params['store_new'] = $this->Vals->getVal ( 'store_new', 'POST', 'integer' );
			$params['date'] = $this->Vals->getVal ( 'date', 'POST', 'string' );
			$params['ready'] = $this->Vals->getVal ( 'ready', 'POST', 'string' );
			$params['order_comment'] = $this->Vals->getVal ( 'order_comment', 'POST', 'string' );
			$params['to'] = $this->Vals->getVal ( 'to', 'POST', 'array' );
			$params['to_time_ready'] = $this->Vals->getVal ( 'to_time_ready', 'POST', 'array' );
			$params['to_house'] = $this->Vals->getVal ( 'to_house', 'POST', 'array' );
			$params['to_corpus'] = $this->Vals->getVal ( 'to_corpus', 'POST', 'array' );
			$params['to_appart'] = $this->Vals->getVal ( 'to_appart', 'POST', 'array' );
			$params['to_fio'] = $this->Vals->getVal ( 'to_fio', 'POST', 'array' );
			$params['to_phone'] = $this->Vals->getVal ( 'to_phone', 'POST', 'array' );
			$params['to_time'] = $this->Vals->getVal ( 'to_time', 'POST', 'array' );
			$params['to_time_end'] = $this->Vals->getVal ( 'to_time_end', 'POST', 'array' );
			$params['cost_route'] = $this->Vals->getVal ( 'cost_route', 'POST', 'array' );
			$params['cost_tovar'] = $this->Vals->getVal ( 'cost_tovar', 'POST', 'array' );
			$params['cost_car'] = $this->Vals->getVal ( 'cost_car', 'POST', 'array' );
			$params['pay_type'] = $this->Vals->getVal ( 'pay_type', 'POST', 'array' );
			$params['comment'] = $this->Vals->getVal ( 'comment', 'POST', 'array' );
			$params['status'] = $this->Vals->getVal ( 'status', 'POST', 'array' );
			if ($params['order_id'] > 0) {
				$order_id = $this->nModel->orderUpdate($params);
                $message_add_text = "Заказ обновлен";
                $send_message = false;
			}else{
                $order_id = $this->nModel->orderInsert($user_id,$params);
                $message_add_text = "Заказ принят, ожидайте курьера.";
                $send_message = true;
			}

			// Если статус больше статуса в исполнении
            if (isset($params['status']) and is_array($params['status'])) {
                foreach ($params['status'] as $route_statuses) {
                    if ($route_statuses > 3) {
                        $send_message = true;
                    }
                }
            }

            if ($send_message) {
                $message = $this->getOrderTextInfo($order_id);
                $message .= $message_add_text;
                $chat_id = $this->nModel->getChatIdByOrder($order_id);
                if (isset($chat_id) and $chat_id != '') {
                    $this->telegram($message, $chat_id);
                }
            }
			$this->nView->viewMessage('Заказ успешно сохранен. Номер для отслеживания: '.$order_id, 'Сообщение');
            $this->updated = true;
		}

		if ($action == 'chg_status'){
            $order_id = $this->Vals->getVal ( 'order_id', 'POST', 'integer' );
            $order_route_id = $this->Vals->getVal ( 'order_route_id', 'POST', 'integer' );
			$new_status = $this->Vals->getVal ( 'new_status', 'POST', 'integer' );
			$stat_comment = $this->Vals->getVal ( 'stat_comment', 'POST', 'string' );
			$order_info_message = $this->Vals->getVal ( 'order_info_message', 'POST', 'string' );
			if ($order_info_message == '') {
                $order_info_message = $this->getOrderTextInfo($order_id);
            }
            if ($order_route_id == ''){
                $order_routes = $this->nModel->getOrderRoutesInfo($order_id);
                $order_route_id = $order_routes[0]['id'];
            }
			if ($new_status > 0){
				$this->nModel->updOrderStatus($user_id, $order_route_id, $new_status, $stat_comment);
				$result = 'Статус успешно изменен. ';
				$chat_id = $this->nModel->getChatIdByOrderRoute($order_route_id);
				$status_name = $this->nModel->getStatusName($new_status);
				if (isset($chat_id) and $chat_id != '') {
					$result .= ' Сообщение клиенту отправлено.';
					$message = $order_info_message."\r\n";
					$message .= '<b>Статус вашего заказа:</b> '.$status_name.''."\r\n";
					if (trim($stat_comment) != '') {
						$message .= '<b>Сообщение с сайта:</b> ' . $stat_comment . '';
					}
					$this->telegram($message, $chat_id);
				}
				echo $result;
			}else {
				$order_route = $this->nModel->getOrderRoute($order_route_id);
				$statuses = $this->nModel->getStatuses();
				$select = "<select class='form-control' name='new_status' >";
				foreach ($statuses as $status) {
					$selected = ($order_route['id_status'] == $status['id']) ? 'selected=""' : '';
					$select .= "<option value='" . $status['id'] . "' $selected>" . $status['status'] . "</option>";
				}
				$select .= "</select>";
				$comment = "<textarea class='form-control' name='comment_status' placeholder='Комментарий для клиента'></textarea>";
				$info = "<div class='alert alert-success'>".str_replace("\r\n","<br/>",$order_info_message)."</div>";
				$info .= "<div class='alert alert-info'>Выберите новый статус и введите комментарий для клиента.</div>
						<input type='hidden' name='order_route_id' value='$order_route_id' />
						<input type='hidden' name='order_info_message' value='$order_info_message' />";
				echo $info . "<br/>" . $select . "<br/>" . $comment;
			}
			exit();
		}

		if ($action == 'chg_courier'){
			$order_id = $this->Vals->getVal ( 'order_id', 'POST', 'integer' );
			$new_courier = $this->Vals->getVal ( 'new_courier', 'POST', 'integer' );
			$new_car_courier = $this->Vals->getVal ( 'new_car_courier', 'POST', 'integer' );
            $courier_comment = $this->Vals->getVal ( 'courier_comment', 'POST', 'string' );
            $order_info_message = $this->Vals->getVal ( 'order_info_message', 'POST', 'string' );
            if ($order_info_message == '') {
                $order_info_message = $this->getOrderTextInfo($order_id);
            }
			if ($new_courier > 0 and $new_car_courier > 0){
				$this->nModel->updOrderRouteCourier($user_id, $order_id, $new_courier, $new_car_courier, $courier_comment);
				$result = 'Курьер успешно назначен.';
                $chat_id= ($new_courier == 1)?$this->nModel->getChatIdByCarCourier($new_car_courier):$this->nModel->getChatIdByCourier($new_courier);
                if (isset($chat_id) and $chat_id != '') {
                    $result .= ' Сообщение курьеру отправлено.';
                    $message = '<i>Вы назначены на заказ</i>'."\r\n";
                    $message .= $order_info_message."\r\n";
                    if (trim($courier_comment) != '') {
                        $message .= 'Сообщение с сайта: ' . $courier_comment . '';
                    }
                    $menu = array('inline_keyboard' => array(
                        array(
                            array(
                                'text' => 'принять заказ', 'callback_data' => '/order_accepted_'.$order_id,
                            ),
                        ),
                    ),
                    );
                    $this->telegram($message, $chat_id, $menu);
                }
				echo $result;
			}else {
				$order = $this->nModel->getOrder($order_id);
				$car_couriers = $this->nModel->getCarCouriers();
                $select = '<div class="input-group"><span class="input-group-addon">Курьер:</span>';
				$select .= "<select class='form-control' name='new_car_courier' >";
				foreach ($car_couriers as $car) {
					$selected = ($order['id_car'] == $car['id']) ? 'selected=""' : '';
					$select .= "<option value='" . $car['id'] . "' $selected>" . $car['fio'] . " (" . $car['car_number'] . ")</option>";
				}
                $select .= "</select></div>";
               /* $couriers = $this->nModel->getCouriers();
                $select2 = '<div class="input-group"><span class="input-group-addon">За рулем:</span>';
                $select2 .= "<select class='form-control' name='new_courier' >";
                foreach ($couriers as $courier) {
                    $selected = ($order['id_car'] == $courier['id']) ? 'selected=""' : '';
                    $select2 .= "<option value='" . $courier['id'] . "' $selected>" . $courier['fio'] . "</option>";
                }
				$select2 .= "</select></div>";*/
                $input2 = "<input type='hidden' name='new_courier' value='1'/>";
                $comment = "<textarea class='form-control' name='courier_comment' placeholder='Комментарий для курьера'></textarea>";
                $info = "<div class='alert alert-success'>".str_replace("\r\n","<br/>",$order_info_message)."</div>";
				$info .= "<div class='alert alert-info'>Выберите курьера для данного заказа.</div>
						<input type='hidden' name='order_id' value='$order_id' />
						<input type='hidden' name='order_info_message' value='$order_info_message' />";
				echo $info . "<br/>" . $select . $input2 . $comment;
			}
			exit();
		}

/** Поиск своего заказа */		/*
		if ($action == 'search_order') {
			$order = $this->Vals->getVal ( 'order_number', 'POST', 'integer' );
//			$items = $this->nModel->SearchOrder ( $order);
			$this->nView->viewSearchOrder (  );
			
		}
*/
		if ($action == 'view') {
            list($from, $to) = $this->get_post_date();
            $statuses = $this->nModel->getStatuses();
//			$orders = $this->nModel->getOrdersList($from, $to);
			$orders = $this->nModel->getLogistList($from, $to, $user_id);
			$this->nView->viewOrders ($from, $to, $orders, $statuses);
		}

		if ($action == 'LogistList') {
            list($from, $to) = $this->get_post_date();
            $statuses = $this->nModel->getStatuses();
			$orders = $this->nModel->getLogistList($from, $to);
			$this->nView->viewLogistList ($from, $to, $orders, $statuses);
		}


		
		if ($this->Vals->isVal ( 'ajax', 'INDEX' )) {
			if ($this->Vals->isVal ( 'xls', 'INDEX' )) {
				$PageAjax = new PageForAjax ( $this->modName, $this->modName, $this->modName, 'page.xls.xsl' );
				$PageAjax->addToPageAttr ( 'xls', '1' );
			} else
				$PageAjax = new PageForAjax ( $this->modName, $this->modName, $this->modName, 'page.ajax.xsl' );
			$isAjax = $this->Vals->getVal ( 'ajax', 'INDEX' );
			$PageAjax->addToPageAttr ( 'isAjax', $isAjax );
			$html = $PageAjax->getBodyAjax2 ( $this->nView );
		
			if ($this->Vals->isVal ( 'xls', 'INDEX' )) {
				$reald = date ( "d.m.Y" );
				header ( "Content-Type: application/vnd.ms-excel", true );
				header ( "Content-Disposition: attachment; filename=\"list_" . $reald . ".xls\"" );
				exit ( $html );
			} else
				sendData ( $html );
		
		}


	}

	public function getTimeForSelect(){
        $time_arr = array();
        for ($h = 8; $h <= 23; $h++) {
            if ($h < 23){
                for ($i= 0; $i <= 11; $i++){
                    $time_arr[] = substr('0'.$h,-2).':'.substr('0'.($i*5),-2);
                }
            }else{
                $time_arr[] = $h.':00';
            }
        }
        return $time_arr;
    }

	public function getOrderTextInfo($order_id){
        $order_info = $this->nModel->getOrderInfo($order_id);
        $order_routes_info = $this->nModel->getOrderRoutesInfo($order_id);
        $order_info_message = "<b>Заказ №</b> " . $order_id . "\r\n";
        $order_info_message .= "<b>Откуда:</b> " . $order_info['from'] . "\r\n\r\n";
        $i = 0;
        foreach ($order_routes_info as $order_route_info) {
            $i++;
            if (count($order_routes_info) > 1){
                $order_info_message .= "<b>Участок № $i:</b>\r\n";
            }

            if ($order_route_info['pay_type'] == 2){
                $inkass = $order_route_info['cost_route']+$order_route_info['cost_tovar']+$order_route_info['cost_tovar']*$order_info['inkass_proc']/100;
            }else{
                $inkass = $order_route_info['cost_tovar']+$order_route_info['cost_tovar']*$order_info['inkass_proc']/100;
            }

            $order_info_message .= " <b>Адрес доставки:</b> " . $order_route_info['to_addr'] . "\r\n";
            $order_info_message .= " <b>Готовность:</b> " . $order_route_info['to_time_ready'] . "\r\n";
            $order_info_message .= " <b>Период получения:</b> " . $order_route_info['to_time'] . " - " . $order_route_info['to_time_end'] . "\r\n";
            $order_info_message .= " <b>Получатель:</b> " . $order_route_info['to_fio'] . " [" . $order_route_info['to_phone'] . "]\r\n";
            $order_info_message .= " <b>Инкассация:</b> " . $inkass . "\r\n";
            if ($order_route_info['id_status'] > 1) {
                $order_info_message .= " <b>Статус:</b> " . $order_route_info['status'] . "\r\n";
            }
//            $order_info_message .= " <b>Стоимость заказа:</b> " . (+$order_route_info['cost_route'] + $order_route_info['cost_tovar']) . "\r\n ";
        }
        return $order_info_message;
    }

	public function get_post_date($type = 'to'){
	    if ($type == 'all') {
            $from = $this->Vals->getVal ( 'date_from', 'POST', 'string' );
        }
        $to = $this->Vals->getVal ( 'date_to', 'POST', 'string' );
        if ($type == 'to') {
            $from = $to;
        }
        if ($to == '') {
            if ($type == 'all') {
        		$from = (isset($_SESSION['date_from']) and $_SESSION['date_from'] != '') ? $_SESSION['date_from'] : date('01.m.Y');
            }
            $to = (isset($_SESSION['date_to']) and $_SESSION['date_to'] != '') ? $_SESSION['date_to'] : date('d.m.Y');
            if ($type == 'to') {
                $from = $to;
            }
        }else{
            $_SESSION['date_from'] = (isset($from))?$from:$to;
            $_SESSION['date_to'] = $to;
        }
        return array((isset($from))?$from:$to,$to);
    }


	public function telegram($message,$chat_id,$menu = array())
	{
		/**
		 * Задаём основные переменные.
		 */
	/*	$output = json_decode(file_get_contents('php://input'), TRUE);
		file_put_contents('log.txt', "\n OK: " . date('d-m-Y H:i:s') . " " . json_encode($output), FILE_APPEND);
		$chat_id = $output['message']['chat']['id'];
		$first_name = $output['message']['chat']['first_name'];
		$message = $output['message']['text'];
		$message_id = $output['message']['message_id'];
*/
		//https://api.telegram.org/bot<YourBOTToken>/getUpdates

        $encodedMarkup = json_encode($menu);
        if ($menu == array()) {
            $params = array(
                'chat_id' => $chat_id,
                'parse_mode' => 'HTML',
                'text' => '' . $message . ''
                //,'reply_to_message_id' => $message_id,
            );
        } else {
            $params = array(
                'chat_id' => $chat_id,
                'parse_mode' => 'HTML',
                'text' => '' . $message . '',
                'reply_markup' => $encodedMarkup
                //,'reply_to_message_id' => $message_id,
            );
        }

		$this->callApiTlg('sendMessage', $params, TLG_TOKEN);

	}
	public function callApiTlg( $method, $params, $access_token) {
		$url = sprintf(
			"https://api.telegram.org/bot%s/%s",
			$access_token,
			$method
		);

		$ch = curl_init();
		curl_setopt_array( $ch, array(
			CURLOPT_URL             => $url,
			CURLOPT_POST            => TRUE,
			CURLOPT_RETURNTRANSFER  => TRUE,
			CURLOPT_FOLLOWLOCATION  => FALSE,
			CURLOPT_HEADER          => FALSE,
			CURLOPT_TIMEOUT         => 10,
			CURLOPT_HTTPHEADER      => array( 'Accept-Language: ru,en-us'),
			CURLOPT_POSTFIELDS      => $params,
			CURLOPT_SSL_VERIFYPEER  => FALSE,
		));

		$response = curl_exec($ch);
		return json_decode($response);
	}
	
}

class ordersView extends module_View {
	public function __construct($modName, $sysMod) {
		parent::__construct ( $modName, $sysMod );
		$this->pXSL = array ();
	}

    public function viewExcelDialog($from, $to, $logist) {
        $this->pXSL [] = RIVC_ROOT . 'layout/orders/orders.excel.xsl';
        $Container = $this->newContainer ( 'excel' );

        $this->addAttr('logist', $logist, $Container);
        $this->addAttr('date_from', $from, $Container);
        $this->addAttr('date_to', $to, $Container);

        return true;
    }

	public function viewOrders($from, $to, $orders, $statuses) {
		$this->pXSL [] = RIVC_ROOT . 'layout/orders/orders.view.xsl';
		$Container = $this->newContainer ( 'list' );

        $this->addAttr('date_from', $from, $Container);
        $this->addAttr('date_to', $to, $Container);

        $ContainerNews = $this->addToNode ( $Container, 'orders', '' );
		foreach ( $orders as $item ) {
			$this->arrToXML ( $item, $ContainerNews, 'item' );
		}
        $ContainerStatuses = $this->addToNode ( $Container, 'statuses', '' );
        foreach ( $statuses as $item ) {
            $this->arrToXML ( $item, $ContainerStatuses, 'item' );
        }
		return true;
	}

	public function viewLogistList($from, $to, $orders, $statuses) {
		$this->pXSL [] = RIVC_ROOT . 'layout/orders/logist.view.xsl';
		$Container = $this->newContainer ( 'list' );

		$this->addAttr('date_from', $from, $Container);
		$this->addAttr('date_to', $to, $Container);

		$ContainerOrders = $this->addToNode ( $Container, 'orders', '' );
		foreach ( $orders as $item ) {
			$this->arrToXML ( $item, $ContainerOrders, 'item' );
		}

        $ContainerStatuses = $this->addToNode ( $Container, 'statuses', '' );
        foreach ( $statuses as $item ) {
            $this->arrToXML ( $item, $ContainerStatuses, 'item' );
        }
		return true;
	}

	public function viewlocations($type,$items,$locs) {
		$this->pXSL [] = RIVC_ROOT . 'layout/'.$this->sysMod->layoutPref.'/turs.locations.xsl';
		$Container = $this->newContainer ( 'locations' );
		$ContainerMenu = $this->addToNode ( $Container, 'menu', '' );
		foreach ( $items as $item ) {
			$this->arrToXML ( $item, $ContainerMenu, 'item' );
		}
		$ContainerType = $this->addToNode ( $Container, 'type', '' );
		foreach ( $type as $item ) {
			$this->arrToXML ( $item, $ContainerType, 'item' );
		}
		$ContainerLocs = $this->addToNode ( $Container, 'locs', '' );
		foreach ( $locs as $item ) {
			$this->arrToXML ( $item, $ContainerLocs, 'item' );
		}
		return true;
	}
	
	public function viewOrderEdit($order, $stores, $routes, $pay_types, $statuses, $timer, $prices, $add_prices, $client_title, $without_menu, $is_single) {
		$this->pXSL [] = RIVC_ROOT . 'layout/orders/order.edit.xsl';
        $Container = $this->newContainer('order');
        $this->addAttr('today', date('d.m.Y'), $Container);
        $this->addAttr('time_now', time(), $Container);
//        $this->addAttr('time_now_five', $time_now_five_h . ":" . $this->roundUpToAny(date('i')), $Container);
        $time_now_five_min = substr('0'.($this->roundUpToAny(date('i'))),-2);
        $time_now_five_h = date('H');
        if ($time_now_five_min == 60){
            $time_now_five_min = '00';
            $time_now_five_h = substr('0'.($time_now_five_h+1),-2);
        }
        $this->addAttr('time_now_five', $time_now_five_h . ":" . $time_now_five_min, $Container);
//        $this->addAttr('time_now_five', "16:" . $time_now_five_min, $Container);
        $this->addAttr('without_menu', $without_menu, $Container);
        $this->addAttr('is_single', $is_single, $Container);

		$this->arrToXML ( $order, $Container, 'order' );
        $this->arrToXML ( $timer, $Container, 'timer' );

        if (count($routes) > 0) {
            $ContainerRoutes = $this->addToNode($Container, 'routes', '');
            foreach ($routes as $item) {
                $this->arrToXML($item, $ContainerRoutes, 'item');
            }
        }else{
            $ContainerRoutes = $this->addToNode($Container, 'routes', '');
            $this->addToNode($ContainerRoutes, 'item', 'fake');
        }

		$ContainerClient = $this->addToNode ( $Container, 'client', '' );
		foreach ( $client_title as $item ) {
			$this->arrToXML ( $item, $ContainerClient, 'item' );
		}
        $ContainerStatuses = $this->addToNode ( $Container, 'statuses', '' );
        foreach ( $statuses as $item ) {
            $this->arrToXML ( $item, $ContainerStatuses, 'item' );
        }
        $ContainerPayTypes = $this->addToNode ( $Container, 'pay_types', '' );
        foreach ( $pay_types as $item ) {
            $this->arrToXML ( $item, $ContainerPayTypes, 'item' );
        }
        $ContainerStores = $this->addToNode ( $Container, 'stores', '' );
        foreach ( $stores as $item ) {
            $this->arrToXML ( $item, $ContainerStores, 'item' );
        }
        $ContainerPrices = $this->addToNode ( $Container, 'prices', '' );
        foreach ( $prices as $item ) {
            $this->arrToXML ( $item, $ContainerPrices, 'item' );
        }
        $ContainerAddPrices = $this->addToNode ( $Container, 'add_prices', '' );
        foreach ( $add_prices as $item ) {
            $this->arrToXML ( $item, $ContainerAddPrices, 'item' );
        }

		return true;
	}
	
	public function viewSearchOrder ( $items ) {
		$this->pXSL [] = RIVC_ROOT . 'layout/'.$this->sysMod->layoutPref.'/turs.travel_search.xsl';
		$Container = $this->newContainer ( 'travellist' );
		$Containerusers = $this->addToNode ( $Container, 'travel', '' );
		foreach ( $items as $item ) {
			$this->arrToXML ( $item, $Containerusers, 'item' );
		}
		return true;
	}
	
	public function viewPayOrder ( $items,$frmData ) {
		$this->pXSL [] = RIVC_ROOT . 'layout/'.$this->sysMod->layoutPref.'/turs.travel_pay.xsl';
		$Container = $this->newContainer ( 'travellist' );
		$Containerusers = $this->addToNode ( $Container, 'travel', '' );
		foreach ( $items as $item ) {
			$this->arrToXML ( $item, $Containerusers, 'item' );
		}
		$this->arrToXML ( $frmData, $Containerusers, 'form_data' );

		return true;
	}
    public function roundUpToAny($n,$x=5) {
        return round(($n+$x/2)/$x)*$x;
    }

}