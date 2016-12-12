<?php

class tcItem extends module_item
{
    public $id;

    public function __construct($Params, $prefix = '')
    {
        parent::__construct();
        if ($prefix != '')
            $prefix .= '_';
        if (isset ($Params [$prefix . 'id']))
            $this->id = $Params [$prefix . 'id'];
        else
            $this->id = 0;
        $this->notInsert ['id'] = 1;
    }

    public function toArray()
    {
        $Params ['id'] = $this->id;
        return $Params;
    }

}

class tcCollection extends module_collection
{

    public function __construct()
    {

        parent::__construct();

    }

    public function addItem($Params)
    {

        $item = new tcItem ($Params);

        $this->add($item);

    }

}

class tcModel extends module_model
{
    public function __construct($modName)
    {
        parent::__construct($modName);
    }

    public function userBan($user_id)
    {
        $sql = 'UPDATE ' . TAB_PREF . '`tc_tourists` SET `ban` = 1 WHERE `id` = %1$u';
        $this->query($sql, $user_id);
        return true;
    }

    public function userDelete($user_id)
    {
        $sql = 'DELETE FROM ' . TAB_PREF . '`tc_tourists` WHERE `id` = %1$u';
        $this->query($sql, $user_id);
        return true;
    }

    public function turDel($tur_id)
    {
        $sql = 'DELETE FROM ' . TAB_PREF . '`tc_tur` WHERE `id` = %1$u';
        $this->query($sql, $tur_id);
        return true;
    }

    public function travelBan($travel_id)
    {
        $sql = 'DELETE FROM ' . TAB_PREF . '`tc_tur_list` WHERE `id` = %1$u';
        $this->query($sql, $travel_id);
        return true;
    }

    public function locDel($loc_id)
    {
        $sql = 'DELETE FROM ' . TAB_PREF . '`tc_locations` WHERE `id` = %1$u';
        $this->query($sql, $loc_id);
        return true;
    }

    public function userGet($user_id)
    {
        if (!$user_id)
            return false;
        $sql = 'SELECT 
		tt.id, tt.name_f,tt.name_i,tt.name_o, tt.phone, tt.dob, tt.passport,
		tt.def_mp,tt.country,c.`code`, mp.`name`, tt.`comment`, tt.dk, 
		(SELECT count(*) FROM ' . TAB_PREF . 'tc_tur_list tl WHERE tl.id_tourist=tt.id AND refused=0) as c_tours
		FROM ' . TAB_PREF . '`tc_tourists` tt
		LEFT JOIN ' . TAB_PREF . 'tc_mp mp ON tt.def_mp = mp.id 
		LEFT JOIN ' . TAB_PREF . 'tc_countrys c ON tt.country = c.id 
		WHERE tt.id = %1$u and tt.ban=0';
        $this->query($sql, $user_id);
        $user = $this->fetchOneRowA();
        $user ['dob'] = ($user ['dob'] != '0000-00-00' and $user ['dob'] != '1970-01-01') ? $this->mydate_to_dmy($user ['dob']) : '';
        return $user;
    }

