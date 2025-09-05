<?php

class Base_model extends CI_Model
{

  /**
   * This must be valid table name in the Database.
   *
   * @var string Name of the table
   */
  protected $table;

  /**
   * @var string Table's Primary Key
   */
  protected $primary_key;

  /**
   * Array of fields name of the Datatables columns.
   *
   * @var array
   */
  protected $column_order = [];
  protected $hidden_xs = [];
  protected $hidden_sm = [];
  protected $hidden_md = [];
  protected $hidden_lg = [];
  protected $datatable_edit_link = null;
  protected $datatable_remove_link = null;
  protected $datatable_view_link = null;
  protected $datatable_customs_link = null;
  protected $datatable_view_authorized = null;
  protected $timestamp = true;
  protected $union_table = false;
  protected $softdelete = null;

  /**
   * Sercheables columns.
   *
   * @var array
   */
  protected $column_search = [];
  protected $order = [];
  protected $dates = [];

  public function __construct()
  {
    parent::__construct();

    $this->order = empty($this->order) ? [$this->primary_key => 'asc'] : $this->order;
  }

  public function table()
  {
    return $this->table;
  }

  public function setTable($table)
  {
    $this->table = $table;
  }

  public function primary_key()
  {
    return $this->primary_key;
  }

  public function save($data)
  {
    if (!empty($data[$this->primary_key])) {
      return $this->update($data);
    } else {
      return $this->insert($data);
    }
  }

  /**
   * Get row by id.
   *
   * @param int $id
   *
   * @return mixed
   */
  public function get_by_id($id)
  {
    return $this->db->get_where($this->table, [$this->primary_key => $id])->row_array();
  }

  public function get_all()
  {
    return $this->db->get_where($this->table)->result_array();
  }

  /**
   * Select columns.
   *
   * @param array $columns
   *
   * @return mixed
   */
  public function select($columns = ['*'])
  {
    $this->db->select($columns);

    return $this->db->get($this->table)->result_array();
  }

  /**
   * Get Where.
   *
   * @param array $where
   * @param array $columns
   *
   * @return mixed
   */
  public function get_where($where = [], $columns = ['*'])
  {
    $this->db->select($columns);
    foreach ($where as $key => $value) {
      if (gettype($value) !== 'array') {
        if ($value !== null)
          $this->db->where($key, $value);
        else
          $this->db->where($key, $value, false);
      } else {
        foreach ($value as $v) {
          if ($value !== null)
            $this->db->where($key, $v);
          else
            $this->db->where($key, $v, false);
        }
      }
    }
    // if (count($where) > 1)
    // die('<pre>' . print_r($this->db->get_compiled_select($this->table), true));
    $result = $this->db->get($this->table);
    if (!empty($result)) {
      return $result->result_array();
    }
    return false;
  }

  /**
   * Get Where.
   *
   * @param array $where
   * @param array $columns
   *
   * @return mixed
   */
  public function get_or_where($where = [[]], $columns = ['*'])
  {
    $this->db->select($columns);
    foreach ($where as $key => $value) {
      if (gettype($value) !== 'array') {
        if ($value !== null)
          $this->db->where($key, $value);
        else
          $this->db->where($key, $value, false);
      } else {
        $this->db->group_start();
        foreach ($value as $idx => $v) {
          if ($idx === 0) {
            if ($value !== null)
              $this->db->where($key, $v);
            else
              $this->db->where($key, $v, false);
          } else {
            if ($value !== null)
              $this->db->or_where($key, $v);
            else
              $this->db->or_where($key, $v, false);
          }
        }
        $this->db->group_end();
      }
    }
    // if (count($where) > 1)
    // die('<pre>' . print_r($this->db->get_compiled_select($this->table), true));
    $result = $this->db->get($this->table);
    if (!empty($result)) {
      return $result->result_array();
    }
    return false;
  }

  /**
   * Insert data.
   *
   * @param object $data
   */
  protected function insert($data)
  {
    if ($this->timestamp) {
      $data['created_at'] = date('Y-m-d G:i:s');
    }
    $this->db->insert($this->table, $data);

    return $this->db->insert_id();
  }

  /**
   * Update data.
   *
   * @param object $data
   */
  protected function update($data)
  {
    if ($this->timestamp) {
      $data['updated_at'] = date('Y-m-d G:i:s');
    }
    $this->db->where($this->primary_key, $data[$this->primary_key]);
    $result = $this->db->update($this->table, $data);
    if ($result) {
      return intval($data[$this->primary_key]);
    } else {
      return ($data[$this->primary_key]);
    }
  }

  /**
   * Delete data.
   *
   * @param int $id
   */
  public function delete($id)
  {
    $this->db->delete($this->table, [$this->primary_key => $id]);
  }

  public function delete_all($where = false)
  {
    if ($where) {
      $this->db->where($where);
    }
    // $this->dd($this->db->get_compiled_select($this->table));
    $this->db->delete($this->table);
  }

  public function check_softdeleted()
  {
    if (!is_null($this->softdelete)) {
      $this->db->where($this->softdelete, 0);
    }

    return $this;
  }

