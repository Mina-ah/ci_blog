<?php

namespace App\Controllers\Posts;

use App\Controllers\BaseController;
use App\Models\Comment\Comment;
use App\Models\Post\Post;
use CodeIgniter\HTTP\ResponseInterface;
use PhpParser\Node\Expr\AssignOp\Pow;

class PostsController extends BaseController
{
    public function __construct()
    {
        $this->db= \Config\Database::connect();
    }
    public function category($name)
    {
        $posts = new Post();
        $postsCategory = $posts->select('*')->where('category', $name)
        ->orderBy('id','DESC')->get(10)->getResult();

        //categories
        $categories=$this->db->query("SELECT COUNT(posts.category) AS count_posts,categories.name AS name,
        categories.id AS id FROM posts INNER JOIN categories ON posts.category = categories.name
        GROUP BY (posts.category)")->getResult();

        //popular posts
        $popularPosts = $posts->select('*')->orderBy('title','DESC')->get(3)->getResult();

        return view('posts/category',compact('postsCategory','name','categories','popularPosts'));       
    }

    public function singlePost($id){
        
        $post = new Post();
        $comment = new Comment();

        $data = $post->find($id);
       
        //categories
        $categories=$this->db->query("SELECT COUNT(posts.category) AS count_posts,categories.name AS name,
        categories.id AS id FROM posts INNER JOIN categories ON posts.category = categories.name
        GROUP BY (posts.category)")->getResult();

        //popular posts
        $popularPosts = $post->select('*')->orderBy('title','DESC')->get(3)->getResult();

        //comments
        $postsComments = $comment->select('*')->where('post_id', $id)
        ->orderBy('id','DESC')->get()->getResult();

        //no. of comments
       $numComments = $this->db->table('comments')->where('post_id',$id)->countAllResults();

       //blogs below comments
       $rowBlogs = $this->db->query("SELECT * FROM posts WHERE id != '$id' ORDER BY id DESC LIMIT 4")->getResult();


        return view('posts/single',compact('data','categories','popularPosts','postsComments','numComments','rowBlogs'));
    }

    public function storeComment($id){
        $comment = new Comment();
        $data=[
            "user_name" => auth()->user()->username,
            "comment" => $this->request->getPost('comment'),
            "post_id" => $id
        ];
        $comment->save($data);
      
        if($comment){
           return redirect()->to(base_url('posts/single/'.$id.''))->with('create','Comment Saved Sucessfully');
        }

    }

    public function createPost() {
        
        
        if(!isset(auth()->user()->id)){
            return redirect()->to(base_url());
        }
        $categories = $this->db->query("SELECT * FROM categories")->getResult();
        return view('posts/create-post',compact('categories'));
    }
    public function storePost(){
        $posts = new Post();
        $img= $this->request->getFile('image');
        $img->move('public/assets/'.'images');

        $data=[
            "title" => $this->request->getPost('title'),
            "image" => $img->getClientName(),
            "body" => $this->request->getPost('body'),
            "user_id" => auth()->user()->id,
            "user_name" => auth()->user()->username,
            "category" => $this->request->getPost('category')
        ];
        $posts->save($data);
      
        if($posts){
           return redirect()->to(base_url('posts/create-post/'))->with('create','Post created Sucessfully');
        }

    }

    public function deletePost($id){
        $posts = new Post();
       
        
        if(!isset(auth()->user()->id)){
            return redirect()->to(base_url());
        }

        if(auth()->user()->id == $posts->user_id){
            $posts->delete($id);
            if($posts){
            
                return redirect()->to(base_url())->with('delete','Post deleted Sucessfully');
            }
        }
        else{
            return redirect()->to(base_url());
        }

    
    }
    public function updatePost($id){
        $posts = new Post();
        $singlePost = $posts->find($id);

        if(!isset(auth()->user()->id)){
            return redirect()->to(base_url());
        }

        if(auth()->user()->id == $singlePost['user_id']){
        $categories = $this->db->query("SELECT * FROM categories")->getResult();

        return view('posts/update-post',compact('singlePost','categories'));
        }
        else{
            return redirect()->to(base_url());
        }
    }

    public function editPost($id){
        $posts = new Post();
        
        $data=[
            "title" => $this->request->getPost('title'),
            "body" => $this->request->getPost('body'),
            "category" => $this->request->getPost('category')
        ];
        $posts->update($id, $data);
      
        if($posts){
           return redirect()->to(base_url('posts/single/'.$id.''))->with('update','Post Updated Sucessfully');
        }

    }

    public function searchPosts(){

         $posts = new Post();
         $keyword = $this->request->getPost('keyword');
         $searches = $posts->like('title', $keyword)->findAll();
         if($searches){
            return view('posts/searches',compact('searches','keyword'));
         }
    }
}
