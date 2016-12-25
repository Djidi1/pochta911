<?php

//include CORE_ROOT . 'classes/tree.class.php';

class adminModel extends module_model {
	public function __construct($modName) {
		parent::__construct ( $modName );
	
		// stop($this->System);
	}
	

	public function userInsert($Params) {
		$passi = md5 ( $Params ['pass'] );
		$sql = 'INSERT INTO `users` (name,email,login,phone,phone_mess,title,isBan,pass,date_reg)
				VALUES (
				    \'%1$s\',
				    \'%2$s\',
				    \'%3$s\',
				    \'%4$s\',
				    \'%5$s\',
				    \'%6$s\',
				    \'%7$u\',
				    \'' . $passi . '\',
				    NOW()
				    )';

		$test = $this->query ( $sql, $Params ['username'], $Params ['email'], $Params ['login'], $Params ['phone'],
			$Params ['phone_mess'], $Params ['title'], $Params ['isBan'] );

//        stop($this->sql);
		$user_id = $this->insertID();
		if ($user_id > 0) {
			$sql = 'INSERT INTO `groups_user` (`group_id`, `user_id`) VALUES (\'%1$u\', \'%2$u\')';
			$this->query($sql, $Params ['group_id'], $user_id);

			$Params ['user_id'] = $user_id;
			$this->updateAddrAndCard($Params);
		}
		return $test;
	}

	public function userUpdate($Params) {
		$sql = 'UPDATE `users`
				SET
				    name = \'%1$s\',
				    email = \'%2$s\',
				    login = \'%3$s\',
				    phone = \'%4$s\',
				    phone_mess = \'%5$s\',
				    title = \'%6$s\',
				    isBan = \'%7$u\'
				    ';

		if ($Params ['pass'] != '') {
			$passi = md5 ( $Params ['pass'] );
			$sql .= ' ,pass = \''.$passi.'\' ';
		}
//		$this->Log->addToLog ( array ($pass, $passi ), __LINE__, __METHOD__ );
		$sql .= ' WHERE `id` = %8$u';
		$test = $this->query ( $sql, $Params ['username'], $Params ['email'], $Params ['login'], $Params ['phone'],
            $Params ['phone_mess'], $Params ['title'], $Params ['isBan'], $Params ['user_id'] );

//        stop($this->sql);

		$sql = 'UPDATE `groups_user` SET `group_id`  = \'%1$u\' WHERE `user_id` = \'%2$u\'';
		$this->query ( $sql, $Params ['group_id'], $Params ['user_id'] );

		$this->updateAddrAndCard($Params);

		return $test;
	}

	public function updateAddrAndCard($Params){
		if (is_array($Params ['credit_card'])) {
			$sql = 'DELETE FROM users_cards WHERE user_id = '.$Params ['user_id'].';';
			$this->query ( $sql );
            $values = '';
			foreach ($Params ['credit_card'] as $key => $item) {
                $values .= ($key > 0)?',':'';
                $values .= ' (\''.$item.'\',\''.$Params ['card_comment'][$key].'\','.$Params ['user_id'].')';
			}
            $sql = 'INSERT INTO users_cards (card_num,comment,user_id) VALUES '.$values;
			$this->query ( $sql );
		}
		if (is_array($Params ['address'])) {
			$sql = 'DELETE FROM users_address WHERE user_id = '.$Params ['user_id'].';';
			$this->query ( $sql );
			$values = '';
			foreach ($Params ['address'] as $key => $address) {
                $values .= ($key > 0)?', ':'';
                $values .= ' (\''.$address.'\',\''.$Params ['addr_comment'][$key].'\','.$Params ['user_id'].')';
			}
            $sql = 'INSERT INTO users_address (address,comment,user_id) VALUES '.$values;
			$this->query ( $sql );
		}
	}
	public function carBan($id) {
		$sql = "UPDATE `cars_couriers`
				SET `isBan` = 1
                WHERE `id` = $id";
		return $this->query ( $sql);
	}

	public function userBan($user_id, $full) {
		$type = 1;
		if ($full)
			$type = 2;
		$sql = "UPDATE `users`
				SET `isban` = $type
                WHERE `id` = $user_id";
		$this->query ( $sql );
		return true;
	}
	
	public function groupHide($group_id) {
		$sql = "UPDATE groups
                SET hidden = 1
                WHERE id = $group_id";
		$this->query ( $sql, $group_id );
		return true;
	}
	public function groupCount($group_id) {
		$sql = "SELECT COUNT(group_id) as count
  				FROM groups_user
  				where group_id = $group_id";
		$this->query ( $sql );
//		$count = array ();
		$count = $this->fetchOneRowA ();
		return $count;
	}
	
	public function userUnBan($user_id) {
		$sql = "UPDATE `users`
				SET `isban` = 0
                WHERE `id` = $user_id";
		$this->query ( $sql );
		return true;
	}
	
//	public function user_rights($user_id) {
//		$sql = 'SELECT allow  FROM user_rights  where right_id=1 AND user_id=' . $user_id;
//		$this->query ( $sql );
//		$user_rights = array ();
//		while ( ($row = $this->fetchRowA ()) !== false ) {
//			$user_rights [] = $row;
//		}
//		return $user_rights;
//	}

    public function getAddress($user_id) {
        $sql = 'SELECT address, comment, main FROM users_address  WHERE user_id=' . $user_id;
        $this->query ( $sql );
        $items = array ();
        while ( ($row = $this->fetchRowA ()) !== false ) {
            $items [] = $row;
        }
        return $items;
    }
    public function getCards($user_id) {
        $sql = 'SELECT card_num, comment, main  FROM users_cards  WHERE user_id=' . $user_id;
        $this->query ( $sql );
        $items = array ();
        while ( ($row = $this->fetchRowA ()) !== false ) {
            $items [] = $row;
        }
        return $items;
    }

	public function checkUserData($this_user_id,$elem_type,$elem_value)	{
		$sql = "SELECT id FROM users WHERE $elem_type = '$elem_value'";
		$this->query ( $sql );
		$user_data = $this->fetchRowA ();
		$result = (isset($user_data['id']) and $user_data['id'] != $this_user_id )?'1':'0';
		return $result;
	}
	
