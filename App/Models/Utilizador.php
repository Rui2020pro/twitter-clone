<?php

/*
    Nota Importante : Relembrando que os namespaces
    são sempre constituídos pelos diretórios onde o
    script se situa. Neste caso, Utilizadores.php
    situa-se em App/Models.

        Em vendor MF, temos uma abstração dos modelos
    que é a Classe Model. Então todos os modelos da
    nossa aplicação precisam de extender essa classe,
    porque é ela quem diz como é feita a conexão com 
    a base de dados. Para fazer a extensão, precisamos 
    de recuperar o Model a partir do namespace 
    MF\Model\Model.
*/

namespace App\Models;

use MF\Model\Model;

class Utilizador extends Model {
        private $id;
        private $nome;
        private $email;
        private $senha;

        /*
                Estes atributos vão representar as
            colunas de registo na base de dados.
        */

        /*
                Uma vez que os nossos atributos são
            privados, eu para manuseá-los preciso
            dos métodos mágicos, set e get.
        */

        public function __get($name)
        {
            return $this->$name;
        }

        public function __set($name, $value)
        {
            $this->$name = $value;
        }

        //guardar

        public function guardar(){
            $query = "insert into usuarios (nome, email, senha) values (:nome, :email , :senha)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':nome' , $this->__get('nome'));
            $stmt->bindValue(':email' , $this->__get('email'));
            $stmt->bindValue(':senha' , $this->__get('senha')); // md5() -> hash de 32 carateres
            $stmt->execute();

            return $this;
        }

        /*
                Após a função guardar estar pronta, a partir do
            indexController posso implementar a função registar.
                Em inscreverse.phtml, ao método POST, vou adicionar 
            a action registar : /registar
                Utilizei /registar e não registar para a aplicação
            ter a inteligência de que é uma rota na raiz da nossa
            aplicação. 
                Como agora, estamos a criar uma nova rota dentro da
            aplicação, nós precisamos de ajustar o nosso arquivo
            route, precisamos de incluir essa rota para que a nossa
            aplicação funcione.
        */

        // validação do registo
        public function validarRegisto(){
            $valido = true;

            // Verificar se cada um dos POST tem 3 ou mais carateres
            if(strlen($this->__get('nome')) < 3 || strlen($this->__get('email')) < 3 || strlen($this->__get('senha')) < 3){
                $valido = false;
            }

            return $valido;
        }

        /*
                Averiguar se o email já está
            registado no sistema.
        */

        // recuperar o utilizador por email
        public function getUtilizadorPorEmail(){
            $query = "select nome, email from usuarios where email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email' , $this->__get('email'));
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function autenticacao(){
            $query = "select id, nome, email from usuarios where email = :email and senha = :senha";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email' , $this->__get('email'));
            $stmt->bindValue(':senha' , $this->__get('senha'));
            $stmt->execute();

            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            /*
                    Caso haja um retorno de $user , 
                ($user = $stmt->fetch(\PDO::FETCH_ASSOC))
                nós podemos fazer um teste se lá em
                usuario existe um índice id e um índice
                nome. 
                    Se id e nome forem diferentes de vazio 
                significa que tivemos um processo de autenticação
                com sucesso. Sendo assim, podemos definir os
                atributos id e nome do próprio objeto Utilizador.
                    $this->__set('id' , $user['id'])
                    $this->__set('nome' , $user['nome'])

                    Agora, ao invés de retornar o resultado que veio
                da base de dados (return $user) , podemos retornar o
                próprio objeto.

            */
            if($user['id'] != '' && $user['id'] != ''){
                $this->__set('id' , $user['id']);
                $this->__set('nome' , $user['nome']);
            }

            //return $user;

            return $this; // Retornar um Objeto
        }

        public function getAll(){
            /*$query = "
                select 
                    id, nome, email
                from 
                    usuarios 
                where 
                    nome like :nome and id != :id";*/

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

            $stmt->execute(); // Executar a Query

            // Por fim, retornar a pesquisa em formato de array
            return $stmt->fetchAll(\PDO::FETCH_ASSOC); 

        }

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

        // Informações do Utilizador

        public function getInfoUtilizador(){
            $query = " select nome from usuarios where id = :id_usuario";
            $stmt = $this->db->prepare($query);
            /* 
                $this->__get('id') - Atributo id do objeto Utilizador, 
            que quando instanciado mais adiante, receberá o id do
            utilizador da sessão.
            */
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
            /* 
                $this->__get('id') - Atributo id do objeto Utilizador, 
            que quando instanciado mais adiante, receberá o id do
            utilizador da sessão.
            */
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
            /* 
                $this->__get('id') - Atributo id do objeto Utilizador, 
            que quando instanciado mais adiante, receberá o id do
            utilizador da sessão.
            */
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
            /* 
                $this->__get('id') - Atributo id do objeto Utilizador, 
            que quando instanciado mais adiante, receberá o id do
            utilizador da sessão.
            */
            $stmt->bindValue(':id_usuario' , $this->__get('id'));

            $stmt->execute();

            // Como é esperado apenas um único registo , vamos só fazer
            // $stmt->fetch()
            return $stmt->fetch(\PDO::FETCH_ASSOC); 
        }
    }

?>