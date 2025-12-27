<?php
// tdc_export_pdf.php - Exportação de registos TDC para PDF
require 'config.php';

// Obter ID do registo
$id_tdc = intval($_GET['id'] ?? 0);
if (!$id_tdc) {
    die('Registo não encontrado.');
}

// Buscar dados do registo
$stmt = $mysqli->prepare('SELECT * FROM tdc_records WHERE id_tdc = ?');
$stmt->bind_param('i', $id_tdc);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

if (!$record) {
    die('Registo não encontrado.');
}

// Função auxiliar para exibir valores
if (!function_exists('esc')) {
    function esc($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }
}

function get_sim_nao($value) {
    return ($value === 1 || $value === '1' || $value === true) ? 'Sim' : 'Não';
}

// Buscar dados relacionados
$stmt = $mysqli->prepare('SELECT * FROM tdc_airway WHERE id_tdc = ?');
$stmt->bind_param('i', $id_tdc);
$stmt->execute();
$airway = $stmt->get_result()->fetch_assoc();

$stmt = $mysqli->prepare('SELECT * FROM tdc_ventilation WHERE id_tdc = ?');
$stmt->bind_param('i', $id_tdc);
$stmt->execute();
$ventilation = $stmt->get_result()->fetch_assoc();

$stmt = $mysqli->prepare('SELECT * FROM tdc_circulation WHERE id_tdc = ?');
$stmt->bind_param('i', $id_tdc);
$stmt->execute();
$circulation = $stmt->get_result()->fetch_assoc();

$stmt = $mysqli->prepare('SELECT * FROM tdc_neurological WHERE id_tdc = ?');
$stmt->bind_param('i', $id_tdc);
$stmt->execute();
$neurological = $stmt->get_result()->fetch_assoc();

$stmt = $mysqli->prepare('SELECT * FROM tdc_exposure WHERE id_tdc = ?');
$stmt->bind_param('i', $id_tdc);
$stmt->execute();
$exposure = $stmt->get_result()->fetch_assoc();

$stmt = $mysqli->prepare('SELECT * FROM tdc_monitoring WHERE id_tdc = ? ORDER BY created_at');
$stmt->bind_param('i', $id_tdc);
$stmt->execute();
$monitoring_result = $stmt->get_result();
$monitoring_data = [];
while ($row = $monitoring_result->fetch_assoc()) {
    $monitoring_data[] = $row;
}

$stmt = $mysqli->prepare('SELECT * FROM tdc_perfusions WHERE id_tdc = ? ORDER BY created_at');
$stmt->bind_param('i', $id_tdc);
$stmt->execute();
$perfusions = $stmt->get_result();

$stmt = $mysqli->prepare('SELECT * FROM tdc_farmacos WHERE id_tdc = ? ORDER BY created_at');
$stmt->bind_param('i', $id_tdc);
$stmt->execute();
$farmacos = $stmt->get_result();

$stmt = $mysqli->prepare('SELECT * FROM tdc_intercurrencies WHERE id_tdc = ? ORDER BY created_at');
$stmt->bind_param('i', $id_tdc);
$stmt->execute();
$intercurrencies = $stmt->get_result();

$stmt = $mysqli->prepare('SELECT * FROM tdc_team WHERE id_tdc = ?');
$stmt->bind_param('i', $id_tdc);
$stmt->execute();
$team = $stmt->get_result()->fetch_assoc();

