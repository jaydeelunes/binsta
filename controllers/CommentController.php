<?php

namespace App\Controllers;

class CommentController extends BaseController
{
    public function create($post_id)
    {
        $this->authorizeUser();
        
        $db_post = \R::load('posts', $post_id);
        $db_user = \R::load('users', $_SESSION['active_user']);
        
        $db_comment = \R::dispense('comments');
        
        $db_comment->datetime = date("Y-m-d H:i:s");
        $db_comment->comment = $_POST['comment'];

        $db_post->ownCommentsList[] = $db_comment;
        $db_user->ownCommentsList[] = $db_comment;

        \R::storeAll([$db_post, $db_user]);

        header("Location:/#post$post_id");
    }

    public function delete($comment_id)
    {
        $this->authorizeUser();

        $db_comment = \R::load('comments', $comment_id);

        // Checks if user is deleting own post
        if ($db_comment['users_id'] != $_SESSION['active_user']) {
            error(403, "This is not your comment");
            die();
        }

        $post_id = $db_comment['posts_id'];
        \R::trash($db_comment);

        header("Location:/#post$post_id");
    }
}