# 🧪 Guia de Teste Rápido

## Passo 1: Criar um Novo Registo

1. Abra http://localhost/visualtdc/
2. Clique em **"➕ Novo Registo"**
3. Preencha:
   - **Motivo do Transporte:** `Acidente rodoviário`
   - **Serviço de Destino:** `Cirurgia Geral`
   - **Hora de Ativação:** `14:30`
   - **Hora de Saída (ULSCB):** `14:45`
   - **Hora de Chegada (SD):** `15:20`
4. Vá para aba **"🏥 ABCDE"**
5. Marque:
   - [x] Via Aérea Patente
   - [x] Ventilação Espontânea
   - [x] Hemorragia Ativa (para testar alerta)
6. Adicione temperatura na aba **E:** `36.5`
7. Vá para **"👥 Equipa"** e preencha:
   - **Elaborado por:** `Enfermeiro João Silva`
   - **Revisto por:** `Supervisor Maria Santos`
8. Clique **"✅ Guardar Registo"**

## Passo 2: Ver o Registo Criado

1. Clique em **"🏥 Fichas TDC (Nova)"** no menu
2. Deverá ver uma tabela com o registo que criou
3. Clique em **"👁️ Ver"** para visualização completa
4. Percorra as diferentes secções

## Passo 3: Editar o Registo

1. De volta à listagem, clique em **"✏️ Editar"**
2. Mude o **Score TDC** para `8`
3. Vá para **"📊 Monitorização"**
4. Adicione um registo de sinais vitais:
   - **Momento:** `Saída`
   - **Hora:** `14:45`
   - **TA Sistólica:** `130`
   - **TA Diastólica:** `85`
   - **FC:** `95`
   - **SPO2:** `98`
   - **FR:** `16`
   - **ETCO2:** `38`
5. Clique **"✅ Guardar Registo"**

## Passo 4: Imprimir Registo

1. Vá para a listagem
2. Clique **"👁️ Ver"** novamente
3. Clique **"🖨️ Imprimir"** para abrir diálogo de impressão
4. Guarde como PDF ou imprima

---

## ✅ Checklist de Testes

- [ ] Criar novo registo com sucesso
- [ ] Ver registo na listagem
- [ ] Visualizar registo completo
- [ ] Editar registo existente
- [ ] Adicionar sinais vitais
- [ ] Imprimir/PDF funcionar
- [ ] Voltar/Cancelar funcionam

---

## 🐛 Se Algo Não Funcionar

1. **Verifique o MySQL está ativo:** `mysql -u root -e "SHOW DATABASES;"`
2. **Verifique a base de dados:** `mysql -u root tdc_enfermagem -e "SHOW TABLES;"`
3. **Verifique os erros em config.php**
4. **Limpe o cache do navegador** (Ctrl+Shift+Delete)

---

**Bom teste! 🎉**
