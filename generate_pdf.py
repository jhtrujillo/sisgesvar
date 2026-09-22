from fpdf import FPDF

class PDF(FPDF):
    def header(self):
        self.set_font('Arial', 'B', 15)
        self.cell(0, 10, 'Resumen SIVar - Presentacion', 0, 1, 'C')

    def chapter_title(self, title):
        self.set_font('Arial', 'B', 12)
        self.set_fill_color(200, 220, 255)
        self.cell(0, 10, title, 0, 1, 'L', 1)
        self.ln(4)

    def chapter_body(self, body):
        self.set_font('Arial', '', 11)
        # remove accents to avoid fpdf latin-1 error
        body = body.replace('ó', 'o').replace('í', 'i').replace('á', 'a').replace('é', 'e').replace('ú', 'u')
        body = body.replace('Ó', 'O').replace('Í', 'I').replace('Á', 'A').replace('É', 'E').replace('Ú', 'U')
        body = body.replace('ñ', 'n').replace('Ñ', 'N')
        self.multi_cell(0, 6, body)
        self.ln()

pdf = PDF()
pdf.add_page()
with open('resumen_sivar.md', 'r', encoding='utf-8') as f:
    text = f.read()

# basic parsing
sections = text.split('## ')
pdf.chapter_body(sections[0])
for section in sections[1:]:
    lines = section.split('\n', 1)
    title = lines[0]
    body = lines[1] if len(lines) > 1 else ''
    pdf.chapter_title(title)
    pdf.chapter_body(body)

pdf.output('Resumen_SIVar.pdf', 'F')
