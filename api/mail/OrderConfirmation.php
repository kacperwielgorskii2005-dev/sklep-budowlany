<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;

require_once __DIR__ . '/../../frameworks/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../frameworks/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../frameworks/PHPMailer/src/SMTP.php';

function sendOrderConfirmationEmail(string $email, string $customerName, int $orderId, array $cartItems, float $subtotal, float $delivery, float $discount, float $total, string $paymentMethod): bool
{
    $itemsHtml = '';
    $itemsText = '';

    foreach ($cartItems as $item) {
        $name = htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8');
        $quantity = (int)$item['quantity'];
        $lineTotal = (float)$item['price'] * $quantity;
        $lineTotalFormatted = number_format($lineTotal, 2, ',', ' ');

        $itemsHtml .= "<tr>
            <td style='padding:8px;border-bottom:1px solid #eee;'>{$name}</td>
            <td style='padding:8px;border-bottom:1px solid #eee;text-align:center;'>{$quantity}</td>
            <td style='padding:8px;border-bottom:1px solid #eee;text-align:right;'>{$lineTotalFormatted} zl</td>
        </tr>";
        $itemsText .= "{$item['name']} x {$quantity} - {$lineTotalFormatted} zl\n";
    }

    $orderNumber = str_pad($orderId, 6, '0', STR_PAD_LEFT);
    $safeName = htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8');
    $paymentLabels = [
        'payu' => 'PayU',
        'blik' => 'BLIK',
        'transfer' => 'Przelew',
        'card' => 'Karta'
    ];
    $paymentLabel = $paymentLabels[$paymentMethod] ?? strtoupper($paymentMethod);

    $body = "
        <div style='font-family:Arial,sans-serif;color:#222;line-height:1.5;'>
            <h2>Potwierdzenie zamowienia #{$orderNumber}</h2>
            <p>Dziekujemy za zamowienie, {$safeName}.</p>
            <p>Metoda platnosci: <strong>{$paymentLabel}</strong></p>
            <table style='width:100%;border-collapse:collapse;margin:20px 0;'>
                <thead>
                    <tr>
                        <th style='padding:8px;text-align:left;border-bottom:2px solid #FFB400;'>Produkt</th>
                        <th style='padding:8px;text-align:center;border-bottom:2px solid #FFB400;'>Ilosc</th>
                        <th style='padding:8px;text-align:right;border-bottom:2px solid #FFB400;'>Wartosc</th>
                    </tr>
                </thead>
                <tbody>{$itemsHtml}</tbody>
            </table>
            <p>Suma produktow: <strong>" . number_format($subtotal, 2, ',', ' ') . " zl</strong></p>
            <p>Dostawa: <strong>" . number_format($delivery, 2, ',', ' ') . " zl</strong></p>
            <p>Rabat: <strong>-" . number_format($discount, 2, ',', ' ') . " zl</strong></p>
            <p style='font-size:18px;'>Do zaplaty: <strong>" . number_format($total, 2, ',', ' ') . " zl</strong></p>
        </div>";

    $altBody = "Potwierdzenie zamowienia #{$orderNumber}\n\n"
        . "Klient: {$customerName}\n"
        . "Metoda platnosci: {$paymentLabel}\n\n"
        . $itemsText
        . "\nSuma produktow: " . number_format($subtotal, 2, ',', ' ') . " zl"
        . "\nDostawa: " . number_format($delivery, 2, ',', ' ') . " zl"
        . "\nRabat: -" . number_format($discount, 2, ',', ' ') . " zl"
        . "\nDo zaplaty: " . number_format($total, 2, ',', ' ') . " zl";

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'specmarketinfo@gmail.com';
        $mail->Password = 'vxba kvld ugau epfr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        $mail->setFrom('specmarketinfo@gmail.com', 'SPEC.market');
        $mail->addAddress($email, $customerName);
        $mail->isHTML(true);
        $mail->Subject = "Potwierdzenie zamowienia #{$orderNumber}";
        $mail->Body = $body;
        $mail->AltBody = $altBody;

        $mail->send();
        return true;
    } catch (MailerException $e) {
        error_log('Order confirmation mail error: ' . $mail->ErrorInfo);
        return false;
    }
}

?>