	public function userGet($user_id) {
		
		if (! $user_id)
			return false;
		$sql = "SELECT u.id as user_id, u.*,g.id as group_id, g.name as group_name
				FROM `users` u
				LEFT JOIN `groups_user` gu ON u.id = gu.user_id
				LEFT JOIN `groups` g ON gu.group_id = g.id
				WHERE u.id = $user_id";
		$this->query ( $sql );
		$user = $this->fetchOneRowA ();
		return $user;
	}

	
	public function userList($order='', $f_name='', $f_tabno='', $f_login='', $f_group='', $f_otdel='', $id_group='') {

		$fsql = '';
		if ($id_group != '') {
			$fsql .= ' AND g.id = \'' . $id_group . '\' ';
		}
		if ($f_name != '') {
			$fsql .= ' AND u.name like \'%%%1$s%%\' ';
		}
		if ($f_tabno != '') {
			$fsql .= ' AND u.tab_no like \'%%%2$s%%\' ';
		}
		if ($f_login != '') {
			$fsql .= ' AND u.login like \'%%%3$s%%\' ';
		}
		if ($f_group != '') {
			$fsql .= ' AND g.name like \'%%%4$s%%\' ';
		}
		$order_by = ' ORDER BY u.name';
		if ($order != '') {
			$order_by = " ORDER BY $order";
		}

		$sql = "SELECT u.id as user_id, u.*,g.id as group_id, g.name as group_name
				FROM `users` u
				LEFT JOIN `groups_user` gu ON u.id = gu.user_id
				LEFT JOIN `groups` g ON gu.group_id = g.id
				WHERE u.isBan < 2 $fsql $order_by ";
		$this->query ( $sql, $f_name, $f_tabno, $f_login, $f_group, $f_otdel );
		$users = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
			$row ['date_reg'] = date ( 'd.m.Y', strtotime ( substr ( $row ['date_reg'], 0, 20 ) ) );
			$users [] = $row;
		}
		return $users;
	}

	public function getCar($car_id) {
		$sql = 'SELECT
				  id,
				  fio,
				  phone,
				  phone2,
				  telegram,
				  car_type,
				  car_year,
				  car_firm,
				  car_number,
				  car_value
				FROM cars_couriers cc
				WHERE cc.id='.$car_id.' ';
		$this->query ( $sql );
		$car = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
			$car = $row;
		}
		return $car;
	}

	public function getCarTypes() {
		$sql = 'SELECT  id,car_type	FROM car_types ct ';
		$this->query ( $sql );
		$items = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
			$items[] = $row;
		}
		return $items;
	}

	public function carsList() {
		$sql = 'SELECT
				  cc.id,
				  fio,
				  phone,
				  phone2,
				  ct.car_type,
				  car_year,
				  car_firm,
				  car_number,
				  car_value
				FROM cars_couriers cc
				  LEFT JOIN car_types ct ON cc.car_type = ct.id
				  WHERE isBan = 0
				ORDER BY cc.fio ';
		$this->query ( $sql );
		$items = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
//			$row ['date_reg'] = date ( 'd.m.Y', strtotime ( substr ( $row ['date_reg'], 0, 20 ) ) );
			$items [] = $row;
		}
		return $items;
	}

	public function carUpdate($param) {
		$sql = "
		UPDATE cars_couriers 
			SET
			  fio = '".$param['fio']."' -- fio - VARCHAR(255)
			 ,phone = '".$param['phone']."' -- phone - VARCHAR(255)
			 ,phone2 = '".$param['phone2']."' -- phone2 - VARCHAR(255)
			 ,telegram = '".$param['telegram']."' -- phone2 - VARCHAR(255)
			 ,car_type = ".$param['car_type']." -- car_type - INT(11)
			 ,car_year = ".$param['car_year']." -- car_year - INT(4)
			 ,car_firm = '".$param['car_firm']."' -- car_firm - VARCHAR(255)
			 ,car_number = '".$param['car_number']."' -- car_number - VARCHAR(255)
			 ,car_value = '".$param['car_value']."' -- car_value - VARCHAR(255)
			WHERE
			  id = ".$param['car_id']." -- id - INT(11) NOT NULL
  		";
		return $this->query ( $sql );
	}
	public function carInsert($param) {
		$sql = "
		INSERT INTO cars_couriers
			(
			 fio
			 ,phone
			 ,phone2
			 ,telegram
			 ,car_type
			 ,car_year
			 ,car_firm
			 ,car_number
			 ,car_value
			)
			VALUES
			(
			 '".$param['fio']."' -- fio - VARCHAR(255)
			 ,'".$param['phone']."' -- phone - VARCHAR(255)
			 ,'".$param['phone2']."' -- phone2 - VARCHAR(255)
			 ,'".$param['telegram']."' -- phone2 - VARCHAR(255)
			 ,".$param['car_type']." -- car_type - INT(11)
			 ,".$param['car_year']." -- car_year - INT(4)
			 ,'".$param['car_firm']."' -- car_firm - VARCHAR(255)
			 ,'".$param['car_number']."' -- car_number - VARCHAR(255)
			 ,'".$param['car_value']."' -- car_value - VARCHAR(255)
			);
  		";
		return $this->query ( $sql );
	}
	public function getLogins($page, $limCount) {
		
		$limStart = 0;
		if ($page != 0) {
			/*$count = $this->getOne ();
			if ($page > $count)
				$page = $count;*/
			if ($page < 1)
				$page = 1;
			
		//$col = 10;
			if ($limCount < 1)
				$limCount = 1;
			$limStart = ($page - 1) * $limCount;
			if ($limStart < 0)
				$limStart = 0;
		}
		
		$sql = 'SELECT 
u.name,lu.ip,lu.date,lu.referer,lu.browser,lu.os,g.name as group_name,
(SELECT COUNT(*) FROM logins) as logscount 
  FROM logins lu
  LEFT JOIN users u ON lu.id_user = u.id
  LEFT JOIN groups_user gu ON u.id = gu.user_id
  LEFT JOIN groups g ON gu.group_id = g.id
   LIMIT ' . $limStart . ', ' . ($limStart + $limCount) . ' ';
		
		$this->query ( $sql );
		
		$logins = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
			$logins [] = $row;
		}
		return $logins;
	}
	
	public function userTest($login_n) {
		$sql = 'SELECT count(id) FROM `users` u  WHERE u.login = \'%1$s\'';
		$this->query ( $sql, $login_n );
		$test = $this->getOne ();
		return $test;
	}
	
