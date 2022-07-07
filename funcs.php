<?php

/**
 * Лучше сопровождать каждую функцию комментариями PHPDoc. Либо пользоваться объявлением типов
 *
 * А функция-то бесполезная. Она нигде не применяется. Удалить
 * @param $data
 * @return void
 */
function debug($data) {
    echo '<pre>' . print_r($data, 1) . '</pre>';
}

/**
 * А вот так гораздо лучше:
 *
 * @param array $array
 * @param \Rundiz\NestedSet\NestedSet|null $NestedSet
 * @param $first
 * @return string
 */
function renderTaxonomyTree(array $array, \Rundiz\NestedSet\NestedSet $NestedSet = null, $first = true)
{
    /**
     * Если $array вдруг не массив - то упадет type error. Бессмысленная проверка.
     */
    if (!is_array($array)) {
        return '';
    }
    /**
     * Функция с минимальными изменениями выдрана из ./vendor/rundiz/nested-set/tests/via-http/test-list-taxonomy.php
     * Можно было бы и стандартной воспользоваться - зачем изобретать велосипед?
     */
    if ($first === true) {
        $output = '<ul class="taxonomy-tree">' . "\n";
    } else {
        $output = '<ul>' . "\n";
    }

    foreach ($array as $item) {
        $output .= '<li>' . "\n";
        $output .= '<a href="#">' . $item->name . '</a>';

        $output .= ' (id: ' . $item->{$NestedSet->id_column_name} . ', ';
        $output .= 'parent_id: ' . $item->{$NestedSet->parent_id_column_name} . ', ';
        $output .= 'position: ' . $item->{$NestedSet->position_column_name} . ', ';
        $output .= 'level: ' . $item->{$NestedSet->level_column_name} . ', ';
        $output .= 'left: ' . $item->{$NestedSet->left_column_name} . ', ';
        $output .= 'right: ' . $item->{$NestedSet->right_column_name} . ')';
        $output .= "\n";

        if (property_exists($item, 'children')) {
            $output .= renderTaxonomyTree($item->children, $NestedSet, false);
        }

        $output .= '</li>' . "\n";
    }// endforeach;

    $output .= '</ul>' . "\n";

    return $output;
}
