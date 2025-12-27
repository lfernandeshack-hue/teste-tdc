-- ============================================================================
-- Database: tdc_enfermagem
-- Sistema de Registos de Enfermagem - Transporte Doente Crítico
-- Baseado em: "Registos de Enfermagem - TDC.pdf"
-- Data: 2025-01-22
-- ============================================================================

-- ============================================================================
-- TABELA: users
-- ============================================================================
CREATE TABLE IF NOT EXISTS users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  name VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABELA: tdc_records (Registo Principal de TDC)
-- ============================================================================
CREATE TABLE IF NOT EXISTS tdc_records (
  id_tdc INT PRIMARY KEY AUTO_INCREMENT,
  created_by INT NOT NULL,
  
  -- Dados Administrativos
  motivo_transporte VARCHAR(500),
  servico_destino VARCHAR(255),
  hora_ativacao TIME,
  hora_saida_ulscb TIME,
  hora_chegada_sd TIME,
  hora_chegada_ulscb TIME,
  
  -- Dados Clínicos Prévios
  antecedentes_pessoais TEXT,
  alergias TEXT,
  medicacao_relevante TEXT,
  ultima_refeicao VARCHAR(255),
  
  -- Score TDC
  score_tdc INT,
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  KEY fk_tdc_records_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABELA: tdc_airway (Avaliação - Via Aérea)
-- ============================================================================
CREATE TABLE IF NOT EXISTS tdc_airway (
  id_airway INT PRIMARY KEY AUTO_INCREMENT,
  id_tdc INT NOT NULL,
  
  -- Via Aérea Patente
  va_patente BOOLEAN,
  secrecoes VARCHAR(500),
  
  -- Adjuvante VA
  adjuvante_va_tipo VARCHAR(100),
  adjuvante_va_numero VARCHAR(50),
  adjuvante_va_data DATE,
  
  -- VA Definitiva
  va_definitiva_tipo VARCHAR(100),
  va_definitiva_numero VARCHAR(50),
  va_definitiva_nivel VARCHAR(100),
  va_definitiva_data DATE,
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  KEY fk_tdc_airway_id_tdc (id_tdc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABELA: tdc_ventilation (Avaliação - Ventilação)
-- ============================================================================
CREATE TABLE IF NOT EXISTS tdc_ventilation (
  id_vent INT PRIMARY KEY AUTO_INCREMENT,
  id_tdc INT NOT NULL,
  
  -- Ventilação Espontânea
  ventilacao_espontanea BOOLEAN,
  
  -- Ventilação Suplementar com O2
  o2_litros DECIMAL(5,2),
  
  -- Tipo de Ventilação Suplementar (ON, MF, MV, MAD, BIPAP, CIPAP, HFNO)
  tipo_vent_suplementar VARCHAR(50),
  
  -- Parâmetros VNI
  vni_ipap DECIMAL(5,2),
  vni_epap DECIMAL(5,2),
  vni_fr INT,
  vni_fio2 INT,
  
  -- VMI (Ventilação Mecânica Invasiva)
  vmi_tipo VARCHAR(50), -- VC, PC, PA, SIMV
  vmi_vc_pc_pa DECIMAL(5,2),
  vmi_fio2 INT,
  vmi_peep DECIMAL(5,2),
  vmi_fr INT,
  
  -- Drenagem Torácica
  drenagem_toracica BOOLEAN,
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  KEY fk_tdc_ventilation_id_tdc (id_tdc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABELA: tdc_circulation (Avaliação - Circulação)
-- ============================================================================
CREATE TABLE IF NOT EXISTS tdc_circulation (
  id_circ INT PRIMARY KEY AUTO_INCREMENT,
  id_tdc INT NOT NULL,
  
  -- Linha Arterial
  dispositivo_la BOOLEAN,
  la_local VARCHAR(100),
  la_data DATE,
  
  -- CVC (Catéter Venoso Central)
  cvc BOOLEAN,
  cvc_lumens INT,
  cvc_local VARCHAR(100),
  cvc_data DATE,
  
  -- CVP (Pressão Venosa Central)
  cvp_valor DECIMAL(5,2),
  cvp_unidade VARCHAR(20),
  cvp_locais VARCHAR(255),
  
  -- Hemorragia Ativa
  hemorragia_ativa BOOLEAN,
  
  -- Suporte Transfusional
  suporte_transfusional BOOLEAN,
  
  -- Sonda Vesical
  sonda_vesical BOOLEAN,
  sonda_vesical_numero VARCHAR(50),
  sonda_vesical_data DATE,
  
  -- Lavagem Vesical
  lavagem_vesical BOOLEAN,
  lavagem_vesical_ml_h DECIMAL(5,2),
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  KEY fk_tdc_circulation_id_tdc (id_tdc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABELA: tdc_neurological (Avaliação - Neurológico)
-- ============================================================================
CREATE TABLE IF NOT EXISTS tdc_neurological (
  id_neuro INT PRIMARY KEY AUTO_INCREMENT,
  id_tdc INT NOT NULL,
  
  -- Escalas de Avaliação
  ecg_pontos INT,         -- Escala de Glasgow
  rass_pontos INT,        -- Richmond Agitation-Sedation Scale
  eva_pontos INT,         -- Escala Visual Analógica
  bps_pontos INT,         -- Behavioral Pain Scale
  
  -- Glicemia Capilar
  glicemia_capilar DECIMAL(5,2),
  
  -- Sondas Naso/Oro/Gastro
  sng_presente BOOLEAN,   -- Sonda Naso-Gástrica
  sng_nivel VARCHAR(100),
  sng_data DATE,
  
  sog_presente BOOLEAN,   -- Sonda Oro-Gástrica
  sog_nivel VARCHAR(100),
  sog_data DATE,
  
  snj_presente BOOLEAN,   -- Sonda Naso-Jejunal
  snj_nivel VARCHAR(100),
  snj_data DATE,
  
  -- Esvaziamento Gástrico
  esvaziamento_gastrico VARCHAR(500),
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  KEY fk_tdc_neurological_id_tdc (id_tdc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABELA: tdc_exposure (Avaliação - Exposição)
-- ============================================================================
CREATE TABLE IF NOT EXISTS tdc_exposure (
  id_exp INT PRIMARY KEY AUTO_INCREMENT,
  id_tdc INT NOT NULL,
  
  -- Temperatura
  temperatura DECIMAL(5,2),
  
  -- Imobilização Cervical
  imobilizacao_cervical BOOLEAN,
  
  -- Fraturas
  fraturas BOOLEAN,
  fraturas_locais VARCHAR(500),
  
  -- Feridas/Pensos
  feridas_pensos BOOLEAN,
  feridas_pensos_local VARCHAR(255),
  feridas_pensos_tratamento VARCHAR(500),
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  KEY fk_tdc_exposure_id_tdc (id_tdc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABELA: tdc_monitoring (Monitorização - Sinais Vitais)
-- ============================================================================
CREATE TABLE IF NOT EXISTS tdc_monitoring (
  id_monitoring INT PRIMARY KEY AUTO_INCREMENT,
  id_tdc INT NOT NULL,
  
  -- Hora de Registo
  hora_registo TIME,
  
  -- Sinais Vitais
  ta_sistolica INT,       -- Tensão Arterial Sistólica (mmHg)
  ta_diastolica INT,      -- Tensão Arterial Diastólica (mmHg)
  fc INT,                 -- Frequência Cardíaca (bpm)
  spo2 INT,               -- Saturação O2 (%)
  fr INT,                 -- Frequência Respiratória (rpm)
  etco2 INT,              -- Dióxido de Carbono Exalado (mmHg)
  
  -- Momento do Registo
  momento VARCHAR(50),    -- 'Saída', 'Chegada SD', 'Chegada ULSCB', etc
  
  sequencia INT,          -- Número sequencial para múltiplos registos
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  KEY fk_tdc_monitoring_id_tdc (id_tdc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABELA: tdc_perfusions (Perfusões e Medicamentos IV)
-- ============================================================================
CREATE TABLE IF NOT EXISTS tdc_perfusions (
  id_perfusao INT PRIMARY KEY AUTO_INCREMENT,
  id_tdc INT NOT NULL,
  
  -- Informação da Perfusão
  farmaco VARCHAR(255),
  posologia VARCHAR(255),
  hora_inicio TIME,
  
  -- Taxas de Infusão Paralelas (ml/h)
  taxa_1 DECIMAL(5,2),
  taxa_2 DECIMAL(5,2),
  taxa_3 DECIMAL(5,2),
  taxa_4 DECIMAL(5,2),
  
  sequencia INT,
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  KEY fk_tdc_perfusions_id_tdc (id_tdc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABELA: tdc_farmacos (Outros Fármacos - Administração)
-- ============================================================================
CREATE TABLE IF NOT EXISTS tdc_farmacos (
  id_farmaco INT PRIMARY KEY AUTO_INCREMENT,
  id_tdc INT NOT NULL,
  
  -- Informação do Fármaco
  farmaco VARCHAR(255),
  hora_administracao TIME,
  
  sequencia INT,
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  KEY fk_tdc_farmacos_id_tdc (id_tdc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABELA: tdc_intercurrencies (Eventos Adversos)
-- ============================================================================
CREATE TABLE IF NOT EXISTS tdc_intercurrencies (
  id_intercorrencia INT PRIMARY KEY AUTO_INCREMENT,
  id_tdc INT NOT NULL,
  
  -- Evento
  hora_evento TIME,
  evento TEXT,
  intervencao_realizada TEXT,
  
  sequencia INT,
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  KEY fk_tdc_intercurrencies_id_tdc (id_tdc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABELA: tdc_team (Equipa - Responsabilidades)
-- ============================================================================
CREATE TABLE IF NOT EXISTS tdc_team (
  id_team INT PRIMARY KEY AUTO_INCREMENT,
  id_tdc INT NOT NULL,
  
  -- Responsabilidades
  elaborado_por VARCHAR(255),
  revisto_por VARCHAR(255),
  aprovado_por VARCHAR(255),
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  KEY fk_tdc_team_id_tdc (id_tdc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABELAS AUXILIARES (Catálogos)
-- ============================================================================

-- ============================================================================
-- TABELA: equipa_tdc (Equipas/Profissionais)
-- ============================================================================
CREATE TABLE IF NOT EXISTS equipa_tdc (
  id_equipa INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(255),
  funcao VARCHAR(255),
  email VARCHAR(255),
  telefone VARCHAR(20),
  ativo BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABELA: farmacos_tdc (Catálogo de Fármacos)
-- ============================================================================
CREATE TABLE IF NOT EXISTS farmacos_tdc (
  id_farmaco INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(255),
  apresentacao VARCHAR(255),
  dose_usual VARCHAR(255),
  via_administracao VARCHAR(100),
  indicacoes TEXT,
  contraindicacoes TEXT,
  ativo BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABELA: intervencoes_tdc (Catálogo de Intervenções)
-- ============================================================================
CREATE TABLE IF NOT EXISTS intervencoes_tdc (
  id_intervencao INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(255),
  descricao TEXT,
  procedimento_operacio TEXT,
  indicacoes TEXT,
  ativo BOOLEAN DEFAULT TRUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- TABELA: estado_atual_doente (Estado do Doente - Estatuto)
-- ============================================================================
CREATE TABLE IF NOT EXISTS estado_atual_doente (
  id_estado INT PRIMARY KEY AUTO_INCREMENT,
  id_tdc INT NOT NULL,
  estado VARCHAR(100),
  observacoes TEXT,
  data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY fk_estado_atual_doente_id_tdc (id_tdc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================================
-- ÍNDICES PARA PERFORMANCE
-- ============================================================================

CREATE INDEX idx_tdc_records_created_by ON tdc_records(created_by);
CREATE INDEX idx_tdc_records_created_at ON tdc_records(created_at);

CREATE INDEX idx_tdc_airway_id_tdc ON tdc_airway(id_tdc);
CREATE INDEX idx_tdc_ventilation_id_tdc ON tdc_ventilation(id_tdc);
CREATE INDEX idx_tdc_circulation_id_tdc ON tdc_circulation(id_tdc);
CREATE INDEX idx_tdc_neurological_id_tdc ON tdc_neurological(id_tdc);
CREATE INDEX idx_tdc_exposure_id_tdc ON tdc_exposure(id_tdc);
CREATE INDEX idx_tdc_monitoring_id_tdc ON tdc_monitoring(id_tdc);
CREATE INDEX idx_tdc_perfusions_id_tdc ON tdc_perfusions(id_tdc);
CREATE INDEX idx_tdc_farmacos_id_tdc ON tdc_farmacos(id_tdc);
CREATE INDEX idx_tdc_intercurrencies_id_tdc ON tdc_intercurrencies(id_tdc);
CREATE INDEX idx_tdc_team_id_tdc ON tdc_team(id_tdc);

-- ============================================================================
-- DADOS INICIAIS (User de Teste)
-- ============================================================================

INSERT INTO users (email, password, name) VALUES 
('admin@tdc.pt', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/F.m', 'Administrador TDC')
ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id);

-- ============================================================================
-- FIM DO SCRIPT
-- ============================================================================
