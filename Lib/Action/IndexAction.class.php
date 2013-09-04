<?php
/**
*  
*/
/**
* 
*/

class classroom_state 
{
    public $name;
    public $total_count;
    public $used_count;
    public $update_date;
    function __construct($name, $total_count, $used_count, $update_date)
    {
        $this->name = $name;
        $this->total_count = $total_count;
        $this->used_count = $used_count;
        $this->update_date = $update_date;
    }
}
// 本文档自动生成，仅供测试运行
class IndexAction extends Action
{
    /**
    +----------------------------------------------------------
    * 默认操作
    +----------------------------------------------------------
    */
    public function checkClientIE() {
    $agent = $_SERVER['HTTP_USER_AGENT'];
    if (eregi("MSIE",$agent))
    {
      return true;
    }
    return false;
  }
  public function checkUTF8($str) {
    $cod = mb_check_encoding($str,"UTF-8");
    if("UTF-8" != $cod ||  empty($cod))
    {
      $str = mb_convert_encoding( $str,'utf-8','gb2312'); 
    }
    return $str;
  }
  public function checkWindows() {
    if(eregi('WIN',PHP_OS))
    {
      return true;
    }
    return false;
  }
  public function checkGB2312($str) {
    $cod = mb_check_encoding($str,"GB2312");
    if("GB2312" != $cod ||  empty($cod))
    {
      $str = mb_convert_encoding( $str,'GB2312','UTF-8'); 
    }
    return $str;
  }
    public function index()
    {
        $this->assign('company', C('COMPANY_SIGN'));
        $this->display();
        // $this->display(THINK_PATH.'/Tpl/Autoindex/hello.html');
    }
    public function tree()
    {
        echo "[
    {id: \"base1\", text: \"教室列表\", expanded: true},
    {id: \"classRoom1\", text: \"教室一\", url: \"/index.php/Index/classroom_state/room_name/教室一\", target:\"_blank\", pid:\"base1\"},
    {id: \"classRoom2\", text: \"教室二\", url: \"/index.php/Index/classroom_state/room_name/教室二\", target:\"_blank\", pid:\"base1\"}
]";
        // $this->display();
    }
    public function overview()
    {
        $this->display();
    }
    public function classroom_state()
    {
        $room_name = $_GET['room_name'];
        $room_name = $this->checkUTF8($room_name);
        $this->assign('ROOM_NAME', $room_name);
        $this->display();
    }
    public function classroom_state_json()
    {
        $room_name = $_GET['room_name'];
        $room_name = $this->checkUTF8($room_name);
        $M =new Model();
        $sql_select = "select ROOM_NAME, TOTAL_COUNT, USED_COUNT, UPDATE_DATE from T_CLASSROOM_STATE where ROOM_NAME = '$room_name' order by UPDATE_DATE desc limit(5)";
        $list = $M->query($sql_select);
        $result = array();
        $list_count = count($list);
        if ($list_count <= 0) {
            date_default_timezone_set("Asia/Shanghai");
            $time= date("Y-m-d H:i:s");
            $c = new classroom_state($room_name, "未知","未知", $time);
            array_push($result, $c);
        }
        else
        {
            for($i = 0; $i < $list_count; $i++)
            {
                $row = $list[$i];
                $s1 = new classroom_state($row['ROOM_NAME'], $row['TOTAL_COUNT'], $row['USED_COUNT'], $row['UPDATE_DATE']);
                array_push($result, $s1);
            }             
        }
       
        $foo_json = json_encode($result);
        echo $foo_json;
    }
    public function add_classroom_state()
    {
        $jsonInput = file_get_contents("php://input"); 
        $jsonInput=$this->checkUTF8($jsonInput);
        $decoded_state = json_decode($jsonInput);
        $name = $decoded_state->name;
        $total_count = $decoded_state->total_count;
        $used_count = $decoded_state->used_count;
        date_default_timezone_set("Asia/Shanghai");
        $time= date("Y-m-d H:i:s");
        $sql_insert = "insert into T_CLASSROOM_STATE(ROOM_NAME, TOTAL_COUNT, USED_COUNT, UPDATE_DATE) 
         values('$name', $total_count, $used_count, '$time')";
        $M =new Model();
        $r = $M->execute($sql_insert);
        if($r)
        {
            echo $jsonInput;
        }
        else
        {
            echo "failed";
        }
    }
    public function software_list()
    {
        $result = array();
        $MCheck =new Model();
        $sqlCheck = "select FILE_ID, FILE_NAME, UPLOAD_DATE, NOTE from T__FILE where FLAG = 'app' order by UPLOAD_DATE desc";
        $checkResult = $MCheck->query($sqlCheck);
        if (count($checkResult) > 0) {

          for($i = 0; $i< count($checkResult); $i++)
          {
            $row = $checkResult[$i];
            $s1 = new software($row['FILE_ID'], $row['FILE_NAME'], $row['NOTE'], $row['UPLOAD_DATE']);
            array_push($result, $s1);
          }
          
        }
        $foo_json = json_encode($result);
        echo $foo_json;
    }
    public function download_software()
    {
        $fileName = $_GET['id'];
        $fileName = $this->checkUTF8($fileName);
        Log::Write("download_software -> fileName = ".$fileName);
        //echo $tmpType;
        //return;
        $file_name_save = $fileName;
        if($this->checkWindows())
        {
        //如果服务器是windows操作系统
            $file_name_save = $this->checkGB2312($file_name_save);
        }
        Log::Write("download_software -> file_name_save = ".$file_name_save);

        if(!empty($fileName))
        {
            $path = C('TEMPLATE_FILE_PATH').$file_name_save;
            
            if(!empty($path) and !is_null($path))
            {
                
                Log::Write("download_software -> path = ".$path);
                if(file_exists($path))
                {
                    $http_path = 'http://'.$_SERVER['HTTP_HOST'].'/'.$path;
                    $http_path = $this->checkUTF8($http_path);
                    echo($http_path);
                    exit;
                }
                else
                {
                    echo("fail");
                }           
            }
            else
            {
                echo($filename." 文件不存在,正在跳转 ...！");
            }
        }
    }
    public function base_softwares_list()
    {
        $result = array();
        $MCheck =new Model();
        $sqlCheck = "select FILE_ID, FILE_NAME, UPLOAD_DATE, NOTE from T__FILE where FLAG = 'base' order by UPLOAD_DATE desc";
        $checkResult = $MCheck->query($sqlCheck);
        if (count($checkResult) > 0) {

          for($i = 0; $i< count($checkResult); $i++)
          {
            $row = $checkResult[$i];
            $s1 = new software($row['FILE_ID'], $row['FILE_NAME'], $row['NOTE'], $row['UPLOAD_DATE']);
            array_push($result, $s1);
          }
          
        }
        $foo_json = json_encode($result);
        echo $foo_json;
    }
    public function base_softwares_html()
    {
        $this->display();
    }
    /**
    +----------------------------------------------------------
    * 探针模式
    +----------------------------------------------------------
    */
    public function checkEnv()
    {
        load('pointer',THINK_PATH.'/Tpl/Autoindex');//载入探针函数
        $env_table = check_env();//根据当前函数获取当前环境
        echo $env_table;
    }

}
?>