//	/**
//	 * Обновить действия только для модуля
//	 * @param $rights
//	 * @return unknown_type
//	 */
//	public function groupRightModuleUpdate($rights, $group_id) {
//
//	}
	/*
	 *
	 * @param $rights array ( mod_id = array( action_id => access));
	 *
	 */
	public function groupRightUpdate($actions, $group_id) {
		//foreach($rights as $mod => $action) {
		foreach ( $actions as $action_id => $access ) {
			$sql = "INSERT INTO `module_access` (`group_id`, `action_id`, `access`) 
                    VALUES ($group_id, $action_id, $access) ON DUPLICATE KEY UPDATE `access` = $access";
			if ($this->query ( $sql, $group_id, $action_id, $access ))
				$this->Log->addToLog ( 'Задано действие', __LINE__, __METHOD__ );
			else
				$this->Log->addError ( array ('Ошибка задания действия', $action_id, $access ), __LINE__, __METHOD__ );
		}
		//}
		return true;
	}
	public function groupReset($group_id, $module_id) {
	
	}
	
	public function groupAdd($group_name, $group_name, $parent = 0, $position = 100) {
		$sql = "INSERT INTO `groups` (`name`, `admin`, `parent`) VALUES ('$group_name', 1, $parent)";
		$this->query ( $sql );
		$group_id = $this->insertID ();
		
		if ($group_id > 0) {
			$tree = new TreeNodes ( 'groups' );
			$tree->add ( $group_id, $parent, 1, $position );
			$this->System->actionLog ( $this->mod_id, $group_id, 'Создана новая группа: ' . $group_name . '/' . $group_name, date ( 'Y-d-m h-i-s' ), $this->User->getUserID (), 1, 'groupAdd' );
		}
		return $group_id;
	}
	
	public function groupUpdate($group_name, $group_name, $group_id) {
		if ($group_id == 0)
			return false;
		$sql = "UPDATE groups SET name = '$group_name' WHERE id = $group_id";
		return $this->query ( $sql );
	}
	
	public function getActions($group_id) {
		$sql = 'SELECT ma.*,
                m.name as mod_name,
                mc.access as mcaccess,
                mc.group_id,
                ug.user_id,
                g.name as gname,
                g.name as gname,
                u.name as uname,';
		
		//         $sql.= '       IF (mc.group_id IS NOT NULL, mc.group_id, 0) as group_adm';
		/*
		$sql.= 'mc.group_id as group_adm
            FROM '.TAB_PREF.'module_actions ma
            INNER JOIN '.TAB_PREF.'modules m ON  ma.mod_id = m.id
            LEFT JOIN ('.TAB_PREF.'module_access mc
            INNER JOIN '.TAB_PREF.'groups_user ug ON mc.group_id = ug.group_id
            INNER JOIN '.TAB_PREF.'groups g ON ug.group_id = g.id AND ug.group_id = %1$u
            INNER JOIN '.TAB_PREF.'users u ON ug.user_id = u.id) ON ma.id = mc.action_id
         
            order by m.id';
		*/
		
		$sql .= 'mc.group_id as group_adm
            FROM module_actions ma
            INNER JOIN modules m ON  ma.mod_id = m.id
            LEFT JOIN module_access mc ON ma.id = mc.action_id AND mc.group_id = %1$u
            LEFT JOIN groups_user ug ON mc.group_id = ug.group_id
            LEFT JOIN groups g ON ug.group_id = g.id 
            LEFT JOIN users u ON ug.user_id = u.id 
          /*  group by ma.id*/
            order by m.id';
		
		if (isset ( $_SESSION ['authorization_LDAP'] )) {
			if ($_SESSION ['authorization_LDAP'] == 1) {
				$sql = 'SELECT ma.*,
                m.name as mod_name,
                mc.access as mcaccess,
                mc.group_id ,       
                mc.group_id as group_adm
            FROM module_actions ma            
            INNER JOIN module_access mc ON ma.id = mc.action_id AND mc.group_id  = 10
            INNER JOIN modules m ON  ma.mod_id = m.id';
			}
		}
		
		$this->query ( $sql, $group_id );
		//stop($this->sql);
//		$coll = array ();
		$lastID = 0;
//		$lastGR = '';
		$actionColl = new actionColl ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
			#stop($row);
			$groupColl = new mcColl ();
			if ($lastID != $row ['id']) {
				$Params = array ();
				$Params ['id'] = $row ['id'];
				$Params ['mod_id'] = $row ['mod_id'];
				$Params ['mod_name'] = $row ['mod_name'];
				$Params ['action_name'] = $row ['action_name'];
				$Params ['action_title'] = $row ['action_title'];
				$Params ['access'] = $row ['access'];
				$Params ['group_adm'] = $row ['group_adm'];
				$Params ['groups'] = $groupColl;
				$action = new moduleAction ( $Params );
				$actionColl->add ( $action );
				$lastID = $row ['id'];
			}
			
			$gr = array ();
			if ($row ['group_id'] > 0 && $row ['mcaccess'] > 0) {
				$gr ['id'] = $row ['group_id'];
				$gr ['action_id'] = $row ['id'];
				$gr ['group_id'] = $row ['group_id'];
				$gr ['group_name'] = $row ['gname'];
				$gr ['group_name'] = $row ['gname'];
				$gr ['user_id'] = $row ['user_id'];
				$gr ['access'] = $row ['access'];
				$gr ['module_id'] = $row ['mod_id'];
				$groupColl->addItem ( $gr );
			}
		
		}
		return $actionColl;
	}
	public function getGroupName($group_id) {
		$sql = "SELECT name FROM groups WHERE id = $group_id";
		$this->query ( $sql, $group_id );
		return $this->getOne ();
	}
	
	public function gelLogs() {
		$logs = array ();
		/*
		if ($type == 'few') {
			$sql = 'SELECT s.*, CONVERT (char(10), s.`date`, 105) as date, u.name as username
					FROM `sys_log` s
					INNER JOIN `users` u ON s.user_id = u.id
					ORDER BY s.`date` DESC';
			$this->query ( $sql );
			while ( ($row = $this->fetchOneRowA ()) !== false ) {
				$logs [] = $row;
			}
		}*/
		return $logs;
	}
}

class adminProcess extends module_process {
	public $updated;
	protected $nModel;
	protected $nView;
	
