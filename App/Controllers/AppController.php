<?php
    namespace App\Controllers;

// recursos de miniframework

use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {

    public function timeline(){
        //session_start();

        //echo 'Chegámos até aqui!';

        //if($_SESSION['id'] != '' && $_SESSION['nome'] != ''){
            /*echo '<pre>';
            print_r($_SESSION);
            echo '</pre>';*/

        $this->validarAutenticacao();

        $tweet_connect = Container::getModel('Tweet');

        $tweet_connect->__set('id_usuario' , $_SESSION['id']);

       /* $total_registos_por_pagina = 10;
        $deslocamento = 0;
        $pagina = 1;*/

        /*$total_registos_por_pagina = 10;
        $deslocamento = 10;
        $pagina = 2;*/

        $total_registos_por_pagina = 10;
        $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
        $deslocamento = ($pagina - 1) * $total_registos_por_pagina;

        /*echo "<br><br><br> Página : $pagina | Total de Registos por Página : 
        $total_registos_por_pagina | Deslocamento : $deslocamento <br>";*/

        $tweets = $tweet_connect->getPorPagina($total_registos_por_pagina , $deslocamento);
        $total_tweets = $tweet_connect->getTotalRegistos();

        //print_r($total_tweets);

        //print_r($total_tweets[0]['total']);

        $this->view->total_de_paginas = ceil($total_tweets['total'] / $total_registos_por_pagina);
        
        /*echo '<br>';
        print_r($total_de_paginas);*/



        //$tweets = $tweet_connect->listar();

        /*echo '<pre>';
        print_r($tweets);
        echo '</pre>';*/

        $this->view->tweets = $tweets;

        $this->view->pagina_ativa = $pagina;

        $user = Container::getModel('Utilizador');
        $user->__set('id' , $_SESSION['id']);

        $this->view->info_utilizador = $user->getInfoUtilizador();
        $this->view->total_tweets = $user->getTotalTweets();
        $this->view->total_seguindo = $user->getTotalSeguindo();
        $this->view->total_seguidores = $user->getTotalSeguidores();

        $this->render('timeline');

        /*}
        else {
            header('location:/');
        }*/
    }

    public function tweet(){
        //session_start();

        //echo 'Chegámos até aqui!';

        //if($_SESSION['id'] != '' && $_SESSION['nome'] != ''){
            /*echo '<pre>';
            print_r($_POST);
            echo '</pre>';*/

        $this->validarAutenticacao();

        $tweet =  Container::getModel('Tweet');

        $tweet->__set('tweet' , $_POST['tweet']);
        $tweet->__set('id_usuario' , $_SESSION['id']);

        //echo $tweet->__get('id_usuario');
        //echo '<br>' . $tweet->__get('tweet');

        $tweet->guardar();

        header('location:/timeline');

        /*}
        else {
            header('location:/');
        }*/
    }

    public function quem_seguir(){
        $this->validarAutenticacao();

        //echo "Estamos aqui no processo de quem_seguir";

        /*echo '<br><br><br><br><br><br>';
        echo '<pre>';
        print_r($_GET['pesquisarPor']);
        echo '</pre>';*/

        $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

        /*echo '<br><br><br><br><pre>';
        print_r($_SESSION);
        echo '</pre>';*/

        //echo '<br><br><br><br><br><br>Pesquisando por : ' . $pesquisarPor;

        $seguidores = array();

        if($pesquisarPor != ''){
            $user_seguir = Container::getModel('Utilizador');

            $user_seguir->__set('nome' , $pesquisarPor);
            $user_seguir->__set('id' , $_SESSION['id']);

            $seguidores = $user_seguir->getAll();

            /*echo '<pre>';
            print_r($seguidores);
            echo '</pre>';*/
        }

        //print_r($seguidores);

        $this->view->utilizadores_seguir = $seguidores;

        $this->render('quem_seguir');
    }

    public function validarAutenticacao(){

        session_start();

        if(!isset($_SESSION['id']) || $_SESSION['id']  == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == ''){
            header('location:/');
        }
    }

    public function acao(){

        $this->validarAutenticacao();

        /*echo '<pre>';
        print_r($_GET);
        echo '</pre>';*/

        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $id_usuario_seguindo = isset($_GET['id_utilizador']) ? $_GET['id_utilizador'] : '';

        $user = Container::getModel('Utilizador');

        $user->__set('id' , $_SESSION['id']);

        if($acao == 'seguir'){
            $user->seguir_utilizador($id_usuario_seguindo);
        } else if ($acao == 'deixar_de_seguir') {
            $user->deixar_de_seguir_utilizador($id_usuario_seguindo);
        }

        header('location: quem_seguir');
    }

}

?>