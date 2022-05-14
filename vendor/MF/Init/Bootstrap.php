<?php

    namespace MF\Init;

    abstract class Bootstrap {
        /*
                Uma classe abstrata não pode ser instanciada.
            Pode ser somente herdada. Como o nosso objetivo, 
            é trazer para o nosso arquivo de bootstrap os
            métodos de route.php que representam a lógica de
            funcionamento do framework.  
        */

    /*
        4º Passo - Criar a variável privada
        routes e um construtor.
    */

        private $routes;

        abstract protected function initRoutes();

        public function __construct()
        {
            $this->initRoutes();
            // 5º Passo
            $this->run($this->getUrl());
        }
        // 4º Passo
        public function getRoutes(){
            return $this->routes;
        }

        public function setRoutes(array $routes){
            $this->routes = $routes;
        }

        protected function run($url){
            //echo '<br>***************' . $url . '***************<hr>';
            // array
            // path : / ou /sobre_nos
            
            foreach($this->getRoutes() as $path => $route)
            {
                //print_r($route) . '<hr>';
                //echo '<br><br><br><br>';
    
                /*
                    6º passo - Com base nos 2 arrays, averiguar 
                    qual rota devemos usar em função do path que
                    estamos a recuperar.
                        Se a url digitada for compatível com a 
                    rota definida dentro da nossa aplicação, 
                    podemos seguir um fluxo , vamos, 
                    dinamicamente criar uma classe com base
                    no atributo controller.
                        Uma vez que a nossa classe IndexController,
                    tem a primeira letra Maiúscula, vamos ter
                    de converter $route['controller'] que recebe 
                    indexController para IndexController 
                */
    
                // /sobre_nos = /sobre_nos
                if($url == $route['route']){
                    $classe = "App\\Controllers\\".ucfirst($route['controller']);
    
                    /*
                            Instância de uma classe cujo
                        nome do namespace foi formado
                        dinamicamente.
                            O resultado desta instância
                        seria : App\Controllers\IndexController
                            Na essência, o que nós estamos a 
                        fazer é isso porém com base no nosso
                        array de rotas. 
                    */
                    $controller = new $classe;
    
                    /*
                            Uma vez instanciada a nossa classe, 
                        podemos agora com base no objeto, disparar
                        os seus próprios métodos tanto o index 
                        como o sobre_nos.
                    */
    
                    $action = $route['action'];
                    //print_r($action);
                    $controller->$action();
                }
            }
        }

        protected function getUrl(){
            //return $_SERVER['REQUEST_URI']; - Array que retorna 
            // todos os detalhes do servidor da nossa aplicação, 
            // podendo aceder aos atributos
    
            return parse_url($_SERVER['REQUEST_URI'] , PHP_URL_PATH); 
            /*
                parse_url - recebe um url, interpreta o url e
                retorna os seus respetivos componentes. Então,
                retorna um array detalhando quais são os
                componentes da url - retorna um path em array
                    No entanto, ao usar PHP_URL_PATH irá ser
                convertido em string.
    
                    //Exemplo de path e query string
                    return parse_url('www.google.com/gmail?x10');
            */
    
        }
    }

?>

<!-- Nota Importante : O termo de Bootstrap.php é muito utilizado 
para estabelecer o nome de scripts de inicialização das aplicações. 
Não se trata, portanto, da lib css Bootstrap, mas sim, de um termo 
que é muito utilizado como sendo o termo para os scripts de 
inicialização. -->

