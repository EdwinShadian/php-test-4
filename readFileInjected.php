<?php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/readFile.php';

 $fileContent = '';

function recursiveCreate($categories,$parentId = null) {
  global $fileContent;
  foreach ( $categories as $categorie){
    $csvStr = '';
    if($parentId){
       $csvStr = $categorie['id'].','.$categorie['category'].','.$parentId.':';
    }else {
      $csvStr = $categorie['id'].','.$categorie['category'].':';
    }
    $fileContent.= $csvStr.'';
    if(array_key_exists('subcategories',$categorie)){
      recursiveCreate($categorie['subcategories'],$categorie['id']);
    } 
  }
  return $fileContent;
}
function convertJsonToCsv($jsonObject){
  $fileStr = recursiveCreate($jsonObject);
  if(!$fileStr){
    return;
  }
  $file =  explode(':',$fileStr);
 
  uasort($file, function($a,$b){
    $dataA = explode(',', $a);
    $dataB = explode(',', $b); 
    return $dataA[0]>$dataB[0];
  });
  return $file;
}

function readFromJson($fileName, $NestedSet){
  $NestedSet->rebuild(); 
  $jsonObject = json_decode(file_get_contents($fileName),true);
  $file = convertJsonToCsv($jsonObject);
  foreach($file as $k){
    if(!$k){
      continue;
    }
    $data = explode(',',$k);
    if($data[0] == 'id'){
      continue;
    }
    if(count($data) == 2){
      createCategoryWithTwo($NestedSet,$data);
    }
    if(count($data)==3){
      createCategoryWithThree($NestedSet,$data,$file);
    }
  } 
  $NestedSet->rebuild();
}
function readFileInjected($fileName,$NestedSet){
  ['extension'=>$ext] = pathinfo($fileName);
  if($ext == 'csv'){
    readFromCsv($fileName,$NestedSet);
  }
  if($ext == 'json'){
    readFromJson($fileName, $NestedSet);
  }
}

// readFileInjected('categories.csv',$NestedSet);
// читаем файл либо csv либо json
readFileInjected('categories.json',$NestedSet);