<?php
/**
 * Created by PhpStorm.
 * User: Mark
 * Date: 2018/6/7
 * Time: 下午3:15
 */


//类
class DB{
    //属性 -- 静态的描述
    private $HOST;
    private $DB_NAME;
    private $DB_USER;
    private $DB_PASSWORD;
    private $pdo; //数据库的连接对象
    private $stmt;
    private $parameters;

    //类的初始化方法:
    public function __construct($host, $db_name, $db_user, $db_password)
    {
        $this->HOST = $host;
        $this->DB_NAME = $db_name;
        $this->DB_USER = $db_user;
        $this->DB_PASSWORD = $db_password;
        $this->connect(); //连接数据库
    }

    //创建PDO对象的方法:
    private function connect(){
        try{
            $this->pdo = new PDO('mysql:host='.$this->HOST.';dbname='.$this->DB_NAME , $this->DB_USER, $this->DB_PASSWORD);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e)
        {
            die($e->getMessage());
        }
    }

    //关闭连接
    public function close(){
        $this->pdo = null;
    }

    //封装增删改查
    public function add($table, $data = [])
    {
        try{
            //
            $all_keys = array_keys($data);
            $columns = implode(',', $all_keys);
            //处理value
            $all_values = array_values($data);
            for ($i = 0; $i < count($all_values); $i++)
            {
                $all_values[$i] = $this->checkValue($all_values[$i]);
            }
            $values_string = implode(',', $all_values);
            $sql = "INSERT INTO $table($columns) VALUES($values_string)";
            $stmt =  $this->pdo->prepare($sql);
            $stmt->execute();
            return $this->pdo->lastInsertId();
        }catch (PDOException $e)
        {
            die($e->getMessage());
        }
    }

    public function delete($table, $where = [])
    {
        try{
            $condition = $this->checkCondition($where);
            $sql = "DELETE FROM $table WHERE $condition";
            $this->stmt = $this->pdo->prepare($sql);
            $this->stmt->execute();
            echo '删除成功';
        }
        catch (PDOException $e)
        {
            die($e->getMessage());
        }
    }

    public function update($table, $data = [], $where = [])
    {
        try{
            $temp_array = [];
            foreach ($data as $key => $value)
            {
                $temp_array[] = "$key = ".$this->checkValue($value);
            }
            $set_string = implode(',', $temp_array);
            $this->stmt = $this->pdo->prepare("UPDATE $table SET $set_string WHERE ".$this->checkCondition($where));
            $this->stmt->execute();
            echo '更新成功';
        }
        catch (PDOException $e)
        {
            die($e->getMessage());
        }
    }

    public function select($table, $columns = "*", $where = [])
    {
        try{
            $cols = $columns;
            if (is_array($columns))
            {
                $cols = implode(',', $columns);
            }
            $this->stmt = $this->pdo->prepare("SELECT $cols FROM $table WHERE ".$this->checkCondition($where));
            $this->stmt->setFetchMode(PDO::FETCH_ASSOC);
            $this->stmt->execute();
            return $this->stmt->fetchAll();
        }
        catch (PDOException $e)
        {

        }
    }

    //处理value参数
    private function checkValue($value){
        $temp = trim($value);
        if (is_string($value))
        {

            return "'$temp'";
        }
        return $temp;
    }

    //检查where条件
    /*
     [
        'name' => ['=', 'tom'] ,
        'age' => ['=', 18],
        'age' => ['>', 20]
        'age' => ['between', [10, 20]]
        'age' => ['in', [10, 23, 20]]
        'age' => ['like', '%_明']
     ]
    group by、orderBy 、limit
     * */
    public function checkCondition($where){
        $con_string = "";
        if (!empty($where))
        {
            if (is_array($where))
            {
                foreach ($where as $key => $value)
                {
                    if (is_array($value))
                    {
                        $con_string .= "$key $value[0] ".$this->checkValue($value[1]).' AND ';
                    }
                }
                $con_string = substr($con_string, 0, -5); //截取掉尾部的and
                return trim($con_string);
            }
            else
            {
                return trim($where);
            }
        }
        else{
            return "1=1";
        }
    }
}

