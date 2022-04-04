<?php

    class User_model extends CI_Model{
        public function __construct()
        {
            parent::__construct();
            $this->load->database();
        }

        public function create_user($data)
        {
            return $this->db->insert('users', $data);
        }
        
        public function fetch_user($key)
        {
            $this->db->select('*')->where(['email' => $key])->or_where(['id' => $key])->order_by('id', 'desc');
            return $this->db->get('users')->row_object();
        }
        
        public function fetch_all_users()
        {
            $this->db->select('fullname, email, image, created_at')->order_by('id', 'desc');
            return $this->db->get('users')->result();
        }
    }