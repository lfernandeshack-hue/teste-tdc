# 📋 Estrutura de Base de Dados Atualizada - TDC Enfermagem

**Data de Atualização:** 27/12/2025  
**Baseado em:** Registos de Enfermagem - TDC.pdf (22/01/2025)  
**Versão:** 2.0

---

## 📊 Tabelas Criadas

### 1. **users** (Utilizadores do Sistema)
- `id` - ID único do utilizador
- `email` - Email (único)
- `password` - Senha encriptada
- `name` - Nome completo
- `created_at` - Data de criação

### 2. **tdc_records** (Registo Principal)
Tabela central que contém todos os dados administrativos e clínicos do transporte.

**Campos Principais:**
- `id_tdc` - ID único do registo
- `created_by` - Referência ao utilizador que criou o registo
- `motivo_transporte` - Razão do transporte
- `servico_destino` - Serviço de destino
- Horários: `hora_ativacao`, `hora_saida_ulscb`, `hora_chegada_sd`, `hora_chegada_ulscb`
- `antecedentes_pessoais` - Histórico clínico
- `alergias` - Alergias do doente
- `medicacao_relevante` - Medicamentos em uso
- `ultima_refeicao` - Horário da última refeição
- `score_tdc` - Score TDC (pontuação)

### 3. **tdc_airway** (Via Aérea - ABCDE)
Avaliação e gestão da via aérea do doente.

- `va_patente` - Via aérea patente (booleano)
- `secrecoes` - Descrição das secreções
- Adjuvante VA: `tipo`, `numero`, `data`
- VA Definitiva: `tipo`, `numero`, `nivel`, `data`

### 4. **tdc_ventilation** (Ventilação - ABCDE)
Dados sobre ventilação e suporte respiratório.

- `ventilacao_espontanea` - Ventilação espontânea (booleano)
- `o2_litros` - Fluxo de O2 em litros/minuto
- `tipo_vent_suplementar` - Tipo (ON, MF, MV, MAD, BIPAP, CIPAP, HFNO)
- Parâmetros VNI: `ipap`, `epap`, `fr`, `fio2`
- Parâmetros VMI: `tipo` (VC, PC, PA, SIMV), `vc_pc_pa`, `fio2`, `peep`, `fr`
- `drenagem_toracica` - Existência de drenagem (booleano)

### 5. **tdc_circulation** (Circulação - ABCDE)
Dados hemodinâmicos e suporte cardiovascular.

- Linha Arterial (LA): `dispositivo_la`, `local`, `data`
- CVC: `cvc`, `lumens`, `local`, `data`
- CVP: `valor`, `unidade`, `locais`
- `hemorragia_ativa` - Hemorragia ativa (booleano)
- `suporte_transfusional` - Transfusão (booleano)
- Sonda Vesical: `presente`, `numero`, `data`
- Lavagem Vesical: `presente`, `ml_h`

### 6. **tdc_neurological** (Neurológico - ABCDE)
Avaliação neurológica e escalas de sedação.

- Escalas: `ecg_pontos` (Glasgow), `rass_pontos` (Sedação), `eva_pontos` (Dor), `bps_pontos` (Dor Comportamental)
- `glicemia_capilar` - Valor de glicose capilar
- Sondas: SNG, SOG, SNJ (cada uma com `presente`, `nivel`, `data`)
- `esvaziamento_gastrico` - Descrição do esvaziamento

### 7. **tdc_exposure** (Exposição - ABCDE)
Avaliação externa e traumatológica.

- `temperatura` - Temperatura em ºC
- `imobilizacao_cervical` - Imobilização cervical (booleano)
- Fraturas: `presente`, `locais`
- Feridas/Pensos: `presente`, `local`, `tratamento`

### 8. **tdc_monitoring** (Monitorização - Sinais Vitais)
Registos periódicos de sinais vitais durante o transporte.

- `hora_registo` - Hora da medição
- `ta_sistolica`, `ta_diastolica` - Tensão Arterial (mmHg)
- `fc` - Frequência Cardíaca (bpm)
- `spo2` - Saturação O2 (%)
- `fr` - Frequência Respiratória (rpm)
- `etco2` - CO2 Exalado (mmHg)
- `momento` - Contexto (Saída, Chegada SD, etc)
- `sequencia` - Número sequencial para múltiplos registos

