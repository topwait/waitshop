<?php


class Mysql
{
    private $pdo;
    private $db;
    private $host;
    private $port;
    private $username;
    private $password;
    private $prefix;
    private $encoding;
    private $clear;
    private $successTable;
    private $post;

    /**
     * 构造方法
     * Mysql constructor.
     * @param $post
     */
    public function __construct($post)
    {
        $this->post     = $post;
        $this->db       = $post['db'];
        $this->port     = $post['port'];
        $this->host     = $post['host'];
        $this->username = $post['username'];
        $this->password = $post['password'];
        $this->prefix   = $post['prefix'];
        $this->clear    = $post['clear'];
        $this->encoding = 'utf8mb4';
        $this->pdo = $this->connect();
    }

    /**
     * 连接数据库
     * @return PDO|string
     */
    public function connect()
    {
        try {
            $dsn = "mysql:host={$this->host}; port={$this->port}";
            $db = new PDO($dsn, $this->username, $this->password);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->exec("SET NAMES {$this->encoding}");
            try{
                $db->exec("SET GLOBAL sql_mode='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';");
            }catch (Exception $e){}
            return $db;
        } catch (PDOException $ex) {
            return $ex->getMessage();
        }
    }

    /**
     * 数据库版本号
     * @return bool|string
     */
    public function mysqlVersion()
    {
        $sql = "SELECT VERSION() AS version";
        $result = $this->pdo->query($sql)->fetch();
        return substr($result->version, 0, 3);
    }

    /**
     * 检查数据库是否存在
     * @return mixed
     */
    public function dbExists()
    {
        $sql = "SHOW DATABASES like '{$this->db}'";
        return $this->pdo->query($sql)->fetch();
    }

    /**
     * 检查是否有表存在
     * @return mixed
     */
    public function tableExits()
    {
        $sql = "SHOW TABLES FROM {$this->db}";
        return $this->pdo->query($sql)->fetch();
    }

    /**
     * 删除数据表
     * @return false|PDOStatement
     */
    public function dropTable()
    {
        $sql = "drop database {$this->db};";
        return $this->pdo->query($sql);
    }

    /**
     * 创建数据库
     * @param $version
     * @return mixed
     */
    public function createDB($version)
    {
        $sql = "CREATE DATABASE `{$this->db}`";
        if ($version > 4.1) $sql .= " DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
        return $this->pdo->query($sql);
    }

    /**
     * 创建数据表
     * @param $version
     * @return bool
     * @throws Exception
     */
    public function createTable($version): bool
    {
        // 加载数据
        $dbFile = INSTALL_ROOT . '/db/wait.sql';
        $tables = str_replace(";\r\n", ";\n", file_get_contents($dbFile));
        $tables = explode(";\n", $tables);
        array_push($tables, $this->initAccount());

        // 插入数据
        $millisecond = microtime(true) * 10000;
        foreach ($tables as $table) {
            // 空行处理
            $table = trim($table);
            if (empty($table)) continue;

            // 版本处理
            if (strpos($table, 'CREATE') !== false and $version <= 4.1) {
                $table = str_replace('DEFAULT CHARSET=utf8mb4', '', $table);
            }

            // 注解处理
            if (strpos($table, '--') === 0) continue;

            // 替换前缀
            $table = str_replace('`wait_', $this->db . '.`wait_', $table);
            $table = str_replace('`wait_', '`' . $this->prefix, $table);

            // 创建表格
            if (strpos($table, 'CREATE') !== false) {
                $tableName = explode('`', $table)[1];
                $millisecond += random_int(3000, 7000);
                $this->successTable[] = [$tableName, date('Y-m-d H:i:s', $millisecond / 10000)];
            }

            // 执行命令
            try {
                if (!$this->pdo->query($table)) return false;
            } catch (Exception $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * 初始化账号
     */
    public function initAccount(): string
    {
        $time = time();
        $username = $this->post['admin_user'];
        $password = $this->post['admin_pwd'];

        $salt = substr(md5($time . $username), 0, 4);
        $password = md5(md5($password) . $salt);

        $sql  = "{$this->db}.INSERT INTO"." `{$this->prefix}admin` ";
        $sql .= "(`id`,`username`,`nickname`,`password`,`salt`,`create_time`,`update_time`) VALUES ";
        $sql .= "(null, '{$username}', '{$username}', '{$password}', '{$salt}', '{$time}', '{$time}');";
        return $sql;
    }

    /**
     * 验证数据库
     */
    public function checkDB()
    {
        if (!is_object($this->pdo)) {
            return '安装错误，请检查连接信息:'.mb_strcut($this->pdo,0,30).'...';
        }

        $version = $this->mysqlVersion();
        if (!$this->dbExists()) {
            if (!$this->createDB($version)) {
                return '创建数据库错误';
            }
        } elseif ($this->tableExits() and $this->clear == false) {
            return '数据表已经存在，您可以清空现有数据库继续安装';
        } elseif ($this->dbExists() and $this->clear == true) {
            if (!$this->dropTable()) {
                return '删除存在的数据库库出错了,请手动清除';
            } else {
                if (!$this->createDB($version)) {
                    return '创建数据库错误!';
                }
            }
        }

        return true;
    }

    /**
     * 安装数据
     * @throws @Exception
     * @return string|array
     */
    public function install()
    {
        $err = $this->checkDB();
        if ($err !== true) {
            return $err;
        }

        if (!$this->createTable($this->mysqlVersion())) {
            return'创建数据表失败';
        }

        return $this->successTable;
    }
}