?><!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exportação TDC - Registo <?php echo $id_tdc; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: white;
            color: #333;
            line-height: 1.5;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 15mm;
            background: white;
        }

        .header {
            border-bottom: 3px solid #003366;
            padding-bottom: 10px;
            margin-bottom: 15px;
            text-align: center;
        }

        .header h1 {
            font-size: 18px;
            color: #003366;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            color: #666;
            margin: 2px 0;
        }

        .admin-info {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
            font-size: 11px;
            background: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
        }

        .info-box {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-weight: bold;
            color: #003366;
            font-size: 10px;
            text-transform: uppercase;
        }

        .info-value {
            color: #333;
            margin-top: 3px;
        }

        .section {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .section-title {
            background: #003366;
            color: white;
            padding: 8px 10px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            border-radius: 3px;
        }

        .section-content {
            padding: 8px 10px;
            background: #fafafa;
            border-left: 4px solid #003366;
            border-radius: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 10px;
        }

        thead {
            background: #003366;
            color: white;
        }

        th {
            padding: 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #003366;
        }

        td {
            padding: 4px 6px;
            border: 1px solid #ddd;
        }

        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .print-button {
            display: block;
            margin: 10px auto;
            padding: 10px 20px;
            background: #003366;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        @media print {
            body { margin: 0; padding: 0; }
            .container { box-shadow: none; max-width: 100%; margin: 0; padding: 15mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (file_exists(__DIR__ . '/header_inc.php')) include 'header_inc.php'; ?>
        <!-- Cabeçalho -->
        <div class="header">
            <h1>REGISTO DE ENFERMAGEM - TRANSPORTE DE DOENTE CRÍTICO (TDC)</h1>
            <p style="color:var(--muted);">Sistema de Documentação de Transporte de Doente Crítico</p>
            <p>Registo nº <?php echo $id_tdc; ?> | Data: <?php echo esc($record['created_at']); ?></p>
        </div>

        <!-- Informações Administrativas -->
        <div class="section">
            <div class="section-title">📋 INFORMAÇÕES ADMINISTRATIVAS</div>
            <div class="admin-info">
                <div class="info-box">
                    <span class="info-label">Motivo do Transporte</span>
                    <span class="info-value"><?php echo esc($record['motivo_transporte']); ?></span>
                </div>
                <div class="info-box">
                    <span class="info-label">Serviço de Destino</span>
                    <span class="info-value"><?php echo esc($record['servico_destino']); ?></span>
                </div>
                <div class="info-box">
                    <span class="info-label">Hora Ativação</span>
                    <span class="info-value"><?php echo esc($record['hora_ativacao']); ?></span>
                </div>
                <div class="info-box">
                    <span class="info-label">Hora Saída ULSCB</span>
                    <span class="info-value"><?php echo esc($record['hora_saida_ulscb']); ?></span>
                </div>
                <div class="info-box">
                    <span class="info-label">Hora Chegada SD</span>
                    <span class="info-value"><?php echo esc($record['hora_chegada_sd']); ?></span>
                </div>
                <div class="info-box">
                    <span class="info-label">Score TDC</span>
                    <span class="info-value"><?php echo esc($record['score_tdc']); ?></span>
                </div>
            </div>
        </div>

        <!-- Monitorização -->
        <?php if (!empty($monitoring_data)): ?>
        <div class="section">
            <div class="section-title">📊 MONITORIZAÇÃO DE SINAIS VITAIS</div>
            <div class="section-content">
                <table>
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Temp (°C)</th>
                            <th>FC (/min)</th>
                            <th>PA (mmHg)</th>
                            <th>FR (/min)</th>
                            <th>SpO₂ (%)</th>
                            <th>Glicemia (mg/dL)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($monitoring_data as $mon): ?>
                        <tr>
                            <td><?php echo esc($mon['hora'] ?? ''); ?></td>
                            <td><?php echo esc($mon['temperatura'] ?? ''); ?></td>
                            <td><?php echo esc($mon['frequencia_cardiaca'] ?? ''); ?></td>
                            <td><?php echo esc($mon['pressao_arterial'] ?? ''); ?></td>
                            <td><?php echo esc($mon['frequencia_respiratoria'] ?? ''); ?></td>
                            <td><?php echo esc($mon['spo2'] ?? ''); ?></td>
                            <td><?php echo esc($mon['glicemia'] ?? ''); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Perfusões -->
        <?php if ($perfusions->num_rows > 0): 
            $perfusions->data_seek(0);
        ?>
        <div class="section">
            <div class="section-title">💉 TERAPÊUTICA - PERFUSÕES INTRAVENOSAS</div>
            <div class="section-content">
                <table>
                    <thead>
                        <tr>
                            <th>Fármaco</th>
                            <th>Posologia</th>
                            <th>Hora Início</th>
                            <th>Taxa 1</th>
                            <th>Taxa 2</th>
                            <th>Taxa 3</th>
                            <th>Taxa 4</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($perf = $perfusions->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo esc($perf['farmaco'] ?? ''); ?></td>
                            <td><?php echo esc($perf['posologia'] ?? ''); ?></td>
                            <td><?php echo esc($perf['hora_inicio'] ?? ''); ?></td>
                            <td><?php echo esc($perf['taxa_1'] ?? ''); ?></td>
                            <td><?php echo esc($perf['taxa_2'] ?? ''); ?></td>
                            <td><?php echo esc($perf['taxa_3'] ?? ''); ?></td>
                            <td><?php echo esc($perf['taxa_4'] ?? ''); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Fármacos -->
        <?php if ($farmacos->num_rows > 0): 
            $farmacos->data_seek(0);
        ?>
        <div class="section">
            <div class="section-title">💊 TERAPÊUTICA - FÁRMACOS</div>
            <div class="section-content">
                <table>
                    <thead>
                        <tr>
                            <th>Fármaco</th>
                            <th>Hora Administração</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($farm = $farmacos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo esc($farm['farmaco'] ?? ''); ?></td>
                            <td><?php echo esc($farm['hora_administracao'] ?? ''); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Antecedentes -->
        <?php if (!empty($record['antecedentes_pessoais']) || !empty($record['alergias']) || !empty($record['medicacao_relevante'])): ?>
        <div class="section">
            <div class="section-title">📋 INFORMAÇÕES CLÍNICAS</div>
            <div class="section-content">
                <?php if (!empty($record['antecedentes_pessoais'])): ?>
                    <p><strong>Antecedentes Pessoais:</strong><br><?php echo nl2br(esc($record['antecedentes_pessoais'])); ?></p>
                <?php endif; ?>
                <?php if (!empty($record['alergias'])): ?>
                    <p><strong>Alergias:</strong><br><?php echo nl2br(esc($record['alergias'])); ?></p>
                <?php endif; ?>
                <?php if (!empty($record['medicacao_relevante'])): ?>
                    <p><strong>Medicação Relevante:</strong><br><?php echo nl2br(esc($record['medicacao_relevante'])); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Responsáveis -->
        <?php if ($team): ?>
        <div class="section">
            <div class="section-title">👥 RESPONSÁVEIS</div>
            <div class="section-content">
                <p><strong>Enfermeiro:</strong> <?php echo esc($team['enfermeiro'] ?? 'N/A'); ?></p>
                <p><strong>Médico:</strong> <?php echo esc($team['medico'] ?? 'N/A'); ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <button class="print-button no-print" onclick="window.print()">🖨️ Imprimir / Exportar para PDF</button>
</body>
</html>
