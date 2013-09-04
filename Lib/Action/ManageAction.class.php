<?php
/**
*  
*/
/**
* 
*/

class software 
{
    public $id;
    public $name;
    public $note;
    public $time_upload;
    function __construct($id, $name, $note, $time_upload)
    {
        $this->id = $id;
        $this->name = $name;
        $this->note = $note;
        $this->time_upload = $time_upload;
    }
}
// 本文档自动生成，仅供测试运行
class ManageAction extends Action
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
    {id: \"base1\", text: \"软件目录\", expanded: true},
    {id: \"logiTechSoftwares\", text: \"实验箱实验\", url: \"/index.php/Manage/logiTechSoftwares\", target:\"_blank\", pid:\"base1\"},
    {id: \"jichu\", text: \"基础环境下载\", url: \"/index.php/Manage/base_softwares_html\", target:\"_blank\", pid:\"base1\"},
    {id: \"upload\", text: \"上传软件\", url: \"/index.php/Manage/uploadSoftware\", target:\"_blank\", pid:\"base1\"}
]";
        // $this->display();
    }
    public function overview()
    {
        $this->display();
    }
    public function logiTechSoftwares()
    {
        $this->display();
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
        // $s2 = new software("2", "软件2", "软件2..");
        // array_push($result, $s2);
        $foo_json = json_encode($result);
        // $this->assign('list',$foo_json);
        echo $foo_json;
        // $this->display();

    }
    public function remove_software()
    {
      $file_name = $_GET['id'];
      $file_name = $this->checkUTF8($file_name);
      
      $sql_delete = "delete from T__FILE where FILE_ID ='$file_name'";
      $M =new Model();
      if($M->execute($sql_delete))
        {
          echo "ok";
        }
        else
        {
          echo "fail";
        }
    }
    public function download_software()
    {
        $fileName = $_GET['id'];
        $fileName = $this->checkUTF8($fileName);
        Log::Write("download_software -> fileName = ".$fileName);
        
        if(!empty($fileName))
        {

            //      $fileName = mb_convert_encoding( $filename,'gb2312','utf-8'); 
            $path = C('TEMPLATE_FILE_PATH').$fileName;
            Log::Write("download_software -> path = ".$path);
            
            if(!empty($path) and !is_null($path))
            {
                //$filename = basename($path);
                $file=@fopen($path,"r");
                if($file)
                {
                    // header("Content-type:application/octet-stream");
                    // //header("Content-type:application/vnd.ms-excel");
                    // header("Accept-ranges:bytes");
                    // header("Accept-length:".filesize($path));
                    // header("Content-Disposition:attachment;filename=".$fileName.'.zip');
                    // echo fread($file,filesize($path));
                    // fclose($file);
                    echo('http://'.$_SERVER['HTTP_HOST'].'/download/'.$path);
                    exit;
                }
                else
                {
                    // $this->ajaxReturn('','表单数据保存失败！',0);
                    echo("fail");
                    
                }           
            }
            else
            {
                echo("fail");
                // $this->assign('jumpUrl','/Index/download_software');
                // $this->success($filename." 文件不存在,正在跳转 ...！");
            }
        }
    }
    public function upload_file_note()
    {
        $fileName = $_GET['id'];
        $fileName = $this->checkUTF8($fileName);
        $file_categary = $_GET['categary'];
        $note = file_get_contents("php://input");
        $M =new Model();
        $sql_update = "update T__FILE set NOTE = '$note', FLAG = '$file_categary' where FILE_ID ='$fileName';";
        echo $sql_update.";";
        if($M->execute($sql_update))
        {
          echo "ok";
        }
        else
        {
          echo "fail";
        }
    }
    public function check_file_status()
    {
        $fileName = $_GET['id'];
        $MCheck =new Model();
        $sqlCheck = "select FILE_ID from T__FILE where FILE_ID ='$fileName'";
        $checkResult=$MCheck->query($sqlCheck);
        if (count($checkResult) > 0) {
          echo "fail";
        }
        else
        {
          echo "ok"; 
        }
    }
    public function upload()
    {
       $file_name =$_FILES["Fdata"]["name"];
       $file_name = $this->checkUTF8($file_name);
       Log::Write("upload -> file_name = ".$file_name);
       
       $file_name_tmp =$_FILES["Fdata"]["tmp_name"];
       
       Log::Write("upload -> tmp_name = ".$file_name_tmp);
       
       $size = $_FILES["Fdata"]["size"] / 1024;
       $file_name_with_path = C('TEMPLATE_FILE_PATH').$file_name;
       
       Log::Write("upload -> file_name_with_path = ".$file_name_with_path);
       // echo $file_name_with_path.";";
      // if (file_exists($file_name_with_path))
      // {
      //   // echo $_FILES["file"]["name"] . " already exists. ";
      //   if(!unlink($file_name_with_path))
      //   {
      //     echo "fail1";
      //     return;
      //   }
      // }

       //最终保存时使用的文件名
       $file_name_save = $file_name_with_path;
      if($this->checkWindows())
      {
        //如果服务器是windows操作系统
        $file_name_save = $this->checkGB2312($file_name_save);
      }
      if(move_uploaded_file($file_name_tmp,$file_name_save))
      {
        $sqlInsertNewFile =
            "insert into T__FILE(FILE_ID, FILE_NAME,UPLOAD_DATE,FILE_SIZE)
              values('$file_name', '$file_name', date('now'),'$size')";
        $M = new Model();  
        if($M->execute($sqlInsertNewFile))
        {
          echo "ok";
        }
        else
        {
          echo "fail";
        }
      }
      else
      {
          echo "fail";
          return;
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
    public function uploadSoftware()
    {
        $this->display();
    }
    public function software_categaries()
    {
      echo "[{ \"id\": \"app\", \"text\": \"实训软件\" },{ \"id\": \"base\", \"text\": \"基础环境\" }]";
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