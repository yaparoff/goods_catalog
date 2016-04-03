<?php

/**
* Получение массива категорий
**/
function print_arr($array){
	echo "<pre>" . print_r($array, true) . "</pre>";
}

/**
* Получение массива категорий
**/
function get_cat() {
	global $connection;
	$query = "SELECT * FROM categories";
	$res = mysqli_query($connection, $query);
	
	$arr_cat = array();
	while($row = mysqli_fetch_assoc($res)) {
		// Ключи массива равны id
		$arr_cat[$row['id']] = $row;
	}
	return $arr_cat;
}

/**
* Построение дерева категорий из массива
**/
function map_tree($dataset) {
	$tree = array();
	
	foreach ($dataset as $id=>&$node) {
		if (!$node['parent']){
			$tree[$id] = &$node;
		}else{
			$dataset[$node['parent']]['childs'][$id] = &$node;
		}
	}
	
	return $tree;
}

/**
* Дерево в строку HTML
**/
function categories_to_string($data){
	foreach($data as $item){
		// Складываем каждый следующий элемент, обрамленный в HTML код
		$string .= categories_to_template($item);
	}
	return $string;
}

/**
* Шаблон вывода категорий
**/
function categories_to_template($category){
	// Буфферизация вывода
	ob_start();
	include 'category_template.php';
	return ob_get_clean();
}

/**
* Хлебные крошки ($id - id из GET-запроса)
**/
function breadcrumbs($array, $id){
	if(!$id) return false;
	
	$count = count($array);
	$breadcrumbs_array = array();
	for($i = 0; $i < $count; $i++){
		// Если такой id существует
		if($array[$id]){
			// Ключом ставим этот же id. Помещаем название этого элемента
			$breadcrumbs_array[$array[$id]['id']] = $array[$id]['title'];
			// Перезаписываем этот id, ставим значение id родителя
			$id = $array[$id]['parent'];
		}else break;
	}
	return array_reverse($breadcrumbs_array, true);
}

/**
* Получение ID дочерних категорий
**/
function cats_id($array, $id){
	if(!$id) return false;
	
	foreach ($array as $item){
		/* Если ключ 'parent' у элемента равен id, который нам передан...
		(Есть ли у элемента родитель, у которого id равен переданному нам id) */
		if($item['parent'] == $id){
			// ... записываем id этого элемента
			$data .= $item['id'] . ",";
			// записываем результат рекурсивного вызова этой же ф-ции cats_id
			$data .= cats_id($array, $item['id']);
		}
	}
	return $data;
	
/*
 Проще говоря: 
		Берем элемент, смотрим является ли он чьим-то родителем -> 
		Если такой элемент есть (ребенок) - то берем id этого элемента (ребенка) (делаем это для последующего вывода)
		Смотрим есть ли еще наследники этого родителя(их id тоже записываем)
		Затем повторяем эти же действия с ребенком (смотрим является ли он чьим-то родителем, записываем id того ребенка ....) 
*/
}



/**
* Получение товаров
**/
// По умолчанию false, т.е. если запрос без параметра, будет выводиться весь список товаров
function get_products($ids, $start_pos, $perpage){
	global $connection;
	if($ids){
		$query = "SELECT * FROM products WHERE parent IN($ids) ORDER BY title LIMIT $start_pos, $perpage";
	}else{
		$query = "SELECT * FROM products ORDER BY title LIMIT $start_pos, $perpage";
	}
	$res = mysqli_query($connection, $query);
	$products = array();
	while($row = mysqli_fetch_assoc($res)){
		$products[] = $row;
	}
	return $products;
}


/**
*	Получение отдельного товара
**/
function get_one_product($product_id){
	global $connection;
	$query = "SELECT * FROM products WHERE id = $product_id";
	$res = mysqli_query($connection, $query);
	return mysqli_fetch_assoc($res);
}

/**
*  Кол-во товаров
**/
function count_goods($ids) {
	global $connection;
	// Если в переменной ids ничего не передается
	if(!ids){
		// Выбираем все из таблицы 'products'
		$query = "SELECT COUNT(*) FROM products";
	}else{
		// Иначе (если в $ids передается параметр) выбираем все элементы, у которых id родителя(родителей) $ids
		$query = "SELECT COUNT(*) FROM products WHERE parent IN($ids)";
	}
	$res = mysqli_query($connection, $query);
	$count_goods = mysqli_fetch_row($res);
	return $count_goods[0];
}

/**
*	Постраничная навигация
**/
function pagination($page, $count_pages){
	// << < 
	// $back - ссылка НАЗАД
	// $forward - ссылка ВПЕРЕД
	// $startpage - ссылка В НАЧАЛО
	// $endpage - ссылка В КОНЕЦ
	// $page2left - вторая страница слева
	// $page1left - первая страница слева
	// $page2right - вторая страница справа
	// $page1right - первая страница справа
	
	// Тот самый знак вопроса перед $_GET-запросом (?category=...)
	$uri = "?";
	//Если есть параметры в запросе
	if($_SERVER['QUERY_STRING']){
		foreach ($_GET as $key => $value){
			// Если ... , то к $uri добавляем значение ключа (+ разделитель '&')
			if($key != 'page') $uri .= "{$key}=$value&amp;";
		}
	}
	
	if($page > 1){
		$back = "<a class='nav-link' href='{$uri}page=". ($page - 1) ."'>&lt;</a>";
	}
	if($page < $count_pages){
		$forward = "<a class='nav-link' href='{$uri}page=". ($page + 1) ."'>&gt;</a>";
	}
	if($page > 3){
		$startpage = "<a class='nav-link' href='{$uri}page=1'>&laquo;</a>";
	}
	if($page < ($count_pages - 2)){
		$endpage = "<a class='nav-link' href='{$uri}page={$count_pages}'>&raquo;</a>";
	}
	if($page - 2 > 0){
		$page2left = "<a class='nav-link' href='{$uri}page=". ($page-2). "'>" . ($page-2) ."</a>";
	}
	if($page - 1 > 0){
		$page1left = "<a class='nav-link' href='{$uri}page=". ($page-1). "'>" . ($page-1) ."</a>";
	}
	if($page + 1 <= $count_pages){
		$page1right = "<a class='nav-link' href='{$uri}page=". ($page+1). "'>" . ($page+1) ."</a>";
	}
	if($page + 2 <= $count_pages){
		$page2right = "<a class='nav-link' href='{$uri}page=". ($page+2). "'>" . ($page+2) ."</a>";
	}
	
	
	return $startpage . $back . $page2left . $page1left . '<a class="nav-active">' . $page . '</a>' . $page1right . $page2right . $forward . $endpage;
	return "Постраничная навигация";
}