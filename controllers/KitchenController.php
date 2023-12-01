<?php

namespace App\Controllers;

class KitchenController extends BaseController
{
    public function index(): string
    {
        $kitchens = \R::getAll('SELECT kitchen_id AS id, name, description FROM kitchens;');

        return displayTemplate('kitchens/index.twig', [
            'kitchens' => $kitchens
        ]);
    }

    public function show($id): string
    {
        $kitchen = \R::getRow("SELECT kitchen_id, name, description FROM kitchens WHERE kitchen_id = :id;", [
            'id' => $id
        ]);

        $recipes = \R::getAll("SELECT * FROM recipes WHERE kitchens_id = :id;", [
            'id' => $kitchen['kitchen_id']
        ]);
        
        if (empty($kitchen)) {
            error(404, 'No kitchen with ID ' . $_GET['id'] . ' found');
        } else {
            return displayTemplate('kitchens/show.twig', [
                'kitchen' => $kitchen,
                'recipes' => $recipes
            ]);
        }
    }

    public function create(): string
    {
        $this->authorizeUser();
        return displayTemplate('kitchens/create.twig', []);
    }

    public function createPost(): void
    {
        $this->authorizeUser();

        $db = \R::dispense('kitchens');

        $new_id = \R::getCell('SELECT MAX(kitchen_id) FROM kitchens;') + 1;
        $db->kitchen_id = $new_id;
        $db->name = $_POST['name'];
        $db->description = $_POST['description'];

        \R::store($db);

        header("Location:/kitchen/show?id=" . $new_id);
    }

    public function edit($id): string
    {
        $this->authorizeUser();
        
        $kitchen = \R::getRow('SELECT kitchen_id, name, description FROM kitchens WHERE kitchen_id = :id;', [
            ':id' => $id
        ]);
        
        return displayTemplate('kitchens/edit.twig', [
            'kitchen' => $kitchen
        ]);
    }

    public function editPost(): void
    {
        $this->authorizeUser();
        
        $db = \R::findOne('kitchens', 'kitchen_id = :kitchen_id', [
            ':kitchen_id' => $_GET['id']
        ]);

        $db->name = $_POST['name'];
        $db->description = $_POST['description'];

        \R::store($db);

        header("Location:/kitchen/show?id=" . $_GET['id']);
    }
}
