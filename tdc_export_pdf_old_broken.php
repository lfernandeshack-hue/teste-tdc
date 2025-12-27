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
function esc($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
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
            height: 297mm;
            margin: 0 auto;
            padding: 15mm;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        /* Cabeçalho */
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

        /* Informações administrativas */
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

        /* Secções */
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

        /* Grid de dados */
        .data-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 8px;
        }

        .data-grid.full {
            grid-template-columns: 1fr;
        }

        .data-item {
            display: flex;
            flex-direction: column;
            font-size: 11px;
        }

        .data-label {
            font-weight: bold;
            color: #003366;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .data-value {
            color: #333;
            padding: 4px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 2px;
            min-height: 20px;
        }

        /* Tabelas */
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

        /* ABCDE Assessment */
        .abcde-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 10px;
        }

        .abcde-item {
            background: white;
            border: 2px solid #003366;
            padding: 8px;
            border-radius: 4px;
            text-align: center;
        }

        .abcde-letter {
            font-weight: bold;
            color: white;
            background: #003366;
            width: 30px;
            height: 30px;
            line-height: 30px;
            border-radius: 50%;
            margin: 0 auto 5px;
            font-size: 14px;
        }

        .abcde-title {
            font-weight: bold;
            color: #003366;
            font-size: 10px;
            margin-bottom: 3px;
        }

        .abcde-content {
            font-size: 10px;
            color: #666;
            line-height: 1.3;
        }

        /* Footer com assinaturas */
        .signatures {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #003366;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            font-size: 11px;
        }

        .signature-block {
            text-align: center;
        }

        .signature-space {
            border-top: 1px solid #333;
            height: 40px;
            margin: 20px 0 5px;
        }

        .signature-name {
            font-weight: bold;
            color: #003366;
        }

        /* Impressão */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .container {
                box-shadow: none;
                max-width: 100%;
                margin: 0;
                padding: 15mm;
            }

            .no-print {
                display: none;
            }

            .section {
                page-break-inside: avoid;
            }
        }

        /* Botão de impressão (escondido na impressão) */
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

        .print-button:hover {
            background: #002244;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Cabeçalho -->
        <div class="header">
            <h1>REGISTO DE ENFERMAGEM - TRANSPORTE DE DOENTE CRÍTICO (TDC)</h1>
            <p>Sistema de Documentação de Transporte de Doente Crítico</p>
            <p>Registo nº <?php echo $id_tdc; ?> | Data: <?php echo esc($record['data']); ?></p>
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

        <!-- ABCDE Assessment -->
        <div class="section">
            <div class="section-title">🏥 AVALIAÇÃO ABCDE</div>
            <div class="abcde-grid">
                <!-- A - Airway -->
                <div class="abcde-item">
                    <div class="abcde-letter">A</div>
                    <div class="abcde-title">Airway</div>
                    <div class="abcde-content">
                        <strong>Status:</strong> <?php echo $airway ? esc($airway['status']) : 'N/A'; ?><br>
                        <strong>Intubado:</strong> <?php echo $airway ? get_sim_nao($airway['intubado']) : 'N/A'; ?>
                    </div>
                </div>

                <!-- B - Breathing -->
                <div class="abcde-item">
                    <div class="abcde-letter">B</div>
                    <div class="abcde-title">Breathing</div>
                    <div class="abcde-content">
                        <strong>Ventilação:</strong> <?php echo $ventilation ? esc($ventilation['tipo_ventilacao']) : 'N/A'; ?><br>
                        <strong>FR:</strong> <?php echo $ventilation ? esc($ventilation['frequencia_respiratoria']) : 'N/A'; ?> /min
                    </div>
                </div>

                <!-- C - Circulation -->
                <div class="abcde-item">
                    <div class="abcde-letter">C</div>
                    <div class="abcde-title">Circulation</div>
                    <div class="abcde-content">
                        <strong>PA:</strong> <?php echo $circulation ? esc($circulation['pressao_arterial']) : 'N/A'; ?><br>
                        <strong>FC:</strong> <?php echo $circulation ? esc($circulation['frequencia_cardiaca']) : 'N/A'; ?> /min
                    </div>
                </div>

                <!-- D - Disability -->
                <div class="abcde-item">
                    <div class="abcde-letter">D</div>
                    <div class="abcde-title">Disability</div>
                    <div class="abcde-content">
                        <strong>Consciência:</strong> <?php echo $neurological ? esc($neurological['estado_consciencia']) : 'N/A'; ?><br>
                        <strong>Pupilas:</strong> <?php echo $neurological ? esc($neurological['pupilas']) : 'N/A'; ?>
                    </div>
                </div>

                <!-- E - Exposure -->
                <div class="abcde-item">
                    <div class="abcde-letter">E</div>
                    <div class="abcde-title">Exposure</div>
                    <div class="abcde-content">
                        <strong>Temp:</strong> <?php echo $exposure ? esc($exposure['temperatura']) : 'N/A'; ?> °C<br>
                        <strong>Lesões:</strong> <?php echo $exposure ? (esc($exposure['lesoes']) ?: 'Nenhuma') : 'N/A'; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monitorização de Sinais Vitais -->
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
                            <td><?php echo esc($mon['hora']); ?></td>
                            <td><?php echo esc($mon['temperatura']); ?></td>
                            <td><?php echo esc($mon['frequencia_cardiaca']); ?></td>
                            <td><?php echo esc($mon['pressao_arterial']); ?></td>
                            <td><?php echo esc($mon['frequencia_respiratoria']); ?></td>
                            <td><?php echo esc($mon['spo2']); ?></td>
                            <td><?php echo esc($mon['glicemia']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Terapêutica - Perfusões -->
        <?php 
        $perfusion_count = 0;
        if ($perfusions->num_rows > 0): 
            $perfusions->data_seek(0);
        ?>
        <div class="section">
            <div class="section-title">💉 TERAPÊUTICA - PERFUSÕES INTRAVENOSAS</div>
            <div class="section-content">
                <table>
                    <thead>
                        <tr>
                            <th>Fármaco</th>
                            <th>Concentração</th>
                            <th>Velocidade (ml/h)</th>
                            <th>Via</th>
                            <th>Observações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($perf = $perfusions->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo esc($perf['farmaco']); ?></td>
                            <td><?php echo esc($perf['posologia']); ?></td>
                            <td><?php echo esc($perf['hora_inicio']); ?></td>
                            <td><?php echo esc($perf['taxa_1']); ?></td>
                            <td><?php echo esc($perf['taxa_2']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Terapêutica - Fármacos -->
        <?php if ($farmacos->num_rows > 0): 
            $farmacos->data_seek(0);
        ?>
        <div class="section">
            <div class="section-title">💊 TERAPÊUTICA - FÁRMACOS</div>
            <div class="section-content">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Dosagem</th>
                            <th>Via</th>
                            <th>Hora</th>
                            <th>Observações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($farm = $farmacos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo esc($farm['farmaco']); ?></td>
                            <td><?php echo esc($farm['hora_administracao']); ?></td>
                            <td><?php echo esc($farm['sequencia']); ?></td>
                            <td><?php echo esc($farm['created_at']); ?></td>
                            <td><?php echo esc($farm['id_farmaco']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Intercorrências -->
        <?php if ($intercurrencies->num_rows > 0): 
            $intercurrencies->data_seek(0);
        ?>
        <div class="section">
            <div class="section-title">⚠️ INTERCORRÊNCIAS</div>
            <div class="section-content">
                <table>
                    <thead>
                        <tr>
                            <th>Hora</th>
                            <th>Evento</th>
                            <th>Descrição</th>
                            <th>Ação Tomada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($inter = $intercurrencies->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo esc($inter['hora']); ?></td>
                            <td><?php echo esc($inter['evento']); ?></td>
                            <td><?php echo esc($inter['descricao']); ?></td>
                            <td><?php echo esc($inter['acao_tomada']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Responsáveis -->
        <?php if ($team): ?>
        <div class="section">
            <div class="section-title">👥 RESPONSÁVEIS PELO TRANSPORTE</div>
            <div class="section-content">
                <div class="data-grid">
                    <div class="data-item">
                        <span class="data-label">Enfermeiro</span>
                        <span class="data-value"><?php echo esc($team['enfermeiro']); ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Médico</span>
                        <span class="data-value"><?php echo esc($team['medico']); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Antecedentes e Informações Clínicas -->
        <?php if (!empty($record['antecedentes_pessoais'])): ?>
        <div class="section">
            <div class="section-title">📋 ANTECEDENTES PESSOAIS</div>
            <div class="section-content">
                <div class="data-value" style="min-height: 40px; white-space: pre-wrap;">
                    <?php echo esc($record['antecedentes_pessoais']); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($record['alergias'])): ?>
        <div class="section">
            <div class="section-title">⚠️ ALERGIAS</div>
            <div class="section-content">
                <div class="data-value" style="min-height: 40px; white-space: pre-wrap;">
                    <?php echo esc($record['alergias']); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($record['medicacao_relevante'])): ?>
        <div class="section">
            <div class="section-title">💊 MEDICAÇÃO RELEVANTE</div>
            <div class="section-content">
                <div class="data-value" style="min-height: 40px; white-space: pre-wrap;">
                    <?php echo esc($record['medicacao_relevante']); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Assinaturas -->
        <div class="signatures">
            <div class="signature-block">
                <div class="signature-name">Enfermeiro</div>
                <div class="signature-space"></div>
                <div><?php echo esc($team['enfermeiro'] ?? 'N/A'); ?></div>
            </div>
            <div class="signature-block">
                <div class="signature-name">Médico</div>
                <div class="signature-space"></div>
                <div><?php echo esc($team['medico'] ?? 'N/A'); ?></div>
            </div>
        </div>
    </div>

    <button class="print-button no-print" onclick="window.print()">🖨️ Imprimir / Exportar para PDF</button>

    <script>
        // Auto-imprimir quando carrega em nova janela
        if (window.name === 'pdf_export') {
            window.print();
        }
    </script>
</body>
</html>


