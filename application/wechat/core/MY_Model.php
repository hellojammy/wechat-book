<?php
/**
 *
 * create at 16/09/15
 * @author hellojammy (http://hello1010.com/about)
 * @version 1.0
 *
 */

class MY_Model extends CI_Model
{
    private $table_name;
    protected $db;
    public function __construct($table_name = '', $database = 'wx_hello1010')
    {
        parent::__construct();
        $this->db = $this->load->database($database, TRUE);
        //log_message('debug', $this->db->database);
        $this->db->query('set names utf8');
        $this->table_name = $table_name;
    }

    function get_by_id($id = 0)
    {
        if ($id == 0) {
            $id = $this->input->post('id');
            $this->db->where('id', intval($id));
        } else {
            $this->db->where('id', intval($id));
        }

        $this->db->order_by('utime desc');
        $query = $this->db->get($this->table_name);
        log_message('DEBUG', "[Model]sql=" . $this->db->last_query());
        return $query->row_array();
    }

    function save_entry($save_data = array())
    {
        log_message('DEBUG', "[Model]save_entry,data=" . json_encode($save_data));
        $data = array();
        foreach ($save_data as $key => $value) {
            if ($value !== null && isset($value)) {
                $data[$key] = $value;
            }
        }
        if (isset($data['id']) && !empty($data['id']) && $data['id'] > 0) {
            $data_id = $data['id'];
            if (!isset($data['utime'])) {
                $data['utime'] = date("Y-m-d H:i:s", time());
            }
            if (isset($data['status']) && $data['status'] === '') {
                unset($data['status']);
            }
            unset($data['id']);
            log_message('DEBUG', 'save_entry,' . json_encode($this));
            $this->db->update($this->table_name, $data, array('id' => $data_id));
            $rtn = $data_id;
        } else {
            if (!isset($data['ctime'])) {
                $data['ctime'] = date("Y-m-d H:i:s", time());
            }
            if (!isset($data['utime'])) {
                $data['utime'] = date("Y-m-d H:i:s", time());
            }
            $this->db->insert($this->table_name, $data);
            $rtn = $this->db->insert_id();
        }

        log_message('DEBUG', "[Model]save_entry,rtn=" . $rtn . ",sql=" . $this->db->last_query());
        return $rtn;
    }
}
?>