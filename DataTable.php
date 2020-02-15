<?php

class Dt{

	private static function objectToArray($data){
		if (is_object($data)) {
			$data = get_object_vars($data);
		}
		if (is_array($data)) {
			return array_map('Dt::objectToArray', $data);
		} else {
			return $data;
		}
	}

	private static function totalRows($instance, $table){
		$query = $instance->db->select("COUNT(*) as num")->get($table);
		$result = $query->row();
		if(isset($result)) return $result->num;
		return 0;
	}

	public static function generate($config = array()){

		$ci =& get_instance();

		$show_correlative = false;

		if(array_key_exists("show_correlative", $config)){
			$show_correlative = $config['show_correlative'];
		}

		$table = $config['table'];
		$fields = $config['fields'];

		$where = "";

		if(array_key_exists("where", $config)){
			$where = $config['where'];
		}

		$or_where = "";

		if (array_key_exists("or_where", $config)){
			$or_where = $config['or_where'];
		}

		$actions = $config['actions'];
		$id = $config['primary_key'];

		$input = $ci->input->post();

		$draw = intval($input["draw"]);
		$start = intval($input["start"]);
		$length = intval($input["length"]);
		$order = $input["order"];
		$search = $input["search"]['value'];
		$col = 0;
		$dir = "";


		$ci->db->select('*');

		if (!empty($order)) {
			foreach ($order as $o) {
				$col = $o['column'];
				$dir = $o['dir'];
			}
		}

		if ($dir != "asc" && $dir != "desc") {
			$dir = "desc";
		}
		$valid_columns = array_keys($fields);

		if (!isset($valid_columns[$col])) {
			$order = null;
		} else {
			$order = $valid_columns[$col];
		}
		if ($order != null) {
			$ci->db->order_by($order, $dir);
		}

		if (!empty($search)) {
			$x = 0;
			foreach ($valid_columns as $sterm) {
				if ($x == 0) {
					$ci->db->like($sterm, $search);
				} else {
					$ci->db->or_like($sterm, $search);
				}
				$x++;
			}
		}
		$ci->db->limit($length, $start);

		if (!empty($where)) {
			$ci->db->where($where);
		}

		if (!empty($or_where)) {
			$ci->db->or_where($or_where);
		}

		$results = $ci->db->get($table);
		$data = array();

		if ($show_correlative){
			$i = 1;
		}

		foreach ($results->result() as $rows) {

			$rows_array = Dt::objectToArray($rows);

			foreach ($actions as $key => $value) {
				$actions[$key]['url'] = str_replace("{:id}", $rows_array[$id], $actions[$key]['url']);
				$actions[$key]['url'] = $ci->router->fetch_class()."/".$actions[$key]['url'];

				if(array_key_exists("formatter", $actions[$key])){
					$actions[$key]['value'] = $rows_array[$actions[$key]['field']];
				}
			}


			$data_tmp = array();

			foreach (array_keys($fields) as $field) {

				if($field == $id && $show_correlative){
					$formatted = $i;
				}else{
					$formatted = $fields[$field]($rows_array[$field]);
				}

				array_push($data_tmp, $formatted);
			}

			array_push($data_tmp, $ci->load->view("components/actions_dt", array("actions" => $actions), TRUE));
			array_push($data, $data_tmp);

			if($show_correlative){
				$i++;
			}
		}
		$count = Dt::totalRows($ci, $table);

		$output = array(
			"draw" => $draw,
			"recordsTotal" => $count,
			"recordsFiltered" => $count,
			"data" => $data
		);
		echo json_encode($output);

	}

}
