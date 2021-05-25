<?php


require_once __DIR__.'/settings/config.php';
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
function findCategoryInTableOrNull($category){
  $dbh = new PDO("mysql:dbname=category_tz;host=localhost;port=3306;charset=UTF8",'root','');
  $stmt = $dbh->prepare("SELECT id,parent_id FROM categories WHERE name = ? "); 
  $stmt->execute([$category]);
  $row = $stmt->fetch();
  if(!$row){
    return null;
  }
  return $row;
}




function readFromCsv($fileName,$NestedSet){
  $file = file($fileName);
  $NestedSet->rebuild(); 
  
  foreach($file as $k){
    $data = explode(',',$k);
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


function createCategoryWithTwo($NestedSet,$data){
  $stmt = $NestedSet->Database->PDO->prepare("INSERT INTO categories (parent_id,name,position) VALUES(?,?,?)");
  [$id,$category] = $data;
  // была ли уже такая категория:
  if(!findCategoryInTableOrNull(trim($category))){
    // если не была добавить:
    $stmt->execute([0,trim($category),$id]); 
    $NestedSet->rebuild();
  }
}
function createCategoryWithThree($NestedSet,$data,$file){
  $stmt = $NestedSet->Database->PDO->prepare("INSERT INTO categories (parent_id,name,position) VALUES(?,?,?)");
  [$id,$category,$parentId] = $data;
  $parentInstance = findCategoryInTableOrNull(findCategoryNameByIdInFile($id, $file));
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

// readFromCsv('categories.csv',$NestedSet);