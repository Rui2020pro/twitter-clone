<?php

namespace App\Models;
/*
        O namespace será o mesmo porque, e revendo o que já escrevi, o script
    Tweet.php está dentro do diretório App/Models.
*/

use MF\Model\Model;
/*
    Vamos importar o Model lá do nosso framework para fazer o extends.
*/

class Tweet extends Model {
    private $id;
    private $id_usuario;
    private $tweet;
    private $data;

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    // guardar
    public function guardar(){
        $query = '
        insert 
            into tweets (id_usuario, tweet) 
            values (:id_usuario , :tweet)
        ';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario' , $this->__get('id_usuario'));
        $stmt->bindValue(':tweet' , $this->__get('tweet'));

        $stmt->execute();

        return $this;
    }

    // recuperar
    public function listar(){
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
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario' , $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // recuperar com paginação
    public function getPorPagina($limit, $offset){
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
        limit
            $limit
        offset
            $offset
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario' , $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

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

        //return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
        
    }

}
?>