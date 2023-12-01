<?php

require __DIR__ . '/vendor/autoload.php';

// Database info    
$dbname = "binsta";
$dbhost = "127.0.0.1";
$dbuser = "bit_academy";
$dbpass = "bit_academy";

// Make connection to database
connectToDatabase($dbname, $dbhost, $dbuser, $dbpass);

// Array users, posts and comments
$users = [
    [
        'username' => 'johnny_doe',
        'name' => 'John Doe',
        'password' => 'pass',
        'bio' => 'Avid reader. Coffee lover. Tech enthusiast.',
        'image' => '/images/profile/john.jpg',
        'likes' => [3, 5, 6],
        'posts' => [
            [
                'datetime' => '2023-11-12 15:30:00',
                'code_snippet' => "<?php\n// Your PHP code snippet here\n// Example PHP code\n\$variable = 'Hello, World!';\necho \$variable;\n?>",
                'likes' => 30,
                'caption' => 'My first PHP snippet!',
                'programming_language' => 'php',
                'comments' => [
                    [
                        'datetime' => '2023-11-16 13:45:00',
                        'users_id' => 2,
                        'comment' => 'Nice work on this snippet!'
                    ],
                    [
                        'datetime' => '2023-11-15 14:30:00',
                        'users_id' => 3,
                        'comment' => 'Awesome coding skills!'
                    ]
                ]
            ],
            [
                'datetime' => '2023-11-11 18:45:00',
                'code_snippet' => "<?php\n// Your PHP code snippet here\n// Another example PHP code\n\$array = [1, 2, 3, 4, 5];\nforeach (\$array as \$element) {\n    echo \$element * 2 . ' ';\n}\n?>",
                'likes' => 22,
                'caption' => 'PHP foreach loop!',
                'programming_language' => 'php',
                'comments' => [
                    [
                        'datetime' => '2023-11-13 10:00:00',
                        'users_id' => 2,
                        'comment' => 'Great code snippet!'
                    ],
                    [
                        'datetime' => '2023-11-14 09:45:00',
                        'users_id' => 3,
                        'comment' => 'Nice explanation!'
                    ]
                ]
            ]
        ]
    ],
    [
        'username' => 'jane83',
        'name' => 'Jane Smith',
        'password' => 'pass',
        'bio' => 'Nature lover. Photographer. Traveler.',
        'image' => '/images/profile/jane.jpg',
        'likes' => [1, 2, 7],
        'posts' => [
            [
                'datetime' => '2023-11-14 09:00:00',
                'code_snippet' => "console.log('Hello, World!');\n// Your JavaScript code snippet here\n// Example JavaScript code",
                'likes' => 25,
                'caption' => 'Exploring JavaScript!',
                'programming_language' => 'javascript',
                'comments' => [
                    [
                        'datetime' => '2023-11-15 16:30:00',
                        'users_id' => 3,
                        'comment' => 'I like how you explained this!'
                    ],
                    [
                        'datetime' => '2023-11-16 17:15:00',
                        'users_id' => 1,
                        'comment' => 'Good job on this code snippet!'
                    ],
                ]
            ],
            [
                'datetime' => '2023-11-13 13:20:00',
                'code_snippet' => "function factorial(num) {\n    // Your JavaScript code snippet here\n    // Factorial calculation\n    if (num === 0 || num === 1)\n        return 1;\n    for (var i = num - 1; i >= 1; i--) {\n        num *= i;\n    }\n    return num;\n}\nconsole.log(factorial(5));",
                'likes' => 18,
                'caption' => 'JavaScript factorial function!',
                'programming_language' => 'javascript',
                'comments' => [
                    [
                        'datetime' => '2023-11-18 12:00:00',
                        'users_id' => 3,
                        'comment' => 'Excellent explanation!'
                    ],
                    [
                        'datetime' => '2023-11-19 09:30:00',
                        'users_id' => 1,
                        'comment' => 'Really useful code!'
                    ]
                ]
            ]
        ]
    ],
    [
        'username' => 'itsemily',
        'name' => 'Emily Brown',
        'password' => 'pass',
        'bio' => 'Fitness freak. Dog person. DIY enthusiast.',
        'image' => '/images/profile/emily.jpg',
        'likes' => [1, 3, 4],
        'posts' => [
            [
                'datetime' => '2023-11-17 08:00:00',
                'code_snippet' => "#include <stdio.h>\n\nint main() {\n    // Your C code snippet here\n    // Example C code\n    printf('Hello, World!');\n    return 0;\n}",
                'likes' => 8,
                'caption' => 'My first C program!',
                'programming_language' => 'c',
                'comments' => [
                    [
                        'datetime' => '2023-11-20 10:45:00',
                        'users_id' => 2,
                        'comment' => 'Impressive code!'
                    ]
                ]
            ],
            [
                'datetime' => '2023-11-16 11:30:00',
                'code_snippet' => "#include <stdio.h>\n\nint main() {\n    // Your C code snippet here\n    // Another example C code\n    int a = 5;\n    int b = 10;\n    int sum = a + b;\n    printf('Sum: %d', sum);\n    return 0;\n}",
                'likes' => 15,
                'caption' => 'Playing with C programming!',
                'programming_language' => 'c',
                'comments' => [
                    [
                        'datetime' => '2023-11-19 09:30:00',
                        'users_id' => 1,
                        'comment' => 'Really useful code!'
                    ]
                ]
            ],
            [
                'datetime' => '2023-11-15 14:45:00',
                'code_snippet' => "#include <stdio.h>\n\nvoid main() {\n    // Your C code snippet here\n    // More C code\n    int num = 10;\n    while(num >= 0) {\n        printf('%d\\n', num);\n        num--;\n    }\n}",
                'likes' => 12,
                'caption' => 'C programming loop example',
                'programming_language' => 'c',
                'comments' => [
                    [
                        'datetime' => '2023-11-20 10:45:00',
                        'users_id' => 2,
                        'comment' => 'Impressive code!'
                    ]
                ]
            ]
        ]
    ]
];

