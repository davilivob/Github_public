<?php
ignore_user_abort();
set_time_limit(0);
include_once './simple_html_dom.php';
$m = new Main();
$m->scraping_google();

class Main{
    public string $dbServerName = 'localhost'; 
    public string $dbUserName = 'root';
    public string $dbPassword = '';
    public string $dbName = 'notes';
    public function conn(){
        return mysqli_connect($this->dbServerName, $this->dbUserName, $this->dbPassword, $this->dbName);
    }

    public function sqlCreateTable(){ 
        $this->conn()->query("CREATE TABLE notes.searchstuffs ( title VARCHAR(255) CHARACTER SET 
        utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL , newDate DATETIME NOT NULL , number INT(11) NOT NULL , 
        eei DECIMAL(18) NOT NULL , amount INT(11) NOT NULL ) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;");
    }

    public function sqlRenameTable($n){
        $this->conn()->query("RENAME TABLE searchstuffs TO searchstuffs_$n");
    }

    private function sqlUpdateRecord(){ 
        $this->conn()->query("UPDATE searchStuffs SET number=number+1 WHERE title='record';");
    }

    public function InsertedDate(){ 
        return date('Y-m-d H:i:s');
    }

    public function sqlInsert($title, $date, $number, $eei){
        $this->conn()->query("INSERT INTO searchstuffs(title, newDate, number, eei) VALUES ('$title','$date',$number,$eei);");
    }

    public function sqlCreateNewRecord(){
        $this->conn()->query("INSERT INTO searchstuffs(title, number, amount) VALUES ('record', 0, 999999);");
    }

    public function sqlUpdateEei($title, $eei){
        $this->conn()->query("UPDATE searchstuffs SET eei=$eei WHERE title='$title';");
    }

    public function sqlGetAmount($title){
        $getAmount = "SELECT amount FROM searchStuffs WHERE title='$title';";
        return (int)mysqli_fetch_assoc(mysqli_query($this->conn(), $getAmount))['amount'];
    }

    public function sqlUpdateAmount($title){
        $getAmount = "SELECT amount FROM searchStuffs WHERE title='$title';";
        $change = (int)mysqli_fetch_assoc(mysqli_query($this->conn(), $getAmount))['amount'] ??= 0;
        $this->conn()->query("UPDATE searchstuffs SET amount=$change+1 WHERE title='$title';");
    }

    public function record(){
        $sqlGetRecord = "SELECT number FROM searchStuffs WHERE title='record';";
        return (int)mysqli_fetch_assoc(mysqli_query($this->conn(), $sqlGetRecord))['number'] ?? 0;
    }

    function Connection(){
        try {
            $conn = new PDO("mysql:host=$this->dbServerName;dbname=$this->dbName", $this->dbUserName, $this->dbPassword);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connected successfully<br>";
            $conn = mysqli_connect($this->dbServerName, $this->dbUserName, $this->dbPassword, $this->dbName);
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage().'<br>';
        }
    }

    public function scraping_google(){
        $n = 7;
        $switch = include 'switch.php';
        while($switch){
            $sleep_time = 60;
            $switch = include 'switch.php';
            $url_content = '邱';
            $this->sqlRenameTable($n);
            $this->sqlCreateTable();
            $this->sqlCreateNewRecord();
            while($switch && $this->record() < 42069){
                $eei = 0; $eeiCalArr = []; $theWord = []; $makeMin = []; $total = [];
                $switch = include 'switch.php';
                ini_set("display_errors","On");
                error_reporting(E_ALL);
                $this->Connection();
                $google_sourse = file_get_html("http://www.google.com/search?q=$url_content");
                foreach($google_sourse->find('div[class=BNeawe vvjwJb AP7Wnd]') as $element){
                    $searchWord = $this->getCleanLetter($element);
                    foreach($searchWord as $s){
                        $s = mb_convert_encoding($s.';', 'UTF-8', 'HTML-ENTITIES');
                        
                        $this->sqlInsert($s, $this->InsertedDate(), $this->record(), 0);
                        $this->sqlUpdateAmount($s);
                        $this->sqlUpdateRecord();

                        if($s != $url_content){
                            if(!in_array($s, $eeiCalArr)){
                                array_push($eeiCalArr, $s);
                                $eei++;
                            }
                        }
                        array_push($total, $s);
                    }
                }
                
                $this->sqlUpdateEei($url_content, $eei);

                foreach ($total as $s){
                    array_push($theWord, $s);
                    array_push($makeMin, $this->sqlGetAmount($s));
                }

                $min = min($makeMin);

                foreach($theWord as $key => $w){
                    if($this->sqlGetAmount($w) > $min){
                        unset($theWord[$key]);
                    }
                }
                $theWord = array_values($theWord);
                
                $url_content = $theWord[0];
                sleep($sleep_time);
            }
            // exit();
            $n++;
        }
        exit();
    }


    public function getCleanLetter($element){
        
        $searchWord = str_replace('<div class="BNeawe vvjwJb AP7Wnd">', '', $element);
        $searchWord = str_replace('</div>', '', $searchWord);

        foreach(range('A', 'Z') as $cha) {
            $searchWord = str_replace($cha, '', $searchWord);
        }

        foreach(range('a', 'z') as $cha) {
            $searchWord = str_replace($cha, '', $searchWord);
        }

        $symb = ['-', '_', ' ', '~', '/', '(', ')', ',', '.', "'", '"', '&#12298;',
                '&#12299;', '&#12297;', '&#12296;', '|', '\\', '&#65292;', '&#65288;', 
                '&#65289;', ':', '&#711;', '&#715;', '&#729;', '&#8220;', '&#8221;',
                '&#12301;', '&#12300;', '&#65281;', '&#12304;', '&#12305;', '&#12289;', 
                '@', ']', '[', '&#12308;', '&#12309;', '&#12290;', '&#65306;', '&#65294;',
                '&#65293;', '&#12302;', '&#12303;', '&#65311;', '�L�k�ѧO�o�x�D���C',
                '&#65374;', '&#9472;'
            ];

        $searchWord = str_replace($symb, '', $searchWord);
        $wordArray = explode(';',$searchWord);
        
        foreach($wordArray as $key => $words){
            if(strlen($words) != 7){
                unset($wordArray[$key]);
            }else{
                $words = mb_convert_encoding($words.';', 'UTF-8', 'HTML-ENTITIES');
            }
        }
        
        return $wordArray??'';
    }
}