	public function __construct($modName) {
		global $values, $User, $LOG;
		
		//	if ($modName != 'admin') exit('Access denied');
		parent::__construct ( $modName );
		$this->Vals = $values;
		if (! $modName)
			unset ( $this );
		
		$this->modName = $modName;
		$this->User = $User;
		$this->Log = $LOG;
		$this->action = false;
		/* actionDefault - должно быбираться из БД!!! */
		
		$this->actionDefault = '';
		
		$this->actionsColl = new actionColl ();
		
		$this->nModel = new adminModel ( $modName );
		$sysMod = $this->nModel->getSysMod ();
		$this->sysMod = $sysMod;
		$this->mod_id = $sysMod->id;
		
		$this->nView = new adminView ( $this->modName, $this->sysMod );
		
		/* Default Process Class actions */
		$this->regAction ( 'useAdmin', 'Использование Админки', ACTION_GROUP );
		$this->regAction ( 'carBan', 'Блокировка машины/курьера', ACTION_GROUP );
		$this->regAction ( 'carEdit', 'Форма добавления/редактирования машины/курьера', ACTION_GROUP );
		$this->regAction ( 'carUpdate', 'Добавление/обновление курьера', ACTION_GROUP );
		$this->regAction ( 'carsList', 'Список машин/курьеров', ACTION_GROUP );
		$this->regAction ( 'newUser', 'Форма создания пользователя', ACTION_GROUP );
		$this->regAction ( 'addUser', 'Вставить пользователя в БД', ACTION_GROUP );
		$this->regAction ( 'userList', 'Список пользователей', ACTION_GROUP );
		$this->regAction ( 'userEdit', 'Редактировать пользователя', ACTION_GROUP );
		$this->regAction ( 'userUpdate', 'Обновить данные пользователя', ACTION_GROUP );
		$this->regAction ( 'userBan', 'Удалить пользователя в корзину', ACTION_GROUP );
		$this->regAction ( 'userUnBan', 'Восстановить пользователя', ACTION_GROUP );
		$this->regAction ( 'checkUser', 'Проверка пользователя', ACTION_GROUP );
		$this->regAction ( 'groupNew', 'Диалог создания группы', ACTION_GROUP );
		$this->regAction ( 'groupAdd', 'Добавить группу', ACTION_GROUP );
		$this->regAction ( 'groupEdit', 'Редактировать группу', ACTION_GROUP );
		$this->regAction ( 'groupUpdate', 'Обновить данные группы', ACTION_GROUP );
		$this->regAction ( 'groupHide', 'Скрыть группу', ACTION_GROUP );
		$this->regAction ( 'groupList', 'Список групп', ACTION_GROUP );
		$this->regAction ( 'groupRights', 'Права групп', ACTION_GROUP );
		$this->regAction ( 'groupRightsAdmin', 'Права групп для Администратора', ACTION_GROUP );
		$this->regAction ( 'groupRightsUpdate', 'Обновление прав групп', ACTION_GROUP );
		$this->regAction ( 'LoginsList', 'Журнал входов', ACTION_GROUP );
		$this->regAction ( 'logs', 'Журнал изменений', ACTION_GROUP );
		$this->regAction ( 'mails', 'Рассылка писем', ACTION_GROUP );
		$this->regAction ( 'getTelegramUpdates', 'Обновления телеграмма', ACTION_GROUP );
		if (DEBUG == 0) {
			$this->registerActions ( 1 );
		}
		if (DEBUG == 1) {
			$this->registerActions ( 0 );
		
		// $this->registerActions(0);
		}
	
	}
	
