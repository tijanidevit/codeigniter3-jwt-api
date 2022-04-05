<?php    
    require APPPATH . 'libraries/REST_Controller.php';

    header('Accesss-Control-Allow-Origin: *');
    header('Accesss-Control-Allow-Methods: POST, GET');

    class Users extends REST_Controller{
        public function __construct()
        {
            parent::__construct();
            $this->load->model('user_model');
            $this->load->helper([
                'authorization',
                'jwt',
                'security'
            ]);
        }

        public function all_get()
        {
            $users = $this->user_model->fetch_all_users();
            return $this->response([
                'status' => 1,
                'message' => 'Users fetched successfully',
                'data' => $users
            ], parent::HTTP_OK);
        }

        public function register_post()
        {
            $data = $this->_getJsonData();
            $user_data = [
                'fullname' => $this->security->xss_clean($data->fullname),
                'email' => $this->security->xss_clean($data->email),
                'password' => password_hash($data->password, PASSWORD_DEFAULT),
                'image' => '$data->image',
            ];

            $check_user_existence = $this->user_model->fetch_user($data->email);

            if (!empty($check_user_existence)) {
                return $this->response([
                    'status' => 0,
                    'message' => 'Email already existed in our database',
                ], parent::HTTP_CONFLICT);    
            }

            if ($this->user_model->create_user($user_data)) {
                $user = $this->user_model->fetch_user($data->email);
                return $this->response([
                    'status' => 1,
                    'message' => 'User account created successfully',
                    'data' => $user,
                ], parent::HTTP_CREATED);
            }
        }

        public function login_post()
        {
            $data = $this->_getJsonData();

            $user = $this->user_model->fetch_user($data->email);

            if (empty($user)) {
                return $this->response([
                    'status' => 0,
                    'message' => 'Email address not found',
                ], parent::HTTP_CONFLICT);    
            }

            $verify_password = password_verify($data->password, $user->password);
            if (!$verify_password) {
                return $this->response([
                    'status' => 0,
                    'message' => 'Inalid password',
                ], parent::HTTP_CONFLICT);
            }
            else {
                $token = authorization::generateToken((array) $user);
                return $this->response([
                    'status' => 1,
                    'message' => 'User logged in successfully',
                    'data' => $token,
                ], parent::HTTP_OK);
            }
        }

        public function getAuthUser_get()
        {
            $token = $this->_getHeaderAuthorization();
            try {
                $user = getAuthUser($token);

                if ($user) {
                    $user = $user->data;
                    echo json_encode($user);
                }
                else{
                    echo 'No';
                }
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
        }
    }

?>