// Wipe database before inserting new data
try {
    R::nuke();

    echo "Wiped database" . PHP_EOL;
} catch (Exception $e) {
    echo "Error while nuking" . PHP_EOL;
    echo $e->getMessage();
}

// Insert users and posts into database
try {
    // Declare count variables so stats can be displayed dynamically
    $count_users = 0;
    $count_posts = 0;

    foreach ($users as $user) {
        $db_user = R::dispense('users');

        $db_user->username = $user['username'];
        $db_user->name = $user['name'];
        $db_user->password = password_hash($user['password'], PASSWORD_DEFAULT);
        $db_user->bio = $user['bio'];
        $db_user->image = $user['image'];

        foreach ($user['posts'] as $post) {
            $db_post = R::dispense('posts');

            $db_post->datetime = $post['datetime'];
            $db_post->code_snippet = $post['code_snippet'];
            $db_post->likes = $post['likes'];
            $db_post->caption = $post['caption'];
            $db_post->programming_language = $post['programming_language'];

            $db_user->ownPostsList[] = $db_post;
            R::store($db_post);
            $count_posts++;
        }

        R::store($db_user);
        $count_users++;
    }

    // Confirmation messages
    echo "Inserted $count_users users" . PHP_EOL;
    echo "Inserted $count_posts posts" . PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage();
}

// Insert comments into database
try {
    $count_comments = 0;
    $post_id = 1;

    foreach ($users as $user) {
        foreach ($user['posts'] as $post) {
            foreach ($post['comments'] as $comment) {
                $db_user = R::load('users', $comment['users_id']);
                $db_post = R::load('posts', $post_id);
                $db_comment = R::dispense('comments');

                $db_comment->datetime = $comment['datetime'];
                $db_comment->comment = $comment['comment'];
    
                $db_user->ownCommentsList[] = $db_comment;
                $db_post->ownCommentsList[] = $db_comment;
    
                R::storeAll([$db_user, $db_post]);
                $count_comments++;
            }
            $post_id++;
        }
    }

    // Confirmation message
    echo "Inserted $count_comments comments" . PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage();
}

// Insert likes into database
try {
    $count_likes = 0;
    $user_id = 1;
    
    foreach ($users as $user) {
        foreach ($user['likes'] as $like) {
            $db_user = R::load('users', $user_id);
            $db_post = R::load('posts', $like);
            $db_like = R::dispense('likes');

            $db_user->ownLikesList[] = $db_like;
            $db_post->ownLikesList[] = $db_like;

            R::storeAll([$db_user, $db_post]);
            $count_likes++;
        }
        $user_id++;
    }

    echo "Inserted $count_likes likes" . PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage();
}