    public function getDobList()
    {
        $sql = 'SELECT *,(SELECT count(*) FROM ' . TAB_PREF . 'tc_tur_list tl WHERE tl.id_tourist=tt.id AND refused=0) as c_tours
		 FROM ' . TAB_PREF . '`tc_tourists` tt WHERE `dob` <>  \'1970-01-01\' and `phone` <> \'\' and IF (366 - DAYOFYEAR(NOW()) > 20,
        (DAYOFYEAR(dob) - DAYOFYEAR(NOW()) < 20) AND (DAYOFYEAR(dob) - DAYOFYEAR(NOW()) >= 0),
        ((DAYOFYEAR(dob) - DAYOFYEAR(NOW()) < 20) AND (DAYOFYEAR(dob) - DAYOFYEAR(NOW()) >= 0)) OR 
		((366 - DAYOFYEAR(dob) - DAYOFYEAR(NOW()) < 20) AND (366 - DAYOFYEAR(dob) - DAYOFYEAR(NOW()) >= 0))
    ) 
    ORDER BY MONTH(dob),DAYOFMONTH(dob)';
        /* ORDER BY DAYOFYEAR(dob),dob';*/
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $row ['dob'] = ($row ['dob'] != '0000-00-00' and $row ['dob'] != '1970-01-01') ? $this->mydate_to_dmy($row ['dob']) : '';
            $items [] = $row;
        }
        return $items;
    }

    public function getLocList()
    {
        $sql = 'SELECT tl.id, tl.name, tl.color, tl.actual, COUNT(ttl.id) turs 
				FROM ' . TAB_PREF . 'tc_locations tl
				LEFT JOIN ' . TAB_PREF . 'tc_mp tm ON tl.id = tm.id_loc
				LEFT JOIN ' . TAB_PREF . 'tc_tur_list ttl ON tm.id = ttl.id_mp
				GROUP BY tl.id, tl.name, tl.color, tl.actual';
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $items [] = $row;
        }
        return $items;
    }

    public function getLoc($id)
    {
        $sql = 'SELECT id,name,color,actual FROM ' . TAB_PREF . 'tc_locations WHERE id = ' . $id . '';
        $this->query($sql);
        return $row = $this->fetchRowA();
    }

    public function turGet($id)
    {
        $sql = 'SELECT t.*
				FROM ' . TAB_PREF . '`tc_tur` t	WHERE t.id = %1$u';
        $this->query($sql, $id);
        $tur = $this->fetchOneRowA();
        $tur ['date'] = $this->mydate_to_dmy($tur ['date']);
        $tur ['date_to'] = $this->mydate_to_dmy($tur ['date_to']);
        return $tur;
    }

    public function GetLastTur()
    {
        $sql = 'SELECT t.`id` id	FROM ' . TAB_PREF . '`tc_tur` t	WHERE `date`>now() ORDER BY `date` LIMIT 0,1';
        $this->query($sql);
        $tur = $this->fetchOneRowA();
        return $tur ['id'];
    }

    /* Список туров (заказов) */
    public function travelGet($id)
    {
        $sql = 'SELECT tl.id,tl.id_tur,tl.id_mp,tl.id_tourist,tt.name_f,tt.name_i,tt.name_o, tt.phone, tt.dob, 
		mp.`name`, tl.book_date, tl.`comment`,tl.book_num,tl.cabin,tl.number,tl.new_site,tl.payed, tl.refused
		FROM ' . TAB_PREF . '`tc_tur_list` tl
		LEFT JOIN ' . TAB_PREF . 'tc_tourists tt ON tl.id_tourist = tt.id
		LEFT JOIN ' . TAB_PREF . 'tc_mp mp ON tl.id_mp = mp.id
		WHERE tl.id = %1$u and tt.ban=0';
        $this->query($sql, $id);
        $tur = $this->fetchOneRowA();
        $tur ['dob'] = ($tur ['dob'] != '0000-00-00' and $tur ['dob'] != '1970-01-01') ? $this->mydate_to_dmy($tur ['dob']) : '';
        $tur ['book_date'] = ($tur ['book_date'] != '0000-00-00 00:00:00') ? $this->mydate_to_dmyhi($tur ['book_date']) : '';
        return $tur;
    }

    public function getTourists()
    {
        $sql = "
				SELECT id, name_f, name_i, name_o, dob, def_mp 
				FROM `tc_tourists` WHERE name_f != '' and ban=0 ORDER BY name_f;
				";
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $row ['dob'] = ($row ['dob'] != '0000-00-00' and $row ['dob'] != '1970-01-01') ? $this->mydate_to_dmy($row ['dob']) : '';
            $row ['name'] = $row ['name_f'] . ' ' . $row ['name_i'] . ' ' . $row ['name_o'] . ' (' . $row ['dob'] . ') ';
            $items [] = $row;
        }
        return $items;
    }

    public function getTouristsByMask($mask, $turist_id)
    {
        $where_id_t = ($turist_id > 0) ? " id=$turist_id " : " name_f LIKE '$mask%%' ";
        $sql = "
				SELECT id, name_f, name_i, name_o, dob, def_mp 
				FROM `tc_tourists` WHERE ( $where_id_t ) and ban=0 ORDER BY name_f,name_i,name_o ;
				";
        $this->query($sql);

        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $row ['dob'] = ($row ['dob'] != '0000-00-00' and $row ['dob'] != '1970-01-01') ? $this->mydate_to_dmy($row ['dob']) : '';
            $row ['name'] = $row ['name_f'] . ' ' . $row ['name_i'] . ' ' . $row ['name_o'] . ' (' . $row ['dob'] . ') ';
            $items [] = $row;
        }
        return $items;
    }

    public function getCountrys()
    {
        $sql = 'SELECT * FROM ' . TAB_PREF . 'tc_countrys ORDER BY sort';
        $this->query($sql);
        $groups = array();
        while (($row = $this->fetchRowA()) !== false) {
            $groups [] = $row;
        }
        return $groups;
    }

    public function getMP()
    {
        $sql = 'SELECT * FROM ' . TAB_PREF . 'tc_mp WHERE order_add>0  ORDER BY  order_add ASC ';
        $this->query($sql);
        $groups = array();
        while (($row = $this->fetchRowA()) !== false) {
            $groups [] = $row;
        }
        return $groups;
    }

    public function saveLoc($name)
    {
        $sql = 'INSERT INTO ' . TAB_PREF . '`tc_locations` (`name`) VALUES (\'%1$s\')';
        $this->query($sql, $name);
        return $this->affectedRows();
    }

    public function saveCity($name, $name_en, $country)
    {
        $sql = 'INSERT INTO ' . TAB_PREF . '`tc_citys` (`name`, `name_en`,`country`) VALUES (\'%1$s\',\'%2$s\', %3$u)';
        $this->query($sql, $name, $name_en, $country);
        return $this->affectedRows();
    }

    public function saveMP($name, $loc_mp)
    {
        $sql = 'INSERT INTO ' . TAB_PREF . '`tc_mp` (`name`, `id_loc`) VALUES (\'%1$s\', %2$u)';
        $this->query($sql, $name, $loc_mp);
        return $this->affectedRows();
    }

    public function saveCT($name, $code)
    {
        $sql = 'INSERT INTO ' . TAB_PREF . '`tc_countrys` (`name`, `code`) VALUES (\'%1$s\', \'%2$s\')';
        $this->query($sql, $name, $code);
        return $this->affectedRows();
    }

    public function saveData($type, $params)
    {
        $table = '';
        switch ($type) {
            case 'gidNew':
                $table = 'tc_gids';
                break;
            case 'busNew':
                $table = 'tc_bus';
                break;
            case 'ctNew':
                $table = 'tc_countrys';
                break;
            case 'mpNew':
                $table = 'tc_mp';
                break;
            case 'cityNew':
                $table = 'tc_citys';
                break;
            case 'locNew':
                $table = 'tc_locations';
                break;
        }
        $keys = array();
        $values = array();
        foreach ($params as $key => $value) {
            $keys[] = '`' . $key . '`';
            $values[] = '\'' . $value . '\'';
        }
        $key_r = implode(',', $keys);
        $values_r = implode(',', $values);

        $sql = 'INSERT INTO ' . TAB_PREF . $table . ' (' . $key_r . ') VALUES (' . $values_r . ')';
        $this->query($sql);
        return $this->affectedRows();
    }

    public function UpdateData($type, $params)
    {
        $table = '';
        switch ($type) {
            case 'gidNew':
                $table = 'tc_gids';
                break;
            case 'busNew':
                $table = 'tc_bus';
                break;
            case 'ctNew':
                $table = 'tc_countrys';
                break;
            case 'mpNew':
                $table = 'tc_mp';
                break;
            case 'cityNew':
                $table = 'tc_citys';
                break;
            case 'locEdit':
                $table = 'tc_locations';
                break;
        }
        $set = array();
        foreach ($params as $key => $value) {
            if ($key != 'id')
                $set[] = '`' . $key . '`' . '=' . '\'' . $value . '\'';
        }
        $set_r = implode(',', $set);

        $sql = 'UPDATE ' . TAB_PREF . $table . ' SET ' . $set_r . ' WHERE id = ' . $params['id'] . '';
        $this->query($sql);
        return true;
    }

    public function getLocations()
    {
        $sql = 'SELECT * FROM ' . TAB_PREF . 'tc_locations';
        $this->query($sql);
        $groups = array();
        while (($row = $this->fetchRowA()) !== false) {
            $groups [] = $row;
        }
        return $groups;
    }

    public function TravelList($order, $id_tur)
    {
        $fsql = '';
        $fsql .= ($id_tur != '') ? ' AND tl.id_tur = \'%1$u\' ' : $fsql;
        $order_by = ($order) ? " ORDER BY $order" : ' ORDER BY tl.book_num DESC, tt.name_f,tt.name_i,tt.name_o';

        $sql = 'SELECT tl.id,tl.id_tur,tt.name_f,tt.name_i,tt.name_o,tt.dob, 
		IF( tl.new_phone <> \'\',tl.new_phone,tt.phone) phone, IF( tl.new_passport <> \'\', tl.new_passport, tt.passport ) passport,  
		mp.`name`, tl.book_date,tl.book_num, tl.`comment`,tl.`cabin`,tl.`number`,tl.new_site, tl.payed, tl.refused,
		(SELECT count(*) FROM ' . TAB_PREF . 'tc_tur_list tl2 WHERE tl2.id_tourist=tt.id AND refused=0) as c_tours,tl.id_tourist,br.status,br.dk
		, tl.agent_id, u.name agent_name, u.tab_no
		FROM ' . TAB_PREF . '`tc_tur_list` tl
		LEFT JOIN ' . TAB_PREF . 'tc_tourists tt ON tl.id_tourist = tt.id
		LEFT JOIN ' . TAB_PREF . 'tc_mp mp ON tl.id_mp = mp.id
		LEFT JOIN ' . TAB_PREF . 'tc_view_bank_results br ON br.Order_ID = tl.id
		LEFT JOIN ' . TAB_PREF . 'users u ON u.id = tl.agent_id
		WHERE 1=1 ' . $fsql . $order_by;

        $this->query($sql, $id_tur);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $row ['dob'] = ($row ['dob'] != '0000-00-00' and $row ['dob'] != '1970-01-01') ? $this->mydate_to_dmy($row ['dob']) : '';
            $row ['dk'] = ($row ['dk'] != '0000-00-00' and $row ['dk'] != '1970-01-01') ? $this->mydate_to_dmy($row ['dk']) : '';
            $row ['book_date'] = ($row ['book_date'] != '0000-00-00 00:00:00') ? $this->mydate_to_dmyhi($row ['book_date']) : '';
            $items [] = $row;
        }
        return $items;
    }

    public function AgentReportList($order, $id_tur)
    {
        $fsql = '';
        //	$fsql .= ($id_tur != '')?' AND tl.id_tur = \'%1$u\' ':$fsql;
        $order_by = ($order) ? " ORDER BY $order" : ' ORDER BY tl.book_num DESC, tt.name_f,tt.name_i,tt.name_o';

        $sql = 'SELECT tl.id,tl.id_tur,tt.name_f,tt.name_i,tt.name_o,tt.dob, 
		ifnull(tl.new_phone,tt.phone) phone, ifnull(tl.new_passport,tt.passport) passport,  
		mp.`name`, tl.book_date,tl.book_num, tl.`comment`,tl.`cabin`,tl.`number`,tl.new_site, tl.payed, tl.refused,
		(SELECT count(*) FROM ' . TAB_PREF . 'tc_tur_list tl2 WHERE tl2.id_tourist=tt.id AND refused=0) as c_tours,tl.id_tourist,br.status,br.dk
		, tl.agent_id, u.name agent_name, u.tab_no, t.name tur_name, t.date tur_date, t.id tur_id
		FROM ' . TAB_PREF . '`tc_tur_list` tl
		LEFT JOIN ' . TAB_PREF . 'tc_tourists tt ON tl.id_tourist = tt.id
		LEFT JOIN ' . TAB_PREF . 'tc_mp mp ON tl.id_mp = mp.id
		LEFT JOIN ' . TAB_PREF . 'tc_view_bank_results br ON br.Order_ID = tl.id
		LEFT JOIN ' . TAB_PREF . 'users u ON u.id = tl.agent_id
		LEFT JOIN ' . TAB_PREF . 'tc_tur t ON t.id = tl.id_tur
		WHERE tl.agent_id > 0 ' . $fsql . $order_by;

        $this->query($sql, $id_tur);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $row ['dob'] = ($row ['dob'] != '0000-00-00' and $row ['dob'] != '1970-01-01') ? $this->mydate_to_dmy($row ['dob']) : '';
            $row ['dk'] = ($row ['dk'] != '0000-00-00' and $row ['dk'] != '1970-01-01') ? $this->mydate_to_dmy($row ['dk']) : '';
            $row ['book_date'] = ($row ['book_date'] != '0000-00-00 00:00:00') ? $this->mydate_to_dmyhi($row ['book_date']) : '';
            $items [] = $row;
        }
        return $items;
    }

    public function AgentTravelList($order, $id_tur, $uid)
    {
        $fsql = '';
        $fsql .= ($id_tur != '') ? ' AND tl.id_tur = \'%1$u\' ' : $fsql;
        $order_by = ($order) ? " ORDER BY $order" : ' ORDER BY tl.book_num DESC, tt.name_f,tt.name_i,tt.name_o';

        $sql = 'SELECT tl.id,tl.id_tur,tt.name_f,tt.name_i,tt.name_o,tt.dob, 
		ifnull(tl.new_phone,tt.phone) phone, ifnull(tl.new_passport,tt.passport) passport,  
		mp.`name`, tl.book_date,tl.book_num, tl.`comment`,tl.`cabin`,tl.`number`,tl.new_site, tl.payed, tl.refused,
		(SELECT count(*) FROM ' . TAB_PREF . 'tc_tur_list tl2 WHERE tl2.id_tourist=tt.id AND refused=0) as c_tours,tl.id_tourist,br.status,br.dk
		FROM ' . TAB_PREF . '`tc_tur_list` tl
		LEFT JOIN ' . TAB_PREF . 'tc_tourists tt ON tl.id_tourist = tt.id
		LEFT JOIN ' . TAB_PREF . 'tc_mp mp ON tl.id_mp = mp.id
		LEFT JOIN ' . TAB_PREF . 'tc_view_bank_results br ON br.Order_ID = tl.id
		WHERE agent_id=\'' . $uid . '\' ' . $fsql . $order_by;

        $this->query($sql, $id_tur);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $row ['dob'] = ($row ['dob'] != '0000-00-00' and $row ['dob'] != '1970-01-01') ? $this->mydate_to_dmy($row ['dob']) : '';
            $row ['dk'] = ($row ['dk'] != '0000-00-00' and $row ['dk'] != '1970-01-01') ? $this->mydate_to_dmy($row ['dk']) : '';
            $row ['book_date'] = ($row ['book_date'] != '0000-00-00 00:00:00') ? $this->mydate_to_dmyhi($row ['book_date']) : '';
            $items [] = $row;
        }
        return $items;
    }

    public function SearchOrder($order)
    {
        $sql = 'SELECT tl.id,tl.id_tur,tt.name_f,tt.name_i,tt.name_o, tt.phone, tt.dob,tt.passport,
		mp.`name`, tl.book_date,tl.book_num, tl.`comment`,tl.`cabin`,tl.`number`,tl.new_site, tl.payed, tl.refused,
		(SELECT count(*) FROM ' . TAB_PREF . 'tc_tur_list tl2 WHERE tl2.id_tourist=tt.id AND refused=0) as c_tours,tl.id_tourist,tc.name tur_name
		FROM ' . TAB_PREF . '`tc_tur_list` tl
		LEFT JOIN ' . TAB_PREF . 'tc_tur tc ON tl.id_tur = tc.id
		LEFT JOIN ' . TAB_PREF . 'tc_tourists tt ON tl.id_tourist = tt.id
		LEFT JOIN ' . TAB_PREF . 'tc_mp mp ON tl.id_mp = mp.id
	
		WHERE tl.id_tur = (SELECT tl2.id_tur FROM ' . TAB_PREF . 'tc_tur_list tl2  WHERE tl2.id = \'' . $order . '\')
		AND tl.book_num = (SELECT tl3.book_num FROM ' . TAB_PREF . 'tc_tur_list tl3  WHERE tl3.id = \'' . $order . '\')';

        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $row ['dob'] = ($row ['dob'] != '0000-00-00' and $row ['dob'] != '1970-01-01') ? $this->mydate_to_dmy($row ['dob']) : '';
            $row ['book_date'] = ($row ['book_date'] != '0000-00-00 00:00:00') ? $this->mydate_to_dmyhi($row ['book_date']) : '';
            $items [] = $row;
        }
        return $items;

    }

    public function SearchPayOrder($order)
    {
        $sql = 'SELECT tl.id,tl.id_tur,tt.name_f,tt.name_i,tt.name_o, tt.phone, tt.dob,tt.passport,
		mp.`name`, tl.book_date,tl.book_num, tl.`comment`,tl.`cabin`,tl.`number`,tl.new_site, tl.payed,tc.cost,
		(SELECT count(*) FROM ' . TAB_PREF . 'tc_tur_list tl2 WHERE tl2.id_tourist=tt.id AND refused=0) as c_tours,tl.id_tourist,tc.name tur_name
		,br.status,br.dk
		FROM ' . TAB_PREF . '`tc_tur_list` tl
		LEFT JOIN ' . TAB_PREF . 'tc_tur tc ON tl.id_tur = tc.id
		LEFT JOIN ' . TAB_PREF . 'tc_tourists tt ON tl.id_tourist = tt.id
		LEFT JOIN ' . TAB_PREF . 'tc_mp mp ON tl.id_mp = mp.id
		LEFT JOIN ' . TAB_PREF . 'tc_bank_results br ON br.Order_ID = tl.id
	
		WHERE tl.id = \'' . $order . '\'';

        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $row ['dob'] = ($row ['dob'] != '0000-00-00' and $row ['dob'] != '1970-01-01') ? $this->mydate_to_dmy($row ['dob']) : '';
            $row ['book_date'] = ($row ['book_date'] != '0000-00-00 00:00:00') ? $this->mydate_to_dmyhi($row ['book_date']) : '';
            $items [] = $row;
        }
        return $items[0];

    }

    public function tourGet($tur_id)
    {
        $sql = 'SELECT t.`id`, t.`name`,`date`,`date_to`,c.`name` city_name, l.`name` loc_name, l.`id` loc_id,
		g.`name` gid_name, b.`number` bus_number,cost,currency,
		(select count(*) from tc_tur_list tl WHERE tl.id_tur = t.`id`) turists
		FROM ' . TAB_PREF . '`tc_tur` t
		LEFT JOIN ' . TAB_PREF . 'tc_citys c ON t.id_city = c.id
		LEFT JOIN ' . TAB_PREF . 'tc_locations l ON t.id_loc = l.id
		LEFT JOIN ' . TAB_PREF . 'tc_gids g ON t.id_gid = g.id
		LEFT JOIN ' . TAB_PREF . 'tc_bus b ON t.id_bus = b.id
		WHERE t.`id` = ' . $tur_id . '';
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $row['daydiff'] = floor((abs(strtotime($row ['date_to']) - strtotime($row ['date'])) / (60 * 60 * 24)));
            $row ['date'] = $this->mydate_to_dmy($row ['date']);
            $row ['date_to'] = $this->mydate_to_dmy($row ['date_to']);
            $items [] = $row;
        }
        return $items[0];
    }

    public function userList($order, $f_name, $f_phone)
    {

        $fsql = '';
        $fsql .= ($f_name != '') ? ' AND tt.name_f like \'%1$s%%\' ' : $fsql;
        $fsql .= ($f_phone != '') ? ' AND tt.phone like \'%%%2$s%%\' ' : '';
        $order_by = ($order) ? " ORDER BY $order" : ' ORDER BY tt.name_f,tt.name_i,tt.name_o';

        $limits = ($fsql == '') ? ' LIMIT 0, 100' : ' LIMIT 0, 1000';

        $sql = '
		SELECT t.*, IFNULL(ct.c_tours,0) c_tours 
		FROM (
			SELECT 
			tt.id, tt.name_f,tt.name_i,tt.name_o, tt.phone, tt.dob, tt.passport,
			c.`code`, mp.`name`, tt.`comment`, tt.dk
			FROM ' . TAB_PREF . '`tc_tourists` tt
			LEFT JOIN ' . TAB_PREF . 'tc_mp mp ON tt.def_mp = mp.id 
			LEFT JOIN ' . TAB_PREF . 'tc_countrys c ON tt.country = c.id 
			WHERE 1=1 and tt.ban=0 ' . $fsql . $order_by . $limits . ' ) t
		LEFT JOIN ' . TAB_PREF . 'tc_count_tours ct ON ct.id_tourist=t.id
		';

        $this->query($sql, $f_name, $f_phone);

        //stop($this->sql);

        $users = array();
        while (($row = $this->fetchRowA()) !== false) {
            $row ['dob'] = ($row ['dob'] != '0000-00-00' and $row ['dob'] != '1970-01-01') ? $this->mydate_to_dmy($row ['dob']) : '';
            $users [] = $row;
        }
        return $users;

    }


    public function turList($order, $f_name, $country, $target, $id_type, $start_date, $end_date)
    {
        $fsql = '';
        $fsql .= ($id_type != '') ? ' AND tur_type=\'' . $id_type . '\' ' : '';
        $fsql .= ($f_name != '') ? ' AND t.`name` like \'%%' . $f_name . '%%\' ' : '';
        $fsql .= ($country != '') ? ' AND l.`name` like \'%%' . $country . '%%\' ' : '';
        $fsql .= ($target != '') ? ' AND g.`target_name` like \'%%' . $target . '%%\' ' : '';
        $order_by = ($order) ? " ORDER BY $order" : ' ORDER BY `date`';

        $sql = 'SELECT t.`id`, t.`name`,`date`,`date_to`,c.`name` city_name, l.`name` loc_name,
				g.`target_name` gid_name, b.`transp_name` bus_number,cost,currency,t.overview,t.bus_size,
				tt.name_type,t.fire,
 				(select count(*) from tc_tur_list tl WHERE tl.id_tur = t.`id`) turists,
 				(select count(*) from tc_tur_list tl WHERE tl.id_tur = t.`id` and new_site = 1) new_turists,
 				(select count(*) FROM `tc_view_bank_results` vbr LEFT JOIN tc_tur_list tl ON tl.id = vbr.Order_ID WHERE tl.id_tur = t.`id` and tl.payed <1) new_pays
				FROM ' . TAB_PREF . '`tc_tur` t
				LEFT JOIN ' . TAB_PREF . 'tc_citys c ON t.id_city = c.id
				LEFT JOIN ' . TAB_PREF . 'tc_countrys l ON t.id_loc = l.id
				LEFT JOIN ' . TAB_PREF . 'tc_tur_targets g ON t.tur_target = g.id
				LEFT JOIN ' . TAB_PREF . 'tc_tur_transports b ON t.tur_transport = b.id
				LEFT JOIN tc_tur_types tt ON tt.id = t.tur_type
				WHERE `date` >= \'' . $start_date . '\' AND `date` <= \'' . $end_date . '\' ' . $fsql . $order_by . '
		  		LIMIT 0,100';
        //	stop($sql);
        $this->query($sql, $f_name);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $row ['date'] = $this->mydate_to_dmy($row ['date']);
            $row ['date_to'] = $this->mydate_to_dmy($row ['date_to']);
            $items [] = $row;
        }
        return $items;
    }

    function mydate_to_dmy($date)
    {
        return ($date != '0000-00-00') ? date('d.m.Y', strtotime($date)) : '';
    }

    function mydate_to_dmyhi($date)
    {
        return ($date != '0000-00-00') ? date('d.m.Y H:i', strtotime($date)) : '';
    }

    function dmy_to_mydate($date)
    {
        return date('Y-m-d', strtotime($date));
    }

    public function travelUpdate($params)
    {
        $sql = 'UPDATE ' . TAB_PREF . '`tc_tur_list` SET
	`id_tur` = \'%1$u\',`id_tourist` = \'%2$u\',`id_mp` = \'%3$u\',dk=NOW(),
	/*`book_date` = \'%4$s\',*/`comment` = \'%5$s\',`book_num` = \'%7$u\',`number` = \'%8$s\',`cabin` = \'%9$s\',`new_site` = \'%10$u\',payed=\'%11$u\', refused=\'%12$u\'
	WHERE `id` = \'%6$u\'';
        if (!$this->query($sql, $params['tur_id'], $params['id_tourist'], $params['id_mp'],
            $this->dmy_to_mydate($params['book_date']), $params['comment'], $params['id'],
            $params['book_num'], $params['number'], $params['cabin'], $params['new_site'], $params['payed'], $params['refused'])
        )
            return false;
        return true;
    }

    public function travelAdd($params)
    {
        $sql = 'INSERT INTO ' . TAB_PREF . '`tc_tur_list`
	(`id_tur`,`id_tourist`,`id_mp`,`book_date`,`comment`,`book_num`,`number`,`cabin`)
	VALUES (\'%1$u\',\'%2$u\',\'%3$u\',\'%4$s\',\'%5$s\',\'%6$u\',\'%7$s\',\'%8$s\')';
        if (!$this->query($sql, $params['tur_id'], $params['id_tourist'], $params['id_mp'],
            $this->dmy_to_mydate($params['book_date']), $params['comment'], $params['book_num'], $params['number'], $params['cabin'])
        )
            return false;
        return true;
    }

    public function turUpdate($params)
    {

        foreach ($params['date'] as $key => $date) {
            $sql = 'UPDATE ' . TAB_PREF . '`tc_tur` SET dk=NOW(),
		`name` = \'' . $params['name'] . '\',
		`date` = \'' . $this->dmy_to_mydate($date) . '\',
		`date_to` = \'' . $this->dmy_to_mydate($params['date_to'][$key]) . '\',
		`days` = \'' . $params['days'][$key] . '\', 
		`id_city` = \'' . $params['id_city'] . '\', 
		`tur_transport` = \'' . $params['id_transport'] . '\', 
		`tur_target` = \'' . $params['id_target'] . '\', 
		`overview` = \'' . htmlspecialchars(str_replace('%', '%%', $params['overview']), ENT_QUOTES) . '\', 
		`id_loc` = \'' . $params['id_loc'] . '\',
		`cost` = \'' . $params['cost'] . '\',
		`currency` = \'' . $params['currency'] . '\',  
		`tur_type` = \'' . $params['id_type'] . '\', 
		`dop_info` = \'' . $params['dop_info'] . '\', 
		`fire` = \'' . $params['fire'] . '\', 
		`bus_size` = \'' . $params['bus_size'] . '\', 
		`comment` = \'' . $params['comment'] . '\'
		WHERE `id` = \'' . $params['tur_id'] . '\'';
            if (!$this->query($sql))
                return false;
        }
        return true;
    }

    public function turAdd($params)
    {
        foreach ($params['date'] as $key => $date) {
            // Если не указана вторая дата, то устанавливаем ее равной первой.
            $date_to = (trim($params['date_to'][$key]) != '') ? $params['date_to'][$key] : $date;

            $sql = 'INSERT INTO ' . TAB_PREF . '`tc_tur` 
		(`name`,
		`date`,
		`date_to`,
		`days`,
		`id_city`,
		`tur_transport`,
		`tur_target`,
		`id_loc`,
		`cost`,
		`currency`,
		`tur_type`,
		`overview`,
		`bus_size`,
		`comment`, dk) 
		VALUES (
		\'' . $params['name'] . '\',
		\'' . $this->dmy_to_mydate($date) . '\',
		\'' . $this->dmy_to_mydate($date_to) . '\',
		\'' . $params['days'][$key] . '\',
		\'' . $params['id_city'] . '\',
		\'' . $params['id_transport'] . '\',
		\'' . $params['id_target'] . '\',
		\'' . $params['id_loc'] . '\',
		\'' . $params['cost'] . '\',
		\'' . $params['currency'] . '\',
		\'' . $params['id_type'] . '\',
		\'' . htmlspecialchars(str_replace('%', '%%', $params['overview']), ENT_QUOTES) . '\',
		\'' . $params['bus_size'] . '\',
		\'' . $params['comment'] . '\', NOW()
		)';
            if (!$this->query($sql))
                return false;
        }
        return true;
    }

    public function userUpdate($params)
    {
        $sql = 'UPDATE ' . TAB_PREF . '`tc_tourists` SET
	`name_f` = \'%1$s\',`name_i` = \'%2$s\',`name_o` = \'%3$s\', 
	`dob` = \'%4$s\',`passport` = \'%5$s\',`phone` = \'%6$s\',
	`def_mp` = \'%7$u\',`country` = \'%8$u\',`comment` = \'%9$s\'
	WHERE `id` = \'%10$u\'';
        if (!$this->query($sql, $params['username_f'], $params['username_i'], $params['username_o'],
            $this->dmy_to_mydate($params['dob']), $params['passport'], $params['phone'],
            $params['def_mp'], $params['country'], $params['comment'], $params['user_id'])
        )
            return false;
        return true;
    }

    public function userAdd($params)
    {
        $sql = 'INSERT INTO ' . TAB_PREF . '`tc_tourists` 
	(`name_f`,`name_i`,`name_o`,`dob`,`passport`,`phone`,`def_mp`,`country`,`comment`) 
	VALUES (\'%1$s\',\'%2$s\',\'%3$s\',\'%4$s\',\'%5$s\',\'%6$s\',\'%7$u\',\'%8$u\',\'%9$s\')';
        if (!$this->query($sql, $params['username_f'], $params['username_i'], $params['username_o'],
            $this->dmy_to_mydate($params['dob']), $params['passport'], $params['phone'],
            $params['def_mp'], $params['country'], $params['comment'])
        )
            return false;
        return true;
    }

    public function getTurs($id_tur)
    {
        $sql = 'SELECT id, `name`, `date`, cost FROM ' . TAB_PREF . '`tc_tur` WHERE `date`>now() or id = \'' . $id_tur . '\' ORDER BY `date` ASC';
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $items[] = $row;
        }
        return $items;
    }

    public function getNewPays()
    {
        $sql = 'SELECT count(tl.id) count_pays 
				FROM ' . TAB_PREF . '`tc_view_bank_results` vbr
				LEFT JOIN ' . TAB_PREF . 'tc_tur_list tl ON tl.id = vbr.Order_ID
				WHERE tl.payed < 1';
        $this->query($sql);
        $row = $this->fetchRowA();
        return $row['count_pays'];
    }

    public function getStoryList()
    {
        $sql = 'SELECT t.id, t.name, t.date, t.overview, l.`name` country
			FROM  ' . TAB_PREF . '`tc_tur` t
			LEFT JOIN ' . TAB_PREF . 'tc_countrys l ON t.id_loc = l.id
			WHERE overview <>  \'\'
			GROUP BY (t.name)
			ORDER BY t.name, t.date';
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $items[] = $row;
        }
        return $items;
    }

    public function getStory($id)
    {
        $sql = 'SELECT t.overview
			FROM  ' . TAB_PREF . '`tc_tur` t
			WHERE t.id = '.$id.'
			ORDER BY t.name, t.date';
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $items[] = $row;
        }
        return $items;
    }

    public function getLocs()
    {
        $sql = 'SELECT * FROM ' . TAB_PREF . '`tc_locations`';
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $items[] = $row;
        }
        return $items;
    }

    public function getTypes()
    {
        $sql = 'SELECT * FROM ' . TAB_PREF . '`tc_tur_types`';
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $items[] = $row;
        }
        return $items;
    }

    public function getTargets()
    {
        $sql = 'SELECT * FROM ' . TAB_PREF . '`tc_tur_targets`';
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $items[] = $row;
        }
        return $items;
    }

    public function getTransport()
    {
        $sql = 'SELECT * FROM ' . TAB_PREF . '`tc_tur_transports`';
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $items[] = $row;
        }
        return $items;
    }

    public function getCitys()
    {
        $sql = 'SELECT id, `name`, name_en, country FROM ' . TAB_PREF . '`tc_citys`';
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $items[] = $row;
        }
        return $items;
    }

    public function getGids()
    {
        $sql = 'SELECT * FROM ' . TAB_PREF . '`tc_gids`';
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $items[] = $row;
        }
        return $items;
    }

    public function getBus()
    {
        $sql = 'SELECT * FROM ' . TAB_PREF . '`tc_bus`';
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $items[] = $row;
        }
        return $items;
    }

    public function getPages()
    {
        $sql = 'SELECT id,title,description FROM ' . TAB_PREF . 'pages WHERE module=11 ORDER BY title';
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $items[] = $row;
        }
        return $items;
    }

    public function getDogovor()
    {
        $sql = 'SELECT * FROM ' . TAB_PREF . 'pages WHERE id = 50';
        $this->query($sql);
        return $row = $this->fetchRowA();
    }


    public function listGid($id_tur)
    {
        $sql = 'SELECT 
t.`name`, t.`date`,
l.book_date, tt.name_f,tt.name_i,tt.name_o,
tt.phone, tt.dob, tt.passport, mp.`name`,
l.`comment`
FROM ' . TAB_PREF . '`tc_tur_list` l
LEFT JOIN ' . TAB_PREF . 'tc_tur t ON l.id_tur = t.id
LEFT JOIN ' . TAB_PREF . 'tc_tourists tt ON l.id_tourist = tt.id
LEFT JOIN ' . TAB_PREF . 'tc_mp mp ON l.id_mp = mp.id
	WHERE l.id_tur=\'' . $id_tur . '\'
ORDER BY mp.`prior`,tt.name_f,tt.name_i,tt.name_o';
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $items[] = $row;
        }
        $sql = "SELECT CONCAT(`name`, ' (',`date`,')') as `name` FROM " . TAB_PREF . "`tc_tur` WHERE id = '" . $id_tur . "'";
        $this->query($sql);
        $row = $this->fetchRowA();
        return $this->table_for_gid($items, $row['name']);
    }


    public function listBorder($id_tur)
    {
        $sql = 'SELECT 
t.`date`, c.name_en,
tt.name_f,tt.name_i,
tt.dob, tt.passport, tc.`code`
FROM ' . TAB_PREF . '`tc_tur_list` l
LEFT JOIN ' . TAB_PREF . 'tc_tur t ON l.id_tur = t.id
LEFT JOIN ' . TAB_PREF . 'tc_tourists tt ON l.id_tourist = tt.id
LEFT JOIN ' . TAB_PREF . 'tc_mp mp ON l.id_mp = mp.id
LEFT JOIN ' . TAB_PREF . 'tc_citys c ON t.id_city = c.id
LEFT JOIN ' . TAB_PREF . 'tc_countrys tc ON tt.country = tc.id
	WHERE l.id_tur=\'' . $id_tur . '\'';
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $items[] = $row;
        }

        return $this->table_for_border($items);
    }

    public function listBooking($id_tur)
    {
        $sql = 'SELECT
	tt.name_f,tt.name_i,
	tt.dob, tt.passport, l.cabin, l.number
	FROM ' . TAB_PREF . '`tc_tur_list` l
	LEFT JOIN ' . TAB_PREF . 'tc_tur t ON l.id_tur = t.id
	LEFT JOIN ' . TAB_PREF . 'tc_tourists tt ON l.id_tourist = tt.id
	WHERE l.id_tur=\'' . $id_tur . '\'';
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            $items[] = $row;
        }

        return $this->table_for_booking($items);
    }

    function table_for_booking($items)
    {
        $i = 0;
        $table = '
	<table border="1">
	<tbody>
	<tr>
	<th>#</th><th></th><th></th><th></th><th>каюты</th><th>отель</th>
	</tr>
	';
        foreach ($items as $item) {
            $i++;
            $table .= '<tr>
		<td>' . $i . '</td>
		<td>' . strtoupper($this->GetInTranslit($item['name_f'])) . ' ' . strtoupper($this->GetInTranslit($item['name_i'])) . '</td>
		<td>' . $item['dob'] . '</td>
		<td>\'' . $item['passport'] . '</td>
		<td>' . $item['cabin'] . '</td>
		<td>' . $item['number'] . '</td>
		</tr>
		';
        }
        $table .= '</tbody></table>';
        return $table;
    }

    function table_for_border($items)
    {
        $i = 0;
        $table = '
<table border="0"><tr><td>' . $items[0]['date'] . ' </td><td></td><td>
' . strtoupper($items[0]['name_en']) . '</td></tr></table>
	<table border="1">
	<tbody>
	<tr>
	<th>#</th><th colspan="2">Tourist</th><th></th><th></th><th></th>
	</tr>
	';
        foreach ($items as $item) {
            $i++;
            $table .= '<tr>
		<td> </td>
		<td>' . strtoupper($this->GetInTranslit($item['name_f'])) . '</td>
		<td>' . strtoupper($this->GetInTranslit($item['name_i'])) . '</td>
		<td>' . $item['dob'] . '</td>
		<td>\'' . $item['passport'] . '</td>
		<td>' . $item['code'] . '</td>
		</tr>
		';
        }
        $table .= '</tbody></table>';
        return $table;
    }

    function table_for_gid($items, $name)
    {
        $i = 0;
        $table = '
	<table border="1">
	<thead>
	<tr><th colspan="7">' . $name . '</th></tr>
	</thead>';
        $tbody = '<tbody>
	<tr>
	<th>Место посадки</th><th>№ п/п</th><th>ФИО</th><th>Телефон</th>
	<th>Дата рождения</th><th>№ паспорта</th><th>Комментарий</th>
	</tr>
	';
        foreach ($items as $item) {
            $i++;
            $tbody .= '<tr>
		<td>' . $item['name'] . '</td>
		<td>' . $i . '</td>
		<td>' . $item['name_f'] . ' ' . $item['name_i'] . ' ' . $item['name_o'] . '</td>
		<td>\'' . $item['phone'] . '</td>
		<td>' . $item['dob'] . '</td>
		<td>\'' . $item['passport'] . '</td>
		<td>' . $item['comment'] . '</td>
		</tr>
		';
        }
        $tbody .= '</tbody></table>';
        $tbody = iconv("UTF-8", "UTF-8//IGNORE", $tbody);
        return $table . $tbody;
    }

    function GetInTranslit($string)
    {
        $replace = array(
            "'" => "",
            "`" => "",
            "а" => "a", "А" => "a",
            "б" => "b", "Б" => "b",
            "в" => "v", "В" => "v",
            "г" => "g", "Г" => "g",
            "д" => "d", "Д" => "d",
            "е" => "e", "Е" => "e",
            "ж" => "zh", "Ж" => "zh",
            "з" => "z", "З" => "z",
            "и" => "i", "И" => "i",
            "й" => "y", "Й" => "y",
            "к" => "k", "К" => "k",
            "л" => "l", "Л" => "l",
            "м" => "m", "М" => "m",
            "н" => "n", "Н" => "n",
            "о" => "o", "О" => "o",
            "п" => "p", "П" => "p",
            "р" => "r", "Р" => "r",
            "с" => "s", "С" => "s",
            "т" => "t", "Т" => "t",
            "у" => "u", "У" => "u",
            "ф" => "f", "Ф" => "f",
            "х" => "kh", "Х" => "kh",
            "ц" => "tc", "Ц" => "tc",
            "ч" => "ch", "Ч" => "ch",
            "ш" => "sh", "Ш" => "sh",
            "щ" => "shch", "Щ" => "shch",
            "ъ" => "", "Ъ" => "",
            "ы" => "y", "Ы" => "y",
            "ь" => "", "Ь" => "",
            "э" => "e", "Э" => "e",
            "ю" => "iu", "Ю" => "iu",
            "я" => "ia", "Я" => "ia",
            "і" => "i", "І" => "i",
            "ї" => "yi", "Ї" => "yi",
            "є" => "e", "Є" => "e"
        );
        return $str = iconv("UTF-8", "UTF-8//IGNORE", strtr($string, $replace));
    }

}