  /**
   * Datatable Query Generator.
   */
  public function _get_datatables_query()
  {
    $post = $this->input->post();
    $like_direction = !empty($post['search_like_direction']) ? $post['search_like_direction'] : 'full';
    $this->extend_datatable_query();

    $this->check_softdeleted();

    if (empty($this->union_table)) {
      $this->db->from($this->table);
    }

    $i = 0;

    foreach ($this->column_search as $item) { // loop column
      if (!empty($post['search']) && !empty($post['search']['value'])) { // if datatable send POST for search
        if ($i === 0) { // first loop
          $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
          $this->db->like($item, $post['search']['value'], $like_direction);
        } else {
          $this->db->or_like($item, $post['search']['value'], $like_direction);
        }

        if (count($this->column_search) - 1 == $i) { //last loop
          $this->db->group_end();
        } //close bracket
      }
      ++$i;
    }

    if (isset($post['order'])) { // here order processing
      foreach ($post['order'] as $ord) {
        $this->db->order_by($this->column_order[$ord['column']], $ord['dir']);
      }
    } elseif (isset($this->order)) {
      $order = $this->order;
      $this->db->order_by(key($order), $order[key($order)]);
    }
  }

  /**
   * Datatable extend query: This method is used to extend the query of the datatable generator.
   */
  protected function extend_datatable_query()
  {
  }

  /**
   * Datatable method.
   *
   * @return mixed
   */
  public function get_datatables()
  {
    $post = $this->input->post();
    $this->_get_datatables_query();
    if (!empty($post['length']) && $post['length'] != -1) {
      $this->db->limit($post['length'], $post['start']);
    }
    $query = $this->db->get();

    return $query->result_array();
  }

  /**
   * Count filtered data.
   *
   * @return mixed
   */
  public function count_filtered()
  {
    $this->_get_datatables_query();
    $query = $this->db->get();

    return $query->num_rows();
  }

  /**
   * Count all data.
   *
   * @return mixed
   */
  public function count_all()
  {
    if (empty($this->union_table)) {
      $this->db->from($this->table);
    }
    $this->extend_datatable_query();

    return $this->db->count_all_results();
  }

  /**
   * Generate the Datatable ajax List.
   *
   *
   * @param array $buttons - ["edit"=> "url edit","view"=> "url view","remove"=> "url remove",]
   *
   * @return array
   */
  public function datatables_ajax_list()
  {
    $list = $this->get_datatables();
    $post = $this->input->post();
    $data = [];
    foreach ($list as $item) {
      $row = [];
      $row = $this->datatables_ajax_row($row, $item);
      $buttons = '';

      if ($this->datatable_customs_link != null) {
        $buttons .= call_user_func($this->datatable_customs_link, $item);
      }
      if ($this->datatable_edit_link != null) {
        $buttons .= edit_button(call_user_func($this->datatable_edit_link, $item));
      }
      if ($this->datatable_remove_link != null) {
        $buttons .= remove_button(call_user_func($this->datatable_remove_link, $item));
      }
      if ($this->datatable_view_link != null) {
        $buttons .= view_button(call_user_func($this->datatable_view_link, $item));
      }


      if ($this->datatable_view_authorized != null) {
        $buttons .= call_user_func($this->datatable_view_authorized, $item);
      }

      if (!empty($buttons)) {
        $row[] = $buttons;
      }

      $data[] = $row;
    }

    $output = [
      'draw' => !empty($post['draw']) ? $post['draw'] : false,
      'recordsTotal' => $this->count_all(),
      'recordsFiltered' => $this->count_filtered(),
      'data' => $data,
    ];

    return $output;
  }

  protected function datatables_ajax_row($row, $item)
  {
    foreach ($this->column_order as $column) {
      if (in_array($column, $this->dates)) {
        $row[] = $this->format_date($item[$column]);
      } else {
        if (method_exists($this, $column . '_attribute')) {
          $row[] = $this->{$column . '_attribute'}(!empty($item[$column]) ? $item[$column] : false, $item);
        } else {
          $row[] = $item[$column];
        }
      }
    }

    return $row;
  }

  protected function validation_rules()
  {
    return [];
  }

  protected function format_date($date)
  {
    return date('d/m/Y H:i:s', strtotime($date));
  }

  public function get()
  {
    return $this->db->get($this->table);
  }

  public function get_array()
  {
    return $this->get()->result_array();
  }

  public function first_array()
  {
    return $this->get()->row_array();
  }

  public function where($where)
  {
    $this->db->where($where);

    return $this;
  }

  public function order_by($column, $option = 'desc')
  {
    $this->db->order_by($column, $option);

    return $this;
  }

  public function where_id($id)
  {
    $this->db->where([$this->primary_key => $id]);

    return $this;
  }

  public function get_query()
  {
    return $this->get();
  }

  public function dd($object, $continue = false)
  {
    echo '<pre>' . print_r($object, true) . '</pre>';
    if (!$continue)
      die();
  }
}
