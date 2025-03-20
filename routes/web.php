<?php
/**
 * Définition des routes web
 */

use App\Core\Router;

// Routes principales
Router::get('/home', 'HomeController@index');

// Routes d'authentification
Router::get('/login', 'AuthController@showLoginForm');
Router::post('/login', 'AuthController@login');
Router::get('/register', 'AuthController@showRegisterForm');
Router::post('/register', 'AuthController@register');
Router::get('/logout', 'AuthController@logout');
Router::get('/forgot-password', 'AuthController@showForgotForm');
Router::post('/forgot-password', 'AuthController@forgotPassword');
Router::get('/reset-password/{token}', 'AuthController@showResetForm');
Router::post('/reset-password', 'AuthController@resetPassword');

// Profil utilisateur
Router::get('/profile', 'UserController@profile');
Router::post('/profile', 'UserController@updateProfile');

// Authentification sociale
Router::get('/auth/google', 'AuthController@redirectToGoogle');
Router::get('/auth/google/callback', 'AuthController@handleGoogleCallback');
Router::get('/auth/facebook', 'AuthController@redirectToFacebook');
Router::get('/auth/facebook/callback', 'AuthController@handleFacebookCallback');

// Routes CRUD exemple
Router::get('/users', 'UserController@index');
Router::get('/users/create', 'UserController@create');
Router::post('/users', 'UserController@store');
Router::get('/users/{id}/edit', 'UserController@edit');
Router::put('/users/{id}', 'UserController@update');
Router::delete('/users/{id}', 'UserController@destroy');