class tcProcess extends module_process
{
    public function __construct($modName)
    {
        global $values, $User, $LOG, $System;
        parent::__construct($modName);
        $this->Vals = $values;
        $this->System = $System;
        if (!$modName)
            unset ($this);
        $this->modName = $modName;
        $this->User = $User;
        $this->Log = $LOG;
        $this->action = false;
        /*
		 * actionDefault - Действие по умолчанию. Должно браться из БД!!!
		 */
        $this->actionDefault = '';
        $this->actionsColl = new actionColl ();
        $this->nModel = new tcModel ($modName);
        $sysMod = $this->nModel->getSysMod();
        $this->sysMod = $sysMod;
        $this->nView = new tcView ($this->modName, $this->sysMod);
        $this->regAction('viewTur', 'Список туров', ACTION_GROUP);
        $this->regAction('viewlist', 'Список туристов', ACTION_GROUP);
        $this->regAction('viewTurList', 'Редактирование списков на отправление', ACTION_GROUP);
        $this->regAction('travelEdit', 'Редактирование участника тура', ACTION_GROUP);
        $this->regAction('travelEditSelect', 'Поиск участника тура', ACTION_GROUP);
        $this->regAction('turEdit', 'Редактирование тура', ACTION_GROUP);
        $this->regAction('turDel', 'Удаление тура', ACTION_GROUP);
        $this->regAction('userEdit', 'Редактирование записи о туристе', ACTION_GROUP);
        $this->regAction('userBan', 'Удаление записи о туристе', ACTION_GROUP);
        $this->regAction('userDelete', 'Блокирование записи о туристе', ACTION_GROUP);
        $this->regAction('turUpdate', 'Изменение тура', ACTION_GROUP);
        $this->regAction('userUpdate', 'Изменение записи о туристе', ACTION_GROUP);
        $this->regAction('travelUpdate', 'Изменение записи об участнике тура', ACTION_GROUP);
        $this->regAction('travelBan', 'Исключение туриста из поездки', ACTION_GROUP);
        $this->regAction('countryNew', 'Добавить страну', ACTION_GROUP);
        $this->regAction('mpNew', 'Добавить место посадки', ACTION_GROUP);
        $this->regAction('locNew', 'Добавить район посадки', ACTION_GROUP);
        $this->regAction('locEdit', 'Редактировать район посадки', ACTION_GROUP);
        $this->regAction('cityNew', 'Добавить город назначения', ACTION_GROUP);
        $this->regAction('gidNew', 'Добавить гида', ACTION_GROUP);
        $this->regAction('busNew', 'Добавить транспорт', ACTION_GROUP);
        $this->regAction('dobList', 'Ближайшие дни рождения', ACTION_GROUP);
        $this->regAction('PrintList', 'Печать списков для Гида и на Границу', ACTION_GROUP);
        $this->regAction('search_order', 'Поиск заказа', ACTION_GROUP);
        $this->regAction('LocList', 'Справочник районов', ACTION_GROUP);
        $this->regAction('locDel', 'Удаление районов', ACTION_GROUP);
        $this->regAction('BankReq', 'Запрос банку', ACTION_GROUP);
        $this->regAction('getDogovor', 'Формирование договора', ACTION_GROUP);
        $this->regAction('agent', 'Раздел Агента', ACTION_GROUP);
        $this->regAction('viewAgentReportList', 'Отчет по бронированиям Агента', ACTION_GROUP);
        $this->regAction('viewStoryList', 'Программы туров', ACTION_GROUP);


        if (DEBUG == 0) {
            $this->registerActions(1);
        }
        if (DEBUG == 1) {
            $this->registerActions(0);
        }
    }

