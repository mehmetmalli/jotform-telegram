 <?php
    require_once __DIR__ . "/config/db_config.php";

    class Database
    {
        private $db;

        public function __construct()
        {
            try {
                $this->db = new PDO(
                    'mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME . '',
                    DB_USERNAME,
                    DB_PASSWORD,
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
                );
            } catch (PDOException $e) {
                exit($e->getMessage());
            }
        }

        private function generate_random_password()
        {
            $rand = rand();
            $pass = base_convert($rand, 10, 36);
            return 'jf' . $pass;
        }

        public function user_exists($chat_id)
        {
            try {
                $stmt = $this->db->prepare("SELECT COUNT(chat_id) FROM users  WHERE chat_id = :chat_id");
                $stmt->bindParam("chat_id", $chat_id);
                $stmt->execute();
                $count = $stmt->fetch();
                return $count[0] > 0;
            } catch (Exception $e) {
                return false;
            }
        }

        public function create_new_user($chat_id)
        {
            try {
                $access_code = $this->generate_random_password();
                $stmt = $this->db->prepare("INSERT INTO users (chat_id, access_code) VALUES (:chat_id, :access_code)");
                $stmt->bindParam("chat_id", $chat_id);
                $stmt->bindParam("access_code", $access_code);
                $stmt->execute();
                return $access_code;
            } catch (Exception $e) {
                return false;
            }
        }

        public function reset_access_code($chat_id)
        {
            try {
                $access_code = $this->generate_random_password();
                $stmt = $this->db->prepare("UPDATE users SET access_code = :access_code WHERE chat_id = :chat_id ");
                $stmt->bindParam("chat_id", $chat_id);
                $stmt->bindParam("access_code", $access_code);
                $stmt->execute();
                return $access_code;
            } catch (Exception $e) {
                return false;
            }
        }

        public function set_api_key($chat_id, $access_code, $api_key)
        {
            try {
                if ($this->user_exists($chat_id) && $access_code == $this->get_access_code($chat_id)) {
                    $stmt = $this->db->prepare("UPDATE users SET api_key = :api_key WHERE chat_id = :chat_id");
                    $stmt->bindParam("api_key", $api_key);
                    $stmt->bindParam("chat_id", $chat_id);
                    $stmt->execute();
                    return $api_key;
                }
            } catch (Exception $e) {
                return false;
            }
        }

        public function set_username($chat_id, $access_code, $username)
        {
            try {
                if ($this->user_exists($chat_id) && $access_code == $this->get_access_code($chat_id)) {
                    $stmt = $this->db->prepare("UPDATE users SET username = :username WHERE chat_id = :chat_id");
                    $stmt->bindParam("username", $username);
                    $stmt->bindParam("chat_id", $chat_id);
                    $stmt->execute();
                    return $username;
                }
            } catch (Exception $e) {
                return false;
            }
        }

        public function get_api_key($username)
        {
            try {
                $stmt = $this->db->prepare("SELECT api_key FROM users WHERE username = :username ");
                $stmt->bindParam("username", $username);
                $stmt->execute();
                $api_key = $stmt->fetch();
                return $api_key[0];
            } catch (Exception $e) {
                return false;
            }
        }

        public function get_api_key_with_chat_id($chat_id)
        {
            try {
                $stmt = $this->db->prepare("SELECT api_key FROM users WHERE chat_id = :chat_id ");
                $stmt->bindParam("chat_id", $chat_id);
                $stmt->execute();
                $api_key = $stmt->fetch();
                return $api_key[0];
            } catch (Exception $e) {
                return false;
            }
        }

        public function has_api_key($username)
        {
            try {
                $stmt = $this->db->prepare("SELECT api_key FROM users WHERE username = :username ");
                $stmt->bindParam("username", $username);
                $stmt->execute();
                $api_key = $stmt->fetch();
                return !is_null($api_key[0]);
            } catch (Exception $e) {
                return false;
            }
        }

        public function get_access_code($chat_id)
        {
            try {
                $stmt = $this->db->prepare("SELECT access_code FROM users WHERE chat_id = :chat_id ");
                $stmt->bindParam("chat_id", $chat_id);
                $stmt->execute();
                $access_code = $stmt->fetch();
                return $access_code[0];
            } catch (Exception $e) {
                return false;
            }
        }

        public function log($chat_id, $form_id, $submission_id, $username)
        {
            try {
                $stmt = $this->db->prepare("INSERT INTO logs (chat_id, form_id, submission_id, username) VALUES (:chat_id, :form_id, :submission_id, :username)");
                $stmt->bindParam("chat_id", $chat_id);
                $stmt->bindParam("form_id", $form_id);
                $stmt->bindParam("submission_id", $submission_id);
                $stmt->bindParam("username", $username);
                $stmt->execute();
            } catch (Exception $e) {
                exit($e->getMessage());
            }
        }
    }
    ?> 