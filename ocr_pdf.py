#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script OCR para extrair texto de PDF escaneado
Requisitos: tesseract-ocr, pytesseract, pillow, pymupdf
"""

import sys
import os
import fitz  # PyMuPDF
from PIL import Image
import pytesseract
from pathlib import Path

# Configurar caminho do Tesseract (ajuste conforme sua instalação)
pytesseract.pytesseract.pytesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract.exe'

def ocr_pdf(pdf_path, output_txt='extracted_text.txt'):
    """
    Extrai texto de um PDF escaneado usando OCR
    
    Args:
        pdf_path (str): Caminho do arquivo PDF
        output_txt (str): Arquivo de saída com o texto extraído
    """
    
    if not os.path.exists(pdf_path):
        print(f"❌ Erro: Arquivo {pdf_path} não encontrado!")
        return False
    
    print(f"📄 Processando: {pdf_path}")
    print(f"⏳ Isto pode levar alguns minutos...")
    
    try:
        # Abrir PDF
        pdf_doc = fitz.open(pdf_path)
        total_pages = pdf_doc.page_count
        print(f"📖 Total de páginas: {total_pages}")
        
        all_text = []
        
        # Processar cada página
        for page_num in range(total_pages):
            print(f"🔄 Processando página {page_num + 1}/{total_pages}...", end='\r')
            
            page = pdf_doc[page_num]
            
            # Renderizar página como imagem (PNG)
            pix = page.get_pixmap(matrix=fitz.Matrix(2, 2))  # 2x zoom para melhor OCR
            img_data = pix.tobytes("ppm")
            
            # Converter para PIL Image
            img = Image.frombytes("RGB", [pix.width, pix.height], img_data)
            
            # Fazer OCR
            text = pytesseract.image_to_string(img, lang='eng')  # English (por não disponível)
            
            if text.strip():
                all_text.append(f"\n--- PÁGINA {page_num + 1} ---\n")
                all_text.append(text)
        
        pdf_doc.close()
        
        # Salvar texto extraído
        output_path = Path(output_txt)
        with open(output_path, 'w', encoding='utf-8') as f:
            f.write(''.join(all_text))
        
        print(f"\n✅ Sucesso! Texto extraído salvo em: {output_path.absolute()}")
        print(f"📊 Total de caracteres: {sum(len(t) for t in all_text)}")
        
        return True
        
    except pytesseract.TesseractNotFoundError:
        print("\n❌ Erro: Tesseract não encontrado!")
        print("📥 Instale Tesseract-OCR de: https://github.com/UB-Mannheim/tesseract/wiki")
        return False
    except Exception as e:
        print(f"\n❌ Erro ao processar PDF: {e}")
        return False

if __name__ == '__main__':
    pdf_file = 'Scan 34.pdf'
    output_file = 'Scan_34_extracted.txt'
    
    if len(sys.argv) > 1:
        pdf_file = sys.argv[1]
    if len(sys.argv) > 2:
        output_file = sys.argv[2]
    
    ocr_pdf(pdf_file, output_file)