	public function update($_action = false) {
		$this->updated = false;
		
		if ($_action)
			$this->action = $_action;
//		$action = $this->actionDefault;
		if ($this->action)
			$action = $this->action;
		else
			$action = $this->checkAction ();
		if (! $action) {
			$this->Vals->URLparams ( $this->sysMod->defQueryString );
			$action = $this->actionDefault;
		}
		
		$user_id = $this->User->getUserID ();
		$user_right = $this->User->getRight ( $this->modName, 'useAdmin' );
		
		if ($user_right == 0) {
			$p = array ('У Вас отсутствуют административные права' );
			$this->nView->viewLogin ( $p, $user_id );
			$this->Log->addError ( $p, __LINE__, __METHOD__ );
			$this->updated = true;
			return true;
		} else {
//			$p = array ('Система администрирования' );
			//$this->nView->viewLogin($p, $user_id);
			//$this->Log->addError($p, __LINE__, __METHOD__);
			$this->updated = false;
		}
		
		$useMod = $this->Vals->getVal ( 'mod', 'GET', 'string' );
		$useAct = $this->Vals->getVal ( 'act', 'GET', 'string' );
//		$useVal = $this->Vals->getVal ( 'actval', 'GET', 'string' );
		
		$user_right = $this->User->getRight ( $useMod, $useAct );
		/*
		if ($useMod) {
			if ($this->User->getRight ( $useMod, $useAct ) == 0) {
				$p = array ('У Вас нет прав доступа к этому модулю', $useMod, $useAct );
//				$this->nView->viewLoginParams ( $p [0], '', $user_id );
				//$this->nView->viewError($p, false);
				$this->Log->addError ( $p, __LINE__, __METHOD__ );
				$this->updated = true;
				return false;
			}
			$modData = modulePreload ( $useMod );
			
			if ($modData) {
				if (is_file ( 'modules/' . $modData->module_defModName . '/' . $modData->module_defModName . '.class.php' )) {
					$this->Log->addWarning ( array ('Модуль подключен', $useMod ), __LINE__, 'index.php' );
				} else {
					$this->Log->addWarning ( array ('подключен виртуальный модуль', $useMod ), __LINE__, 'index.php' );
				}
				//stop($modData->module_isSystem.' '.$modData->module_processName,0 );
				if (! $modData->module_isSystem && ! class_exists ( $modData->module_processName ))
					include ('modules/' . $modData->module_defModName . '/' . $modData->module_defModName . '.class.php');
				if ($modData->module_isSystem && ! class_exists ( $modData->module_processName ))
					include ('classes/' . $modData->module_defModName . '.class.php');
				$autoClass = $modData->module_processName;
				//$values->URLparams($modData->module_defQueryString);
				$sysMod = new $autoClass ( $modData->module_codename );
				$sysMod->setDefaultAction ( $modData->module_defAction );
				$this->Vals->setValTo ( $useAct, $useVal, 'GET' );
				$sysMod->update ( $useAct );
				$modBodySet = $sysMod->getBody ( 'xml' );
				$this->nView->addXML ( $modBodySet, 'adm_' . $modData->module_codename );
				//$this->nView->mergeXML($this->)
				$this->updated = true;
			}
		
		}
		*/
		/* * Пользователи * */
		if ($user_right == 0 && $user_id == 0 && ! $_action) {
			$this->nView->viewLogin ( 'FD', '', $user_id );
			$this->updated = true;
			return true;
		}
		
		if ($user_id > 0 && ! $_action) {
			$this->User->nView->viewLoginParams ( 'FD', '', $user_id, array (), array (), $this->User->getRightModule ( 'admin' ) );
		}
		
		if ($action == 'newUser') {
			$groups = $this->nModel->getGroups ();
			$this->nView->viewNewUser ( $groups );
			$this->updated = true;
		}
		

		
		if ($action == 'groupHide') {
			$Params ['group_id'] = $this->Vals->getVal ( 'groupHide', 'GET', 'integer' );
			$count = $this->nModel->groupCount ( $Params ['group_id'] );
			
			if ($count ['count'] > 0) {
				$this->nView->viewError ( array ('Ошибка удаления группы. В группе есть пользователи.' ) );
			} else {
				$res = $this->nModel->groupHide ( $Params ['group_id'] );
				if ($res) {
					$this->nView->viewMessage ( 'Группа перемещена в корзину', 'Сообщение' );
//					$message1 = ' Группа удалена "SkyLC"<br />' . rn . rn;
//					$message2 = ' Группа успешно удалена<br />' . rn . rn;
//					$usInfo = '';
//					foreach ( $Params as $key => $val ) {
//						$usInfo .= $key . ' : ' . $val . '<br />' . rn;
//					}
//					$message1 .= $usInfo;
//					$message2 .= $usInfo;
				} else {
					$this->nView->viewError ( array ('Ошибка удаления группы' ) );
				}
				header ( "Location:/admin/groupList-1/" );
			}
		}
		
		if ($action == 'userBan') {
			$Params ['user_id'] = $this->Vals->getVal ( 'userBan', 'GET', 'integer' );
			$Params ['full'] = $this->Vals->getVal ( 'full', 'GET', 'integer' );
			$res = $this->nModel->userBan ( $Params ['user_id'], $Params ['full'] );
			if ($res) {
				$this->nView->viewMessage ( 'Пользователь перемещен в корзину', 'Сообщение' );
//				$message1 = ' Ваш профиль удален <br />' . rn . rn;
//				$message2 = ' Пользователь успешно удален<br />' . rn . rn;
//				$usInfo = '';
//				foreach ( $Params as $key => $val ) {
//					$usInfo .= $key . ' : ' . $val . '<br />' . rn;
//				}
//				$message1 .= $usInfo;
//				$message2 .= $usInfo;
			} else {
				$this->nView->viewError ( array ('Ошибка удаления пользователя' ) );
			}
			header ( "Location:/admin/userList-1/" );
		}
		
		if ($action == 'userUnBan') {
			$Params ['user_id'] = $this->Vals->getVal ( 'userUnBan', 'GET', 'integer' );
			$res = $this->nModel->userUnBan ( $Params ['user_id'] );
			if ($res) {
				$this->nView->viewMessage ( 'Пользователь восстановлен из корзины', 'Сообщение' );
//				$message1 = ' Ваш профиль восстановлен<br />' . rn . rn;
//				$message2 = ' Пользователь успешно восстановлен<br />' . rn . rn;
//				$usInfo = '';
//				foreach ( $Params as $key => $val ) {
//					$usInfo .= $key . ' : ' . $val . '<br />' . rn;
//				}
//				$message1 .= $usInfo;
//				$message2 .= $usInfo;
			} else {
				$this->nView->viewError ( array ('Ошибка восстановления пользователя' ) );
			}
			header ( "Location:/admin/userList-1/" );
		}
		
		if ($action == 'userUpdate') {
			$Params ['user_id'] = $this->Vals->getVal ( 'user_id', 'POST', 'integer' );

			$Params ['username'] = $this->Vals->getVal ( 'username', 'POST', 'string' );
			$Params ['email'] = $this->Vals->getVal ( 'email', 'POST', 'string' );
			$Params ['title'] = $this->Vals->getVal ( 'title', 'POST', 'string' );
			$Params ['login'] = $this->Vals->getVal ( 'login', 'POST', 'string' );
			$Params ['phone'] = $this->Vals->getVal ( 'phone', 'POST', 'string' );
			$Params ['phone_mess'] = $this->Vals->getVal ( 'phone_mess', 'POST', 'string' );
			$Params ['pass'] = $this->Vals->getVal ( 'pass', 'POST', 'string' );
			$Params ['group_id'] = $this->Vals->getVal ( 'group_id', 'POST', 'integer' );
			$Params ['address'] = $this->Vals->getVal ( 'address', 'POST', 'array' );
			$Params ['addr_comment'] = $this->Vals->getVal ( 'addr_comment', 'POST', 'array' );
			$Params ['credit_card'] = $this->Vals->getVal ( 'credit_card', 'POST', 'array' );
			$Params ['card_comment'] = $this->Vals->getVal ( 'card_comment', 'POST', 'array' );
			$Params ['isAutoPass'] = $this->Vals->getVal ( 'isAutoPass', 'POST', 'integer' );
			$Params ['isBan'] = $this->Vals->getVal ( 'isBan', 'POST', 'integer' );
			
			if ($Params ['isAutoPass'] > 0) {
				$pass = $this->generatePass ( 6 );
				$Params ['pass'] = $pass;
			}
			// $username, $email, $login, $pass, $ip, $group_id
			if ($Params ['username'] != '' && $Params ['email'] != '' && $Params ['group_id'] > 0) {
				if ($Params ['user_id'] == 0) {
					$res = $this->nModel->userInsert ( $Params );
					$msg = 'добавлен';
				}else{
					$res = $this->nModel->userUpdate ( $Params );
					$msg = 'обновлен';
				}
				if ($res) {
					//					$this->System->actionLog($this->mod_id, $Params['user_id'], 'Пользователь обновлен: '.$Params['username'], dateToDATETIME (date('Y-d-m h-i-s')), $this->User->getUserID(), 1, $action);
					$this->nView->viewMessage ( 'Профиль клиента успешно '.$msg, 'Сообщение' );
					$message1 = ' Ваш профиль '.$msg.'<br />' . rn . rn;
					$message2 = ' Профиль клиента успешно '.$msg.'<br />' . rn . rn;
					$usInfo = '';
					foreach ( $Params as $key => $val ) {
						$usInfo .= $key . ' : ' . (is_array($val)?json_encode($val):$val) . '<br />' . rn;
					}
					$message1 .= $usInfo;
					$message2 .= $usInfo;
				
					sendMail('Профиль '.$msg, $message1, $Params['email'],'Интранет портал');
					sendMail('Пользователь '.$msg, $message2, 'djidi@mail.ru','Интранет портал');
				} else {
					$this->nView->viewError ( array ('Ошибка профиля' ) );
				}
			} else {
				$this->nView->viewError ( array ('Заполнены не все обязательные поля', $Params ['username'], $Params ['email'], $Params ['group_id'] ) );
				return true;
			}
			$action = 'userList';
			$this->updated = true;
		}
		
		if ($action == 'userEdit') {
			$user_id = $this->Vals->getVal ( 'userEdit', 'GET', 'integer' );

			$user = ($user_id > 0)?$this->nModel->userGet ( $user_id ):array();
			$address = ($user_id > 0)?$this->nModel->getAddress ($user_id):array();
			$cards = ($user_id > 0)?$this->nModel->getCards ($user_id):array();
			$groups = $this->nModel->getGroups ();

			$this->nView->viewUserEdit ( $user, $groups, $address, $cards );

			$this->updated = true;
		}

		if ($action == 'carBan') {
			$id = $this->Vals->getVal ( 'carBan', 'GET', 'integer' );
			$res = $this->nModel->carBan ( $id );
			if ($res) {
				$this->nView->viewMessage ( 'Курьер заблокирован', 'Сообщение' );
			} else {
				$this->nView->viewError ( array ('Ошибка блокировки' ) );
			}
			header ( "Location:/admin/carsList-1/" );
		}
		
		if ($action == 'userList') {
			$order = $this->Vals->getVal ( 'srt', 'POST', 'string' );
			$f_name = $this->Vals->getVal ( 'f_name', 'POST', 'string' );
			$f_tabno = $this->Vals->getVal ( 'f_tabno', 'POST', 'string' );
			$f_login = $this->Vals->getVal ( 'f_login', 'POST', 'string' );
			$f_group = $this->Vals->getVal ( 'f_group', 'POST', 'string' );
			$f_otdel = $this->Vals->getVal ( 'f_otdel', 'POST', 'string' );
			$id_group = $this->Vals->getVal ( 'idg', 'INDEX', 'string' );
			$isAjax = $this->Vals->getVal ( 'ajax', 'INDEX' );
			$users = $this->nModel->userList ( $order, $f_name, $f_tabno, $f_login, $f_group, $f_otdel, $id_group );
			$groups = $this->nModel->getGroups ();
			$this->nView->viewUserList ( $users, $order, $isAjax, $id_group, $groups );
			$this->updated = true;
		}
		/* * Конец Пользователи * */
//user_id:user_id,elem_type:elem_type,value:elem_val
		if ($action == 'checkUser') {
			$this_user_id = $this->Vals->getVal ( 'user_id', 'POST', 'string' );
			$elem_type = $this->Vals->getVal ( 'elem_type', 'POST', 'string' );
			$elem_value = $this->Vals->getVal ( 'value', 'POST', 'string' );
			$result = $this->nModel->checkUserData($this_user_id,$elem_type,$elem_value);
			echo $result;
			exit();
		}

		if ($action == 'carEdit') {
			$car_id = $this->Vals->getVal ( 'carEdit', 'GET', 'integer' );
			$car = $this->nModel->getCar ( $car_id );
			$types = $this->nModel->getCarTypes ();
			$this->nView->viewCarEdit ( $car, $types);
			$this->updated = true;
		}
		if ($action == 'carUpdate') {
			$param['car_id'] = $this->Vals->getVal ( 'car_id', 'POST', 'integer' );
			$param['fio'] = $this->Vals->getVal ( 'fio', 'POST', 'string' );
			$param['phone'] = $this->Vals->getVal ( 'phone', 'POST', 'string' );
			$param['phone2'] = $this->Vals->getVal ( 'phone2', 'POST', 'string' );
			$param['telegram'] = $this->Vals->getVal ( 'telegram', 'POST', 'string' );
			$param['car_firm'] = $this->Vals->getVal ( 'car_firm', 'POST', 'string' );
			$param['car_number'] = $this->Vals->getVal ( 'car_number', 'POST', 'string' );
			$param['car_year'] = $this->Vals->getVal ( 'car_year', 'POST', 'integer' );
			$param['car_value'] = $this->Vals->getVal ( 'car_value', 'POST', 'integer' );
			$param['car_type'] = $this->Vals->getVal ( 'car_type', 'POST', 'integer' );
			if ($param['car_id'] > 0) {
				$result = $this->nModel->carUpdate($param);
			}else{
				$result = $this->nModel->carInsert($param);
			}
			if ($result){
				$this->nView->viewMessage('Операция выполнена успешно', '' );
			}else{
				$this->nView->viewError ( array ('Ошибка выполнения' ) );
			}
			$action = 'carsList';
		}
		if ($action == 'carsList') {
			$cars = $this->nModel->carsList ( );
			$this->nView->viewCarsList ( $cars );
			$this->updated = true;
		}
		/* * Группы * */
		
		if ($action == 'groupNew') {
			$this->nView->viewNewGroup ();
			$this->updated = true;
		}
		
		if ($action == 'groupAdd') {
			$group_name = $this->Vals->getVal ( 'name', 'POST', 'string' );
			if ($group_name != '')
				$this->nModel->groupAdd ( $group_name, $group_name );
			else
				$this->nView->viewError ( array ('Укажите название группы (rus)' ) );
			$action = 'groupList';
		}
		
		if ($action == 'groupEdit') {
			$group_id = $this->Vals->getVal ( 'groupEdit', 'GET', 'integer' );
			$group_name = $this->nModel->getGroupName ( $group_id );
			$this->nView->viewEditGroup ( $group_name, $group_name, $group_id );
			$this->updated = true;
		}
		if ($action == 'groupUpdate') {
			$group_id = $this->Vals->getVal ( 'group_id', 'POST', 'integer' );
			$group_name = $this->Vals->getVal ( 'name', 'POST', 'string' );
			if (! $this->nModel->groupUpdate ( $group_name, $group_name, $group_id ))
				$this->nView->viewError ( array ('Ошибка обновления группы' ) );
			
		//	else $this->System->actionLog($this->mod_id, $group_id, 'Обновлена группа: '.$group_name.'/'.$group_name, dateToDATETIME (date('d-m-Y h-i-s')), $this->User->getUserID(), 1, $action);
			$action = 'groupList';
		}
		
		if ($action == 'groupRightsUpdate') {
			$actions = $this->Vals->getVal ( 'action', 'POST', 'array' );
			$group_id = $this->Vals->getVal ( 'group_id', 'POST', 'integer' );
			if ($group_id < 1) {
				$this->nView->viewError(array('Группа не найдена'));
				return true;
			}
			
		//stop($actions);
			if ($this->nModel->groupRightUpdate ( $actions, $group_id )) {
				$this->nView->viewMessage ( 'Права группы обновлены', 'Сообщение' );
			
		//	$this->System->actionLog($this->mod_id, $group_id, 'Обновлены права группы: '.$group_id, dateToDATETIME (date('d-m-Y h-i-s')), $this->User->getUserID(), 1, $action);
			} else {
				$this->nView->viewError ( array ('Ошибка обновления группы' ) );
			}
		}
		
		if ($action == 'groupList') {
			
			$groups = $this->nModel->getGroups ();
			$this->nView->viewGroups ( $groups );
			$this->updated = true;
		}
		
		if ($action == 'LoginsList') {
			
			$limCount = $this->vals->getVal ( 'count', 'get', 'integer' );
			if (! $limCount)
				$limCount = $this->vals->getModuleVal ( $this->modName, 'count', 'GET' );
			$page = $this->vals->getVal ( 'page', 'GET', 'integer' );
			if ($page <= 0 || $page === NULL) {
				$this->Vals->setValTo ( 'page', '1', 'GET' );
				$page = 1;
			}
			if ($limCount == 0)
				$limCount = 20;
			
			$logins = $this->nModel->getLogins ( $page, $limCount );
			
			$Archive = new archiveStruct ( $this->modName, $logins [0] ['logscount'], $limCount, $page, '' );
			
			$this->nView->viewLogins ( $logins, $Archive );
			$this->updated = true;
		}
		
		if ($action == 'groupRights') {
			$group_id = $this->Vals->getVal ( 'groupRights', 'GET', 'integer' );
			$actions = $this->nModel->getActions ( $group_id );
			$group_name = $this->nModel->getGroupName ( $group_id );
			$this->nView->viewGroupRight ( $actions, $group_name, $group_id );
			$this->updated = true;
		}
		
		if ($action == 'groupRightsAdmin') {
			$group_id = $this->Vals->getVal ( 'groupRightsAdmin', 'GET', 'integer' );
			$actions = $this->nModel->getActions ( $group_id );
			$group_name = $this->nModel->getGroupName ( $group_id );
			$this->nView->viewGroupRightAdmin ( $actions, $group_name, $group_name, $group_id );
			$this->updated = true;
		}
		/* * Конец Группы * */
		
		if ($action == 'logs') {
			$type = 'few';
			$logs = $this->nModel->gelLogs (  );
			$this->nView->viewLogs ( $logs, $type );
			$this->updated = true;
		}

		if ($action == 'getTelegramUpdates') {
			$users = $this->nModel->userList();
			$response =  $this->callApiTlg('getUpdates', array(), TLG_TOKEN);
			$items = array();
			foreach ($response->result as $data){
                if (isset($data->message)) {
                    $from = $data->message->from;
                    $chat = $data->message->chat;
                    $date = $data->message->date;
                    $text = $data->message->text;

                    $row['user_name'] = $from->first_name . " " .
                        (isset($from->last_name) ? $from->last_name : '') .
                        (isset($from->username) ? " [" . $from->username . "]" : '');
                    $row['chat_id'] = $chat->id;
                    $row['date'] = date('d.m.Y H:i', $date);
                    $row['text'] = $text;
                    $items[] = $row;
                }
			}
			$this->nView->viewTelegramUpdates ( $items, $users );
			$this->updated = true;
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
		
		if (! $this->updated) {
			$this->nView->viewMainPage ();
			$this->updated = true;
		}
	    return true;
	}
	
	function generatePass($length = 6) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789_;.";
		$code = "";
		$clean = strlen ( $chars ) - 1;
		while ( strlen ( $code ) < $length ) {
			$code .= $chars [mt_rand ( 0, $clean )];
		}
		return $code;
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
		if(curl_error($ch))
		{
			stop ('error:' . curl_error($ch));
		}
		return json_decode($response);
	}

}

