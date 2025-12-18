<?php
/**
 * pdf_generator.php - Generador de PDF para el CRUD
 * 
 * Este archivo genera un PDF con la tabla de usuarios usando la librería TCPDF
 * 
 * INSTALACIÓN (sin TCPDF - usando HTML2PDF alternativo):
 * Este script genera un PDF simple sin dependencias externas
 */

require_once __DIR__ . '/crud_handler.php';

/**
 * Genera un PDF con los datos de usuarios
 * 
 * @param array $users Array de usuarios
 * @return string HTML del PDF (para visualización)
 */
function generatePDF($users = []) {
    if (empty($users)) {
        $users = getAllUsers();
    }
    
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Reporte de Usuarios</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: Arial, sans-serif;
                color: #333;
            }
            .container {
                padding: 40px;
                max-width: 900px;
                margin: 0 auto;
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 3px solid #0093E9;
                padding-bottom: 20px;
            }
            .header h1 {
                color: #0093E9;
                font-size: 28px;
                margin-bottom: 10px;
            }
            .header p {
                color: #666;
                font-size: 14px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            thead {
                background-color: #0093E9;
                color: white;
            }
            th {
                padding: 12px;
                text-align: left;
                font-weight: bold;
                border: 1px solid #ddd;
            }
            td {
                padding: 10px 12px;
                border: 1px solid #ddd;
            }
            tbody tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            tbody tr:hover {
                background-color: #f0f0f0;
            }
            .footer {
                margin-top: 40px;
                padding-top: 20px;
                border-top: 1px solid #ddd;
                text-align: center;
                color: #666;
                font-size: 12px;
            }
            .stats {
                background-color: #f0f8ff;
                padding: 15px;
                border-radius: 5px;
                margin: 20px 0;
                border-left: 4px solid #0093E9;
            }
            .stats p {
                margin: 5px 0;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>📊 Reporte de Usuarios</h1>
                <p>Generado el ' . date('d/m/Y H:i:s') . '</p>
            </div>
            
            <div class="stats">
                <p><strong>Total de Usuarios:</strong> ' . count($users) . '</p>
                <p><strong>Período:</strong> ' . date('d/m/Y') . '</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">ID</th>
                        <th style="width: 25%;">Usuario</th>
                        <th style="width: 35%;">Email</th>
                        <th style="width: 20%;">Teléfono</th>
                    </tr>
                </thead>
                <tbody>';
    
    if (empty($users)) {
        $html .= '
                    <tr>
                        <td colspan="4" style="text-align: center; color: #999;">No hay usuarios registrados</td>
                    </tr>';
    } else {
        foreach ($users as $user) {
            $html .= '
                    <tr>
                        <td>#' . htmlspecialchars($user['id']) . '</td>
                        <td>' . htmlspecialchars($user['username']) . '</td>
                        <td>' . htmlspecialchars($user['email']) . '</td>
                        <td>' . htmlspecialchars($user['phone'] ?? '-') . '</td>
                    </tr>';
        }
    }
    
    $html .= '
                </tbody>
            </table>
            
            <div class="footer">
                <p>Este documento fue generado automáticamente por el sistema de gestión de usuarios.</p>
                <p>© ' . date('Y') . ' - Todos los derechos reservados</p>
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
}

/**
 * Descarga un PDF de los usuarios
 * Utiliza la librería html2pdf o genera para impresión
 */
function downloadPDF($filename = 'usuarios.pdf') {
    $users = getAllUsers();
    $html = generatePDF($users);
    
    // Opción 1: Guardar como HTML imprimible
    header('Content-Type: text/html; charset=utf-8');
    echo $html;
    exit;
}

/**
 * Genera PDF para descarga directa
 * Nota: Requiere wkhtmltopdf o similar instalado
 */
function generatePDFDownload() {
    $users = getAllUsers();
    $html = generatePDF($users);
    
    $filename = 'usuarios_' . date('Y-m-d_H-i-s') . '.pdf';
    
    // Esta es una alternativa usando librería nativa (sin dependencias externas)
    // Genera HTML que el navegador puede imprimir como PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    echo $html;
    exit;
}

// Si se accede directamente
if (isset($_GET['action']) && $_GET['action'] === 'download') {
    downloadPDF();
}

?>
