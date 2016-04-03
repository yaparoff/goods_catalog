<?php
include "config.php";
include "functions.php";

$categories = get_cat();
$categories_tree = map_tree($categories);
$categories_menu = categories_to_string($categories_tree);


// Если в массиве $_GET существует элемент 'product'
// (Если есть обращение к конкретному товару)
if(isset($_GET['product'])){
	
	$product_id = (int)$_GET['product'];
	// Массив данных продукта
	$get_one_product = get_one_product($product_id);
	// Получаем ID категории
	$id = $get_one_product['parent'];
	
// Иначе (если номер продукта не поступает) берем номер категории
}else{
	$id = (int)$_GET['category'];
}

	// хлебные крошки
	// return true (array not empty) || return false
	$breadcrumbs_array = breadcrumbs($categories, $id);
	
	if($breadcrumbs_array){
		$breadcrumbs = "<a href='" . PATH . "'>Главная</a> / ";
		foreach($breadcrumbs_array as $id => $title){
			// Сложим все элементы массива, обернув их в ссылку
			$breadcrumbs .= "<a href='" . PATH . "?category={$id}'>{$title}</a> / ";
		}
		
		// Если массив '$get_one_product' не существует
		// (Если нет обращения к продукту)
		if(!isset($get_one_product)){
			// Убираем разделитель '/' у последнего элемента
			$breadcrumbs = rtrim($breadcrumbs, " / ");
			// Убираем ссылку у последнего элемента
			$breadcrumbs = preg_replace("#(.+)?<a.+>(.+)</a>$#", "$1$2", $breadcrumbs);
		
		// Иначе (если массив '$get_one_product' существует)
		}else{
			// К хлебным крошкам добавляем название продукта
			$breadcrumbs .= $get_one_product['title'];
		}
		
	}else{
		$breadcrumbs = "<a href='" . PATH . "'>Главная</a> / Каталог";
	}
	
	// ID дочерних категорий
	$ids = cats_id($categories, $id);
	// Если в $ids false - тогда мы положим туда значение $id этой категории,
	// иначе положим туда значение переменной ids
	$ids = !$ids ? $id : rtrim($ids, ",");
	

	/*============= Пагинация ==============*/

	// кол-во товаров на страницу
	$perpage = 5;
	// Общее количество товаров
	$count_goods = count_goods($ids);
	// Необходимое количество страниц 
	// (Делим "общее количество товаров" на "кол-во товаров на страницу", при этом округляем их в большую сторону )
	$count_pages = ceil($count_goods / $perpage);
	
	// Если кол-во страниц = 0, то будет выводиться 1 страница
	if(!$count_pages) $count_pages = 1;

	/* Получение запрошенной страницы */
	// Если в массиве $_GET существует переменная $page
	if(isset($_GET['page'])){
		// тогда $page приводим к целому числу значение данного параметра
		$page = (int)$_GET['page'];
		// меньше одной страницы не должно быть
		if( $page < 1 ) $page = 1;
	//	Иначе в переменную $page мы положим 1
	}else{
		$page = 1;
	}
	
	// Если запрошенная страница больше, чем количество страниц в категории
	if($page > $count_pages ) $page = $count_pages;
	
	// Начальная позиция для запроса
	// (С какого ряда начинается выборка)(подробнее урок 4, 31 мин)
	// Т.е. с какого по какой идет выборка по количеству $perpage на одну страницу
	$start_pos = ($page - 1) * $perpage;
	$pagination = pagination($page, $count_pages);
	

	/*============= Пагинация ==============*/
	
	$products = get_products($ids, $start_pos, $perpage);