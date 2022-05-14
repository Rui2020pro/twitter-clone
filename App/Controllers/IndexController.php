<?php
    namespace App\Controllers;

// recursos de miniframework

use MF\Controller\Action;
use MF\Model\Container;
//use App\Connection;

/*  Models - Requisitos Funcionais da Aplicação 
    Neste caso, estamos a limpá-los para iniciar um novo projeto
        use App\Models\Produto;
        use App\Models\Info;
W*/


class IndexController extends Action {

        public function index(){
            /*
                Limpei todo o conteúdo para viabilizar
                a realização de um teste para verificar
                se o framework está ok.
                    A seguir, vou remover os 2 modelos,
                Produto e Info, uma vez que não preciso
                deles para este projeto.
                    Quanto às views, vou apagar só
                o layout2, e vou renomear o layout1 para
                layout uma vez que quero fazer o
                teste só com o layout.
                    Em index dentro de Views, apago o 
                ficheiro sobreNos e vou remover todo o
                conteúdo em index.phtml
                    Ao layout.phtml fiz a inserção de
                bootstrap.min.css , font-awesome, jquery, 
                popper ebootstrap.min.js
                    Em Connection, por agora, mantive o 
                conteúdo copiado do velho miniframework.
            */
            /*
                    Ajustar as Rotas, uma vez que não precisamos
                mais do sobre_nos.
                    A rota home - / que aciona (action) o
                indexController e dispara uma ação (action) -
                index.
                    Dentro de Init, situado em vendor/MF/ não
                precisamos de fazer nada uma vez que ele 
                incorpora recursos relativos à estrutura da 
                plicação e nenhum requisito funcional.
                    No diretório Model, também, não é preciso
                fazer nada, porque são, apenas, implementações
                de requisitos não funcionais para a nossa
                aplicação.
        */

            $this->view->login = isset($_GET['login']) ? $_GET['login'] : '';

            $this->render('index');
        }

        /*
                Completar a Rota
        */

        /* rota criada em index.phtml - link a*/
        public function inscreverse(){
            $this->view->utilizador = array(
                'nome' => '',
                'email' => '',
                'senha' => '',
            );

            $this->view->erroCadastro = false;

            $this->render('inscreverse');
        }

        // representar a action de registar criada em
        // Route.php
        public function registar(){

            // receber os dados do form de inscreverse.phtml
            /*echo '<pre>';
            print_r($_POST);
            echo '</pre>';*/

            /*
                Instanciar a classe Utilizador com a conexão
                à Bd.
            */
            $utilizador = Container::getModel('Utilizador');

            $utilizador->__set('nome' , $_POST['nome']);
            $utilizador->__set('email' , $_POST['email']);
            $utilizador->__set('senha' , md5($_POST['senha']));

            /*echo '<pre>';
            print_r($utilizador);
            echo '</pre>';*/ 

            // guardar
            //$utilizador->guardar();
            
            // sucesso de gravação
            if($utilizador->validarRegisto() && count($utilizador->getUtilizadorPorEmail()) == 0){
                /*
                
                    echo '<pre>';
                    print_r(count($utilizador->getUtilizadorPorEmail()));
                    echo '</pre>';

                        A função count vai retornar 2, uma vez que 
                    temos 2 elementos iguais.
                
                */

                //if(count($utilizador->getUtilizadorPorEmail()) == 0){
                    $utilizador->guardar();

                    /*
                            Agora, podemos renderizar o sucesso do
                        registo através de uma View. 
                            No diretório Views, em index vamos criar
                        o cadastro
                    */

                    $this->render('cadastro');
                //}

            } else {
                // erro de gravação

                $this->view->utilizador = array(
                    'nome' => $_POST['nome'],
                    'email' => $_POST['email'],
                    'senha' => $_POST['senha']
                );

                /*
                        Caso o passo acima não aconteça, vou encaminhar
                    um parâmetro para a view (criação de um atributo 
                    dinamicamente dentro do objeto view, lembrando que
                    o view está acessível através do this porque extendemos
                    a action )
                */
                $this->view->erroCadastro = true;

                $this->render('inscreverse');
            }
        }
    }   
?>