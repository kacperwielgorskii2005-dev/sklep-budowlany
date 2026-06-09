<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

ini_set('memory_limit', '256M');

header('Content-Type: application/json; charset=utf-8');
ob_start();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Nieprawidłowa metoda żądania');
    }

    require_once('../../api/database.php');
    
    if (!isset($conn)) {
        $conn = $db;
    }

    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? null;
    $price = $_POST['price'] ?? null;
    $stock = $_POST['stock'] ?? null;

    if (!$id || !$name || $price === null || $stock === null) {
        throw new Exception('Brak wymaganych danych');
    }

    $stmt = $conn->prepare("SELECT image_url FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_product = $result->fetch_assoc();
    $stmt->close();

    if (!$current_product) {
        throw new Exception('Produkt nie został znaleziony');
    }

    $old_image_url = $current_product['image_url'];
    $new_image_url = $old_image_url;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../src/img/products/';
        
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                throw new Exception('Nie można utworzyć katalogu dla obrazów');
            }
        }

        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        $file_extension_check = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array($file_type, $allowed_types) && !in_array($file_extension_check, $allowed_extensions)) {
            throw new Exception('Nieprawidłowy format pliku. Dozwolone: JPG, PNG, GIF');
        }

        if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            throw new Exception('Plik jest za duży. Maksymalny rozmiar: 5MB');
        }

        $random_name = bin2hex(random_bytes(30));
        $file_extension = 'jpg';
        $new_filename = $random_name . '.' . $file_extension;
        $target_path = $upload_dir . $new_filename;

        $source_image = null;
        
        $image_info = @getimagesize($_FILES['image']['tmp_name']);
        if ($image_info === false) {
            throw new Exception('Nie można odczytać informacji o obrazie');
        }
        
        switch ($image_info[2]) {
            case IMAGETYPE_JPEG:
                $source_image = @imagecreatefromjpeg($_FILES['image']['tmp_name']);
                break;
            case IMAGETYPE_PNG:
                $source_image = @imagecreatefrompng($_FILES['image']['tmp_name']);
                break;
            case IMAGETYPE_GIF:
                $source_image = @imagecreatefromgif($_FILES['image']['tmp_name']);
                break;
            default:
                throw new Exception('Nieobsługiwany typ obrazu');
        }

        if ($source_image === false || $source_image === null) {
            throw new Exception('Nie można przetworzyć obrazu');
        }

        if (!@imagejpeg($source_image, $target_path, 85)) {
            imagedestroy($source_image);
            throw new Exception('Nie można zapisać obrazu');
        }

        imagedestroy($source_image);

        $new_image_url = '../../src/img/products/' . $new_filename;

        if ($old_image_url && $old_image_url !== $new_image_url) {
            $old_file_path = __DIR__ . '/../../src/img/products/' . basename($old_image_url);
            if (file_exists($old_file_path) && is_file($old_file_path)) {
                @unlink($old_file_path);
            }
        }
    }

    $stmt = $conn->prepare("UPDATE products SET product_name = ?, price = ?, stock_quantity = ?, image_url = ? WHERE product_id = ?");
    $stmt->bind_param("sdisi", $name, $price, $stock, $new_image_url, $id);

    if (!$stmt->execute()) {
        if ($new_image_url !== $old_image_url && isset($target_path) && file_exists($target_path)) {
            @unlink($target_path);
        }
        throw new Exception('Błąd podczas aktualizacji: ' . $conn->error);
    }

    $response = [
        'success' => true,
        'message' => 'Produkt został zaktualizowany',
        'image_url' => $new_image_url
    ];

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    error_log("Product update error: " . $e->getMessage());
    
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

ob_end_clean();
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
?>