<?php
ignore_user_abort();
set_time_limit(0);
class Connection {
    public string $dbServerName = 'localhost'; 
    public string $dbUserName = 'root';
    public string $dbPassword = '';
    public string $dbName = 'notes';
    public string $tableName = 'searchstuffs_4';
    
    public function conn(){
        return mysqli_connect($this->dbServerName, $this->dbUserName, $this->dbPassword, $this->dbName);
    }
    
    public function record(){
        $sqlGetRecord = "SELECT number FROM $this->tableName WHERE title='record';";
        return (int)mysqli_fetch_assoc(mysqli_query($this->conn(), $sqlGetRecord))['number'];
    }

    public function sqlSelectByNum($num){
        $sqlGetTitle = "SELECT title, amount FROM $this->tableName WHERE number=$num";
        return [mysqli_fetch_assoc(mysqli_query($this->conn(), $sqlGetTitle))['title'], 
                mysqli_fetch_assoc(mysqli_query($this->conn(), $sqlGetTitle))['amount']];
    }

    // public function BigArr(){
    //     $Str = "";
    //     $file = "./4.txt";
    //     for($i = 0; $i < 25000; $i++){
    //         $Str = $Str."['".$this->sqlSelectByNum($i)[0]."',".$this->sqlSelectByNum($i)[1]."],";
    //         file_put_contents($file, "['".$this->sqlSelectByNum($i)[0]."',".$this->sqlSelectByNum($i)[1]."],", FILE_APPEND);
    //     }
    //     return substr($Str, 0, strlen($Str)-1);
    // }
}
$c = new Connection();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Search Automaton</title>
</head>
<style>
Main{
    text-align:center;
}
</style>
<body>
<script src="./p5.js/p5.js"></script>
<script>

let pixelSize = 50;
let scl = 160;
let alreadyHave = [];
let newFont;

chessboard = create2dArray();
let BigArr = new Array(scl*scl);
// BigArr = [<?php //echo $c->BigArr();?>];

let json;

var m = new MapSetUp(scl/2, scl/2); 

function preload(){
    newFont = loadFont("./font/FOT-MatissePro-B.otf");
    json = loadJSON("./json/searchstuffs_twin_i.json");
}

function setup(){
    for(var i = 0; i < 42069; i++){
        BigArr[i] = [json[2]['data'][i+1]['title'], json[2]['data'][i+1]['amount']];
    }
    createCanvas(scl*pixelSize, scl*pixelSize);
    background(255);
    chessboard = create2dArray();
    m.main();
    m.drawMap();
}

function draw(){
    // translate(scl*pixelSize/2, scl*pixelSize/2, 0)
    // m = new MapSetUp(scl/2, scl/2);
    // camera(0, 0, (height/2)/tan(PI/6), 00, 00, 0, 00, 1, 0);
    // stroke(20);
    // box(200);
}

function create2dArray(){
    var arr = new Array(scl);
    for(var i = 0; i < scl; i++){
        arr[i] = new Array(scl);
    }
    for(var i = 0; i < scl; i++){
        for(var j = 0;j <= scl; j++){
            arr[i][j] = 0;
        }
    }
    return arr;
}

function in_array(array, element){
    var check = 0;
    for(var i = 0; i < array.length; i++){
        if (array[i] == element){
            check++;
        }
    }
    if(check > 0){
        return true;
    }else {
        return false;
    }
}

function MapSetUp(beginX, beginY){
    this.x = beginX;
    this.y = beginY;
    this.dir = 0;

    this.main = function(){
        for(var i = 0; i < scl*scl; i++){
            if(BigArr[i] != null){
                if (in_array(alreadyHave, BigArr[i][0]) == false){
                    alreadyHave.push(BigArr[i][0]);
                    chessboard[this.x][this.y] = BigArr[i];
                    this.move();
                }else{

                }
            }
        }
    }

    this.move = function(){
        if (this.dir % 4 == 0) {
            if(chessboard[this.x][this.y+1] == 0){
                this.dir++;
                this.y++;
            }else{
                this.x++;
            }
        }
        else if (this.dir % 4 == 1) {
            if(chessboard[this.x-1][this.y] == 0){
                this.dir++;
                this.x--;
            }else{
                this.y++;
            }
        }
        else if (this.dir % 4 == 2) {
            if(chessboard[this.x][this.y-1] == 0){
                this.dir++;
                this.y--;
            }else{
                this.x--;
            }
        }
        else if (this.dir % 4 == 3) {
            if(chessboard[this.x+1][this.y] == 0){
                this.dir++;
                this.x++;
            }else{
                this.y--;
            }
        }
    }

    this.drawMap = function(){
        for(var i = 0; i < scl; i++){
            for(var j = 0; j < scl; j++){
                if(chessboard[i][j][1] != 0){
                    textFont(newFont);
                    textAlign(CENTER, CENTER);
                    textSize(pixelSize/3+chessboard[i][j][1]*2.2);
                    var col = map(chessboard[i][j][1], 0, 2284, 0, 255);
                    colorMode(HSB);
                    fill(col, 255, 255, 75/col);
                    text(chessboard[i][j][0], i*pixelSize, j*pixelSize);    
                }
            }
        }
    }
}

</script>
    
</body>
</html>