### 9. **tdc_perfusions** (Perfusões IV)
Medicamentos administrados em perfusão intravenosa.

- `farmaco` - Nome do medicamento
- `posologia` - Dose e via
- `hora_inicio` - Hora de início
- `taxa_1` a `taxa_4` - 4 linhas de taxas de infusão (ml/h)
- `sequencia` - Número sequencial

### 10. **tdc_farmacos** (Outros Fármacos)
Fármacos administrados fora de perfusão.

- `farmaco` - Nome do medicamento
- `hora_administracao` - Hora de administração
- `sequencia` - Número sequencial

### 11. **tdc_intercurrencies** (Eventos Adversos)
Registo de complicações ou eventos durante o transporte.

- `hora_evento` - Hora do evento
- `evento` - Descrição detalhada do evento
- `intervencao_realizada` - Ações executadas
- `sequencia` - Número sequencial

### 12. **tdc_team** (Equipa - Responsabilidades)
Responsáveis pela documentação.

- `elaborado_por` - Profissional que elaborou
- `revisto_por` - Profissional que reviu
- `aprovado_por` - Profissional que aprovou

### 13. **equipa_tdc** (Catálogo de Profissionais)
Registo de todos os profissionais.

- `nome` - Nome completo
- `funcao` - Função/Cargo
- `email` - Email
- `telefone` - Contacto
- `ativo` - Estado ativo (booleano)

### 14. **farmacos_tdc** (Catálogo de Fármacos)
Base de dados de medicamentos disponíveis.

- `nome` - Nome do fármaco
- `apresentacao` - Apresentação comercial
- `dose_usual` - Dose recomendada
- `via_administracao` - Via (IV, IM, etc)
- `indicacoes` - Quando usar
- `contraindicacoes` - Quando não usar
- `ativo` - Disponível (booleano)

### 15. **intervencoes_tdc** (Catálogo de Intervenções)
Procedimentos disponíveis.

- `nome` - Nome da intervenção
- `descricao` - Descrição breve
- `procedimento_operacio` - Protocolo detalhado
- `indicacoes` - Quando usar
- `ativo` - Disponível (booleano)

### 16. **estado_atual_doente** (Estado do Doente)
Seguimento do estado do doente.

- `id_tdc` - Referência ao registo TDC
- `estado` - Status atual
- `observacoes` - Notas adicionais
- `data_atualizacao` - Quando foi atualizado

---

## 🔗 Relacionamentos

```
users (1) ──── (N) tdc_records
                    ├─── (1) tdc_airway
                    ├─── (1) tdc_ventilation
                    ├─── (1) tdc_circulation
                    ├─── (1) tdc_neurological
                    ├─── (1) tdc_exposure
                    ├─── (N) tdc_monitoring (múltiplos registos)
                    ├─── (N) tdc_perfusions (múltiplas perfusões)
                    ├─── (N) tdc_farmacos (múltiplos fármacos)
                    ├─── (N) tdc_intercurrencies (eventos)
                    ├─── (1) tdc_team
                    └─── (1) estado_atual_doente
```

---

## 📈 Índices para Performance

Criados índices em todas as chaves estrangeiras para otimização de queries:
- `idx_tdc_*_id_tdc` - Para rapidez nas buscas por registo TDC
- `idx_tdc_records_created_by` - Para buscas por utilizador
- `idx_tdc_records_created_at` - Para buscas por data

---

## ✅ User Padrão

**Email:** admin@tdc.pt  
**Senha:** password123 (hash: $2y$10$...)

---

## 📝 Notas

- A estrutura segue o formato ABCDE de avaliação clínica (padrão de emergência)
- Suporta múltiplos registos de sinais vitais (monitorização contínua)
- Permite rastreabilidade completa com timestamps
- Tabelas catálogo para manutenção de dados de referência
- Sem integridade referencial (FK) para flexibilidade durante importação

---

**Última Alteração:** 27 Dezembro 2025  
**Próximos Passos:** Criar frontend para entrada de dados nesta estrutura