    /* Запрос в банк о результатах */
    public function check_bank_results($Order_ID)
    {
        //$items = $this->nModel->SearchPayOrder ( $Order_ID);
        $postdata = http_build_query(
            array(
                'Shop_ID' => '0788126593-3756',
                'Login' => '2717',
                'Password' => 'Ot2cxNrp2bn1QAI89XH8NPbyMpierGBsnsUI50ZcieaOLvOBYe5KROJpHWoiHWoZA3MPwFe9FaPwLYHX',
                'Format' => '4',
                'ShopOrderNumber' => $Order_ID
                /*		,
				'StartDay' => date('d',strtotime('-5 day', strtotime($items['dk']))),
				'StartMonth' => date('m',strtotime($items['dk'])),
				'StartYear' => date('Y',strtotime($items['dk'])),
				'EndDay' => date('d',strtotime('+1 day', strtotime($items['dk']))),
				'EndMonth' => date('m',strtotime($items['dk'])),
				'EndYear' => date('Y',strtotime($items['dk']))*/
            )
        );
        // Создать контекст и инициализировать POST запрос
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                'content' => $postdata,
            ),
        ));
        $XML = file_get_contents('https://test.wpay.uniteller.ru/results/', false, $context);
        $doc = new DOMDocument;
        $doc->loadXML($XML);
        $result = $doc->getElementsByTagName('status')->item(1)->nodeValue; // Результат оплаты
        $total = $doc->getElementsByTagName('total')->item(1)->nodeValue; // Сумма оплаты
        //	$date =  $doc->getElementsByTagName('date')->item(1)->nodeValue; // Дата оплаты
        return array($result, $total);
    }


    public function update($_action = false)
    {

        if ($_action)
            $this->action = $_action;
        if ($this->action)
            $action = $this->action;
        else
            $action = $this->checkAction();
        if (!$action) {
            $this->Vals->URLparams($this->sysMod->defQueryString);
            $action = $this->actionDefault;
        }
        $user_id = $this->User->getUserID();
        $user_right = $this->User->getRight($this->modName, $action);
        if ($user_right == 0 && $user_id > 0) {
            $p = array('У Вас нет прав для использования модуля', '$this->modName' => $this->modName, 'action' => $action, 'user_id' => $user_right, 'user_right' => $user_right);
            $this->nView->viewError('У Вас нет прав на это действие', 'Предупреждение');
            $this->Log->addError($p, __LINE__, __METHOD__);

            return;
        }

        if ($user_right == 0 && $user_id == 0 && !$_action) {
            $this->nView->viewLogin('Система БЛТ', '', $user_id, array(), array());

            return;
        }

        if ($user_id > 0 && !$_action) {
            $this->User->nView->viewLoginParams('Система БЛТ', '', $user_id, array(), array(), $this->User->getRight('admin', 'view'));
        }

        /**
         *  authorized — средства успешно заблокированы (выполнена авторизационная транзакция);
         *  not authorized — средства не заблокированы (авторизационная транзакция не выполнена) по ряду причин.
         *  paid — оплачен (выполнена финансовая транзакция или заказ оплачен в электронной платёжной системе);
         *  canceled — отменён (выполнена транзакция разблокировки средств или выполнена операция по возврату платежа после списания средств).
         *  waiting — ожидается оплата выставленного счёта. Статус используется только для оплат электронными валютами, при
         * которых процесс оплаты может содержать этап выставления через систему Uniteller счёта на оплату и этап фактической
         * оплаты этого счёта Покупателем, которые существенно разнесённы во времени.
         */
        if ($action == 'BankReq') {
            $Order_ID = $this->Vals->getVal('BankReq', 'GET', 'integer');

            list($result, $total) = $this->check_bank_results($Order_ID);

            if (empty($result)) {
                list($result, $total) = $this->check_bank_results($Order_ID * 1000);
            }

            $good_res = array('authorized', 'paid');
            if (in_array(strtolower($result), $good_res)) {
                $items = $this->nModel->SearchPayOrder($Order_ID);
                if ($total == $items['cost']) {// Все в порядке
                    exit('Заказ оплачен. <br /> Номер заказа: ' . $Order_ID);
                } else {//Сумма не сходится
                    exit('Сумма не сходится.<br /> Проверьте в личном кабинете Uniteller. <br /> Номер заказа: ' . $Order_ID);
                }
            } else {// Нет оплаты
                exit('Еще не оплачено или нет данных.<br /> Проверьте в личном кабинете Uniteller. <br /> Номер заказа: ' . $Order_ID);
            }

        }


        if ($action == 'turEdit') {
            $tur_id = $this->Vals->getVal('turEdit', 'GET', 'integer');
            $tur = array();
            // Если выбран тур, то подгружаем его значения
            if ($tur_id > 0) {
                $tur = $this->nModel->turGet($tur_id);
            }
            $types = $this->nModel->getTypes();
            $transport = $this->nModel->getTransport();
            $targets = $this->nModel->getTargets();
            //	$locs = $this->nModel->getLocs ();
            $locs = $this->nModel->getCountrys();
            $citys = $this->nModel->getCitys();
            $gids = $this->nModel->getGids();
            $bus = $this->nModel->getBus();
            $pages = $this->nModel->getPages();
            $this->nView->viewTurEdit($tur, $locs, $citys, $gids, $bus, $pages, $types, $transport, $targets);

        }

        if ($action == 'userEdit') {
            $user_id = $this->Vals->getVal('userEdit', 'GET', 'integer');
            $tur_id = $this->Vals->getVal('tur_id', 'GET', 'integer');
            $user = array();
            if ($user_id > 0) {
                $user = $this->nModel->userGet($user_id);
            }
            $countris = $this->nModel->getCountrys();
            $mp = $this->nModel->getMP();
            $this->nView->viewUserEdit($user, $countris, $mp, $tur_id);

        }
        if ($action == 'turDel') {
            $Params ['tur_id'] = $this->Vals->getVal('turDel', 'GET', 'integer');
            $this->nModel->turDel($Params ['tur_id']);
            $this->nView->viewMessage('Тур успешно удален', 'Сообщение');
            $action = 'viewTur';

        }

        if ($action == 'userDelete') {
            $Params ['user_id'] = $this->Vals->getVal('userDelete', 'GET', 'integer');
            $this->nModel->userDelete($Params ['user_id']);
            $this->nView->viewMessage('Турист успешно удален', 'Сообщение');
            $action = 'viewlist';

        }

        if ($action == 'userBan') {
            $Params ['user_id'] = $this->Vals->getVal('userBan', 'GET', 'integer');
            $this->nModel->userBan($Params ['user_id']);
            $this->nView->viewMessage('Турист успешно заблокирован', 'Сообщение');
            $action = 'viewlist';

        }

        if ($action == 'travelBan') {
            $Params ['travel_id'] = $this->Vals->getVal('travelBan', 'GET', 'integer');
            $Params ['tur_id'] = $this->Vals->getVal('tur_id', 'GET', 'integer');
            $this->nModel->travelBan($Params ['travel_id']);
            $this->nView->viewMessage('Турист успешно исключен', 'Сообщение');
            $action = 'viewTurList';

        }

        /*
Array ( [id] => 502 [name] => Хельсинки-Стокгольм-Хельсинки [date] => 15.11.2014 [city_name] => [loc_name] => [loc_id] => [gid_name] => [bus_number] => [cost] => 149 [currency] => у.е. [turists] => 2 )
Array ( [id] => 16524 [id_tur] => 502 [id_mp] => 3 [id_tourist] => 2991 [name_f] => Ильина [name_i] => Ксения [name_o] => Александровна [phone] => [dob] => 11.10.1987 [name] => Димитрова/Будапештская [book_date] => [comment] => [book_num] => 1 [cabin] => [number] => [new_site] => 1 [payed] => 0 [refused] => 0 )
*/


        if ($action == 'getDogovor') {
            $travel_id = $this->Vals->getVal('getDogovor', 'GET', 'integer');
            if (!isset($tur_id)) $tur_id = '';
            if ($travel_id == 0 and $tur_id == '') {
                exit('Выберите тур!');
            } else {
                $travel = $this->nModel->travelGet($travel_id);
            }
            $tur = $this->nModel->tourGet($travel['id_tur']);
            $dogovor = $this->nModel->getDogovor();
            $dogovor_doc = stripslashes($dogovor['content']);
            $dogovor_doc = str_replace('{turist}', $travel['name_f'] . ' ' . $travel['name_i'] . ' ' . $travel['name_o'], $dogovor_doc);
            $dogovor_doc = str_replace('{name_turist}', $travel['name_f'] . ' ' . substr($travel['name_i'], 0, 2) . '.' . substr($travel['name_o'], 0, 2) . '.', $dogovor_doc);
            $dogovor_doc = str_replace('{tur_name}', $tur['name'], $dogovor_doc);
            $dogovor_doc = str_replace('{tur_date_start}', $tur['date'], $dogovor_doc);
            $dogovor_doc = str_replace('{tur_date_end}', $tur['date_to'], $dogovor_doc);
            $dogovor_doc = str_replace('{tur_days}', $tur['daydiff'], $dogovor_doc);
            $dogovor_doc = str_replace('{tur_cost}', $tur['cost'], $dogovor_doc);
            $dogovor_doc = str_replace('{tur_curr}', $tur['currency'], $dogovor_doc);
            setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
            $dogovor_doc = str_replace('{dog_date}', strftime("«%d» %m %Yг.", time()), $dogovor_doc);

            header("Content-type: application/vnd.ms-word");
            header("Content-Disposition: attachment; Filename=Dogovor_" . $travel_id . ".doc");
            echo "<html><head></head><body>";
            echo iconv("UTF-8", "CP1251", $dogovor_doc);
            echo "</body></html>";
            exit();

        }

        if ($action == 'travelEdit') {
            $travel_id = $this->Vals->getVal('travelEdit', 'GET', 'integer');
            $tur_id = $this->Vals->getVal('tur_id', 'GET', 'integer');
            $turist_id = $this->Vals->getVal('turist_id', 'GET', 'integer');
            $mask = $this->Vals->getVal('mask', 'GET', 'string');

            if ($travel_id == 0 and $tur_id == '') {
                exit('Выберите тур!');
            }
            // Если редактируем, то выбираем данные по туристу в данном туре
            $travel = array('id_tur' => $tur_id);
            if ($travel_id > 0) {
                $travel = $this->nModel->travelGet($travel_id);
            }
            //$tourists = $this->nModel->getTourists ();

            $tourists = (strlen($mask) > 3 or $turist_id > 0) ? $this->nModel->getTouristsByMask($mask, $turist_id) : array();

            $mp = $this->nModel->getMP();
            $this->nView->viewTravelEdit($travel, $tourists, $mp, $turist_id);

        }

        if ($action == 'travelEditSelect') {
            $mask = $this->Vals->getVal('mask', 'POST', 'string');
            $turist_id = $this->Vals->getVal('turist_id', 'GET', 'integer');
            $tourists = (strlen($mask) > 1) ? $this->nModel->getTouristsByMask($mask, $turist_id) : array();
            //заглушка
            if (!isset($travel['id_tourist'])) $travel['id_tourist'] = '';
            $select = '<select id="update_select" class="multi_tur" name="id_tourist" style="width: 263px;" onchange="change_mp_by_tourist()">';
            foreach ($tourists as $tourist) {
                $selected = ($tourist['id'] == $travel['id_tourist'] or $tourist['id'] == $turist_id) ? 'selected' : '';
                $select .= '<option value="' . $tourist['id'] . '" rel="' . $tourist['def_mp'] . '" ' . $selected . '>' . $tourist['name'] . '</option>';
            }
            $select .= '</select>';

            exit($select);
        }

        if ($action == 'userUpdate') {
            $Params ['tur_id'] = $this->Vals->getVal('tur_id', 'GET', 'integer');
            $Params ['user_id'] = $this->Vals->getVal('user_id', 'POST', 'integer');
            $Params ['username_f'] = $this->Vals->getVal('username_f', 'POST', 'string');
            $Params ['username_i'] = $this->Vals->getVal('username_i', 'POST', 'string');
            $Params ['username_o'] = $this->Vals->getVal('username_o', 'POST', 'string');
            $Params ['phone'] = $this->Vals->getVal('phone', 'POST', 'string');
            $Params ['passport'] = $this->Vals->getVal('passport', 'POST', 'string');
            $Params ['dob'] = $this->Vals->getVal('dob', 'POST', 'string');
            $Params ['def_mp'] = $this->Vals->getVal('def_mp', 'POST', 'integer');
            $Params ['country'] = $this->Vals->getVal('country', 'POST', 'integer');
            $Params ['comment'] = $this->Vals->getVal('comment', 'POST', 'string');

            if ($Params ['user_id'] > 0)
                $res = $this->nModel->userUpdate($Params);
            else
                $res = $this->nModel->userAdd($Params);

            if ($res) {
                if ($Params ['user_id'] > 0)
                    $this->nView->viewMessage('Турист успешно обновлен', 'Сообщение');
                else
                    $this->nView->viewMessage('Турист успешно добавлен', 'Сообщение');
            } else {
                $this->nView->viewMessage('Ошибка обновления туриста', 'Сообщение');
            }

            $action = ($Params ['tur_id'] > 0) ? 'viewTurList' : 'viewlist';

        }

        if ($action == 'turUpdate') {
            $Params ['tur_id'] = $this->Vals->getVal('tur_id', 'POST', 'integer');
            $Params ['name'] = $this->Vals->getVal('name', 'POST', 'string');
            $Params ['id_type'] = $this->Vals->getVal('id_type', 'POST', 'integer');
            $Params ['id_loc'] = $this->Vals->getVal('id_loc', 'POST', 'integer');
            $Params ['id_city'] = $this->Vals->getVal('id_city', 'POST', 'integer');
            $Params ['id_transport'] = $this->Vals->getVal('id_transport', 'POST', 'integer');
            $Params ['id_target'] = $this->Vals->getVal('id_target', 'POST', 'integer');
            $Params ['date'] = $this->Vals->getVal('date', 'POST', 'array');
            $Params ['date_to'] = $this->Vals->getVal('date_to', 'POST', 'array');
            $Params ['days'] = $this->Vals->getVal('days', 'POST', 'array');
            $Params ['cost'] = $this->Vals->getVal('cost', 'POST', 'integer');
            $Params ['currency'] = $this->Vals->getVal('currency', 'POST', 'string');
            $Params ['overview'] = $this->Vals->getVal('overview', 'POST', 'string');
            $Params ['bus_size'] = $this->Vals->getVal('bus_size', 'POST', 'string');
            $Params ['comment'] = $this->Vals->getVal('comment', 'POST', 'string');
            $Params ['fire'] = $this->Vals->getVal('fire', 'POST', 'integer');
            $Params ['dop_info'] = $this->Vals->getVal('dop_info', 'POST', 'string');
            /*
			$Params ['id_gid'] = $this->Vals->getVal ( 'id_gid', 'POST', 'integer' );
			$Params ['id_bus'] = $this->Vals->getVal ( 'id_bus', 'POST', 'integer' );
			$Params ['id_page'] = $this->Vals->getVal ( 'id_page', 'POST', 'integer' );
			*/
            //		stop($Params);
            if ($Params ['tur_id'] > 0)
                $res = $this->nModel->turUpdate($Params);
            else
                $res = $this->nModel->turAdd($Params);

            if ($res) {
                if ($Params ['tur_id'] > 0)
                    $this->nView->viewMessage('Тур успешно обновлен', 'Сообщение');
                else
                    $this->nView->viewMessage('Тур успешно добавлен', 'Сообщение');
            } else {
                $this->nView->viewMessage('Ошибка обновления тура', 'Сообщение');
            }

            $action = 'viewTur';

        }


        if ($action == 'travelUpdate') {
            $Params ['id'] = $this->Vals->getVal('travel_id', 'POST', 'integer');
            $Params ['tur_id'] = $this->Vals->getVal('tur_id', 'POST', 'integer');
            $Params ['id_tourist'] = $this->Vals->getVal('id_tourist', 'POST', 'integer');
            $Params ['book_date'] = $this->Vals->getVal('date', 'POST', 'string');
            $Params ['book_num'] = $this->Vals->getVal('book_num', 'POST', 'integer');
            $Params ['id_mp'] = $this->Vals->getVal('id_mp', 'POST', 'integer');
            $Params ['number'] = $this->Vals->getVal('number', 'POST', 'string');
            $Params ['cabin'] = $this->Vals->getVal('cabin', 'POST', 'string');
            $Params ['payed'] = $this->Vals->getVal('payed', 'POST', 'integer');
            $Params ['refused'] = $this->Vals->getVal('refused', 'POST', 'integer');
            $Params ['new_site'] = $this->Vals->getVal('new_site', 'POST', 'integer');
            $Params ['comment'] = $this->Vals->getVal('comment', 'POST', 'string');

            if ($Params ['id'] > 0)
                $res = $this->nModel->travelUpdate($Params);
            else
                $res = $this->nModel->travelAdd($Params);

            if ($res) {
                if ($Params ['id'] > 0)
                    $this->nView->viewMessage('Участник тура успешно обновлен', 'Сообщение');
                else
                    $this->nView->viewMessage('Участник тура успешно добавлен', 'Сообщение');
            } else {
                $this->nView->viewMessage('Ошибка обновления участника тура', 'Сообщение');
            }

            $action = 'viewTurList';

        }

        if ($action == 'countryNew') {
            $params['test'] = $this->Vals->getVal('round_id', 'POST', 'string');
            $params['name'] = $this->Vals->getVal('ct_name', 'POST', 'string');
            $params['code'] = $this->Vals->getVal('ct_code', 'POST', 'string');;
            $params_name['name'] = 'Название';
            $params_name['code'] = 'Код';
            $this->new_save_data($params, $params_name, $action, 'user');
        }

        if ($action == 'busNew') {
            $params['test'] = $this->Vals->getVal('round_id', 'POST', 'string');
            $params['number'] = $this->Vals->getVal('bus_number', 'POST', 'string');
            $params['mark'] = $this->Vals->getVal('bus_mark', 'POST', 'string');;
            $params['driver'] = $this->Vals->getVal('bus_driver', 'POST', 'string');
            $params['phones'] = $this->Vals->getVal('bus_phones', 'POST', 'string');
            $params['comment'] = $this->Vals->getVal('bus_comment', 'POST', 'string');
            $params_name['number'] = 'Номер';
            $params_name['mark'] = 'Марка';
            $params_name['driver'] = 'Водитель';
            $params_name['phones'] = 'Телефон';
            $params_name['comment'] = 'Комментарий';
            $this->new_save_data($params, $params_name, $action, 'tur');
        }

        if ($action == 'gidNew') {
            $params['test'] = $this->Vals->getVal('round_id', 'POST', 'string');
            $params['name'] = $this->Vals->getVal('gid_name', 'POST', 'string');
            $params['phone'] = $this->Vals->getVal('gid_phone', 'POST', 'string');
            $params['comment'] = $this->Vals->getVal('gid_comment', 'POST', 'string');
            $params_name['name'] = 'Имя';
            $params_name['phone'] = 'Телефон';
            $params_name['comment'] = 'Комментарий';
            $this->new_save_data($params, $params_name, $action, 'tur');
        }
        if ($action == 'locDel') {
            $Params ['loc_id'] = $this->Vals->getVal('locDel', 'GET', 'integer');
            $this->nModel->locDel($Params ['loc_id']);
            $this->nView->viewMessage('Район успешно удален', 'Сообщение');
            $action = 'LocList';

        }
        if ($action == 'locNew') {
            $params['test'] = $this->Vals->getVal('round_id', 'POST', 'string');
            $params['name'] = $this->Vals->getVal('loc_name', 'POST', 'string');
            $params['color'] = $this->Vals->getVal('loc_color', 'POST', 'string');
            $params['actual'] = $this->Vals->getVal('loc_actual', 'POST', 'string');
            $params_name['name'] = 'Название';
            $params_name['color'] = 'Цвет';
            $params_name['actual'] = 'Актуальность';
            $this->new_save_data($params, $params_name, $action, 'tur');
        }
        if ($action == 'locEdit') {
            $params['id'] = $this->Vals->getVal('id', 'GET', 'integer');
            $params['test'] = $this->Vals->getVal('round_id', 'POST', 'string');
            $params['name'] = $this->Vals->getVal($action . '_id', 'POST', 'string');
            $params['name'] = $this->Vals->getVal($action . '_name', 'POST', 'string');
            $params['color'] = $this->Vals->getVal($action . '_color', 'POST', 'string');
            $params['actual'] = $this->Vals->getVal($action . '_actual', 'POST', 'string');
            if ($params['id'] > 0) {
                $params = $this->nModel->getLoc($params['id']);
            }
            $params['id'] = ($params['id'] > 0) ? $params['id'] : $this->Vals->getVal($action . '_id', 'POST', 'string');

            $params_name['name'] = 'Название';
            $params_name['color'] = 'Цвет';
            $params_name['actual'] = 'Актуальность';
            $this->edit_save_data($params, $params_name, $action, 'loc');
        }

        if ($action == 'mpNew') {
            $name = $this->Vals->getVal('name', 'POST', 'string');
            $loc_mp = $this->Vals->getVal('loc_id', 'POST', 'integer');

            if ($name != '') {
                $result = $this->nModel->saveMP($name, $loc_mp);
                $result = ($result == 1) ? 'Место посадки добавлено' : 'Ошибка добавления';
                exit($result);
            } else {
                $userid = $this->Vals->getVal('user_id', 'GET', 'integer');
                $round_id = $this->Vals->getVal('round_id', 'GET', 'string');
                $locations = $this->nModel->getLocations();
                $form = '<div id="result_save" style="padding: 5px; font: bold 14px Verdana; color: maroon;"></div>';
                $form .= '<p>Название:<br><input type="text" id="name_mp" name="name_mp"/></p>
				<p>Район:<br><select id="location_mp" class="multi" style="width: 263px;" name="locations">';
                foreach ($locations as $loc) {
                    $form .= '<option value="' . $loc['id'] . '">' . $loc['name'] . '</option>';
                }
                $form .= '</select></p>';
                $form .= '<input class="ui-button ui-state-default ui-corner-all" type="button"
				 onclick="save_mp(\'' . $userid . '\',\'' . $round_id . '\');" value="сохранить">';
                exit($form);
            }

        }

        if ($action == 'cityNew') {
            $name = $this->Vals->getVal('name', 'POST', 'string');
            $name_en = $this->Vals->getVal('name_en', 'POST', 'string');
            $ct_id = $this->Vals->getVal('ct_id', 'POST', 'integer');

            if ($name != '') {
                $result = $this->nModel->saveCity($name, $name_en, $ct_id);
                $result = ($result == 1) ? 'Город назначения добавлен' : 'Ошибка добавления';
                exit($result);
            } else {
                $turid = $this->Vals->getVal('tur_id', 'GET', 'integer');
                $round_id = $this->Vals->getVal('round_id', 'GET', 'string');
                $countris = $this->nModel->getCountrys();
                $form = '<div id="result_save" style="padding: 5px; font: bold 14px Verdana; color: maroon;"></div>';
                $form .= '
				<p>Название:<br><input type="text" id="name_ru" name="name_ru"/></p>
				<p>Название (en):<br><input type="text" id="name_en" name="name_en"/></p>
				<p>Страна:<br><select id="countrys" class="multi" style="width: 263px;" name="countrys">';
                foreach ($countris as $item) {
                    $form .= '<option value="' . $item['id'] . '">' . $item['name'] . '</option>';
                }
                $form .= '</select></p>';
                $form .= '<input class="ui-button ui-state-default ui-corner-all" type="button"
				onclick="save_city(\'' . $turid . '\',\'' . $round_id . '\');" value="сохранить">';
                exit($form);
            }

        }


        if ($action == 'viewlist') {
            $order = $this->Vals->getVal('srt', 'POST', 'string');
            $f_name = $this->Vals->getVal('f_name', 'POST', 'string');
            $f_phone = $this->Vals->getVal('f_phone', 'POST', 'string');
            $isAjax = $this->Vals->getVal('ajax', 'INDEX');
            $users = $this->nModel->userList($order, $f_name, $f_phone);
            $turs = $this->nModel->getTurs(0);
            $id_group = 1;
            //$groups = $this->nModel->getGroups ();
            $this->nView->viewUserList($users, $order, $isAjax, $id_group, $turs);

        }
        if ($action == 'viewStoryList') {
            //$isAjax = $this->Vals->getVal ( 'ajax', 'INDEX' );
            $StoryList = $this->nModel->getStoryList();
            $this->nView->viewStoryList($StoryList);

        }
        // Недоделан экшен
        if ($action == 'viewStory') {
            $story_id = $this->Vals->getVal('story_id', 'INDEX');
            $Story = $this->nModel->getStory($story_id);
            echo $Story;
            exit();
        }

        if ($action == 'viewTurList') {
            $order = $this->Vals->getVal('srt', 'POST', 'string');
            $id_tur = (isset($Params ['tur_id'])) ? $Params ['tur_id'] : $this->Vals->getVal('id_tur', 'POST', 'string');
            $id_tur = (isset($id_tur)) ? $id_tur : $this->Vals->getVal('id_tur', 'GET', 'string');

            $isAjax = $this->Vals->getVal('ajax', 'INDEX');
            $id_tur = ($id_tur == 0) ? $this->nModel->GetLastTur() : $id_tur;
            $id_tur = ($id_tur == 0) ? '0' : $id_tur;

            $items = $this->nModel->TravelList($order, $id_tur);
            $turs = $this->nModel->getTurs($id_tur);
            $this->nView->viewTravelList($items, $order, $isAjax, $id_tur, $turs);

        }
        if ($action == 'viewAgentReportList') {
            $order = $this->Vals->getVal('srt', 'POST', 'string');
            $id_tur = (isset($Params ['tur_id'])) ? $Params ['tur_id'] : $this->Vals->getVal('id_tur', 'POST', 'string');
            $id_tur = (isset($id_tur)) ? $id_tur : $this->Vals->getVal('id_tur', 'GET', 'string');

            $isAjax = $this->Vals->getVal('ajax', 'INDEX');
            $id_tur = ($id_tur == 0) ? $this->nModel->GetLastTur() : $id_tur;
            $id_tur = ($id_tur == 0) ? '0' : $id_tur;

            $items = $this->nModel->AgentReportList($order, $id_tur);
            $turs = $this->nModel->getTurs($id_tur);
            $this->nView->viewAgentReportList($items, $order, $isAjax, $id_tur, $turs);

        }
        if ($action == 'search_order') {
            $order = $this->Vals->getVal('order_number', 'POST', 'integer');
            $items = $this->nModel->SearchOrder($order);
            $this->nView->viewSearchOrder($items);

        }
        if ($action == 'viewTur') {
            $order = $this->Vals->getVal('srt', 'POST', 'string');
            $f_name = $this->Vals->getVal('name', 'POST', 'string');
            $country = $this->Vals->getVal('country', 'POST', 'string');
            $target = $this->Vals->getVal('target', 'POST', 'string');
            $id_type = $this->Vals->getVal('type', 'POST', 'integer');
            $start_date = $this->Vals->getVal('start_date', 'POST', 'string');
            $end_date = $this->Vals->getVal('end_date', 'POST', 'string');
            $isAjax = $this->Vals->getVal('ajax', 'INDEX');

            $start_date = ($start_date == '') ? date('Y-m-d') : $start_date;
            $end_date = ($end_date == '') ? ((integer)date('Y') + 1) . date('-m-d') : $end_date;

            $users = $this->nModel->turList($order, $f_name, $country, $target, $id_type, $start_date, $end_date);

            $new_pays = $this->nModel->getNewPays();
            $locs = $this->nModel->getLocs();
            $types = $this->nModel->getTypes();
            $this->nView->viewTurList($users, $order, $isAjax, $target, $locs, $types, $start_date, $end_date, $new_pays);

        }

        if ($action == 'dobList') {
            $items = $this->nModel->getDobList();
            $this->nView->viewDobList($items);

        }
        /** Справочники*/

        // Список районов
        if ($action == 'LocList') {
            $items = $this->nModel->getLocList();
            $this->nView->viewLocList($items);

        }

        if ($action == 'PrintList') {
            $type = $this->Vals->getVal('PrintList', 'GET', 'string');
            $id_tur = $this->Vals->getVal('id_tur', 'POST', 'string');
            $html = '';
            if ($id_tur > 0) {
                if ($type == '1') {
                    $html = $this->nModel->listGid($id_tur);
                } elseif ($type == '2') {
                    $html = $this->nModel->listBorder($id_tur);
                } elseif ($type == '3') {
                    $html = $this->nModel->listBooking($id_tur);
                }
            } else {
                stop('Выберите тур!');
            }
            header("Content-Type: application/vnd.ms-excel", true);
            header("Content-Disposition: attachment; filename=\"list_" . date("d.m.Y") . ".xls\"");
            exit ($html);


        }

        if ($action == 'agent') {
            $order = $this->Vals->getVal('srt', 'POST', 'string');
            $id_tur = (isset($Params ['tur_id'])) ? $Params ['tur_id'] : $this->Vals->getVal('id_tur', 'POST', 'string');
            $id_tur = (isset($id_tur)) ? $id_tur : $this->Vals->getVal('id_tur', 'GET', 'string');

            $isAjax = $this->Vals->getVal('ajax', 'INDEX');
            $items = $this->nModel->AgentTravelList($order, $id_tur, $user_id);
            $turs = $this->nModel->getTurs($id_tur);
            $this->nView->viewAgentList($items, $order, $isAjax, $id_tur, $turs);

        }

        if ($this->Vals->isVal('ajax', 'INDEX')) {
            if ($this->Vals->isVal('xls', 'INDEX')) {
                $PageAjax = new PageForAjax ($this->modName, $this->modName, $this->modName, 'page.xls.xsl');
                $PageAjax->addToPageAttr('xls', '1');
            } else
                $PageAjax = new PageForAjax ($this->modName, $this->modName, $this->modName, 'page.ajax.xsl');
            $isAjax = $this->Vals->getVal('ajax', 'INDEX');
            $PageAjax->addToPageAttr('isAjax', $isAjax);
            $html = $PageAjax->getBodyAjax2($this->nView);

            if ($this->Vals->isVal('xls', 'INDEX')) {
                $reald = date("d.m.Y");
                header("Content-Type: application/vnd.ms-excel", true);
                header("Content-Disposition: attachment; filename=\"list_" . $reald . ".xls\"");
                exit ($html);
            } else
                sendData($html);

        }


    }

    function new_save_data($params, $params_name, $type, $main)
    {
        if ($params['test'] != '') {
            unset($params['test']);
            $result = $this->nModel->saveData($type, $params);
            $result = ($result == 1) ? 'Данные добавлены' : 'Ошибка добавления';
            exit($result);
        } else {
            unset($params['test']);
            $mainid = $this->Vals->getVal($main . '_id', 'GET', 'integer');
            $round_id = $this->Vals->getVal('round_id', 'GET', 'string');

            $form = '<div id="result_save" style="padding: 5px; font: bold 14px Verdana; color: maroon;"></div>
							<form id="new_' . $type . '" method="POST">';
            foreach ($params as $key => $param) {
                $form .= '<p>' . $params_name[$key] . ':<br><input type="text" id="' . $type . '_' . $key . '" name="' . $type . '_' . $key . '"/></p>';
            }
            $form .= '<input class="ui-button ui-state-default ui-corner-all" type="button"
				onclick="save_data(\'' . $main . '\',\'' . $type . '\',\'' . $mainid . '\',\'' . $round_id . '\');" value="сохранить">
				</form>';
            exit($form);
        }
    }

    function edit_save_data($params, $params_name, $type, $main)
    {
        if ($params['test'] != '') {
            unset($params['test']);
            $result = $this->nModel->UpdateData($type, $params);
            $result = ($result == 1) ? 'Данные обновлены' : 'Ошибка обновления';
            exit($result);
        } else {
            unset($params['test']);
            $mainid = $this->Vals->getVal($main . '_id', 'GET', 'integer');
            $round_id = $this->Vals->getVal('round_id', 'GET', 'string');
            $form = '<div id="result_save" style="padding: 5px; font: bold 14px Verdana; color: maroon;"></div>
				<form id="new_' . $type . '" method="POST">';
            foreach ($params as $key => $param) {
                $type_input = ($key == 'id') ? 'type="hidden"' : 'type="text"';
                $form .= '<p>' . $params_name[$key] . '<br><input ' . $type_input . ' id="' . $type . '_' . $key . '" name="' . $type . '_' . $key . '" value="' . $param . '"/></p>';
            }
            $form .= '<input class="ui-button ui-state-default ui-corner-all" type="button"
				onclick="save_data(\'' . $main . '\',\'' . $type . '\',\'' . $mainid . '\',\'' . $round_id . '\');" value="сохранить">
				</form>';
            exit($form);
        }
    }

}

