<?php

/**
 * Можно было так же и в index.php подключить, но там зачем-то находится дубликат кода
 */
require_once __DIR__.'/settings/config.php';
/**
 * Функция выглядит максимально бесполезной: мы уже нашли категорию в файле при прогоне в функции readFromCsv.
 * Зачем нам еще раз ее искать в том же файле?
 *
 * PHPDoc здесь и везде дальше:
 * @param $id
 * @param $file
 * @return mixed|string|void
 */
function findCategoryNameByIdInFile($id, $file){
      foreach($file as $k){
        $data = explode(',', $k);
        if(trim($data[0])==trim($id)){
          // parent category Name
          return $data[1];
        }
      }
  }

function isCategoryExist($category,$parentId){
  $dbh = new PDO("mysql:dbname=category_tz;host=localhost;port=3306;charset=UTF8",'root','');
  $stmt = $dbh->prepare("SELECT id,parent_id FROM categories WHERE name = ? and parent_id = ? "); 
  $stmt->execute([$category,$parentId]);
  
  $row = $stmt->fetch();
  if(!$row){
    return null;
  }
  return $row;
}

/**
 * Нэйминг. Функция дублирует isCategoryExist во всем, кроме присутсвия $parentId.
 */
function findCategoryInTableOrNull($category){
    /**
     * Объект PDO каждый раз создается заново в каждой функции. Проще вынести в отдельную функцию. А еще лучше - вынести
     * в файл конфига все параметры подключения.
     */
  $dbh = new PDO("mysql:dbname=category_tz;host=localhost;port=3306;charset=UTF8",'root','');
  $stmt = $dbh->prepare("SELECT id,parent_id FROM categories WHERE name = ? "); 
  $stmt->execute([$category]);
  $row = $stmt->fetch();
    /**
     * А чем плохо просто вернуть строку? Если запрос ничего не найдет - вернется false. Нам то и нужно ля проверки.
     */
  if(!$row){
    return null;
  }
  return $row;
}

/**
 * То отступов между функциями нет, то они в километр шириной. Надо бы к чему-то одному прийти.
 */


/**
 * Так это у нас не просто readFile, а readFileCSV? Название файла никак не отражает его сути.
 */
function readFromCsv($fileName,$NestedSet){
  $file = file($fileName);
    /**
     * По-моему, этот rebuild здесь лишний. В функциях ниже он выполняется после изменений
     */
  $NestedSet->rebuild(); 
  
  foreach($file as $k){
    $data = explode(',',$k);
      /**
       * Зная, что в CSV первая строка задает столбцы, не проще ли ее просто пропустить (напр. unset(file[0]))?
       * Сэкономит времени немного, но все же
       */
    if($data[0] == 'id'){
      continue;
    }
    if(count($data)==2){
      createCategoryWithTwo($NestedSet,$data);
     
    }
    if(count($data)==3){
      createCategoryWithThree($NestedSet,$data,$file);
    }
  }
}

/**
 * Создать категорию с двумя чем? Нэйминг ужасный
 */
function createCategoryWithTwo($NestedSet,$data){
  $stmt = $NestedSet->Database->PDO->prepare("INSERT INTO categories (parent_id,name,position) VALUES(?,?,?)");
  [$id,$category] = $data;
    /**
     * Если нужны комментарии - ищем проблему с нэймингом и читаемостью кода.
     */
  // была ли уже такая категория:
    /**
     * Лучше $category сразу 1 раз обработать trim и сохранить.
     */
  if(!findCategoryInTableOrNull(trim($category))){
    // если не была добавить:
    $stmt->execute([0,trim($category),$id]); 
    $NestedSet->rebuild();
  }
}

/**
 * У нас же функция выполняется для строки в readFromCsv. Зачем передавать файл, когда можно передать строку?
 */
function createCategoryWithThree($NestedSet,$data,$file){
  $stmt = $NestedSet->Database->PDO->prepare("INSERT INTO categories (parent_id,name,position) VALUES(?,?,?)");
  [$id,$category,$parentId] = $data;
  $parentInstance = findCategoryInTableOrNull(findCategoryNameByIdInFile($id, $file));
    /**
     * Если в строке CSV было передано 3 параметра, значит parent_id нам уже известен. Проверка $parentInstance, как
     * и запрос на его получение - бессмысленны
     */
  if($parentInstance){
    if(!isCategoryExist($category, $parentId)){
      $stmt->execute([$parentInstance['parent_id'],trim($category),$id]); 
      $NestedSet->rebuild();
    }
     
  } else {
    $stmt->execute([$parentId,trim($category),$id]); 
    $NestedSet->rebuild();
  }
}
/**
 * Закомментированный тест, который нужно удалить.
 * И он все равно не работает из-за неправильной структуры БД (см. create_database.sql)
 */
// readFromCsv('categories.csv',$NestedSet);