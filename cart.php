<?php
session_start();
require __DIR__ . '/config.php';

// Redirection si utilisateur non connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?message=connexion_requise');
    exit();
}

// Récupération des produits du panier
$cart = $_SESSION['cart'] ?? [];
$items = [];
$total = 0;

foreach ($cart as $pid => $qty) {
    $stmt = $pdo->prepare("SELECT name, price, image FROM products WHERE id = ?");
    $stmt->execute([$pid]);
    $product = $stmt->fetch();

    if ($product) {
        $subtotal = $product['price'] * $qty;
        $items[] = [
            'id' => $pid,
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $qty,
            'subtotal' => $subtotal
        ];
        $total += $subtotal;
    }
}

include __DIR__ . '/templates/partials/head.php';
include __DIR__ . '/templates/partials/header.php';
?>

<main class="container">
    <h1 style="text-align:center; margin-bottom: 30px;">Mon Panier</h1>

    <?php if (empty($items)): ?>
        <p style="text-align:center; font-size:18px;">Votre panier est vide.</p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Qté</th>
                    <th>Sous-total</th>
                    <th>Modifier</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td style="display:flex; align-items:center; gap:10px;">
                            <img src="assets/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width:60px; height:auto; border-radius:4px;">
                            <?= htmlspecialchars($item['name']) ?>
                        </td>
                        <td><?= $item['quantity'] ?></td>
                        <td>€<?= number_format($item['subtotal'], 2, ',', ' ') ?></td>
                        <td>
                            <form method="POST" action="update_cart.php">
                                <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1">
                                <button type="submit" class="btn-outline">Modifier</button>
                            </form>
                        </td>
                        <td>
                            <a href="remove_from_cart.php?product_id=<?= $item['id'] ?>"
                               class="btn-outline" style="color:red;"
                               onclick="return confirm('Voulez-vous vraiment supprimer ce produit du panier ?');">
                               Supprimer
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"><strong>Total</strong></td>
                    <td><strong>€<?= number_format($total, 2, ',', ' ') ?></strong></td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
        <div style="text-align: center; margin-top: 20px;">
            <a href="checkout.php" class="btn">Passer la commande</a>
        </div>
    <?php endif; ?>
</main>

<?php include __DIR__ . '/templates/partials/footer.php'; ?>