class tcView extends module_View
{
    public function __construct($modName, $sysMod)
    {
        parent::__construct($modName, $sysMod);
        $this->pXSL = array();
    }

    public function viewTravelEdit($travel, $tourists, $mp, $turist_id)
    {
        $this->pXSL [] = RIVC_ROOT . 'layout/' . $this->sysMod->layoutPref . '/tc.traveledit.xsl';
        $Container = $this->newContainer('traveledit');
        $this->addAttr('turist_id', $turist_id, $Container);

        $select = "<input name='mask' value='' onchange=\"var mask = $(this).val(); update_select_travels(mask);\" /><input type='button' value='Найти'/><span id='upd_loading'></span><br/>";

        $select .= '<span><select id="update_select" class="multi_tur" name="id_tourist" style="width: 263px;" onchange="var def_mp = $(this).find(\'option:selected\').attr(\'rel\'); $(\'#id_mp_new\').val(def_mp);">';
        foreach ($tourists as $tourist) {
            $selected = ($tourist['id'] == $travel['id_tourist'] or $tourist['id'] == $turist_id) ? 'selected' : '';
            $select .= '<option value="' . $tourist['id'] . '" rel="' . $tourist['def_mp'] . '" ' . $selected . '>' . $tourist['name'] . '</option>';
        }
        $select .= '</select></span>';

        $this->addToNode($Container, 'select', $select);

        $this->arrToXML($travel, $Container, 'travel');
        $ContainerTourists = $this->addToNode($Container, 'tourists', '');
        foreach ($tourists as $item) {
            $this->arrToXML($item, $ContainerTourists, 'item');
        }
        $ContainerMP = $this->addToNode($Container, 'mp', '');
        foreach ($mp as $item) {
            $this->arrToXML($item, $ContainerMP, 'item');
        }
        return true;
    }

