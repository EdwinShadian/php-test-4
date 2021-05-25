<?php 



require_once __DIR__.'/settings/config.php';
  $fileContent = 'id,category,parent_id';

function recursiveCreate($categories,$parentId = null) {
  global $fileContent;
  foreach ( $categories as $categorie){
    $csvStr = '';
    if($parentId){
       $csvStr = $categorie['id'].','.$categorie['category'].','.$parentId.'';
    }else {
      $csvStr = $categorie['id'].','.$categorie['category'];
    }
    $fileContent.= $csvStr.' ';
    if(array_key_exists('subcategories',$categorie)){
      recursiveCreate($categorie['subcategories'],$categorie['id']);
    } 
  }

}
function readFromJson($fileName,$NestedSet){
  $jsonObject = json_decode(file_get_contents($fileName),true);
  // print_r($jsonObject);
  return recursiveCreate($jsonObject);
}


readFromJson('categories.json',$NestedSet);
print_r( $fileContent);