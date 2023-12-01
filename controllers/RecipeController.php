<?php

namespace App\Controllers;

class RecipeController extends BaseController
{
    protected $types = ['breakfast', 'lunch', 'dinner'];
    protected $levels = ['easy', 'medium', 'hard'];

    public function index(): string
    {
        $recipes = \R::getAll('SELECT recipe_id AS id, name, type, level FROM recipes;');
        
        return displayTemplate('recipes/index.twig', [
            'recipes' => $recipes
        ]);
    }

    public function show($id): string
    {
        $recipe = \R::getRow("SELECT * FROM recipes WHERE recipe_id = :id;", [
            ':id' => $id
        ]);

        $kitchen = \R::getRow("SELECT kitchen_id, name FROM kitchens WHERE kitchen_id = :id", [
            ':id' => $recipe['kitchens_id']
        ]);

        if (empty($recipe)) {
            return error(404, 'No recipe with ID ' . $_GET['id'] . ' found');
        } else {
            return displayTemplate('recipes/show.twig', [
                'recipe' => $recipe,
                'kitchen' => $kitchen
            ]);
        }
    }

    public function create(): string
    {
        $this->authorizeUser();
        
        $kitchens = \R::getAll('SELECT id, name FROM kitchens');
        
        return displayTemplate('recipes/create.twig', [
            'kitchens' => $kitchens,
            'types' => $this->types,
            'levels' => $this->levels
        ]);
    }

    public function createPost(): void
    {
        $this->authorizeUser();
        
        $db = \R::dispense('recipes');

        $new_id = \R::getCell('SELECT MAX(recipe_id) FROM recipes;') + 1;
        $db->recipe_id = $new_id;
        $db->name = $_POST['name'];
        $db->type = $_POST['type'];
        $db-> level = $_POST['level'];
        $db->kitchens_id = $_POST['kitchen'];

        \R::store($db);

        header("Location:/kitchen/show?id=" . $_POST['kitchen']);
    }

    public function edit(): string
    {
        $this->authorizeUser();
        
        $recipe = \R::findOne('recipes', 'recipe_id = :recipe_id', [
            'recipe_id' => $_GET['id']
        ]);

        $kitchens = \R::getAll('SELECT id, name FROM kitchens');
        
        return displayTemplate('recipes/edit.twig', [
            'recipe' => $recipe,
            'kitchens' => $kitchens,
            'types' => $this->types,
            'levels' => $this->levels
        ]);
    }

    public function editPost(): void
    {
        $this->authorizeUser();
        
        $db = \R::findOne('recipes', 'recipe_id = :recipe_id', [
            'recipe_id' => $_GET['id']
        ]);

        $db->name = $_POST['name'];
        $db->type = $_POST['type'];
        $db->level = $_POST['level'];
        $db->kitchens_id = $_POST['kitchen'];

        \R::store($db);
        
        header('Location: /recipe/show?id=' . $_GET['id']);
    }
}