    public function viewUserEdit($user, $countris, $mp, $tur_id)
    {
        $this->pXSL [] = RIVC_ROOT . 'layout/' . $this->sysMod->layoutPref . '/tc.edit.xsl';
        $Container = $this->newContainer('useredit');
        $this->addAttr('tur_id', $tur_id, $Container);
        $this->arrToXML($user, $Container, 'user');
        $ContainerCountris = $this->addToNode($Container, 'countris', '');
        foreach ($countris as $item) {
            $this->arrToXML($item, $ContainerCountris, 'item');
        }
        $ContainerMP = $this->addToNode($Container, 'mp', '');
        foreach ($mp as $item) {
            $this->arrToXML($item, $ContainerMP, 'item');
        }
        return true;
    }

    public function viewTurEdit($tur, $locs, $citys, $gids, $bus, $pages, $types, $transport, $targets)
    {
        $this->pXSL [] = RIVC_ROOT . 'layout/' . $this->sysMod->layoutPref . '/tc.turedit.xsl';
        $Container = $this->newContainer('turedit');
        $this->arrToXML($tur, $Container, 'tur');
        $ContainerCitys = $this->addToNode($Container, 'citys', '');
        foreach ($citys as $item) {
            $this->arrToXML($item, $ContainerCitys, 'item');
        }
        $ContainerLocs = $this->addToNode($Container, 'locs', '');
        foreach ($locs as $item) {
            $this->arrToXML($item, $ContainerLocs, 'item');
        }
        $ContainerGids = $this->addToNode($Container, 'gids', '');
        foreach ($gids as $item) {
            $this->arrToXML($item, $ContainerGids, 'item');
        }
        $ContainerBus = $this->addToNode($Container, 'bus', '');
        foreach ($bus as $item) {
            $this->arrToXML($item, $ContainerBus, 'item');
        }
        $ContainerPages = $this->addToNode($Container, 'pages', '');
        foreach ($pages as $item) {
            $this->arrToXML($item, $ContainerPages, 'item');
        }
        $ContainerTypes = $this->addToNode($Container, 'types', '');
        foreach ($types as $item) {
            $this->arrToXML($item, $ContainerTypes, 'item');
        }
        $ContainerItems = $this->addToNode($Container, 'transport', '');
        foreach ($transport as $item) {
            $this->arrToXML($item, $ContainerItems, 'item');
        }
        $ContainerItems = $this->addToNode($Container, 'targets', '');
        foreach ($targets as $item) {
            $this->arrToXML($item, $ContainerItems, 'item');
        }
        return true;
    }

