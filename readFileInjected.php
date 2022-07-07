<?php

/**
 * autoload имеет смысл при применении namespaces. Читаем psr-4, чтобы узнать, как их организовать
 */
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/readFile.php';
/**
 * Глобальная переменная - зачем она тут существует? Она точно нужна?
 * У меня на этот счет большие сомнения.
 */
 $fileContent = '';
/**
 * Что создаем? Текст файла CSV из JSON?
 */
function recursiveCreate($categories,$parentId = null) {
  global $fileContent;
    /**
     * Опечатка. Категория = category
     */
  foreach ( $categories as $categorie){
      /**
       * Неиспользуемая пустая переменная. Если нужен был символ конца строки - есть PHP_EOL
       */
    $csvStr = '';
    if($parentId){
       $csvStr = $categorie['id'].','.$categorie['category'].','.$parentId.':';
    }else {
      $csvStr = $categorie['id'].','.$categorie['category'].':';
    }
      /**
       * Бесполезное применение бесполезной переменной
       */
    $fileContent.= $csvStr.'';
    if(array_key_exists('subcategories',$categorie)){
      recursiveCreate($categorie['subcategories'],$categorie['id']);
    } 
  }
  return $fileContent;
}

/**
 * И зачем нам конвертировать JSON в CSV? Для JSON есть прекрасные методы работы с ним, JSON отлично мутирует в массив.
 */
function convertJsonToCsv($jsonObject){
  $fileStr = recursiveCreate($jsonObject);
    /**
     * Здесь мы просто без объяснения причин роняем программу. Причем роняем ее в случае, если у нас произошла ошибка
     * при чтении файла в другой функции
     */
  if(!$fileStr){
    return;
  }
    /**
     * Чудеса нэйминга: строка файла преобразуется в полноценный файл, который на самом деле массив.
     */
  $file =  explode(':',$fileStr);
    /**
     * Если смотреть по примеру - то сортировка просто переместит пустую строку на самый верх. Усилий много - но мало
     * толка
     */
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
    /**
     * Дубль кода из readFile.php
     */
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

/**
 * Еще один нерабочий тест - все та же проблема с БД
 */
// readFileInjected('categories.csv',$NestedSet);
// читаем файл либо csv либо json
readFileInjected('categories.json',$NestedSet);