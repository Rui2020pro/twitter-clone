<?php
    namespace App\Controllers;

// recursos de miniframework

use MF\Controller\Action;
use MF\Model\Container;

class AuthController extends Action {
    /*
            Bem, após termos a rota e o controlador,
        precisamos do método autenticar.
    */

    public function autenticar(){
        //echo 'Chegámos até aqui!';
        /*
                Para chegarmos até aqui, precisamos de 
            ter o form preenchido com o email e a senha.
            A seguir, vamos submeter o form para a rota.
                Precisamos de abrir a View principal e 
            ajustar o form.
                Abrindo a aba View, em index.phtml, no form
            que só diz email e senha, vamos alterar o method
            para post e a action para autenticar (/autenticar).
                Após fazer esse post para autenticar, a expetativa
            é em AuthController receber esses dados através da 
            superglobal Post.

                echo '<pre>';
                print_r($_POST);
                echo '</pre>';

                Após a validação do recebimento dos dados, temos de
            validar o utilizador.
                E, como vamos fazer isso? Simples, criando uma
            instância do modelo Utilizador.

                $user = Container::getModel('Usuario');

            Nota:   O modelo Utilizador é responsável por manipular 
            os dados de utilizadores da base de dados.

                Agora, podemos aceder aos atributos email e senha :

                    $user->__set('email' , $_POST['email']);
                    $user->__set('senha' , $_POST['senha']);

                Definido o email e a senha para esse objeto, nós
            devemos executar o método que será responsável por
            averiguar na base de dados se o utilizador, de facto,
            existe na base de dados. Eu vou chamar a esse método
            autenticação.
                Através da instância do objeto, eu vou executar o
            método autenticação ($user->autenticacao()).
                Porém, nós precisamos de implementar esse método
            lá na classe do objeto.
                Voltando ao modelo Utilizador, vou criar um novo 
            método designado autenticacao que fará uma consulta bem
            simples : consultar a tabela tb_usuarios para saber
            se com base no email e na senha, teremos um registo
            compatível.
                A função de autenticacao vai-nos retornar um objeto.
                Após fazer o processo de autenticacao, se o 
            utilizador existir, os dados de id e nome do objeto
            utilizador serão preenchidos,relembrando que os atributos
            email e senha já estavam preenchidos.


                $user->__set('email' , $_POST['email']);
                $user->__set('senha' , $_POST['senha']);

                echo '<pre>';
                print_r($user);
                echo '</pre>'; 

                ******** Depois do Processo de Autenticação ********

                $user->autenticacao();

                echo '<pre>';
                print_r($user);
                echo '</pre>';

                Agora, podemos fazer um teste em AuthController para 
            averiguar se os atributos id e nome estão ou não preenchidos.
                Caso não estiverem, é porque, após o processo de
            autenticação, verificou-se que essa pessoa não está
            registada no sistema ou enganou-se no preenchimento dos dados.
                Se houver erro na autenticação, podemos forçar o 
            redirecionamento do utilizador para a página raiz com uma
            mensagem de erro. header('location:/?login=erro').

                Então em IndexController, antes da renderização da página
            index, na função index, nós podemos capturar esse parâmetro 
            recebido via GET e tratar esse parâmetro dentro da view.
                Mas porquê tratar em IndexController? Uma razão muito
            simples, em Route.php , a rota raiz ela faz a instância de
            indexController e chama a action index.
                Sempre que a raiz for chamada, a action em questão é o
            método index chamado em IndexController. E é dentro desse
            método ou função index, que vamos receber o parâmetro via
            get. 
                Dentro do método index, vou criar um atributo dinâmico
            chamado login. Ao login, vou fazer uma verificação se 
            recebo algum valor via get, se sim tomo esse valor, se não,
            é vazio. 

            $this->view->login = isset($_GET['login']) ? $_GET['login'] : '';

                Agora, dentro da view index (index.phtml), vou localizar o
            formulário de login, vou verificar se o valor recebido pelo
            atributo dinâmico é igual a erro e, se sim, vou incluir uma tag 
            de spam :
           
                <?php if($this->view->login == 'erro') { ?>
                    <div class="row">
                        <div class="col">
                            <span class="text text-danger">Email ou Senha inválidos!</span>
                        </div>
                    </div>
			    <?php } ?>

                Caso o utilizador passe pelo processo de autenticação, o
            próximo passo será a criação das variáveis de sessão.
                Vamos criar 2 atributos dentro da super global session
            que serão id e nome. Vou comentar o echo, e vou dar ao
            início da sessão : session_start();
                Na sequência, vamos recuperar a super global session,
            e vamos definir os atributos id e nome.
                Ao atributo id, vou utilizar o objeto $user e recuperar
            o id, usando o método get e para nome, a mesma coisa.
                Agora, podemos forçar o redirecionamento, após o 
            processo de autenticação, para uma página protegida :

                    header(location:/timeline)

                Dentro dessa action, dentro de um novo controlador que
            irei criar, vamos decidir se devemos ou não exibir a view
            correspondente à timeline do user. 
                Em Route.php, vamos criar uma nova rota designada 
            timeline, vamos definir um novo controlador designado
            AppController que irá conter as páginas restritas da 
            aplicação configuradas de acordo com o user autenticado e 
            a action vai ser timeline.
                Em AuthController, vou copiar a parte de cima, as 
            instruções de cima e vou colar lá no AppController.
                Agora, vou criar a função pública timeline e dar
            um echo 'Chegámos até aqui!'
                Uma vez que redirecionamos para a página timeline e,
            antes temos o tratamento da super global session, podemos
            em AppController, no momento em que a timeline for chamada,
            podemos abrir a sessão novamente e recuperar os valores
            contidos na super global Session.
                Vou dar um print_r da super global Session para verificar
            se os dados preenchidos estão de facto sendo carregados.

                Acontece que a nossa timeline ainda não está protegida,
            uma vez que se eu abrir uma nova janela, por exemplo, em
            navegador anónimo e copiar a url de um navegador para outro :
            http://localhost:8080/timeline , o acesso vai ser feito, 
            porém nós não passamos pelo processo de autenticação, os
            dados da superglobal Session não foram preenchidos. 
                Dentro da action timeline, antes de fazer qualquer coisa,
            verificar se, de facto, os dados estão mesmo preenchidos.
                Se forem diferentes de vazio, significa que nós passamos 
            pelo processo de autenticação. Caso alguém tente aceder à 
            timeline, sem fazer o processo de autenticação, forçamos o
            redirecionamento para a raiz.

                Uma vez que já temos a autenticação e uma vez que já 
            temos a timeline, agora vamos criar uma view para a timeline 
            designada timeline.phtml e, vamos, também, implementar a rota 
            sair com o propósito de destruir a sessão.
                Como vamos criar views para a AppController, ou seja, para
            um novo controlador, então dentro de views, nós precisamos de
            criar um novo diretório que será o diretório app.
                Nós precisamos de agrupar as Views de AppController dentro
            de um diretório que possua o seu respetivo nome dentro da
            camada de visualização.
                Nota : Relembrando que criámos um novo diretório para a 
            index que representa a IndexController. Assim, dentro de Views,
            vamos criar um novo diretório designado app que irá representar
            a AppController. Se fosse necessário criar uma view para 
            AuthController, teríamos de ir dentro de Views e criar um novo
            diretório chamado auth.
                Mas porque não um diretório chamado indexcontroller ou
            appcontroller? Bem, a resposta é simples. O nosso framework,
            faz isso para nós. Em Action.php, lá em Controller dentro do
            diretório de vendor/MF/ , quando realizamos a renderizção de
            uma view, nós definimos o diretório como sendo o nome da classe
            daquele controlador sem o termo controller.
                Por isso, é que a criação desses diretórios é fundamental.
                
                Bem, dentro de Views, dentro do diretório app, vou criar,
            então, um novo arquivo designado timeline.phtml.
                Em AppController, caso o processo de autenticação seja
            realizado com sucesso, eu vou renderizar a página timeline.

                    <h1>TimeLine</h1>
                    <a href="/sair">Sair</a>

                Criei dentro de timeline.phtml um link para a rota sair
            que ainda não foi criada. Assim, em Route.php, vou criar a 
            rota sair que vai disparar o controlador AuthController. 
                Como se trata de um processo de controlo de utilizador,
            então, eu vou centralizar dentro de AuthController.
                Caso essa rota seja chamada, AuthController será instanciado
            e nós vamos chamar a ação sair.
                Dentro de AuthController, vou criar um novo método chamado
            sair que representa, portanto, a nossa ação sair. 
                A ação sair, começa com uma instrução bem simples, que é
            a instrução session_start (Sempre que trabalhamos com sessão,
            precisamos de informar isso para o PHP). Na sequência, vamos
            destruir essa sessão ( session_destroy ) e, por fim, vamos
            utilizar o método header para forçar o redirecionamento para a
            raiz.                
        */

        /*
                Bem, neste preciso momento, acontece que as nossas chaves não
            estão protegidas uma vez que elas ainda não tem o hash (md5). 
            Sendo, assim, no método registar em IndexController, onde recebemos 
            os dados do formulário para registar novos utilizadores, vamos
            apenas ajustar o recebimento da senha :

                    $utilizador->__set('senha' , md5($_POST['senha']));

                Na sequência, o hash de 32 carateres será atribuído como sendo
            o valor do atributo senha do objeto $user. Agora, ao criar um novo
            utilizador, a senha que vai para a base de dados vai ser um hash
            de 32 carateres.
                Agora, vamos ter de fazer um ajuste na nossa aplicação. No 
            momento em que for feita a tentativa de autenticação, estamos a 
            encaminhar para AuthController, na action autenticar, a senha 
            digitada no campo está a ser comparada com o hash e, naturalmente,
            essa string não é igual ao hash. Sendo assim, precisamos de 
            converter para hash essa string, de modo a comparar hash com hash.
                Na tabela usuarios fizemos um update às senhas cujos id eram 1 
            e 2, uma vez que as senhas dos id's (1 e 2), ainda não tinham sido
            convertidas para hash(md5) :

                Update tb_usuarios set senha = md5(senha) where id IN(1,2)
            
        */

        /*
                Agora, passando para os tweets, vamos criar uma nova base de 
            dados que se vai chamar tweet.
                Uma vez tendo criado essa base de dados, precisamos de um novo
            modelo que manipule esses dados. 
                A esse modelo eu vou designá-lo de Tweet (Tweet.php criado 
            dentro de Models). 
                Para agilizar um pouco o desenvolvimento desse modelo, dessa
            classe que vai permitir a instância dos objetos que manipula os
            registos da base de dados, vou copiar o conteúdo da parte de cima
            de Utilizador e colar em Tweet.php
                A seguir, dentro da classe Tweet, vou criar os atributos 
            privados que vão referir aos campos da base de dados ($id, 
            $id_usuario, $tweet, $data).
                Para manipular esses atributos, é necessário haver os métodos
            mágicos, get e set. 
                Como a ideia é inserir e listar tweets, vamos criar 2 funções,
            ou seja, 2 métodos que serão públicos, cujos objetivos serão
            guardar os tweets(insert) e recuperar (select) os tweets.
                Após isso, vamos criar uma nova rota chamada Tweet para 
            receber o Post do Tweet feito pelo utilizador. 
                O que irá acontecer é bem simples, o utilizador escreve 
            qualquer coisa dentro do formulário, clica no botão de Tweet
            e nós precisamos de submeter esse form para alguma URL, ou seja,
            para alguma rota dentro da nossa aplicação que será a rota tweet
            e, essa rota, vai instanciar AppController e vai disparar a 
            action tweet, responsável por receber esses parâmetros e
            instanciar o nosso modelo e tratar o processo de gravação lá na
            base de dados. 
                No entanto, o utilizador tem de estar logado, por isso lá
            na função tweet, verificar se o utilizar está logado. Caso não
            esteja, a aplicação forçará o utilizador a ser redirecionado
            para a página inicial.
                Neste momento, a função tweet vai dar um print_r da super 
            global POST dos dados que vamos receber dessa rota a partir do
            submit do form.
                Em timeline.phtml, no form tweet, precisamos de o ajustar 
            uma vez que ainda não foi definido o caminho do POST.

                    <div class="col tweetBox">
                        <form method="POST" action="/tweet">
                            <textarea name="tweet" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                            
                            <div class="col mt-2 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Tweet</button>
                            </div>

                        </form>
				    </div>

                Ao disparar esse form, os dados serão encaminhados para a 
            rota tweet, essa rota, portanto, faz a instância de AppController
            e dispara a action tweet. A action tweet, basicamente testa se
            o user está autenticado e, caso esteja, exibe os dados recebidos
            da super global POST.
                Próximo Passo : Dentro da ação tweet, vou instanciar o 
            modelo tweet, e, a partir da instância desse modelo, fazer a 
            inserção e a recuperação dos dados da base de dados :

                    Container::getModel('Tweet')

                Este método retorna para nós o objeto já com a conexão 
            para a base de dados.
                Podemos associar esse retorno a uma variável e, a partir 
            dessa variável, podemos trabalhar com o objeto recuperado :

                    $tweet
                    
                À variável tweet, vamos definir o tweet (atributo criado em
            Tweet.php), o que recebemos via POST : 

                    $tweet->__set('tweet' , $_POST['tweet'])

                Uma vez que queremos também o id_usuario, podemos definir o
            id_usuario (atributo criado em Tweet.php) como sendo o valor da
            Session. 

                    $tweet->__set('id_usuario' , $_SESSION['id'])

                O próximo passo é guardar o registo na base de dados. 

                    $tweet->guardar();

                Voltando ao Modelo Tweet, vou criar um método público que se
            vai chamar guardar( guardar() ). Esse método, portanto, irá
            conter uma query que vai inserir o registo na base de dados:
            
                    $query = '
                    insert 
                        into tweets (id_usuario, tweet) 
                        values (:id_usuario , :tweet)
                    '

                    $stmt = $this->db->prepare($query)
                    $stmt->bindValue(':id_usuario' , $this->__get('id_usuario'))
                    $stmt->bindValue(':tweet' , $this->__get('tweet'))

                    $stmt->execute()

                    return $this // Retorno do próprio tweet

                Feito isto, vamos testar para saber se estamos a guardar
            alguma coisa na base de dados - Sucesso na gravação (O tweet
            inserido com sucesso e está associado ao id = 1, que por sua
            vez, é o Jorge).
                Se eu sair (session_destroy()) , e entrar com uma nova 
            pessoa e guardar um novo tweet, o resultado vai ser o mesmo.
                
                Após a gravação do tweet, o próximo passo será forçar o
            redirecionamento da aplicação para a timeline, para que, 
            posteriormente seja possível ver na timeline os tweets 
            registados. Sendo assim, após $tweet->guardar() vou utilizar
            o comando :
                    header('Location: /timeline')

                Próximo Passo - Listar os tweets, ou seja, recuperar os
            tweets da base de dados e listá-los. Em Model, após o método
            guardar, vamos criar um método público com o nome de listar()
            e dentro desse método público, criar uma nova query.

                    $query = ' select * from tweets ';
                    $stmt = $this->db->prepare($query);
                    $stmt->execute();

                    return $stmt->fetchAll(\PDO::FETCH_ASSOC); // retornar um array
                
                O importante agora é que a timeline receba os tweets da
            base de dados. Assim, em AppController na função timeline, 
            precisamos de recuperar os tweets vindos da base de dados e,
            na sequência, encaminhar esses tweets para a view timeline.
                Primeiro, criar a instância do nosso modelo Tweet através
            do container de modo a receber o objeto já com a conexão 
            estabelecida para a base de dados. Essa instância vai ser 
            atribuída a uma variável, no qual lhe vou designar por 
            tweet_connect. 

                    $tweet_connect = Container::getModel('Tweet');

                A seguir, vou utilizar a variável $tweet_connect para executar
            o método listar().
                O retorno deste método é um array de tweets e, portanto, esse
            retorno, vamos associá-lo a uma nova variável. 

                    $tweets = $tweet_connect->listar();

                    echo '<pre>';
                    print_r($tweets);
                    echo '</pre>';

                O retorno deu sucesso porque mostra todos os tweets listados
            lá da base de dados.
                No entanto, eu quero exibir os tweets de um só utilizador,
            exemplo : o utilizador jorge entra e o utilizador jorge só
            quer ver os tweets associados a ele.
                Assim, antes de realizar a função listar, precisamos de
            passar um parâmetro para o nosso objeto que é a informação
            id_usuario.
            
                    $tweet->__set('id_usuario' , $_SESSION['id']);

                Agora, no método listar, vamos modificar a nossa query 
            passando um novo parâmetro, que nada mais é do que o id da sessão
            que definimos em AppController na função timeline.

                    $query = " select * from tweets
                    where id_usuario = :id_usuario";

                    $stmt = $this->db->prepare($query);

                    $stmt->bindValue(':id_usuario' , $this->__get('id_usuario'))

                    $stmt->execute();

                    return $stmt->fetchAll(\PDO::FETCH_ASSOC);

                De forma dinâmica, críamos a timeline.    
                Agora para submeter os dados para a view timeline, ou seja, 
            para os exibir os tweets para o utilizador, vou ter de criar
            um atributo dinâmico chamado tweets ($this->view->tweets) que 
            vai receber a variável tweets ($tweets).

                    $this->view->tweets = $tweets;

                Agora, dentro da view timeline, posso adaptar a div que 
            possui row tweet com a informação da view timeline. 
                Acontece que a nossa query não retorna o nome do usuário,
            logo terei de fazer uma modificação à query, uma vez que ela
            apenas retorna o id do usuario.

                    $query = "
                    select 
                        t.id , t.id_usuario , u.nome , t.tweet , t.data   
                    from 
                        tweets as t
                        inner join usuarios as u on (t.id_usuario = u.id)
                    where 
                        id_usuario = :id_usuario"

                Assim, conseguimos obter a informação do nome do usuario.
                Bem, tendo, agora, a informação do nome do usuario, já 
            podemos ajustar a nossa view. 

            <?php foreach($this->view->tweets as $id_tweet => $tweet) { ?>
                <div class="row tweet">
                    <div class="col">
                        <p><strong><?php echo $tweet['nome'] ?></strong> <span class="text text-muted"><?php echo $tweet['data'] ?></span></p>
                        <p><?php echo $tweet['tweet'] ?></p>

                        <br />
                        <form>
                            <div class="col d-flex justify-content-end">
                                <button type="submit" class="btn btn-danger"><small>Remover</small></button>
                            </div>
                        </form>
                    </div>
                </div>
			<?php } ?>

                No entanto, a data passa a hora, o dia, mês e ano. Querendo
            só passar o dia, mês e ano, tenho de converter a data para um
            novo tipo de formato. Assim, em Model, que consiste na manipulação
            dos dados, na consulta (select) vou ter de fazer essa modificação.
                Além dessa modificação, vou também, fazer uma outra 
            modificação que consiste em apresentar as datas por ordem
            descendente. 

            $query = " 
            select 
                t.id , t.id_usuario , u.nome , t.tweet , DATE_FORMAT(t.data , '%d/%m/%Y') as data 
            from 
                tweets as t
                left join usuarios as u on (t.id_usuario = u.id)
            where 
                t.id_usuario = :id_usuario";
            order by
                data desc

                      
                Em AppController, podemos melhorar o código uma vez que temos
            repetido o processo de verificação da sessão (comportamento comum
            em actions diferentes). Além de o ter copiado, ainda o melhorei.
                Assim sendo, vou criar um novo método que consista só no
            processo de verificação da sessão.
                A esse método, eu vou-lhe dar o nome de validarAutenticacao()
            que vai testar se o utilizador está ou não autenticado. Eu vou
            começar pelo processo de não autenticado. Se o utilizador tentar
            aceder à página timeline ou tentar a criação de um tweet, se ele 
            não estiver autenticado, ou seja, se o atributo id e o atributo 
            nome não estiverem definidos, o utilizador será forçado para a 
            página inicial.
                E uma vez que estamos a trabalhar com sessões, é fundamental
            informar isso para o php através do método session_start.

                public function validarAutenticacao(){

                    session_start();

                    if(!isset($_SESSION['id']) || $_SESSION['id']  == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == ''){
                        header('location:/');
                    }
                }

                Assim, podemos comentar ou tirar as Session em cada uma das
            funções públicas : tweet e timeline e, para validarmos a 
            autenticação, teremos de colocar o código 
            $this->validarAutenticacao() de modo a averiguar se o usuario 
            está ou não autenticado.   
                
        */

        /*
                Pesquisar Por Outros Utilizadores : Primeiro Passo -
            Criar uma nova rota : quem_seguir , Controlador : AppController,
            Ação a ser disparada: quem_seguir
                
                Agora, em AppController, vamos criar um novo método público
            que se irá chamar quem_seguir. Não esquecendo que dentro desse
            método público, vamos começar com o processo tradicional de
            autenticação para ver se de facto o utilizador está ou não
            autenticado. 
                Após o processo tradicional de autenticação, eu vou dar um
            echo : "Estamos aqui no processo de quem_seguir".
                Tendo já a rota definida, podemos aceder à rota em 
            timeline.phtml através do link : Procurar por pessoas conhecidas.
                Em AppController, ao invés de exibir o texto de "Estamos 
            aqui no ... ", vamos renderizar a view quem_seguir.

                <div class="col-md-3">
                    <div class="quemSeguir">
                        <span class="quemSeguirTitulo">Quem seguir</span><br />
                        <hr />
                        <a href="/quem_seguir" class="quemSeguirTxt">Procurar por pessoas conhecidas</a>
                    </div>
                </div>
                
                A seguir, como primeiro passo da view quem_seguir, será
            informar o que nós queremos pesquisar e submeter o que está
            escrito para a própria view, ou seja, dentro da própria
            action quem_seguir.
                No form procurar, vamos definir uma action e um method. 
                Neste caso, o método pode ser get e, uma vez, que queremos 
            exibir os dados dentro da própria view, a action vai ser 
            quem_seguir. Lembrando que precisamos de atribuir aos elementos
            do formulário names para que seja possível recuperar essas
            informações lá no back-end.

                <form method="GET" action="/quem_seguir">
                    ...
                        <input type="text" class="form-control" name="pesquisarPor" placeholder="Quem você está procurando?">
                    ...
                </form>

                Como vamos direcionar esse form para a rota quem_seguir, lá
            em AppController na action quem_seguir, nós vamos ter condições
            de receber esses parâmetros via super global GET por conta do
            método definido no formulário. 

                echo '<br><br><br><br><br><br>';
                echo '<pre>';
                //print_r($_GET);
                print_r($_GET['pesquisarPor']);
                echo '</pre>';

                Bem, agora podemos trabalhar com a informação recebida via
            get. Eu vou atribuir essa informação a uma variável :

                $pesquisarPor
                
                Começando por fazer um teste : Se o índice da super global 
            GET estiver citado (isset) , vamos atribuir o valor para a 
            variável $pesquisarPor, o valor recebido nesse índice. Caso 
            contrário, vou atribuir um valor vazio. E porquê fazer um teste?
            Bem, simples, porque o utilizador pode clicar no botão de
            pesquisarPor e nada acontecer porque ele não preencheu nada.

                $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : ''

                A intenção agora é obter como resultado final uma relação de
            utilizadores. 
                A pesquisa é bem simples, ou seja, se a variável pesquisarPor
            for diferente de vazio, vamos utilizar para esse fim o modelo
            Utilizador. 

                  if($pesquisarPor != ''){ ... }

                Nós já temos esse modelo criado dentro do diretório models
            que é responsável por manipular os dados dos utilizadores. 
                Então, dentro de Utilizador.php, vamos implementar um novo
            método para recuperar todos os utilizadores com base no termo
            de pesquisa. A esse método público, eu vou-lhe designar por
            getAll().
                Dentro desse método público, vamos estabelecer uma query
            que permite pesquisar por ocorrências semelhantes na base de 
            dados. Dessa forma, a quantidade de retornos possíveis é maior. 
            Fica mais fácil para o utilizador localizar os registos que ele 
            deseja. 

            public function getAll(){
                $query = "
                select 
                    id, nome, email
                from 
                    usuarios 
                where 
                    nome like :nome ";

                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':nome' , '%' . $this->__get('nome') . '%' );

                $stmt->execute(); // Executar a Query

                // Por fim, retornar a pesquisa em formato de array
                return $stmt->fetchAll(\PDO::FETCH_ASSOC); 
                
            }

                Nota : $this->__get('nome') - Espera que o atributo nome
            do objeto esteja definido/configurado. Neste momento, ainda não
            está.
                Outra Nota : Como estamos a trabalhar com o like, precisamos
            de concatenar ao termo de pesquisa, os carateres percentagem para
            indicar que esse termo pode ter qualquer coisa à esquerda ou
            qualquer coisa à direita. O like espera os carateres percentagem
            para saber como é que ele se deve comportar durante a pesquisa do
            termo em questão. 

                Como referi anteriormente, no bindValue, estamos a utilizar
            um atributo do próprio objeto, e esse atributo ainda não está
            definido/configurado. Então em AppController nós precisamos de
            instanciar esse objeto e definir/configurar esse atributo que
            será utilizado no método público getAll().
                Primeiro, vou utilizar o Container::getModel para fazer a
            instância do modelo Utilizador. Nós teremos o retorno de um
            objeto já com a conexão estabelecida para a base de dados. Esse
            retorno vou atribuí-lo a uma variável designada $user_seguir.

                A partir da instância do objeto, vamos configurar/definir o 
            atributo nome com o valor recebido na variável $pesquisarPor.
                Tendo essa associação (atributo nome passa a tomar o valor 
            recebido na variável $pesquisarPor), podemos a partir da 
            instância do objeto executar o método getAll().
                Relembrando que o método getAll() retorna para nós um array
            e nós podemos atribuir esse retorno a uma variável designada por
            $seguidores.
                Agora, para fins de teste vamos usar a função print_r de
            modo a averiguar o que estamos a receber. 

                Nota : A pesquisar só se realizará se houver valor na 
            variável pesquisarPor e a variável pesquisarPor recebe valor 
            apenas se o índice da super global estiver citado.

                Outra Nota Importante : Neste momento, estamos também a 
            retornar a pessoa que está autenticada e não faz sentido
            seguir a nós próprios. No entanto, esse bug será resolvido mais
            adiante.

                Por agora, já fora da condição if, vamos submeter os dados 
            para a view e, para fazer isso, vou criar um atributo dinâmico 
            chamado utilizadores_seguir que vai receber como parâmetro 
            $seguidores. Dessa forma, os nossos dados serão submetidos para 
            a view.
                No entanto, a variável utilizadores_seguir é criada dentro
            da condição if e, para evitar qualquer erro, eu vou declarar a
            variável $seguidores fora da condição if e vou-lhe dizer que ela
            é do tipo array. E porquê do tipo array? Simples, porque ela
            recebe um array daí-lhe ter dito que ela era do tipo array. 
                Desta forma, caso não haja um parâmetro de pesquisa, ou seja,
            se a variável pesquisarPor for igual a vazio, então um array 
            vazio será atribuído ao atributo utilizadores_seguir que será 
            submetido para a view. 
                
                Lá na view quem_seguir, podemos agora trabalhar com a 
            variável $this->view->utilizadores_seguir
                A cada iteração do nosso foreach, vamos receber os dados de
            cada um dos utilizadores. 
             
        */

        /*
                Tal como escrevi na nota importante acima, não faz sentido
            seguir-mos a nós próprios. Assim sendo, vamos agora resolver
            esse problema. 
                Para começar, vamos a AppController e na função quem_seguir, 
            vamos dar um print_r da session para saber quem é o utilizador
            que está autenticado. 
                Na sequência, após obtermos os valores dos atributos id e
            nome (echo $_SESSION), eu vou configurar a variável 
            $user_seguir, definindo o atributo id como sendo o id da sessão.

                $user_seguir->__set('id', $_SESSION['id']);

                E, aí, no método getAll do objeto Utilizador(Utilizador.php), 
            vamos modificar a nossa query. Vamos, também, considerar (and) o
            parâmetro id, uma vez que o id do utilizador pesquisado não
            pode ser igual ao id do utilizador autenticado

                $query = "
                    select 
                        id, nome, email
                    from 
                        usuarios 
                    where 
                        nome like :nome and id != :id ";

                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':nome' , '%'. $this->__get('nome') . '%' );
                $stmt->bindValue(':id' , $this->__get('id'));

                $stmt->execute(); // Executar a Query

                // Por fim, retornar a pesquisa em formato de array
                return $stmt->fetchAll(\PDO::FETCH_ASSOC); 

                
                Partindo, agora, para seguir e deixar de seguir utilizadores,
            precisamos de armazenar essa informação na base de dados, ou
            seja, precisamos de informar que o utilizador autenticado está
            seguindo um determinado utilizador. Essa informação precisa de
            estar armazenada para que seja possível depois comparar as 
            informações (utilizador_seguir).

                Próximo passo é a partir dos links Seguir ou Deixar de Seguir
            que temos em quem_seguir.phtml, realizar uma requisição para uma
            rota, rota essa que vai estar em Route.php, que vai definir qual é
            que a ação que deve ser tomada, ou seja, se devemos esperar um 
            fluxo de seguir ou deixar de seguir um determinado utilizador. A 
            essa rota, eu vou-lhe designar por acao que vai disparar 
            uma ação com o nome de acao. 
                Essa rota recebe o id do utilizador em questão e, juntamente 
            com o id do utilizador da sessão, vai tomar uma ação com base no link 
            clicado.
                A ação deixar_seguir vai estar em AppController, que, por sua
            vez, é um método público. 
                O primeiro passo é verificar se o utilizador está autenticado.
            Para isso, basta copiar $this->validarAutenticacao() e colocar no
            topo do método público.
                A seguir, precisamos de descobrir qual a ação que o utilizador
            vai tomar, tendo em conta que existem duas ações, a ação de seguir
            e a ação de deixar de seguir e, também precisamos de descobrir o 
            id do utilizador que será seguido pelo utilizador que está 
            autenticado. 
                Na view quem_seguir, ou seja, em quem_seguir.phtml , vamos 
            modificar os links passando-lhes 2 parâmetros : ação e id.
                Como referi anteriormente, a ação vai tomar 2 valores, seguir
            e deixar de seguir. O id conseguimos obter com base no foreach
            que temos em quem_seguir.phtml. Para averiguar as informações da
            pessoa, podemos dar um print_r($utilizador). Obtendo a informação do
            utilizador, podemos, agora nos links, meter mais o parâmetro do id.

            <?php foreach($this->view->utilizadores_seguir as $indice_array => $utilizador) { ?>
				...
                <div class="col-md-6">
                    <?php echo $utilizador['nome']; print_r($utilizador)?>
                </div>
                ...
                    <div>
                       <a href="/acao?acao=seguir&id_utilizador=<?php echo $utilizador['id']; ?>" class="btn btn-success">Seguir</a>
                       <a href="/acao?acao=deixar_de_seguir&id_utilizador=<?php echo $utilizador['id']; ?>" class="btn btn-danger">Deixar de seguir</a>
                    </div>
									
			<?php } ?>

                Os links estão prontos, com os parâmetros acao desejado para a
            rota acao e o id do utilizador cujo utilizador autenticado pretende
            seguir ou deixar de seguir.
                Como os parâmetros estão sendo encaminhados, podemos averiguar
            em acao (método público em AppController) se estamos a receber
            esses valores.

                    public function acao(){

                        $this->validarAutenticacao();

                        echo '<pre>';
                        print_r($_GET);
                        echo '</pre>';
                    }

                Agora, precisamos de implementar a lógica que vai guardar ou
            remover as informações, dependendo da ação a ser tomada, lá na base 
            de dados (utilizador_seguir).
                
                Assim, vou criar uma variável designada acao que vai receber
            a acao vinda da super global GET. Porém, nós só vamos fazer essa
            atribuição, apenas se esse índice realmente existir na super global.
                Então, se existir, o valor contido nele será atribuido à
            variável acao. Caso contrário, vamos atribuir um valor vazio

                public function acao(){
                    ...
                    $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
                }

                Agora, vou fazer o mesmo para o id do utilizador, criando uma
            variável com o nome de id_usuario_seguindo, ajustando os índices.

                public function acao(){
                    ...
                    $id_usuario_seguindo = isset($_GET['id']) ? $_GET['id'] : '';
                }
                
                Feito isto, vamos fazer agora uma instância da classe Utilizador.
                Porquê? Uma vez que deviámos ter criado um novo modelo ...
                R : Bem, de modo, a facilitar o nosso desenvolvimento. Como nós
            vamos implementar 2 métodos bem simples, seguir e deixar de seguir,
            então, eu optei por utilizar o mesmo modelo, ao invés de criar um
            novo modelo.
                Através do Container, vou utilizar o getModel para recuperar a
            classe Utilizador e, com isso, fazer uma instância dessa classe já
            com a conexão com a base de dados. 
                Essa instância, seu objeto, será, portanto, atribuido a uma
            variável com o nome de $user.

                public function acao(){
                    ...
                    $user = Container::getModel('Utilizador');
                }

                Tendo a ação seguir ou deixar de seguir e tendo já o id do 
            utilizador a quem seguir ou deixar de seguir, falta o id do 
            utilizador da Sessão, visto que, precisamos de informar na base de
            dados, quem é o utilizador da Sessão (id_utilizador).
                Bem, esse id é possível recuperar através da super global
            SESSION. 
                Assim, precisamos de configurar o atributo id (variável id em 
            Utilizador.php - modelo responsável pela manipulaçao dos dados),
            atribuindo-lhe o valor $_SESSION['id'].

                public function acao(){
                    ...
                    $user->__set('id' , $_SESSION['id']);
                    
                }

                Agora, precisamos de tomar uma decisão com base na acao, que 
            pode ser seguir ou deixar de seguir. Se a ação for seguir, a ideia
            é recuperar a instância do nosso objeto e disparar o método 
            seguir_utilizador, lá em Utilizador.php, passando o id do utilizador 
            que queremos seguir ($id_usuario_seguindo). 
                Se a ação for deixar de seguir, a ideia é recuperar a instância 
            do nosso objeto e disparar o método deixar_de_seguir_utilizador, lá 
            em Utilizador.php, passando o id do utilizador que queremos deixar 
            de seguir ($id_usuario_seguindo).

                AppController.php
                
                public function acao(){
                    ...
                    if($acao == 'seguir'){
                        $user->seguir_utilizador($id_usuario_seguindo);
                    } else if ($acao == 'deixar_de_seguir') {
                        $user->deixar_de_seguir_utilizador($id_usuario_seguindo);
                    }
                }

                Utilizador.php

                public function seguir_utilizador($id_usuario_seguindo){
                    echo 'seguir utilizador';
                }

                public function deixar_de_seguir_utilizador($id_usuario_seguindo){
                    echo 'deixar de seguir utilizador';
                }

                Agora, em Utilizador.php, vou criar uma query em 
            seguir_utilizador, que irá inserir as informações lá na base de 
            dados utilizador_seguir.

                public function seguir_utilizador($id_usuario_seguindo){
                    $query = "
                    insert 
                        into utilizador_seguir (id_usuario , id_usuario_seguindo)
                    values
                        (:id_usuario , :id_usuario_seguindo)
                    ";

                    $stmt = $this->db->prepare($query);
                    $stmt->bindValue(':id_usuario' , $this->__get('id'));
                    $stmt->bindValue(':id_usuario_seguindo' , $id_usuario_seguindo);

                    $stmt->execute();

                    return true; // Inserir com Sucesso

                }

                Agora, vamos testar se o respetivo utilizador da sessão está
            ou não a seguir um outro utilizador. Apesar de ainda termos um bug,
            o utilizador está a seguir novamente o outro utilizador, esse
            bug vai ser corrigido para mais tarde. 
                Passando para o próximo passo, configurar o método público
            deixar_de_seguir_utilizador. Vamos copiar a instrução que foi
            utilizada em seguir_utilizador, remodelando a query.

                Uma pequena, MAS IMPORTANTE, NOTA: Em ambos os métodos 
            (seguir_utilizador e deixar_de_seguir_utilizador), é possível
            utilizar o get('id') dado que, em AppController, configurámos o
            atributo id, antes de entrar na decisão da acao a ser tomada.
                Então, no momento, em que o método de seguir_utilizador ou
            deixar_de_seguir_utilizador é executado, o atributo id do objeto
            já está definido. Por isso, é que nós podemos utilizá-lo dessa
            forma. 

                public function deixar_de_seguir_utilizador($id_usuario_seguindo){
            
                    $query = "
                    delete 
                        from utilizador_seguir
                    where
                        id_usuario = :id_usuario and 
                        id_usuario_seguindo = :id_usuario_seguindo
                    ";

                    $stmt = $this->db->prepare($query);
                    $stmt->bindValue(':id_usuario' , $this->__get('id'));
                    $stmt->bindValue(':id_usuario_seguindo' , $id_usuario_seguindo);

                    $stmt->execute();

                    return true; 
                }

                Muito bem, tendo dado sucesso o teste acima, agora precisamos de
            alterar a exibição dos botões com base no facto do utilizador da 
            sessão já estar seguindo ou não o utilizador em questão. 
                Ou seja, se eu já estiver seguindo a Cristina, eu quero ocultar
            o botão de seguir e, apresentar apenas o botão de deixar de seguir.
                Se eu não estiver seguindo a Maria, eu quero ocultar o botão
            de deixar de seguir e, exibir apenas o botão de seguir para evitar
            registos duplicados (bug referido acima).
                A exibição dos botões é feita na View quem_seguir. Sendo assim,
            temos de ir a quem_seguir.phtml para tratar essa exibição. 
                Porém, nos links, não temos nenhuma informação que nos permita
            parametrizar qual o link que deve ser exibido
            . 
                Uma vez que as informações de id_usuario e de id_usuario_seguindo
            estejam sendo armazenadas dentro da tabela utilizador_seguir, a 
            nossa consulta não retorna essas informações.
                Se eu dar um print_r do array lá em Utilizador.php, repare que
            no contexto, só teremos as informações do id_utilizador em questão
            e o email dele.
                Precisamos de, alguma forma, consultar a tabela 
            utilizador_seguir, para averiguar se o utilizador autenticado na
            aplicação está a seguir ou não o user em questão.
                Para fazer isso, é muito simples. Na classe Utilizador, vamos
            trabalhar no método getAll(). Porquê no método getAll()?
                Simples, porque os registos que estamos vendo na view 
            quem_seguir são recuperados a partir desse método. 
                Se abrirmos a appController, na action quem_seguir que é
            disparada quando a rota quem_seguir é acionada, repare que
            estamos fazendo a instância da classe Utilizador e estamos
            executando o método getAll para recuperar os utilizadores. Então,
            neste método, haverá uma adaptação/modificação para, também,
            saber se o utilizador que estamos a exibir está sendo ou não
            seguido pelo utilizador da sessão.
                
                Assim, em getAll, nós podemos fazer uma sobConsulta, incluindo
            um () aplicando um alias - as seguindo_sn , de sim ou não, ou
            seja, o utilizador autenticado está ou não seguindo o utilizador
            que está a ser recuperado no método getAll. 
                Para responder à pergunta do sim ou não, teremos de fazer uma
            sobConsulta para cada um dos registos recuperados. Para ser mais
            fácil, vou aplicar, também, um alias à tabela usuarios (usuarios
            as u) e, vou atualizar a recuperação dos campos da tabela e, 
            também, o where.

                Então, dentro de () vou fazer uma nova consulta utilizando o
            count para contar os registos localizados da tabela
            utilizador_seguir (count * from utilizador_seguir)
            e, na sequência, vou aplicar um alias (as us) e, agora, podemos
            partir aqui para o where. Nós vamos verificar se lá em 
            utilizador_seguir existe algum registo cujo id_usuario é igual ao
            id usuario da sessão (where us.id_usuario = :id_usuario). 
                Relembrando, que o parâmetro (:id_usuario) já está sendo 
            preenchido pelo bindValue do PDO. Ou seja, nós já estamos a
            recuperar qual é que o id do utilizador autenticado.

                Lá em utilizador_seguir, caso exista algum registo que atenda 
            a condição de us.id_usuario = :id e, ao mesmo tempo, em que o 
            id_usuario_seguindo seja igual ao u.id, nós iremos retornar o total de
            registos. Caso exista o total de registos seja 1, quer dizer que estamos
            a seguir essa pessoa. Caso contrário, podemos seguir a pessoa.


                public function getAll(){
                $query = "
                    select 
                        u.id, u.nome, u.email , 
                        (
                            select 
                                count(*)
                            from 
                                utilizador_seguir as us
                            where
                                us.id_usuario = :id and us.id_usuario_seguindo = u.id
                        ) 
                        as seguindo_sn
                    from 
                        usuarios as u
                    where 
                        u.nome like :nome and u.id != :id";

                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':nome' , '%'. $this->__get('nome') . '%' );
                $stmt->bindValue(':id' , $this->__get('id'));

                $stmt->execute(); 

                return $stmt->fetchAll(\PDO::FETCH_ASSOC); 

                Tendo, agora, o trabalho todo feito, nós temos condições de
            fazer essa consulta, a sobConsulta, para cada um dos registos
            consultados na tabela usuarios e, isso, agiliza bastante o nosso
            trabalho. 
                Voltando na view quem_seguir (quem_seguir.phtml), vamos dar,
            um print_r novamente de utilizador para, verificar o que nós 
            recebemos, uma vez que a nossa consulta foi modificada. 
                
                <?php foreach($this->view->utilizadores_seguir as $indice_array => $utilizador) { ?>
                    ...
                    <?php echo $utilizador['nome']; print_r($utilizador);?>
                    ...                                   
                <?php } ?>

                Como pudemos ver, estamos a receber se estamos a seguir ou não
            um determinado utilizador, através da contagem de registos. 
                0 - Identifica que não estamos a seguir a pessoa
                1 - Identifica que já estamos a seguir a pessoa

                Por exemplo, o utilizador autenticado : testehash, segue o
            utilizador Jorge (1) , porém, não segue o utilizador cristina (0).
                
                Uma vez tendo a informação de estarmos ou não a seguir a pessoa,
            podemos agora trabalhar com essa informação e, para fazer isso, é
            muito simples, basta recuperar o índice e antes de exibir os 
            links, tomar uma decisão.
                Eu só vou exibir o primeiro link se seguindo_sn = 0 e, só vou
            exibir o segundo link que é deixar de seguir, se eu já estiver
            seguindo o utilizador, ou seja, se seguindo_sn = 1.
                De modo muito prático, nós estamos a decidir qual o link que
            será exibido. 

                <?php foreach($this->view->utilizadores_seguir as $indice_array => $utilizador) { ?>
                    ...

                    <?php if($utilizador['seguindo_sn'] == 0) {?>
                        <a href="/acao?acao=seguir&id_utilizador=<?php echo $utilizador['id']; ?>" class="btn btn-success">Seguir</a>
                    <?php } ?>

                    <?php if($utilizador['seguindo_sn'] == 1) {?>
                        <a href="/acao?acao=deixar_de_seguir&id_utilizador=<?php echo $utilizador['id']; ?>" class="btn btn-danger">Deixar de seguir</a>
                    <?php } ?>

                    ...

			    <?php } ?>

                O próximo passo, é fazer com que a ação, seja ela seguir ou
            deixar de seguir, faça com que a view, a rota quem_seguir, seja
            exibida novamente.
                Então, no término da operação que é feito no método acao, 
            podemos utilizar o header passando a instrução de location, como
            sendo a rota quem_seguir.

        */

        /*
                Agora que nós já estamos seguindo e deixando de seguir outros
            utilizadores da aplicação, chegou a hora de exibir os tweets de
            outros utilizadores na timeline. 
                Dentro de Route.php, a rota timeline executa a ação de timeline
            dentro de AppController e, em AppController, lá dentro da função
            timeline, estamos a usar o modelo Tweet e executando o método
            listar(). Então é dentro deste método que precisamos de adaptar
            como os tweets estão a ser recuperados. 
                Neste momento, estamos a recuperar os tweets com base no id
            do utilizador autenticado. Assim, precisamos de uma sobConsulta.
                Essa sobConsulta retorna uma relação de registos de 
            id_usuario_seguindo, o in terá inteligência para pesquisar dentro
            dessa relação. 

                        $query = " 
                        select 
                            t.id , 
                            t.id_usuario , 
                            u.nome , 
                            t.tweet , 
                            DATE_FORMAT(t.data , '%d/%m/%Y') as data 
                        from 
                            tweets as t
                            left join usuarios as u on (t.id_usuario = u.id)
                        where 
                            t.id_usuario = :id_usuario
                            or t.id_usuario in(select id_usuario_seguindo from utilizador_seguir
                            where id_usuario = :id_usuario)
                            
                        order by
                            t.data desc
                        ";

                Com uma pequena mudança na nossa query, já temos condições de
            listar os tweets dos utilizadores que estamos seguindo. 
                No entanto, temos um bug, que é não faz muito sentido o utilizador
            autenticado permitir a remoção de tweets de outros utilizadores. 
                Então, nós precisamos de controlar a exibição dessa opção para o
            utilizador. 
                Para fazer isso, é bem simples. Basta irmos a AppController e na
            action timeline que renderiza a view timeline, repare que nós temos 
            acesso ao id do utilizador autenticado através da super global Session.
                Nós estamos a conseguir utilizar a variável Session, porque estamos
            a excutar o método validaAutenticacao, que, por sua vez, inicia a sessão.

                Então, como nós temos acesso a essa informação no escopo da action,
            nós podemos utilizar essa super global dentro da própria view, fazendo
            uma comparação. Podemos verificar se o id do utilizador responsável pela
            tweet é igual ao utilizador da sessão. Se sim, significa que o tweet em
            questão é do utilizador autenticado, então ele pode remover aquele tweet
            senão, o botão de remover não deve ser exibido. 

                Na view timeline, vamos dar um print_r de tweet para ver quais os
            dados que temos acesso dentro desse foreach. 
                Na sequência, vou fazer o mesmo para a super global Session. 
                Assim, podemos fazer uma comparação de id_usuario com o id do
            utilizador da sessão.
                Em cima do botão visível que permite a remoção do tweet, vou incluir
            uma condição que vai verificar se $tweet['id_usuario] é igual a
            $_SESSION['id'].
            
                Desta forma, conseguimos resolver a questão de exibição de tweets de 
            outros utilizadores na timeline do utilizador autenticado, desde claro, 
            o utilizador autenticado esteja seguindo os outros utilizadores em 
            questão. Além disso, ajustámos a exibição de botões de remoção de tweets.
        */

        /*
                Agora, vamos modificar o título do nosso website. Para isso, é muito
            simples, basta abrirmos o layout.phtml que se encontra em Views -> app
            e, simplesmentar, modificar o title. Como todas as nossas views são
            renderizadas dentro do nosso layout, então todas as páginas passam a 
            trabalhar com esse layout e esse title. 

                A seguir, vamos trabalhar na exibição de dados no perfil do utilizador.
                Vamos responder a questões como quantos tweets, quantos utilizadores
            estamos a seguir e quantos seguidores temos. E, também, modificar o nome
            do utilizador autenticado. 
                Como todas as informações são consultas à base de dados, e, são todas
            relativas ao utilizador , nós podemos trabalhar no Modelo Utilizador.

                Primeiro, precisamos de um método para recuperar as informações , neste
            caso o nome, na sequência, precisamos de um método para recuperar o total
            de tweets , a seguir, o total de utilizadores que estamos a seguir e, por
            fim, o total de seguidores. 

                // Informações do Utilizador

                public function getInfoUtilizador(){
                    $query = " select nome from usuarios where id = :id_usuario";
                    $stmt = $this->db->prepare($query);
                    
                        $this->__get('id') - Atributo id do objeto Utilizador, 
                    que quando instanciado mais adiante, receberá o id do
                    utilizador da sessão.
                    
                    $stmt->bindValue(':id_usuario' , $this->__get('id'));

                    $stmt->execute();

                    // Como é esperado apenas um único registo , vamos só fazer
                    // $stmt->fetch()
                    return $stmt->fetch(\PDO::FETCH_ASSOC); 
                }

                // Total de Tweets
                public function getTotalTweets(){
                    $query = " 
                    select 
                        count(*) as total_tweets 
                    from 
                        tweets
                    where   
                        id = :id_usuario";
                    $stmt = $this->db->prepare($query);
                    
                        $this->__get('id') - Atributo id do objeto Utilizador, 
                    que quando instanciado mais adiante, receberá o id do
                    utilizador da sessão.
                    
                    $stmt->bindValue(':id_usuario' , $this->__get('id'));

                    $stmt->execute();

                    // Como é esperado apenas um único registo , vamos só fazer
                    // $stmt->fetch()
                    return $stmt->fetch(\PDO::FETCH_ASSOC); 
                }

                // Total de utilizadores que estamos a seguir
                public function getTotalSeguindo(){
                    $query = " 
                    select 
                        count(*) as total_seguindo 
                    from 
                        utilizador_seguir
                    where   
                        id_usuario = :id_usuario";
                    $stmt = $this->db->prepare($query);
                    
                        $this->__get('id') - Atributo id do objeto Utilizador, 
                    que quando instanciado mais adiante, receberá o id do
                    utilizador da sessão.
                    
                    $stmt->bindValue(':id_usuario' , $this->__get('id'));

                    $stmt->execute();

                    // Como é esperado apenas um único registo , vamos só fazer
                    // $stmt->fetch()
                    return $stmt->fetch(\PDO::FETCH_ASSOC); 
                }

                // Total de seguidores
                public function getTotalSeguidores(){
                    $query = " 
                    select 
                        count(*) as total_seguidores 
                    from 
                        utilizador_seguir
                    where   
                        id_usuario_seguindo = :id_usuario";
                    $stmt = $this->db->prepare($query);
                    
                        $this->__get('id') - Atributo id do objeto Utilizador, 
                    que quando instanciado mais adiante, receberá o id do
                    utilizador da sessão.
                    
                    $stmt->bindValue(':id_usuario' , $this->__get('id'));

                    $stmt->execute();

                    // Como é esperado apenas um único registo , vamos só fazer
                    // $stmt->fetch()
                    return $stmt->fetch(\PDO::FETCH_ASSOC); 
                }

                Tendo os métodos prontos, precisamos de, apenas, reutilizar esses
            métodos nos locais apropriados.
                Em AppController, no método timeline que renderiza a view timeline,
            vamos fazer, agora, uma instância, do modelo Utilizador. Para isso,
            vamos utilizar o Container::getModel('Utilizador') e vamos receber um
            objeto instanciado com a conexão à base de dados e, vou associar esse
            objeto a uma variável designada de $user.

                $user = Container::getModel('Utilizador');

                E, aí, na sequência, basta executarmos os métodos que criámos a 
            partir da instância do objeto.

                $user->getInfoUtilizador()
                $user->getTotalTweets()
                $user->getTotalSeguindo()
                $user->getTotalSeguidores()

                Agora, podemos associar cada um dos retornos desses métodos, que são
            arrays, a variáveis da view.

                $this->view->info_utilizador = $user->getInfoUtilizador()
                $this->view->total_tweets = $user->getTotalTweets()
                $this->view->total_seguindo = $user->getTotalSeguindo()
                $this->view->total_seguidores = $user->getTotalSeguidores()

                Dessa forma, podemos utilizar esses atributos dentro da view timeline.
                Eu vou abrir a view timeline, e vou testar cada um dos atributos.

                Porém, não estamos a apresentar nada porque eu esqueci de um detalhe
            importante, estamos a fazer a instância do modelo Utilizador contudo, não
            estamos citando o atributo id do utilizador que é fundamental. É 
            notório que o id do utilizador é utilizado em todos os nossos métodos.
                Então, a seguir ao Container::getModel, eu vou recuperar o objeto 
            user e através do método set, eu vou configurar o id do objeto Utilizador
            com o valor da sessão que é o indice id que contém o id do utilizador
            autenticado.

                AppController
                
                    $user = Container::getModel('Utilizador');
                    $user->__set('id' , $_SESSION['id']);

                timeline

                    
                    <span class="perfilPainelNome">
                        <?php echo $this->view->info_utilizador['nome']; ?>
                    </span>
                
                    ...

                    <span class="perfilPainelItemValor">
                        <?php echo $this->view->total_tweets['total_tweets']; ?>
                    </span>

                    ...

                    <span class="perfilPainelItemValor">
                        <?php echo $this->view->total_seguindo['total_seguindo']; ?>
                    </span>

                    ...

                    <span class="perfilPainelItemValor">
                        <?php echo $this->view->total_seguidores['total_seguidores']; ?>
                    </span>

                Temos o perfil de utilizador dinâmico com os indicadores necessários e
            o projeto está concluido

        */

        /*
                Vamos acrescentar mais uma coisa no nosso projeto que é a paginação
            de registos. Para fazer isso, eu vou utilizar a timeline , onde temos a
            listagem de registos de tweets. 
                Primeiro passo é aceder à action timeline para começar a configurar
            as variáveis que fazem parte da estrutura de paginação que vamos
            implementar. 
                Em AppController, onde se situa a função timeline, logo após a consulta
            de todos os tweets, nós podemos colocar as variáveis de paginação. 
                A primeira variável que nós podemos determinar é quantos registos nós
            queremos por página. 
                Eu querendo 10 registos por página, vou atribuir a uma variável 
            esse valor (10) - $total_registos_por_pagina = 10
                Após o total de registos por página, que nada mais é do que o limit da
            nossa consulta, a próxima informação que precisamos de determinar é saber
            qual o deslocamento que nós faremos para começar a contar esses 10 registos,
            esse limit que nós estabelecemos. 
                Então eu vou criar uma variável chamada deslocamento ($deslocamento) e 
            a ela eu vou atribuir o valor de 0. Mas porquê 0? Porque, no primeiro 
            momento, eu não quero fazer deslocamento nenhum. Eu quero que os 10 
            registos comecem a contar a partir do primeiro zero, portanto zero seria o 
            primeiro registo retornado.
                Próximo passo é ajustar essa informação na nossa consulta. 
                Repare que nós estamos a fazer um $tweet_connect->listar() para listar
            os nossos registos, e essa informação vai ser comentada.
                Em vez de chamarmos o método listar, vamos chamar um novo método que
            é o de getPorPagina ($tweet_connect->getPorPagina) que vai ter como
            parâmetros, o limite e o deslocamento 
            ($tweet_connect->getPorPagina($total_registos_por_pagina , $deslocamento))

                $tweets = $tweet_connect->getPorPagina($total_registos_por_pagina , $deslocamento);

                Como estamos encaminhando esses valores por parâmetro, nós precisamos
            de receber esses parâmetros na função getPorPagina que irá estar no 
            modelo Tweet.

                public function getPorPagina($limit, $offset){ ... }

                Vamos copiar o código que está em listar e passar para a função 
            getPorPagina. Esses parâmetros serão utilizados na query. Relembrando
            que as bases de dados, de modo geral, suportam a utilização de limit e 
            offset para composição de querys que retornam valores limitados com base
            em um deslocamento que na prática são registos paginados.
                O que pode mudar de uma base de dados para outra é a sintaxe de 
            implementação do limit e offset. No caso do MySQL é muito simples. Basta
            só passar no final da query, após o Order By.
                Primeira consulta projeta os registos que serão retornados de modo
            ordenado para na sequência limitar esse retorno com base no limit e 
            offset. 

                $query = " 
                select 
                    ...
                from 
                    ...
                where 
                    ...      
                order by
                    t.data desc
                limit
                    $limit
                offset
                    $offset
                ";

                No primeiro momento, teremos um deslocamento de 0, ou seja, vamos 
            recuperar a partir do primeiro registo da consulta, os próximos 10. 
                Para facilitar, em AppController, eu vou dar um echo desses valores.

                echo "<br><br><br> <br><br><br> Página : $pagina |
                Total de Registos por Página : $total_registos_por_pagina | 
                Deslocamento : $deslocamento";

                Uma vez que já estamos a pegar os 10 primeiros registos da nossa
            consulta, na prática significa que estamos na 1ª página.

                $pagina = 1;
                
                E, agora, como vamos para a página 2? Bem, nós contínuaríamos com um
            limit de 10, ou seja, vamos listar sempre 10 registos a cada página. 
                A diferença é que, agora, faríamos um deslocamento de 10 registos. Ao
            invés de pegar os 10 primeiros, pegaríamos os 10 registos a partir do 
            décimo. Sendo assim, nós precisamos de ajustar o nosso offset da nossa
            consulta. 

                $total_registos_por_pagina = 10;
                $deslocamento = 10;
                $pagina = 2;

                echo "<br><br><br> Página : $pagina | Total de Registos por Página : 
                $total_registos_por_pagina | Deslocamento : $deslocamento";

                Para fazer a paginação, voltamos à view timeline e, após a listagem
            dos tweets, ou seja, no fim do foreach, vou criar uma div com a classe
            row e vou copiar a documentação de paginação retirada em bootstrap
            pagination.

                <div class="row mt-5">
                    <nav aria-label="...">
                        <ul class="pagination">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Previous</a>
                            </li>

                            <li class="page-item">
                                <a class="page-link" href="#">1</a>
                            </li>

                            <li class="page-item active">
                                <a class="page-link" href="#">2 <span class="sr-only">(current)</span></a>
                            </li>

                            <li class="page-item">
                                <a class="page-link" href="#">3</a>
                            </li>

                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
			    </div>

                Dando continuidade ao recurso de paginação de tweets, na timeline do
            projeto Twitter Clone, vamos trabalhar de forma dinâmica, o 
            o $total_registos_por_pagina , o $deslocamento e a $pagina.
                Para seguir no desenvolvimento da aplicação, nós precisamos de uma
            informação muito importante que é o total de páginas que devem ser 
            apresentadas, uma vez que, de forma estática, estão apresentadas 3 páginas
            e essas 3 páginas vieram do bootstrap pagination. Precisamos de tornar
            isso dinâmico e, para fazer isso, precisamos de encontrar o total de 
            páginas que representam os totais de registos que serão exibidos. 
                Em AppController, vou criar uma nova função que vai recuperar o total
            de registos contidos na tabela tweets. 
                Temos um método em AppController que recupera os tweets em si,
            getPorPagina, mas vamos precisar de mais uma informação que vou chamar
            total_tweets que vai receber tweet->totalRegistos()

                $total_tweets = $tweet_connect->getTotalRegistos();

                Nós, agora, precisamos de implementar o getTotalRegistos lá em
            Tweet.php
                Vou copiar a instrução da função getPorPagina e vou modificar a query.
                Nota : Essa função não recebe nenhum parâmetro.
                À query, vou tirar o order by, o limit e o offset porque nós queremos
            de facto contar todos os registos dessa tabela que se encaixam na
            condição. 
                
                function getTotalRegistos(){
                    $query = " 
                    select 
                        count(*) as total
                    from 
                        tweets as t
                        left join usuarios as u on (t.id_usuario = u.id)
                    where 
                        t.id_usuario = :id_usuario
                        or t.id_usuario in(select id_usuario_seguindo from utilizador_seguir
                        where id_usuario = :id_usuario)
                    ";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindValue(':id_usuario' , $this->__get('id_usuario'));
                    $stmt->execute();

                    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    
                }

                Ao executar esse método, vamos recuperar um array. Assim, podemos
            fazer um teste para averiguar o que estamos a receber.

                AppController

                print_r($total_tweets);

                O array foi recuperado, o valor total são 20 tweets. 
                E, agora, para determinar quantas páginas nós teremos, basta dividir
            o total de registos pelo número de registos exibidos por página. 
                Para isso, criamos uma variável total_paginas e ela vamos atribuir
            a divisão de total de tweets por total_registos_por_pagina. Uma nota
            importante é que temos de arredondar para cima o valor da divisão e,
            para fazer o arredondamento, vamos utilizar a função ceil()

                
                //$total_de_paginas = ceil($total_tweets['total'] / $total_registos_por_pagina);

                Para pegar essa informação e colocar na view timeline, precisamos de
            direcionar essa variável para a view. E, para fazer isso, basta utilizar
            o $this->view e, assim, já teremos acesso à informação.

                $this->view->total_de_paginas = ceil($total_tweets['total'] / $total_registos_por_pagina);

                Na view, nós agora podemos automatizar a exibição das nossas páginas.
                A primeira página, vou definir o href como pagina = 1 e a última 
            página vou definir o href com pagina=$this->view->total_de_paginas

                Desta forma, estou a atribuir o total de paginas a um parâmetro página.
                As páginas intermediárias, eu vou apagar essas informações e nós
            podemos criá-las dinamicamente.

                <?php for($i = 1; $i < $this->view->total_de_paginas ; $i++) { ?>

                Então, vou criar cada um dos links dentro desse for e vou substituir
            a composição da informação desse link. 
                Eu vou falar que o valor da variável $i deve ser apresentado e no href
            vou atribuir à variável pagina, o valor da variável i. 
                
                <div class="row mt-5">
                    <nav aria-label="...">
                        <ul class="pagination">
                            <li class="page-item">
                                <a class="page-link" href="?pagina=1" tabindex="-1">Previous</a>
                            </li>

                            <?php for($i = 1; $i <= $this->view->total_de_paginas; $i++) { ?>
                            <li class="page-item">
                                <a class="page-link" href="pagina=<?php echo $i ?>"><?php echo $i; ?></a>
                            </li>
                            <?php } ?>
                            <li class="page-item">
                                <a class="page-link" href="?pagina=<?php echo $this->view->total_de_paginas ?>">Última</a>
                            </li>
                        </ul>
                    </nav>
                </div>

                A criação dos links, agora, está de forma dinâmica. A criação da 
            nossa informação está de forma dinâmica que pode ser usada na lógica da
            composição da nossa consulta do offset e do limit.
                O nosso desafio agora é com base nesse parâmetro (pagina = 1,2,3)
            determinar os valores que estão, neste momento, estáticos. Mas que valores?

                $total_registos_por_pagina = 10;
                $deslocamento = 0;
                $pagina = 1;

                ... 

                $total_registos_por_pagina = 10;
                $deslocamento = 10;
                $pagina = 2;

                Vou comentar o código $total_registos_por_pagina = 10, 
            $deslocamento = 10 e $pagina = 2, e repare que a pagina vai vir da super
            global GET porque nós temos essa informação através do url. Se a super
            global GET, índice pagina, estiver definida, vamos atribuir à variável
            página, o valor que está vindo da super global GET, caso contrário, 
            vamos atribuir o valor de 1, ou seja, se a pessoa aceder diretamente à
            url timeline, a página será definida como 1 porque não existe na super
            global GET o índice página.

                $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

                Então, nós tornamos dinâmica a identificação do parâmetro pagina. 
                O próximo passo é com base na pagina, calcular o deslocamento. 
                Nós podemos fazer isso, usando uma razão que pode ser utilizada para
            determinar o deslocamento em função da pagina. Repare que o deslocamento
            será a página menos um vezes o total de registos por página, ou seja, este
            é o cálculo que podemos utilizar para identificar o deslocamento que 
            precisa de ser feito em função da página que estamos e, também, do
            número de registos que estamos exibindo por página. 

                $deslocamento = 0; : (1 - 1) * 10
                
                ...

                $deslocamento = 10; : (2 - 1) * 10

                $deslocamento = ($pagina - 1) * $total_registos_por_pagina;

                Desta forma, teremos um deslocamento dinâmico. Para informar, também,
            qual é a página que está ativa. Ainda em AppController, vou criar mais
            uma variável que será empurrada para a view com o nome de pagina_ativa e
            a ela, eu vou atribuir a variável página.

                $this->view->pagina_ativa = $pagina;

                Agora, na timeline, nós temos, portanto, a informação de página ativa.
                
                    timeline.phtml

                    <?php echo $this->view->pagina_ativa ?>

                Ao entrar diretamente na timeline, na url timeline sem nenhum parâmetro, 
            a página ativa será 1, por conta da lógica que nós estabelecemos em 
            AppController : $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1

                No entanto, se passarmos como parâmetro da pagina, o valor de 2,
            a página ativa será a 2.
                Com essa informação, nós podemos pegar nessa variável e, podemos fazer
            uma comparação na composição das opções de paginação.
                Dentro do for, na classe da tag li, é que vamos fazer essa comparação.

                <div class="row mt-5">

                    ...

						<?php for($i = 1; $i <= $this->view->total_de_paginas; $i++) { ?>
						<li class="page-item <?php $this->view->pagina_ativa == $i ? 'active' : ''; ?>">
							<a class="page-link" 
							href="?pagina=<?php echo $i ?>"><?php echo $i; ?></a>
						</li>
						<?php } ?>
						
                    ...

			</div>                
                
                
        */

        $user = Container::getModel('Utilizador');

        $user->__set('email' , $_POST['email']);
        $user->__set('senha' , md5($_POST['senha']));

        /*echo '<pre>';
        print_r($user);
        echo '</pre>';  */

        $user->autenticacao();

        //$retorno_autenticar = $user->autenticacao();

        /*echo '<pre>';
        print_r($retorno_autenticar);
        echo '</pre>';*/

        /*echo '<pre>';
        print_r($user);
        echo '</pre>';*/

        if($user->__get('id') != '' && $user->__get('nome') != ''){
            //echo 'Autenticado!';

            session_start();

            $_SESSION['id'] = $user->__get('id');
            $_SESSION['nome'] = $user->__get('nome');

            header('location:/timeline');
        } else {
            //echo 'Erro na Autenticação!';
            header('location:/?login=erro');
        }
        
    }

    public function sair(){
        session_start();
        session_destroy();
        header('location:/');
    }
}   

?>