    public function viewUserList($users, $order, $isAjax, $id_group, $turs)
    {
        $this->pXSL [] = RIVC_ROOT . 'layout/' . $this->sysMod->layoutPref . '/tc.list.xsl';
        if ($isAjax == 1) {
            $this->pXSL [] = RIVC_ROOT . 'layout/head.page.xsl';
        }

        $Container = $this->newContainer('userlist');
        $Containerusers = $this->addToNode($Container, 'users', '');
        $this->addAttr('order', $order, $Containerusers);
        $this->addAttr('id_group', $id_group, $Containerusers);
        foreach ($users as $user) {
            $this->arrToXML($user, $Containerusers, 'user');
        }
        $ContainerLocs = $this->addToNode($Container, 'turs', '');
        foreach ($turs as $item) {
            $this->arrToXML($item, $ContainerLocs, 'item');
        }
        /*$ContainerGroups = $this->addToNode ( $Container, 'groups', '' );
		foreach ( $groups as $item ) {
			$this->arrToXML ( $item, $ContainerGroups, 'item' );
		}*/
        return true;
    }

    public function viewAgentList($items, $order, $isAjax, $id_tur, $turs)
    {
        $this->pXSL [] = RIVC_ROOT . 'layout/' . $this->sysMod->layoutPref . '/tc.agentlist.xsl';
        if ($isAjax == 1) {
            $this->pXSL [] = RIVC_ROOT . 'layout/head.main.page.xsl';
        }

        $Container = $this->newContainer('agentlist');
        $Containerusers = $this->addToNode($Container, 'travel', '');
        $this->addAttr('order', $order, $Containerusers);
        $this->addAttr('id_tur', $id_tur, $Containerusers);
        foreach ($items as $item) {
            $this->arrToXML($item, $Containerusers, 'item');
        }
        $ContainerLocs = $this->addToNode($Container, 'turs', '');
        foreach ($turs as $item) {
            $this->arrToXML($item, $ContainerLocs, 'item');
        }
        return true;
    }

