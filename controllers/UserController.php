<?php

namespace App\Controllers;

class UserController extends BaseController
{
    public function login($id = 0): string
    {
        return displayTemplate('users/login.twig', [
            'id' => $id,
        ]);
    }

    public function loginPost(): void
    {
        // Retrieve user from database
        $user = \R::getRow("SELECT id, username, password FROM users WHERE username = :username;", [
            ':username' => strtolower($_POST['username'])
        ]);

        if (empty($user)) { // User not found
            header("Location:/user/login?id=1");
        } elseif (password_verify($_POST['password'], $user['password'])) { // Password matches
            $_SESSION['active_user'] = $user['id'];
            header("Location:/");
        } else { // Password not correct
            header("Location:/user/login?id=1");
        }
    }

    public function logout(): void
    {
        session_unset();
        header("Location:/user/login");
    }

    public function register($id = 0): string
    {
        return  displayTemplate('users/register.twig', [
            'id' => $id,
        ]);
    }

    public function registerPost(): void
    {
        // Check if the username is unique
        $validate_name = \R::findOne('users', 'username = :username', [
            ':username' => $_POST['username']
        ]);

        if ($validate_name != null) {
            header("Location:/user/register?id=1");
            die();
        } elseif (preg_match("/\s+/", $_POST['username'])) {
            header("Location:/user/register?id=2");
            die();
        }

        // Check if passwords match
        if ($_POST['password'] != $_POST['passwordconfirm']) {
            header("Location:/user/register?id=3");
            die();
        }

        // Save new user
        $db = \R::dispense('users');

        $db->username = strtolower($_POST['username']);
        $db->name = $_POST['name'];
        $db->password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $db->bio = '';
        $db->image = '/images/profile/generic.jpg';

        $userId = \R::store($db);

        // Save active user in session
        $_SESSION['active_user'] = $userId;

        // Go to home page
        header("Location:/");
        die();
    }

    public function profile($id): string
    {
        // Load user from database
        $user = \R::getRow(
            'SELECT id, username, name, bio, image
            FROM users
            WHERE id = :id;',
            [
                ':id' => $id
            ]
            );
        
        // Load all posts from the database
        $posts = \R::getAll(
            'SELECT posts.id, posts.datetime, posts.code_snippet, posts.caption, posts.programming_language, posts.users_id,
            users.username, users.image,
            COUNT(likes.posts_id) AS likes
            FROM posts
            INNER JOIN users ON posts.users_id = users.id
            LEFT JOIN likes ON posts.id = likes.posts_id
            WHERE posts.users_id = :users_id
            GROUP BY posts.id
            ORDER BY posts.datetime DESC;',
            [
                ':users_id' => $id
            ]
        );

        // Loop through all posts and transform them to be rendered
        for ($i = 0, $l = count($posts); $i < $l; $i++) { 
            $posts[$i] = transformPost($posts[$i]);
        }
        
        // Render view with data
        return displayTemplate('users/profile.twig', [
            'user' => $user,
            'posts' => $posts
        ]);
    }

    public function update($id): string
    {
        $this->authorizeUser();
        
        // Load user from database
        $user = \R::getRow(
            'SELECT id, username, name, bio, image
            FROM users
            WHERE id = :id;',
            [
                ':id' => $_SESSION['active_user']
            ]
            );
        
        // Render view with data
        return displayTemplate('users/update.twig', [
            'user' => $user,
            'id' => $id
        ]);
    }

    public function updatePost(): void
    {
        $this->authorizeUser();

        // Load user
        $db_user = \R::load('users', $_SESSION['active_user']);

        // If username is changed, check if unique
        if ($_POST['username'] != $db_user['username']) {
            $validate_name = \R::findOne('users', 'username = :username', [
                ':username' => $_POST['username']
            ]);

            if ($validate_name != null) {
                header("Location:/user/update?id=1");
                die();
            }  elseif (preg_match("/\s+/", $_POST['username'])) {
                header("Location:/user/update?id=2");
                die();
            }
        }

        // Check if passwords match, if password is set
        if (isset($_POST['password'])) {
            if ($_POST['password'] != $_POST['passwordconfirm']) {
                header("Location:/user/update?id=3");
                die();
            } else {
                $db_user->password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            }
        }
        
        // Upload image
        if (isset($_FILES['image']['name']) && $_FILES['image']['size'] > 0) {
            $filename = $_FILES['image']['name'];
            $tempname = $_FILES['image']['tmp_name'];
            $folder = '/images/profile/' . $filename;
            if (move_uploaded_file($tempname, SITE_ROOT . $folder)) {
                unlink(SITE_ROOT . $db_user['image']);
                $db_user->image = $folder;
            } else {
                header("Location:/user/update?id=4");
                die();
            }
        }

        // Insert data into database
        $db_user->username = strtolower($_POST['username']);
        $db_user->name = $_POST['name'];
        $db_user->bio = $_POST['bio'];

        \R::store($db_user);

        // Go back to update page
        header("Location:/user/update");
        die(); 
    }

    public function search(): string
    {
        $this->authorizeUser();

        $search = $_POST['search-user'] ?? '';

        $users = \R::getAll(
            'SELECT id, username, name, image
            FROM users
            WHERE username LIKE :username OR name LIKE :name;',
            [
                ':username' => "%$search%",
                ':name' => "%$search%"
            ]);
        
        return displayTemplate('/users/search.twig', [
            'matches' => $users,
            'count' => count($users),
            'search' => $search
        ]);
    }
}