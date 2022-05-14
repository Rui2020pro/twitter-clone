<?php

namespace App;

use MF\Init\Bootstrap;

class Route extends Bootstrap {

    // 2º Passo - initRoutes
    protected function initRoutes(){

        $routes['home'] = array(
            'route' => '/',
            'controller' => 'indexController',
            'action' => 'index'
        );
        $routes['inscreverse'] = array(
            'route' => '/inscreverse',
            'controller' => 'indexController',
            'action' => 'inscreverse' // ação disparada em indexController
        );

        $routes['registar'] = array(
            'route' => '/registar',
            'controller' => 'indexController',
            'action' => 'registar' // ação disparada em indexController
        );

        $routes['autenticar'] = array(
            'route' => '/autenticar',
            'controller' => 'AuthController', // Novo Controlador para Login
            'action' => 'autenticar' // ação disparada em authController
            // método autenticar dentro do AuthController
        );

        $routes['timeline'] = array(
            'route' => '/timeline',
            'controller' => 'AppController', // Novo Controlador para Processo de Autenticação
            'action' => 'timeline' // ação disparada em AppController
            // método timeline dentro do AppController
        );

        $routes['sair'] = array(
            'route' => '/sair',
            'controller' => 'AuthController', // 
            'action' => 'sair' // ação disparada em AuthController
            // método sair dentro do AuthController
        );

        $routes['tweet'] = array(
            'route' => '/tweet',
            'controller' => 'AppController', // 
            'action' => 'tweet' // ação disparada em AppController
            // método sair dentro do AppController
        );

        $routes['quem_seguir'] = array(
            'route' => '/quem_seguir',
            'controller' => 'AppController', // 
            'action' => 'quem_seguir' // ação disparada em AppController
            // método sair dentro do AppController
        );

        $routes['acao'] = array(
            'route' => '/acao',
            'controller' => 'AppController', // 
            'action' => 'acao' // ação disparada em AppController
            // método sair dentro do AppController
        );

        $this->setRoutes($routes);

    }
}

?>