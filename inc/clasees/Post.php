<?php
require_once 'PostsMain.php';
require_once 'inc/user.php';


class Post extends PostsMain
{
    public $postId;
    public $postCreatedAt;
    public $postText;
    public $postTitle;
    public $postIMG;
    public $errors = [];


    public function test($postsArray)
    {
        foreach ($postsArray as $value){
               echo '          
          <div class="single-blog-item style-2 d-flex flex-wrap align-items-center mb-50">
            <!-- Blog Thumbnail -->
            <div class="blog-thumbnail">
              <a href="blog-details.php">
                <img src="'.htmlspecialchars($value['imgURL']).'" alt="">
              </a>
            </div>
            <!-- Blog Content -->
          <div class="blog-content">
              <a href="blog-details.php" class="post-title">'.htmlspecialchars($value['title']).'</a>
              <p>'.htmlspecialchars(substr($value['text'], 0, 128)).'...</p>
              <div class="post-meta">
                <a href="#"><i class="icon_clock_alt"></i> '.date('d M Y', strtotime($value['created_at'])).'</a>
              </div>';
               if($this->isAdmin()){
            echo '<div class="text-right mt-2">
                     <a href="#" class="text-success pr-2">Edit</a>
                     <a href="postCreate.php?id='.$value['id_post'].'" class="text-danger pl-2">Remove</a>
               </div>';}
            echo'
              </div>
          </div>';
        }
    }

    /**
     * @param mixed $postText
     * @param mixed $postTitle
     * @param mixed $postIMG
     */
    //vkladani dat do DB
    public function insertInto($postText, $postTitle, $postIMG){
        if($this->isAdmin()){

            $this->validText($postText);
            $postText = $this->getPostText();

            $this->validText($postTitle);
            $postTitle = $this->getPostTitle();


            if(empty($this->errors)){
                $insertQuery=$this->db->prepare('INSERT into posts_sem(created_at, text, title, id_user, imgURL) value (:date, :text, :title, :user_id, :url);');
                $insertQuery->execute([
                    ':date'=>date('Y-m-d H:i:s', time()),
                    ':text'=>$postText,
                    ':title'=>$postTitle,
                    ':user_id'=>$_SESSION['user_id'],
                    ':url'=>$this->validIMG($postIMG)
                ]);
            }else{
                var_dump($this->errors);
            }
        }else{
            $this->errors['status']='Not a admin';
        }
    }
    //vkladani dat do DB end

    //funkce kontola na admin status
    private function isAdmin(){
        if (!empty($_SESSION['user_id'])) {
            $adminQuery = $this->db->prepare('SELECT id_user FROM users_sem where id_user=:id and role=\'admin\' LIMIT 1');
            $adminQuery->execute([
                ':id'=>$_SESSION['user_id']
            ]);
            if($adminQuery->rowCount()>0){
                return true;
            }else{
                return false;
            }
        }
    }
    //end funkce kontrola na admin status

    //Function to validate text
    public function validText($text){
        $text = trim(htmlspecialchars($text));
        if($_POST['postTitle']){
            if (!empty($text)){
                $this->postTitle=$text;
            }else{
                $this->errors['postTitle']='Invalid post title';
            }
        }
        if($_POST['postText']){
            if (!empty($text)){
                $this->postText=$text;
            }else{
                $this->errors['postText']='Invalid post text';
            }
        }
    }
    //function to validate text

    public function validIMG($img){

        $errors = [];

        $fileName = $img['name'];
        $fileSize = $img['size'];
        $fileTmp = $img['tmp_name'];
        $fileType = $img['type'];
        $fileExt = @strtolower(end(explode('.',$img['name'])));

        $expensions= array("jpeg","jpg","png");

        if(in_array($fileExt,$expensions)=== false){
            $errors['extension']="Extension not allowed, please choose a JPEG or PNG file.";
        }else

        if($fileSize > 2097152) {
            $errors['size']='File size must be less than 2 MB';
        }

        if($this->get_mime_type($fileTmp) === 'image/png' ||
           $this->get_mime_type($fileTmp) === 'image/jpeg'||
           $this->get_mime_type($fileTmp) === 'image/jpg' and empty($errors)){
           move_uploaded_file($fileTmp, './img/uploads/'.$fileName);
           return './img/uploads/'.$fileName;
        }else{
            var_dump(0);
        }
    }

    public function get_mime_type($file) {
        $mtype = false;
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mtype = finfo_file($finfo, $file);
            finfo_close($finfo);
        } elseif (function_exists('mime_content_type')) {
            $mtype = mime_content_type($file);
        }
        return $mtype;
    }

    /**
     * @return mixed
     */
    public function getPostText()
    {
        return $this->postText;
    }

    /**
     * @return mixed
     */
    public function getPostTitle()
    {
        return $this->postTitle;
    }

    /**
     * @return mixed
     */
    public function getURL()
    {
        return $this->postIMG;
    }


}