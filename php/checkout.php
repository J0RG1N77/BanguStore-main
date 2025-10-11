<?php
// checkout.php - recebe dados do checkout, gera nota fiscal PDF fictícia e envia por email
session_start();

// Apenas aceitar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../carrinho.html');
    exit;
}

$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$card_name = trim($_POST['card_name'] ?? '');
$card_number = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
$expiry = trim($_POST['expiry'] ?? '');
$cvv = preg_replace('/\D/', '', $_POST['cvv'] ?? '');
$cart_json = $_POST['cart_data'] ?? '[]';

if (!$email) {
    $_SESSION['error'] = 'E-mail inválido.';
    header('Location: ../carrinho.html');
    exit;
}

$cart = json_decode($cart_json, true);
if (!is_array($cart)) $cart = [];

// Gerar conteúdo da nota fiscal (fictícia)
$invoice_no = 'NF-' . date('YmdHis') . '-' . rand(1000,9999);
$date = date('d/m/Y H:i:s');
$lines = [];
$lines[] = "Bangu Store - Nota Fiscal (fictícia)";
$lines[] = "Número: $invoice_no";
$lines[] = "Data: $date";
$lines[] = "Cliente (email): $email";
$lines[] = "";
$lines[] = "Itens:";
$total = 0.0;
foreach ($cart as $item) {
    $name = $item['name'] ?? ($item['titulo'] ?? 'Produto');
    $qty = isset($item['qty']) ? (int)$item['qty'] : 1;
    $price = isset($item['price']) ? floatval($item['price']) : (isset($item['preco']) ? floatval($item['preco']) : 0);
    $subtotal = $qty * $price;
    $total += $subtotal;
    $lines[] = sprintf("- %s x%d - R$ %.2f", $name, $qty, $subtotal);
}
$lines[] = "";
$lines[] = sprintf("Total: R$ %.2f", $total);
$lines[] = "";
$lines[] = "Pagamento (fictício):";
$masked_card = '**** **** **** ' . substr($card_number, -4);
$lines[] = "Titular: $card_name";
$lines[] = "Cartão: $masked_card";
$lines[] = "Validade: $expiry";
$lines[] = "";
$lines[] = "Obrigado pela sua compra!";

// Criar diretório de invoices
$invoicesDir = __DIR__ . '/invoices';
if (!is_dir($invoicesDir)) mkdir($invoicesDir, 0755, true);

$pdfPath = $invoicesDir . '/invoice_' . $invoice_no . '.pdf';

// Função mínima para criar um PDF simples com texto (usa fontes padrão)
function create_simple_pdf(array $lines, string $filePath)
{
    // Conteúdo do stream
    $content = "BT\n/F1 12 Tf\n50 800 Td\n";
    foreach ($lines as $i => $line) {
        // escapar parênteses e barras
        $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $line);
        $content .= '(' . $escaped . ') Tj\n0 -14 Td\n';
    }
    $content .= "ET\n";

    $stream = $content;
    $len = strlen($stream);

    // Montar PDF básico
    $objects = [];

    // 1 catalog
    $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
    // 2 pages
    $objects[] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
    // 3 page
    $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 4 0 R >> >> /MediaBox [0 0 595 842] /Contents 5 0 R >>\nendobj\n";
    // 4 font
    $objects[] = "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
    // 5 contents
    $objects[] = "5 0 obj\n<< /Length $len >>\nstream\n$stream\nendstream\nendobj\n";

    $pdf = "%PDF-1.4\n%âãÏÓ\n";
    $offsets = [];
    $pos = strlen($pdf);
    foreach ($objects as $obj) {
        $offsets[] = $pos;
        $pdf .= $obj;
        $pos = strlen($pdf);
    }

    // xref
    $xrefPos = strlen($pdf);
    $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";
    foreach ($offsets as $off) {
        $pdf .= sprintf('%010d 00000 n \n', $off);
    }

    // trailer
    $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n$xrefPos\n%%EOF\n";

    file_put_contents($filePath, $pdf);
}

create_simple_pdf($lines, $pdfPath);

// Enviar email com anexo (usa mail()). Se não estiver configurado localmente, pode não entregar.
$to = $email;
$subject = "Sua Nota Fiscal - Bangu Store";
$messageBody = "Olá,\n\nAnexo segue a nota fiscal (fictícia) da sua compra.\n\nAtenciosamente,\nBangu Store";

$separator = md5(time());
$eol = "\r\n";

$filename = basename($pdfPath);
$filedata = file_get_contents($pdfPath);
$attachment = chunk_split(base64_encode($filedata));

$headers = [];
$headers[] = "From: Bangu Store <no-reply@localhost>";
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: multipart/mixed; boundary=\"$separator\"";

// Message
$body = "--$separator" . $eol;
$body .= "Content-Type: text/plain; charset=iso-8859-1" . $eol;
$body .= "Content-Transfer-Encoding: 7bit" . $eol . $eol;
$body .= $messageBody . $eol;

// Attachment
$body .= "--$separator" . $eol;
$body .= "Content-Type: application/pdf; name=\"$filename\"" . $eol;
$body .= "Content-Transfer-Encoding: base64" . $eol;
$body .= "Content-Disposition: attachment; filename=\"$filename\"" . $eol . $eol;
$body .= $attachment . $eol;
$body .= "--$separator--" . $eol;

$sent = @mail($to, $subject, $body, implode($eol, $headers));

if ($sent) {
    $_SESSION['success'] = 'Compra finalizada. A nota fiscal foi enviada para ' . $email;
} else {
    $_SESSION['error'] = 'Compra finalizada, mas falha ao enviar o e-mail (verifique configuração de mail). A nota está salva em: ' . $pdfPath;
}

header('Location: ../index.html');
exit;