class adminView extends module_view {
	public function __construct($modName, modsetItem $sysMod) {
		parent::__construct ( $modName, $sysMod );
		$this->pXSL = array ();
	}
	
	public function addXML(bodySet $bodySet, $contName) {
		$this->pXSL = array_merge ( $this->pXSL, $bodySet->getXSL () );
		$Container = $this->newContainer ( $contName );
		parent::mergeXML ( $this->xml, $Container, $bodySet->getXML (), 'xx' );
	}
	
	public function viewNewUser($groups) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/user.new.xsl';
		$Container = $this->newContainer ( 'newuser' );
		$ContainerGroups = $this->addToNode ( $Container, 'groups', '' );
		// stop($groups);
		foreach ( $groups as $item ) {
			$this->arrToXML ( $item, $ContainerGroups, 'item' );
		}
		return true;
	}
	/*
	 *
	 *
	 * @param $users array()
	 * @return boolean
	 */
	public function viewUserList($users, $order, $isAjax, $id_group, $groups) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/user.list.xsl';
		if ($isAjax == 1) {
			$this->pXSL [] = RIVC_ROOT . 'layout/head.page.xsl';
		}
		
		$Container = $this->newContainer ( 'userlist' );
		$Containerusers = $this->addToNode ( $Container, 'users', '' );
		$this->addAttr ( 'order', $order, $Containerusers );
		$this->addAttr ( 'id_group', $id_group, $Containerusers );
		foreach ( $users as $user ) {
			$this->arrToXML ( $user, $Containerusers, 'user' );
		}
		$ContainerGroups = $this->addToNode ( $Container, 'groups', '' );
		foreach ( $groups as $item ) {
			$this->arrToXML ( $item, $ContainerGroups, 'item' );
		}
		return true;
	}

	public function viewCarsList($cars) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/cars.list.xsl';

		$Container = $this->newContainer ( 'carslist' );
		$ContainerCars = $this->addToNode ( $Container, 'cars', '' );
		foreach ( $cars as $user ) {
			$this->arrToXML ( $user, $ContainerCars, 'car' );
		}
		return true;
	}

	public function viewCarEdit($car, $car_types) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/car.edit.xsl';
		$Container = $this->newContainer ( 'caredit' );
		$this->arrToXML ( $car, $Container, 'car' );
		$ContainerCarTypes = $this->addToNode ( $Container, 'car_types', '' );
		foreach ( $car_types as $item ) {
			$this->arrToXML ( $item, $ContainerCarTypes, 'item' );
		}
		return true;
	}

	public function viewMainPage() {
		$this->pXSL [] = RIVC_ROOT . 'layout/admin/admin.main.xsl';
		$this->newContainer ( 'adminmain' );
		return true;
	}
	
	public function viewUserEdit($user, $groups, $address, $cards) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/user.edit.xsl';
		$Container = $this->newContainer ( 'useredit' );
		$this->arrToXML ( $user, $Container, 'user' );
		$ContainerGroups = $this->addToNode ( $Container, 'groups', '' );
		foreach ( $groups as $item ) {
			$this->arrToXML ( $item, $ContainerGroups, 'item' );
		}
        $ContainerAddress = $this->addToNode ( $Container, 'address', '' );
        foreach ( $address as $item ) {
            $this->arrToXML ( $item, $ContainerAddress, 'item' );
        }
        $ContainerCards = $this->addToNode ( $Container, 'cards', '' );
        foreach ( $cards as $item ) {
            $this->arrToXML ( $item, $ContainerCards, 'item' );
        }
