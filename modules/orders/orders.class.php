<?php

class ordersModel extends module_model {
	public function __construct($modName) {
		parent::__construct ( $modName );
	}

	public function get_assoc_array($sql){
		$this->query ( $sql );
		$items = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
			$items[] = $row;
		}
		return $items;
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

	public function getRoutes($order_id) {
		$sql = 'SELECT `to`,to_house,to_corpus,to_appart,
					  to_fio,to_phone,to_coord,from_coord,lenght,cost_route,
					  `to_time`,`comment`
				FROM orders_routes
				WHERE id_order = '.$order_id;
		return $this->get_assoc_array($sql);
	}

	public function getSpbStreets(){
		$sql = 'SELECT id, street_name name FROM spb_streets';
		return $this->get_assoc_array($sql);
	}

	public function getOrder($order_id) {
		$sql = 'SELECT o.id,
					   o.id_user,
					   o.id_address,
					   o.ready,
					   o.date,
					   o.cost,
					   o.comment,
					   o.dk,
					   u.name,
					   u.title,
					   u.phone,
					   os.status,
					   o.id_status
				FROM orders o
				LEFT JOIN users u ON o.id_user = u.id
				LEFT JOIN orders_status os ON os.id = o.id_status
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
	
	public function getOrdersList($from, $to) {
		$sql = 'SELECT o.id, o.comment, o.cost, a.address `from`, s.status, u.name, u.title, o.dk, o.id_user
                  FROM orders o
                LEFT JOIN users_address a ON a.id = o.id_address
                LEFT JOIN orders_status s ON s.id = o.id_status
                LEFT JOIN users u ON u.id = o.id_user
                  WHERE o.dk BETWEEN \''.$this->dmy_to_mydate($from).'\' AND \''.$this->dmy_to_mydate($to).' 23:59:59\'
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
	
	function dateToRuFormat($date) {
		$date = $this->mydate_to_dmy( $date );
		setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
		$date = strftime("%a, %d.%m.%Y", strtotime($date));
		return iconv('windows-1251','Utf-8', $date);
	}
	
	public function orderGet($tur_id) {
		$sql = 'SELECT t.`id`, t.`name`,`date`,c.`name` city_name, l.`name` loc_name, l.`id` loc_id,
		g.`name` gid_name, b.`number` bus_number,cost,currency, t.fire,
		(select count(*) from tc_tur_list tl WHERE tl.id_tur = t.`id`) turists
		FROM ' . TAB_PREF . '`tc_tur` t
		LEFT JOIN ' . TAB_PREF . 'tc_citys c ON t.id_city = c.id
		LEFT JOIN ' . TAB_PREF . 'tc_locations l ON t.id_loc = l.id
		LEFT JOIN ' . TAB_PREF . 'tc_gids g ON t.id_gid = g.id
		LEFT JOIN ' . TAB_PREF . 'tc_bus b ON t.id_bus = b.id
		WHERE t.`id` = '.$tur_id.' ';
		//	stop($sql);
		$this->query ( $sql );
		$items = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
			$row ['date'] = $this->mydate_to_dmy( $row ['date'] );
			$items [] = $row;
		}
		return $items;
	}

	public function orderInsert($id_user, $params) {
		$sql = "
		INSERT INTO orders (id_user, ready, `date`, comment, id_address, id_status, dk)
		VALUES ($id_user,'".$params['ready']."','".$this->dmy_to_mydate($params['date'])."','".$params['order_comment']."','".$params['store_id']."','1',NOW());
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
		`comment` = '".$params['order_comment']."',
		`dk` = NOW()
		WHERE id = ".$params['order_id']."
		";
		$this->query($sql);

		$this->update_routes($params['order_id'],$params);

		return $params['order_id'];
	}

	public function updOrderStatus($user_id, $order_id, $new_status, $stat_comment){
		$sql = "UPDATE orders SET id_status = $new_status, `dk` = NOW()	WHERE id = ".$order_id." ";
		$this->query($sql);

		$sql = "INSERT INTO order_status_history (user_id, order_id, new_status, comment, dk)
				VALUES ($user_id, $order_id, $new_status, '$stat_comment', NOW())";
		$this->query($sql);
	}

	public function update_routes($order_id,$params){
		if (is_array($params ['to'])) {
			$sql = 'DELETE FROM orders_routes WHERE id_order = '.$order_id.';';
			$this->query ( $sql );
			$sql = 'INSERT INTO orders_routes (id_order,`to`,`to_house`,`to_corpus`,`to_appart`,`to_fio`,`to_phone`,`cost_route`,`to_time`,`comment`) VALUES ';
			foreach ($params ['to'] as $key => $item) {
				$sql .= ($key > 0)?',':'';
				$sql .= ' (\''.$order_id.'\',\''.$params ['to'][$key].'\',\''.$params ['to_house'][$key].'\',\''.$params ['to_corpus'][$key].'\',
							\''.$params ['to_appart'][$key].'\',\''.$params ['to_fio'][$key].'\',\''.$params ['to_phone'][$key].'\',
							\''.$params ['cost_route'][$key].'\',\''.$params ['to_time'][$key].'\',\''.$params ['comment'][$key].'\'	)';
			}
			$this->query ( $sql );
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
		$this->regAction ( 'view', 'Главная страница', ACTION_GROUP );
		$this->regAction ( 'order', 'Заявка', ACTION_GROUP );
		$this->regAction ( 'orderUpdate', 'Редактирование заявки', ACTION_GROUP );
		$this->regAction ( 'orderBan', 'Удаление заявки', ACTION_GROUP );
		$this->regAction ( 'chg_status', 'Изменение статуса заявки', ACTION_GROUP );
		$this->regAction ( 'get_data', 'Получение интерактивных данных', ACTION_GROUP );

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



		if ($action == 'get_data'){
			$type_data = $this->Vals->getVal ( 'get_data', 'GET', 'string' );
			if ($type_data == 'spbStreets'){
				$items = $this->nModel->getSpbStreets();
				echo json_encode($items);
				exit();
			}
		}

		
		if ($action == 'order') {
			$order_id = $this->Vals->getVal ( 'order', 'GET', 'integer' );
			$order = $this->nModel->getOrder($order_id);
			$routes = $this->nModel->getRoutes($order_id);
			$stores = $this->nModel->getStores(isset($order['id_user'])?$order['id_user']:$user_id);
			$client_title = $this->nModel->getClientTitle(isset($order['id_user'])?$order['id_user']:$user_id);
			$this->nView->viewOrderEdit ( $order, $stores, $routes, $client_title );
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
			$params['date'] = $this->Vals->getVal ( 'date', 'POST', 'string' );
			$params['ready'] = $this->Vals->getVal ( 'ready', 'POST', 'string' );
			$params['order_comment'] = $this->Vals->getVal ( 'order_comment', 'POST', 'string' );
			$params['to'] = $this->Vals->getVal ( 'to', 'POST', 'array' );
			$params['to_house'] = $this->Vals->getVal ( 'to_house', 'POST', 'array' );
			$params['to_corpus'] = $this->Vals->getVal ( 'to_corpus', 'POST', 'array' );
			$params['to_appart'] = $this->Vals->getVal ( 'to_appart', 'POST', 'array' );
			$params['to_fio'] = $this->Vals->getVal ( 'to_fio', 'POST', 'array' );
			$params['to_phone'] = $this->Vals->getVal ( 'to_phone', 'POST', 'array' );
			$params['to_time'] = $this->Vals->getVal ( 'to_time', 'POST', 'array' );
			$params['cost_route'] = $this->Vals->getVal ( 'cost_route', 'POST', 'array' );
			$params['comment'] = $this->Vals->getVal ( 'comment', 'POST', 'array' );
			if ($params['order_id'] > 0) {
				$id_code = $this->nModel->orderUpdate($params);
			}else{
				$id_code = $this->nModel->orderInsert($user_id,$params);
			}
			$this->nView->viewMessage('Заказ успешно сохранен. Номер для отслеживания: '.$id_code, 'Сообщение');
			$action = 'view';
		}

		if ($action == 'chg_status'){
			$order_id = $this->Vals->getVal ( 'order_id', 'POST', 'integer' );
			$new_status = $this->Vals->getVal ( 'new_status', 'POST', 'integer' );
			$stat_comment = $this->Vals->getVal ( 'stat_comment', 'POST', 'string' );
			if ($new_status > 0){
				$this->nModel->updOrderStatus($user_id, $order_id, $new_status, $stat_comment);
				echo 'Статус успешно изменен. Сообщение клиенту отправлено.';
			}else {
				$order = $this->nModel->getOrder($order_id);
				$statuses = $this->nModel->getStatuses();
				$select = "<select class='form-control' name='new_status' >";
				foreach ($statuses as $status) {
					$selected = ($order['id_status'] == $status['id']) ? 'selected=""' : '';
					$select .= "<option value='" . $status['id'] . "' $selected>" . $status['status'] . "</option>";
				}
				$select .= "</select>";
				$comment = "<textarea class='form-control' name='comment_status' placeholder='Комментарий для клиента'></textarea>";
				$info = "<div class='alert alert-info'>Выберите новый статус и введите комментарий для клиента.</div>
						<input type='hidden' name='order_id' value='$order_id' />";
				echo $info . "<br/>" . $select . "<br/>" . $comment;
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
			$from = $this->Vals->getVal ( 'order', 'POST', 'string' );
			$to = $this->Vals->getVal ( 'order', 'POST', 'string' );
			if ($from == '') {
				$from = (isset($_SESSION['from']) and $_SESSION['from'] != '') ? $_SESSION['from'] : date('01.m.Y');
				$to = (isset($_SESSION['to']) and $_SESSION['to'] != '') ? $_SESSION['to'] : date('d.m.Y');
			}else{
				$_SESSION['from'] = $from;
				$_SESSION['to'] = $to;
			}
			$orders = $this->nModel->getOrdersList($from, $to);
			$this->nView->viewOrders ($from, $to, $orders);
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
	// Функции оплаты
	public function getSignature( $Shop_IDP, $Order_IDP, $Subtotal_P, $MeanType, $EMoneyType,
			$Lifetime, $Customer_IDP, $Card_IDP, $IData, $PT_Code, $password ) {
		$Signature = strtoupper(
				md5(
						md5($Shop_IDP) . "&" .
						md5($Order_IDP) . "&" .
						md5($Subtotal_P) . "&" .
						md5($MeanType) . "&" .
						md5($EMoneyType) . "&" .
						md5($Lifetime) . "&" .
						md5($Customer_IDP) . "&" .
						md5($Card_IDP) . "&" .
						md5($IData) . "&" .
						md5($PT_Code) . "&" .
						md5($password)
				)
		);
		return $Signature;
	}
	
	
	
}

class ordersView extends module_View {
	public function __construct($modName, $sysMod) {
		parent::__construct ( $modName, $sysMod );
		$this->pXSL = array ();
	}
	
	public function viewOrders($from, $to, $orders) {
		$this->pXSL [] = RIVC_ROOT . 'layout/orders/orders.view.xsl';
		$Container = $this->newContainer ( 'list' );

        $this->addAttr('from', $from, $Container);
        $this->addAttr('to', $to, $Container);

        $ContainerNews = $this->addToNode ( $Container, 'orders', '' );
		foreach ( $orders as $item ) {
			$this->arrToXML ( $item, $ContainerNews, 'item' );
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
	public function viewTurList($turs, $isAjax, $locs, $countris) {
		$this->pXSL [] = RIVC_ROOT . 'layout/'.$this->sysMod->layoutPref.'/turs.turlist.xsl';
		if ($isAjax == 1) {
			$this->pXSL [] = RIVC_ROOT . 'layout/head.page.xsl';
		}
		$Container = $this->newContainer ( 'turlist' );
		$Containerusers = $this->addToNode ( $Container, 'turs', '' );
		foreach ( $turs as $item ) {
			$this->arrToXML ( $item, $Containerusers, 'item' );
		}
		$ContainerLocs = $this->addToNode ( $Container, 'locs', '' );
		foreach ( $locs as $item ) {
			$this->arrToXML ( $item, $ContainerLocs, 'item' );
		}
		$ContainerCntr = $this->addToNode ( $Container, 'countris', '' );
		foreach ( $countris as $item ) {
			$this->arrToXML ( $item, $ContainerCntr, 'item' );
		}
		return true;
	}
	
	public function viewOrderEdit($order, $stores, $routes, $client_title) {
		$this->pXSL [] = RIVC_ROOT . 'layout/orders/order.edit.xsl';
		$Container = $this->newContainer ( 'order' );

		$this->arrToXML ( $order, $Container, 'order' );

		$ContainerClient = $this->addToNode ( $Container, 'client', '' );
		foreach ( $client_title as $item ) {
			$this->arrToXML ( $item, $ContainerClient, 'item' );
		}

		$ContainerStores = $this->addToNode ( $Container, 'stores', '' );
		foreach ( $stores as $item ) {
			$this->arrToXML ( $item, $ContainerStores, 'item' );
		}
		$ContainerRoutes = $this->addToNode ( $Container, 'routes', '' );
		foreach ( $routes as $item ) {
			$this->arrToXML ( $item, $ContainerRoutes, 'item' );
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
	

}