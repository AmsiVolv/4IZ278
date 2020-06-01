<?php

require_once 'Post.php';
#Asi nejjednodušší část projektu chtěl jsem udělat přes OOP.
#Nevím jak to dopadne, ale pokusím se.

class PostsMain
{
   public $db;
   private $postsArray;

   #nechtel jsem prepisovat db.php na class, takze vytvoril obycejnou fci
   public function __construct(){
       $this->db=new PDO('mysql:host=127.0.0.1;dbname=semestralka;charset=utf8', 'root', '');
       $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   }

   #funkce ktera nacita vsechni prispevky
   public function getPosts(){
       $postQuery=$this->db->prepare('SELECT * FROM posts_sem;');
       $postQuery->execute([]);
       $this->postsArray=$postQuery->fetchAll(PDO::FETCH_ASSOC);
       return($this->postsArray);
   }
   #konec fce

    #fce ktera kontroluje existence prispevku
    public function checkPost($postId){
        $postQuery=$this->db->prepare('SELECT * FROM posts_sem WHERE id_post=:id LIMIT 1;');
        $postQuery->execute([
           ':id'=>$postId
        ]);
        if($postQuery->rowCount()>0){
            return true;
        }else{
            return false;
        }
    }
    #endregion kontrola prispevku

    public function deletePost($postId){
        $deleteQuery=$this->db->prepare('DELETE FROM posts_sem WHERE id_post=:id LIMIT 1;');
        $deleteQuery->execute([
            ':id'=>$postId
        ]);
    }

}