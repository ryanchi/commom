<?php
/*
* mysql 单例
*/
class mysql{
    private $host    ='localhost'; //数据库主机
    private $user     = 'root'; //数据库用户名
    private $pwd     = 'root'; //数据库用户名密码
    private $database = 'test'; //数据库名
    private $charset = 'utf8'; //数据库编码，GBK,UTF8,gb2312
    private $link;             //数据库连接标识;
    private $rows;             //查询获取的多行数组
    private $port = 3306 ; //端口
    static $_instance; //存储对象
    /**
     * 构造函数
     * 私有
     */
    private function __construct($pconnect = false) {
        if (!$pconnect) {
            $this->link = mysql_connect("{$this->host}:{$this->port}", $this->user, $this->pwd) or $this->err();
        } else {
            $this->link = mysql_pconnect("{$this->host}:{$this->port}", $this->user, $this->pwd) or $this->err();
        }
        mysql_select_db($this->database) or $this->err();
        $this->query("SET NAMES '{$this->charset}'", $this->link);
        return $this->link;
    }
    /**
     * 防止被克隆
     *
     */
    private function __clone(){}
    public static function getInstance($pconnect = false){
        if(FALSE == (self::$_instance instanceof self)){
            self::$_instance = new self($pconnect);
        }
        return self::$_instance;
    }
    /**
     * 查询
     */
    public function query($sql, $link = '') {
        $this->result = mysql_query($sql, $this->link) or $this->err($sql);
        return $this->result;
    }
    
    
    /**
     *  返回修改、新增受影响的条数的行数
     */
    public function affect_rows() {
    	
    	return mysql_affected_rows($this->link);
    	
    }
    /**
     * 单行记录
     */
    public function getRow($sql, $type = MYSQL_ASSOC) {
        $result = $this->query($sql);
        $data = @ mysql_fetch_array($result, $type);
        mysql_free_result($result);
        return $data;
    }
    /**
     * 多行记录
     */
    public function getRows($sql, $type = MYSQL_ASSOC) {
        $result = $this->query($sql);
        while ($row = @ mysql_fetch_array($result, $type)) {
            $this->rows[] = $row;
        }
        /**
         *  释放资源
         */
        mysql_free_result($result);
        return $this->rows;
    }
    

    /**
     * 错误信息输出
     */
    protected function err($sql = null) {
        //这里输出错误信息
        echo 'error number'.mysql_errno($this->link);
        exit();
    }

    /**
     * escape 
     */
    public function escape($str)
    {
        if (is_string($str))
        {
            $str = "'".$this->escape_str($str)."'";
        }
        elseif (is_bool($str))
        {
            $str = ($str === FALSE) ? 0 : 1;
        }
        elseif (is_null($str))
        {
            $str = 'NULL';
        }

        return $str;
    }

    public function escape_str($str, $like = FALSE)
    {
        if (is_array($str))
        {
            foreach ($str as $key => $val)
            {
                $str[$key] = $this->escape_str($val, $like);
            }

            return $str;
        }

        if (function_exists('mysql_real_escape_string') AND is_resource($this->link))
        {
            $str = mysql_real_escape_string($str, $this->link);
        }
        elseif (function_exists('mysql_escape_string'))
        {
            $str = mysql_escape_string($str);
        }
        else
        {
            $str = addslashes($str);
        }

        // escape LIKE condition wildcards
        if ($like === TRUE)
        {
            $str = str_replace(array('%', '_'), array('\\%', '\\_'), $str);
        }

        return $str;
    }

    public function escape_like_str($str)
    {
        return $this->escape_str($str, TRUE);
    }


}

?>