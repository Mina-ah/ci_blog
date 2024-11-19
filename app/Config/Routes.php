<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index', ['as' => 'home']);
$routes->get('/about-us', 'Home::aboutUs', ['as' => 'about']);
$routes->get('/contact', 'Home::contact', ['as' => 'contact']);
service('auth')->routes($routes);

$routes->group('posts',function($routes){

//posts

$routes->get('category/(:any)', 'Posts\PostsController::category/$1');
$routes->get('single/(:num)', 'Posts\PostsController::singlePost/$1');
$routes->post('store-comment/(:num)', 'Posts\PostsController::storeComment/$1');
$routes->get('create-post/', 'Posts\PostsController::createPost');
$routes->post('store-post/', 'Posts\PostsController::storePost', ['as'=> 'store.post']);
$routes->get('delete-post/(:num)', 'Posts\PostsController::deletePost/$1', ['as'=> 'post.delete']);
$routes->get('update-post/(:num)', 'Posts\PostsController::updatePost/$1', ['as'=> 'post.update']);
$routes->post('edit-post/(:num)', 'Posts\PostsController::editPost/$1', ['as'=> 'post.edit']);
$routes->post('search', 'Posts\PostsController::searchPosts', ['as'=> 'search']);
});

//users
$routes->get('users/my-posts/', 'Users\UsersController::myPost', ['as'=> 'my.post']);

//Admin
$routes->get('admins/login/', 'Admins\AdminsController::viewlogin', ['as'=> 'view.login', 'filter'=>'loginfilter']);
$routes->post('admins/check-login/', 'Admins\AdminsController::checklogin', ['as'=> 'check.login']);

$routes->group('admins',['filter'=>'authfilter'], function($routes){
$routes->get('index/', 'Admins\AdminsController::adminindex', ['as'=> 'index']);
$routes->get('admin-logout/', 'Admins\AdminsController::adminlogout', ['as'=> 'admin.logout']);

//admin
$routes->get('alladmins/', 'Admins\AdminsController::alladmin', ['as'=> 'alladmins']);
$routes->get('createadmins/', 'Admins\AdminsController::createadmin', ['as'=> 'createadmins']);
$routes->post('createadmins/', 'Admins\AdminsController::storeadmin', ['as'=> 'storeadmins']);


});

$routes->group('category',['filter'=>'authfilter'], function($routes){
//categories
$routes->get('allcategories/', 'Admins\AdminsController::allcategory', ['as'=> 'allcategories']);
$routes->get('createcategories/', 'Admins\AdminsController::createcategory', ['as'=> 'createcategories']);
$routes->post('createcategories/', 'Admins\AdminsController::storecategory', ['as'=> 'storecategories']);
$routes->get('editcategories/(:num)', 'Admins\AdminsController::editcategory/$1', ['as'=> 'editcategories']);
$routes->post('editcategories/(:num)', 'Admins\AdminsController::updatecategory/$1', ['as'=> 'updatecategories']);
$routes->get('deletecategories/(:num)', 'Admins\AdminsController::deletecategory/$1', ['as'=> 'deletecategories']);

//posts
$routes->get('allposts/', 'Admins\AdminsController::allposts', ['as'=> 'allposts']);
$routes->get('deleteposts/(:num)', 'Admins\AdminsController::deletepost/$1', ['as'=> 'deleteposts']);
});