//		$ContainerRights = $this->addToNode ( $Container, 'user_rights', '' );
//		foreach ( $user_rights as $item ) {
//			$this->arrToXML ( $item, $ContainerRights, 'item' );
//		}
		
		return true;
	}
	
	public function viewGroups($groups) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/group.list.xsl';
		$Container = $this->newContainer ( 'grouplist' );
		$ContainerGroups = $this->addToNode ( $Container, 'groups', '' );
		foreach ( $groups as $item ) {
			$this->arrToXML ( $item, $ContainerGroups, 'item' );
		}
		return true;
	}
	
	public function viewNewGroup() {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/group.new.xsl';
		$this->newContainer ( 'groupnew' );
		return true;
	}
	
	public function viewEditGroup($group_name, $group_name, $group_id) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/group.edit.xsl';
		$Container = $this->newContainer ( 'groupedit' );
		$groupC = $this->addToNode ( $Container, 'group', '' );
		$this->addAttr ( 'group_id', $group_id, $groupC );
		$this->addAttr ( 'group_name', $group_name, $groupC );
		$this->addAttr ( 'group_name', $group_name, $groupC );
		return true;
	}
	
	public function viewGroupRight(actionColl $actions, $group_name, $group_id) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/group.rights.xsl';
		$Container = $this->newContainer ( 'grouprights' );
		$actIterator = $actions->getIterator ();
		$ContainerAct = $this->addToNode ( $Container, 'actions', '' );
		$this->addAttr ( 'group_id', $group_id, $ContainerAct );
		$this->addAttr ( 'group_name', $group_name, $ContainerAct );
		/* moduleAction */
		//$action = new moduleAction();
		$lastMod = 0;
		foreach ( $actIterator as $action ) {
			if ($lastMod != $action->mod_id) {
				$modElememt = $this->addToNode ( $ContainerAct, 'module', '' );
				$this->addAttr ( 'mod_name', $action->mod_name, $modElememt );
				$this->addAttr ( 'mod_id', $action->mod_id, $modElememt );
				$lastMod = $action->mod_id;
			}
			$aArray = $action->toArray ();
            if (isset($modElememt)) {
                $actElememt = $this->arrToXML($aArray, $modElememt, 'action');
                if ($action->groups->count() > 0) {
                    $this->addToNode($actElememt, 'inGroup', $action->groups->count());
                } else {
                    $this->addToNode($actElememt, 'inGroup', 0);
                }
            }
		//stop($action->groups->count(), 0);
		}
		return true;
	}
	
	public function viewGroupRightAdmin(actionColl $actions, $group_name, $group_name, $group_id) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/group.rights.Admin.xsl';
		$Container = $this->newContainer ( 'grouprights' );
		$actIterator = $actions->getIterator ();
		$ContainerAct = $this->addToNode ( $Container, 'actions', '' );
		$this->addAttr ( 'group_id', $group_id, $ContainerAct );
		$this->addAttr ( 'group_name', $group_name, $ContainerAct );
		$this->addAttr ( 'group_name', $group_name, $ContainerAct );
		/* moduleAction */
		//$action = new moduleAction();
		$lastMod = 0;
		foreach ( $actIterator as $action ) {
			if ($lastMod != $action->mod_id) {
				$modElememt = $this->addToNode ( $ContainerAct, 'module', '' );
				$this->addAttr ( 'mod_name', $action->mod_name, $modElememt );
				$this->addAttr ( 'mod_id', $action->mod_id, $modElememt );
				$lastMod = $action->mod_id;
			}
			$aArray = $action->toArray ();
            if (isset($modElememt)) {
                $actElememt = $this->arrToXML($aArray, $modElememt, 'action');
                if ($action->groups->count() > 0) {
                    $this->addToNode($actElememt, 'inGroup', $action->groups->count());
                } else {
                    $this->addToNode($actElememt, 'inGroup', 0);
                }
            }
		//stop($action->groups->count(), 0);
		}
		return true;
	}
	
	public function viewLogins($logins, archiveStruct $Archive) {
		$this->pXSL [] = RIVC_ROOT . 'layout/admin/admin.logins.xsl';
		$Container = $this->newContainer ( $Archive->module . '/LoginsList-1' );
		$this->addAttr ( 'module', $Archive->module . '/LoginsList-1', $Container );
		$this->addAttr ( 'count', $Archive->count, $Container );
		$this->addAttr ( 'size', $Archive->size, $Container );
		$this->addAttr ( 'curPage', $Archive->curPage, $Container );
		foreach ( $logins as $aArray ) {
            $this->arrToXML($aArray, $Container, 'item');
        }
		return true;
	}
	
	public function viewLogs($logs, $type) {
		$this->pXSL [] = RIVC_ROOT . 'layout/admin/admin.logs.xsl';
		$Container = $this->newContainer ( 'logsfew' );
		
		if ($type == 'few') {
			foreach ( $logs as $aArray ) {
                $this->arrToXML($aArray, $Container, 'item');
            }
		}
		return true;
	}

	public function viewTelegramUpdates($items,$users) {
		$this->pXSL [] = RIVC_ROOT . 'layout/admin/telegram.logs.xsl';
		$Container = $this->newContainer ( 'messages' );
		foreach ( $items as $aArray ) {
			$this->arrToXML($aArray, $Container, 'item');
		}
		$Containerusers = $this->addToNode ( $Container, 'users', '' );
		foreach ( $users as $user ) {
			$this->arrToXML ( $user, $Containerusers, 'user' );
		}
		return true;
	}



}