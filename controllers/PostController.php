<?php

namespace App\Controllers;

class PostController extends BaseController
{
    public function feed(): string
    {
        // Load all posts from the database
        $posts = \R::getAll(
            'SELECT posts.id, posts.datetime, posts.code_snippet, posts.caption, posts.programming_language, posts.users_id,
            users.username, users.image,
            COUNT(likes.posts_id) AS likes
            FROM posts 
            INNER JOIN users ON posts.users_id = users.id
            LEFT JOIN likes ON posts.id = likes.posts_id
            GROUP BY posts.id
            ORDER BY posts.datetime DESC;'
        );

        // Loop through all posts and transform them to be rendered
        for ($i = 0, $l = count($posts); $i < $l; $i++) { 
            $posts[$i] = transformPost($posts[$i]);
        }

        // Render the page
        return displayTemplate('posts/feed.twig', [
            'posts' => $posts
        ]);
    }

    public function create()
    {
        $this->authorizeUser();
        
        return displayTemplate('posts/create.twig', []);
    }

    public function createPost()
    {
        $this->authorizeUser();
        
        $db_post = \R::dispense('posts');

        $db_post->datetime = date('Y-m-d H:i:s');
        $db_post->code_snippet = $_POST['code_snippet'];
        $db_post->caption = $_POST['caption'];
        $db_post->likes = 0;
        $db_post->programming_language = $_POST['programming_language'];
        $db_post->users_id = $_SESSION['active_user'];

        \R::store($db_post);
        header("Location:/");
    }

    public function delete($id): void
    {
        $this->authorizeUser();
        
        $db_post = \R::load('posts', $id);

        // Checks if user is deleting own post
        if ($db_post['users_id'] != $_SESSION['active_user']) {
            error(403, "This is not your post");
            die();
        }

        \R::exec('DELETE from comments WHERE posts_id = :posts_id;', [
            ':posts_id' => $db_post['id']
        ]);

        \R::exec('DELETE from likes WHERE posts_id = :posts_id;', [
            ':posts_id' => $db_post['id']
        ]);

        \R::trash($db_post);

        header("Location:/");
    }

    public function like($id)
    {
        $this->authorizeUser();
        
        $try_like = \R::findOne('likes', 'users_id = :users_id AND posts_id = :posts_id;', [
            ':users_id' => $_SESSION['active_user'],
            ':posts_id' =>  $id
        ]);

        if (!empty($try_like)) {
            \R::trash($try_like);
        } else {
            $db_post = \R::findOne('posts', 'id = :id;', [':id' => $id]);
            $db_user = \R::findOne('users', 'id = :users_id;', [':users_id' => $_SESSION['active_user']]);
            
            $db_like = \R::dispense('likes');
    
            $db_user->ownLikesList[] = $db_like;
            $db_post->ownLikesList[] = $db_like;
    
            \R::storeAll([$db_user, $db_post]);
        }

        header("Location:/#post$id");
    }
}