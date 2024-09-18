<?php

namespace Repository;

use Model\Product;
use PDO;
use PDOException;

class ProductRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function productInstance($data)
    {
        return new Product($data['id'],
            $data['tipo'],
            $data['nome'],
            $data['descricao'],
            $data['preco'],
            $data['imagem'],
        );
    }

    public function getBreakfastMenu():array
    {
        $sqlCoffee = 'SELECT * FROM produtos where tipo = "Café" order by preco';
        $statement = $this->pdo->query($sqlCoffee);
        $coffeeMenus = $statement->fetchAll(PDO::FETCH_ASSOC);

        $breakfast = array_map(function ($coffee) {
            return $this->productInstance($coffee);
        }, $coffeeMenus);

        return $breakfast;
    }

    public function getLunchMenu():array
    {
        $sqlLunch = 'SELECT * FROM produtos where tipo = "Almoço" order by preco';
        $statement = $this->pdo->query($sqlLunch);
        $lunchMenus = $statement->fetchAll(PDO::FETCH_ASSOC);

        $lunch = array_map(function ($lunch) {
            return $this->productInstance($lunch);
        }, $lunchMenus);

        return $lunch;
    }

    public function registerProduct(Product $product)
    {
        $sql = 'INSERT INTO produtos (tipo, nome, descricao, preco, imagem) VALUES (?,?,?,?,?)';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $product->getTipo());
        $statement->bindValue(2, $product->getNome());
        $statement->bindValue(3, $product->getDescricao());
        $statement->bindValue(4, $product->getPreco());
        $statement->bindValue(5, $product->getImagem());
        $statement->execute();
    }

    public function searchAllProducts():array
    {
        $sqlProducts = 'SELECT * FROM produtos order by preco';
        $statement = $this->pdo->query($sqlProducts);
        $products = $statement->fetchAll(PDO::FETCH_ASSOC);

        $allProducts = array_map(function ($product) {
            return $this->productInstance($product);
        }, $products);

        return $allProducts;
    }

    public function searchProduct(int $id)
    {
        $sqlProduct = 'SELECT * FROM produtos WHERE id = ?';
        $statement = $this->pdo->prepare($sqlProduct);
        $statement->bindValue(1, $id);
        $statement->execute();

        $product = $statement->fetch(PDO::FETCH_ASSOC);

        return $this->productInstance($product);

    }

    public function updateProduct(Product $product)
    {
        $sqlProduct = 'UPDATE produtos SET tipo = ?, nome = ?, descricao = ?, preco = ?, imagem = ? WHERE id = ?';
        $statement = $this->pdo->prepare($sqlProduct);
        $statement->bindValue(1, $product->getTipo());
        $statement->bindValue(2, $product->getNome());
        $statement->bindValue(3, $product->getDescricao());
        $statement->bindValue(4, $product->getPreco());
        $statement->bindValue(6, $product->getId());
        $statement->execute();

        if($product->getImagem() !== 'logo-serenatto.png'){
            $this->updatePhoto($product);
        }
    }

    private function updatePhoto(Product $product)
    {
        $sql = 'UPDATE produtos SET imagem = ? WHERE id = ?';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(1, $product->getImagem());
        $statement->bindValue(2, $product->getId());
        $statement->execute();
    }

    public function removeProduct(int $id)
    {
        $sqlProducts = 'DELETE FROM produtos WHERE id = ?';
        $statement = $this->pdo->prepare($sqlProducts);
        $statement->bindValue(1, $id);

        $statement->execute();
    }

}