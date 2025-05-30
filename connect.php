<?php
class Connect{
    public $server;
    public $user;
    public $password;
    public $dbName;

    public $port;

    public function __construct()
    {
        $this->server ='localhost';
        $this->user ='root';
        $this->password =''; 
        $this->dbName ='motorbike';
        $this->port = 3306; 
    }

    //option 1 : use Mysqli
    function connectToMySQL():mysqli{
        $conn_my = new mysqli($this->server,
         $this->user,$this->password,$this->dbName, $this->port);
         if($conn_my->connect_error){
            die("failed".$conn_my->connect_error);
         }else{
            // echo "Connect!!!";
         }
         return $conn_my;
        }    
        //option 2: Use PDO  
        function  connectToPDO():PDO{
            try{
                $conn_pdo = new PDO
                ("mysql:host=$this->server;port=$this->port;dbname=$this->dbName",$this->user,$this->password);
                // echo"connect to PDO";
            }catch(PDOException $e){
                die("Failed $e");
            }
            return $conn_pdo;
        } 
}
    //test connect
// $c = new Connect();
// $c->connectToMySQL();
// $c = new Connect();
// $c->connectToPDO();

?>

