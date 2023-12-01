<?php

// Display a twig template with the right information
function displayTemplate(string $template, array $input): string
{
    // Create a new twig instance
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/views');
    $twig = new \Twig\Environment($loader);

    // Change lexer so that the tags are simpler
    $lexer = new \Twig\Lexer($twig, [
        'tag_block' => ['{', '}'],
        'tag_variable' => ['{{', '}}']
    ]);

    $twig->setLexer($lexer);
    
    // Check if there is an active user stored in the session
    if (isset($_SESSION['active_user'])) {
        $input['logged_in'] = 1;
        $input['active_user'] = \RedBeanPHP\R::getRow("SELECT id, username, image FROM users WHERE id = :id;" , [
            ':id' => $_SESSION['active_user']
        ]);
    } else {
        $input['logged_in'] = 0;
    }

    // Return HTML string to be rendered
    return $twig->render($template, $input);
}

// Display an error page
function error(int $errorNumber, string $errorMessage)
{
    http_response_code($errorNumber);
    echo displayTemplate('error.twig', [
        'error' => [
            'number' => $errorNumber,
            'message' => $errorMessage
        ]]);
    die();
}

// Connect to the database with RedBeanPHP
function connectToDatabase($dbname, $dbhost, $dbuser, $dbpass)
{
    class_alias('\RedBeanPHP\R', '\R');
    try {
        \R::setup("mysql:host=" . $dbhost . ";dbname=" . $dbname, $dbuser, $dbpass);
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}

// Highlights code snippets and return html that can be rendered
function highlightCode(string $language, string $code): string
{
    $hl = new \Highlight\Highlighter();
    
    $highlighted = $hl->highlight($language, $code)->value;

    return "<pre class=\"mb-0\"><code class=\"hljs " . $language . " p-3\">" . $highlighted . "</code></pre>";
}

// Return the time underneath a post
function displayPostTime($postDateTime)
{
    $current = strtotime(date("Y-m-d H:i:s"));
    $post = strtotime($postDateTime);

    $difference = $current - $post;
    
    if ($difference < 60) {
        $time = $difference == 1 ? $difference . " second ago" : $difference . " seconds ago";
        return $time;
    } elseif ($difference < 60 * 60) {
        $minutes = round($difference / 60, 0);
        $time = $minutes == 1 ? $minutes . " minute ago" : $minutes . " minutes ago";
        return $time;
    } elseif ($difference < 60 * 60 * 24) {
        $hours =  round($difference / 60 / 60, 0);
        $time = $hours == 1 ? $hours . " hour ago" : $hours . " hours ago";
        return $time;
    } elseif ($difference < 60 * 60 * 24 * 3) {
        $days = round($difference / 60 / 60 / 24, 0);
        $time = $days == 1 ? $days . " day ago" : $days . " days ago";
        return $time;
    } else {
        return date("j F", $post);
    }
}



function transformPost($post)
{
    // Retrieve all comments
    $post['comments'] = \R::getAll(
        'SELECT comments.id, comments.comment, users.username, comments.users_id
        FROM comments
        INNER JOIN users ON comments.users_id = users.id
        WHERE posts_id = :posts_id
        ORDER BY comments.datetime ASC;',
        [
            ':posts_id' => $post['id']
        ]
    );

    // Change time of posting to relative post time
    $post['datetime'] = displayPostTime($post['datetime']);

    // See if currently liked or not
    $try_like = \R::find('likes', 'users_id = :users_id AND posts_id = :posts_id;', [
        ':users_id' => $_SESSION['active_user'] ?? 0,
        ':posts_id' => $post['id']
    ]);
    
    $post['has_liked'] = empty($try_like) ? 0 : 1;

    // Syntax highlight code snippet
    $post['code_snippet'] = highlightCode($post['programming_language'], $post['code_snippet']);

    return $post;
}