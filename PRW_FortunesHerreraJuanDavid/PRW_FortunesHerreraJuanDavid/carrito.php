<?php
session_start();
require_once 'includes/Auth.php';
require_once 'includes/Cart.php';
require_once 'includes/UserPreferences.php';

$auth = new Auth();
$preferences = UserPreferences::getInstance();

if (!$auth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$cart = new Cart($_SESSION['user_id']);
$mensaje = '';
$error = '';

// Procesar acciones del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update':
                $producto_id = (int)$_POST['producto_id'];
                $cantidad = (int)$_POST['cantidad'];
                if ($cart->updateQuantity($producto_id, $cantidad)) {
                    $mensaje = 'Cantidad actualizada correctamente';
                } else {
                    $error = 'Error al actualizar la cantidad';
                }
                break;

            case 'remove':
                $producto_id = (int)$_POST['producto_id'];
                if ($cart->removeItem($producto_id)) {
                    $mensaje = 'Producto eliminado del carrito';
                } else {
                    $error = 'Error al eliminar el producto';
                }
                break;

            case 'clear':
                if ($cart->clear()) {
                    $mensaje = 'Carrito vaciado correctamente';
                } else {
                    $error = 'Error al vaciar el carrito';
                }
                break;
        }
    }
}

$items = $cart->getItems();
$total = $cart->getTotal();
?>

<!DOCTYPE html>
<html lang="<?= $preferences->getLanguage() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $preferences->translate('cart_title') ?> - Mercatto Revalia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
    <?= $preferences->getThemeCSS() ?>
    .navbar {
        padding: 0;
        flex-direction: column;
    }
    .navbar-top {
        width: 100%;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .navbar-bottom {
        width: 100%;
        padding: 0.8rem 0;
    }
    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1.6rem;
        margin-right: 2rem;
        padding: 0;
    }
    .nav-link {
        display: flex !important;
        align-items: center;
        gap: 8px;
        padding: 0.5rem 1rem;
    }
    .material-icons {
        font-size: 24px;
    }
    .btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 0.5rem 1.2rem;
        font-size: 0.95rem;
    }
    .btn .material-icons {
        font-size: 20px;
    }
    .navbar-nav {
        gap: 0.75rem;
    }
    body {
        padding-top: 120px;
    }
    .main-container {
        padding-top: 2rem;
    }
    @media (max-width: 991.98px) {
        .navbar-collapse {
            padding: 1rem 0;
        }
        .navbar-nav {
            gap: 0.5rem;
        }
        .d-flex {
            gap: 0.5rem;
        }
    }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <!-- Top Row -->
        <div class="navbar-top">
            <div class="container d-flex align-items-center">
                <a class="navbar-brand" href="index.php">
                    <span class="material-icons">storefront</span>
                    Mercatto Revalia
                </a>
                <div class="d-flex align-items-center gap-3 ms-auto">
                    <!-- Selector de tema -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <span class="material-icons"><?= $preferences->getTheme() === 'dark' ? 'dark_mode' : 'light_mode' ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="update_preferences.php" method="POST">
                                    <input type="hidden" name="theme" value="light">
                                    <button type="submit" class="dropdown-item">
                                        <span class="material-icons">light_mode</span>
                                        <?= $preferences->translate('theme_light') ?>
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form action="update_preferences.php" method="POST">
                                    <input type="hidden" name="theme" value="dark">
                                    <button type="submit" class="dropdown-item">
                                        <span class="material-icons">dark_mode</span>
                                        <?= $preferences->translate('theme_dark') ?>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Selector de idioma -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <span class="material-icons">language</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form action="update_preferences.php" method="POST">
                                    <input type="hidden" name="lang" value="es">
                                    <button type="submit" class="dropdown-item">
                                        <?= $preferences->translate('lang_es') ?>
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form action="update_preferences.php" method="POST">
                                    <input type="hidden" name="lang" value="en">
                                    <button type="submit" class="dropdown-item">
                                        <?= $preferences->translate('lang_en') ?>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row -->
        <div class="navbar-bottom">
            <div class="container">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <div class="d-flex align-items-center gap-3 me-auto">
                        <a class="btn btn-outline-light" href="mis-productos.php">
                            <span class="material-icons">inventory_2</span>
                            <?= $preferences->translate('nav_my_products') ?>
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <a href="crear-producto.php" class="btn btn-success">
                            <span class="material-icons">add_circle</span>
                            <?= $preferences->translate('nav_publish') ?>
                        </a>
                        <a href="favoritos.php" class="btn btn-outline-light">
                            <span class="material-icons">favorite</span>
                            <?= $preferences->translate('nav_favorites') ?>
                        </a>
                        <a href="perfil.php" class="btn btn-outline-light">
                            <span class="material-icons">person</span>
                            <?= $preferences->translate('nav_profile') ?>
                        </a>
                        <a href="logout.php" class="btn btn-outline-light">
                            <span class="material-icons">logout</span>
                            <?= $preferences->translate('nav_logout') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container main-container">
        <h1><?= $preferences->translate('cart_title') ?></h1>

        <?php if ($mensaje): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($items)): ?>
            <div class="alert alert-info">
                Tu carrito está vacío. <a href="index.php">Ir a comprar</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <a href="producto.php?id=<?= $item['id_producto'] ?>">
                                        <?= htmlspecialchars($item['nombre']) ?>
                                    </a>
                                </td>
                                <td>€<?= number_format($item['precio'], 2) ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="producto_id" value="<?= $item['id_producto'] ?>">
                                        <input type="number" name="cantidad" value="<?= $item['cantidad'] ?>" 
                                               min="1" max="<?= $item['stock'] ?>" 
                                               class="form-control form-control-sm" style="width: 80px"
                                               onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td>€<?= number_format($item['precio'] * $item['cantidad'], 2) ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="producto_id" value="<?= $item['id_producto'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td><strong>€<?= number_format($total, 2) ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <form method="POST">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" class="btn btn-warning">Vaciar Carrito</button>
                    </form>
                </div>
                <div class="col-md-6 text-end">
                    <a href="checkout.php" class="btn btn-success">Proceder al Pago</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 