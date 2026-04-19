import pypdf
import os
import sys

def extract_to_txt(pdf_path, txt_path):
    try:
        reader = pypdf.PdfReader(pdf_path)
        text = ""
        for page in reader.pages:
            content = page.extract_text()
            if content:
                text += content + "\n"
        
        with open(txt_path, 'w', encoding='utf-8') as f:
            f.write(text)
        print(f"Successfully converted {pdf_path} to {txt_path}")
    except Exception as e:
        print(f"Error converting {pdf_path}: {e}")

if __name__ == "__main__":
    output_dir = "ducuments/Text Versions"
    
    # Files to convert
    files = [
        ("ducuments/My Proposal/course_management_system_proposal.pdf", "proposal.txt"),
        ("ducuments/Research Documents/1. পারমাকালচারের ভিত্তি_ নৈতিকতা, নকশা নীতি ও ভূমি পর্যবেক্ষণ.pdf", "foundations.txt"),
        ("ducuments/Research Documents/2. ফুড ফরেস্ট যুক্তি, সীমাবদ্ধতা ও স্টুয়ার্ডশিপ.pdf", "food_forest.txt"),
        ("ducuments/Research Documents/3. ডিজাইন বিচারবুদ্ধি ও স্টুডিও প্রস্তুতি.pdf", "design_wisdom.txt"),
        ("ducuments/Research Documents/টেকসই জীবনের দিকে যাত্রা .pdf", "sustainable_journey.txt"),
        ("ducuments/Research Documents/তৃতীয়_ শেখা থেকে কর্ম.pdf", "learning_to_action.txt")
    ]
    
    for pdf, txt in files:
        full_pdf_path = pdf
        full_txt_path = os.path.join(output_dir, txt)
        extract_to_txt(full_pdf_path, full_txt_path)
