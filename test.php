<?
  #h&4%BwxW;
  header('Content-type: text/html; charset=utf-8');
  set_time_limit(0);
 // ini_set('session.gc_maxlifetime', 14400);
  $directory = $argv[1];
  //$directory = "data/";
  $arrFiles = array_diff(scandir($directory), array('..', '.'));
  sort($arrFiles);
  require_once("db.php");
  require_once("translit.php");

  //exit(var_dump($argv));
  
  for($i=0; $i <= (count($arrFiles)/ 2)-1; $i++){
    importXML($directory . $arrFiles[$i]);
    offersXML($directory . $arrFiles[$i+count($arrFiles)/ 2]);
  }
  echo("XML parcing complite!!!");
  
  function importXML($file){
    $xml = simplexml_load_file($file);
    //echo($xml->Классификатор->Наименование);
    
    global $mysqli;
    $result = $mysqli->query("SHOW COLUMNS FROM `taskitems`");
    $isField = false;
    $city = translStr(str_replace(array('Классификатор','(',')',' '),'',$xml->Классификатор->Наименование));
    while ($row=$result->fetch_assoc()){
      if($isField = array_search("quantity_".$city,$row)) {
        break;
      }
    }
    if(!$isField) {
      $result = $mysqli->query("ALTER TABLE `taskitems` ADD COLUMN quantity_".$city." float(20)  NULL DEFAULT '0'");
      $result = $mysqli->query("ALTER TABLE `taskitems` ADD COLUMN price_".$city." float(20)  NULL DEFAULT '0'");
    }
    echo $city.PHP_EOL;
    //exit(var_dump($isField));
    //exit(var_dump($a->fetch_assoc()));
  
    foreach($xml->Каталог->Товары->Товар as $item)
    {
      
      //echo( "Наименование: ".$item->Наименование.'<br>');
      //echo( "Вес: ".$item->Вес.'<br>');
      //echo( "<br>");
      $usage="";
      
      if(!empty($item->Взаимозаменяемости->Взаимозаменяемость))
      {
        foreach($item->Взаимозаменяемости->Взаимозаменяемость as $usageItem){
        $usage .= $usageItem->Марка.'-'.$usageItem->Модель.'-'.$usageItem->КатегорияТС.'|';
        } 
      }
      //echo( "Usage: ".$usage.'<br>');
      //echo('<br>');
      $code=trim($item->Код);
      echo( $city." Import: ".$code.PHP_EOL);
      $result = $mysqli->query("SELECT * FROM `taskitems` WHERE `code`='$code'");
      $result = $result->fetch_assoc();
      if(isset($result)){
        $result = $mysqli->query("UPDATE `taskitems` SET `name`='$item->Наименование',`code`='$code',`weight`='$item->Вес',`usage`='$usage' WHERE `code`='$code'");
      } else {
        $mysqli->query("INSERT INTO `taskitems` (`name`, `code`, `weight`, `usage`) VALUES ('$item->Наименование','$code', '$item->Вес', '$usage')");
      }
    }
  } 
  
  function offersXML($file){
    global $mysqli;
    $xml = simplexml_load_file($file);
    $city = translStr(str_replace(array('Классификатор','(',')',' '),'',$xml->Классификатор->Наименование));
    $price = "price_".$city;
    $quantity = "quantity_".$city;
    //echo($price);
    //echo($quantity);
  
    foreach($xml->ПакетПредложений->Предложения->Предложение as $item)
    {
      
      $priceValue=$item->Цены->Цена->ЦенаЗаЕдиницу;
      $quantityValue=$item->Количество;
      $code=trim($item->Код);
      echo($city." Offer: ".$code.PHP_EOL);
      $result = $mysqli->query("UPDATE `taskitems` SET `$price`='$priceValue',`$quantity`='$quantityValue' WHERE `code`='$code'"); 
      
    }
  }