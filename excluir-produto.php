<?php

use Repository\ProductRepository;

require 'src/db-connection.php';
require 'src/Model/Product.php';
require 'src/Repository/ProductRepository.php';

$productRepository = new ProductRepository($pdo);
$productRepository->removeProduct($_POST['id']);

header('Location: admin.php');