    public function viewAgentReportList($items, $order, $isAjax, $id_tur, $turs)
    {
        $this->pXSL [] = RIVC_ROOT . 'layout/' . $this->sysMod->layoutPref . '/tc.agentreportlist.xsl';
        if ($isAjax == 1) {
            $this->pXSL [] = RIVC_ROOT . 'layout/head.page.xsl';
        }

        $Container = $this->newContainer('travellist');
        $Containerusers = $this->addToNode($Container, 'travel', '');
        $this->addAttr('order', $order, $Containerusers);
        $this->addAttr('id_tur', $id_tur, $Containerusers);
        foreach ($items as $item) {
            $this->arrToXML($item, $Containerusers, 'item');
        }
        $ContainerLocs = $this->addToNode($Container, 'turs', '');
        foreach ($turs as $item) {
            $this->arrToXML($item, $ContainerLocs, 'item');
        }
        return true;
    }

    public function viewTravelList($items, $order, $isAjax, $id_tur, $turs)
    {
        $this->pXSL [] = RIVC_ROOT . 'layout/' . $this->sysMod->layoutPref . '/tc.travellist.xsl';
        if ($isAjax == 1) {
            $this->pXSL [] = RIVC_ROOT . 'layout/head.page.xsl';
        }

        $Container = $this->newContainer('travellist');
        $Containerusers = $this->addToNode($Container, 'travel', '');
        $this->addAttr('order', $order, $Containerusers);
        $this->addAttr('id_tur', $id_tur, $Containerusers);
        foreach ($items as $item) {
            $this->arrToXML($item, $Containerusers, 'item');
        }
        $ContainerLocs = $this->addToNode($Container, 'turs', '');
        foreach ($turs as $item) {
            $this->arrToXML($item, $ContainerLocs, 'item');
        }
        return true;
    }

    public function viewSearchOrder($items)
    {
        $this->pXSL [] = RIVC_ROOT . 'layout/' . $this->sysMod->layoutPref . '/tc.travel_search.xsl';
        $Container = $this->newContainer('travellist');
        $Containerusers = $this->addToNode($Container, 'travel', '');
        foreach ($items as $item) {
            $this->arrToXML($item, $Containerusers, 'item');
        }
        return true;
    }

    public function viewStoryList($items)
    {
        $this->pXSL [] = RIVC_ROOT . 'layout/' . $this->sysMod->layoutPref . '/tc.storylist.xsl';
        $Container = $this->newContainer('storylist');
        $Containerstory = $this->addToNode($Container, 'story', '');
        foreach ($items as $item) {
            $this->arrToXML($item, $Containerstory, 'item');
        }
        return true;
    }

    public function viewTurList($items, $order, $isAjax, $id_loc, $locs, $types, $start_date, $end_date, $new_pays)
    {
        $Container = $this->newContainer('turlist');
        $this->pXSL [] = RIVC_ROOT . 'layout/' . $this->sysMod->layoutPref . '/tc.turlist.xsl';
        if ($isAjax == 1) {
            $this->pXSL [] = RIVC_ROOT . 'layout/head.page.xsl';
        }
        $this->addAttr('start_date', $start_date, $Container);
        $this->addAttr('end_date', $end_date, $Container);
        $this->addAttr('new_pays', $new_pays, $Container);


        $Containerusers = $this->addToNode($Container, 'turs', '');

        $this->addAttr('order', $order, $Containerusers);
        $this->addAttr('id_loc', $id_loc, $Containerusers);
        foreach ($items as $item) {
            $this->arrToXML($item, $Containerusers, 'item');
        }
        $ContainerLocs = $this->addToNode($Container, 'locs', '');
        foreach ($locs as $item) {
            $this->arrToXML($item, $ContainerLocs, 'item');
        }
        $ContainerTypes = $this->addToNode($Container, 'types', '');
        foreach ($types as $item) {
            $this->arrToXML($item, $ContainerTypes, 'item');
        }
        return true;
    }

    public function viewDobList($items)
    {
        $this->pXSL [] = RIVC_ROOT . 'layout/' . $this->sysMod->layoutPref . '/tc.doblist.xsl';
        $Container = $this->newContainer('doblist');
        $Containerusers = $this->addToNode($Container, 'items', '');
        foreach ($items as $item) {
            $this->arrToXML($item, $Containerusers, 'item');
        }

        return true;
    }

    public function viewLocList($items)
    {
        $this->pXSL [] = RIVC_ROOT . 'layout/' . $this->sysMod->layoutPref . '/tc.loclist.xsl';
        $Container = $this->newContainer('loclist');
        $Containerusers = $this->addToNode($Container, 'items', '');
        foreach ($items as $item) {
            $this->arrToXML($item, $Containerusers, 'item');
        }

        return true;
    }

    public function viewMainPage()
    {
        $this->pXSL [] = RIVC_ROOT . 'layout/users/admin.main.xsl';
        $this->newContainer('adminmain');
        return true;
    }


}