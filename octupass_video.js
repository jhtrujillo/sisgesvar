const {
  Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
  AlignmentType, HeadingLevel, BorderStyle, WidthType, ShadingType,
  PageNumber, Header, Footer, LevelFormat
} = require('docx');
const fs = require('fs');

const border = { style: BorderStyle.SINGLE, size: 1, color: "CCCCCC" };
const borders = { top: border, bottom: border, left: border, right: border };
const cellMargins = { top: 80, bottom: 80, left: 120, right: 120 };

function heading1(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_1,
    spacing: { before: 360, after: 120 },
    children: [new TextRun({ text, bold: true, size: 32, font: "Arial", color: "1A1A2E" })]
  });
}

function heading2(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_2,
    spacing: { before: 240, after: 80 },
    children: [new TextRun({ text, bold: true, size: 26, font: "Arial", color: "3D3D8F" })]
  });
}

function heading3(text, color = "555555") {
  return new Paragraph({
    spacing: { before: 200, after: 60 },
    children: [new TextRun({ text, bold: true, size: 22, font: "Arial", color })]
  });
}

function body(text, options = {}) {
  return new Paragraph({
    spacing: { before: 60, after: 60 },
    children: [new TextRun({ text, size: 22, font: "Arial", ...options })]
  });
}

function promptBox(text) {
  return new Table({
    width: { size: 9360, type: WidthType.DXA },
    columnWidths: [9360],
    rows: [
      new TableRow({
        children: [
          new TableCell({
            borders,
            width: { size: 9360, type: WidthType.DXA },
            shading: { fill: "F4F4FF", type: ShadingType.CLEAR },
            margins: cellMargins,
            children: [
              new Paragraph({
                children: [new TextRun({ text, size: 20, font: "Courier New", color: "1A1A2E", italics: true })]
              })
            ]
          })
        ]
      })
    ]
  });
}

function spacer() {
  return new Paragraph({ spacing: { before: 100, after: 100 }, children: [new TextRun("")] });
}

function configTable(rows) {
  const headerShade = { fill: "1A1A2E", type: ShadingType.CLEAR };
  const colWidths = [3000, 6360];
  return new Table({
    width: { size: 9360, type: WidthType.DXA },
    columnWidths: colWidths,
    rows: [
      new TableRow({
        children: [
          new TableCell({
            borders, width: { size: colWidths[0], type: WidthType.DXA },
            shading: headerShade, margins: cellMargins,
            children: [new Paragraph({ children: [new TextRun({ text: "Parametro", bold: true, size: 20, font: "Arial", color: "FFFFFF" })] })]
          }),
          new TableCell({
            borders, width: { size: colWidths[1], type: WidthType.DXA },
            shading: headerShade, margins: cellMargins,
            children: [new Paragraph({ children: [new TextRun({ text: "Valor", bold: true, size: 20, font: "Arial", color: "FFFFFF" })] })]
          }),
        ]
      }),
      ...rows.map(([param, val], i) => new TableRow({
        children: [
          new TableCell({
            borders, width: { size: colWidths[0], type: WidthType.DXA },
            shading: { fill: i % 2 === 0 ? "EFEFFF" : "FFFFFF", type: ShadingType.CLEAR },
            margins: cellMargins,
            children: [new Paragraph({ children: [new TextRun({ text: param, bold: true, size: 20, font: "Arial" })] })]
          }),
          new TableCell({
            borders, width: { size: colWidths[1], type: WidthType.DXA },
            shading: { fill: i % 2 === 0 ? "EFEFFF" : "FFFFFF", type: ShadingType.CLEAR },
            margins: cellMargins,
            children: [new Paragraph({ children: [new TextRun({ text: val, size: 20, font: "Arial" })] })]
          }),
        ]
      }))
    ]
  });
}

function summaryTable() {
  const colWidths = [3800, 3000, 2560];
  const headerShade = { fill: "1A1A2E", type: ShadingType.CLEAR };
  const dataRows = [
    ["Clip 1 — Apertura", "6 seg", "6 seg"],
    ["Clip 2 — Transformacion digital", "5 seg", "11 seg"],
    ["Clip 3 — Construccion del sitio", "6 seg", "17 seg"],
    ["Clip 4 — Los 8 poderes", "5 seg", "22 seg"],
    ["Clip 5 — El cliente", "6 seg", "28 seg"],
    ["Clip 6 — Reveal logo", "6 seg", "34 seg"],
  ];
  return new Table({
    width: { size: 9360, type: WidthType.DXA },
    columnWidths: colWidths,
    rows: [
      new TableRow({
        children: ["Clip", "Duracion", "Total acumulado"].map((h, i) =>
          new TableCell({
            borders, width: { size: colWidths[i], type: WidthType.DXA },
            shading: headerShade, margins: cellMargins,
            children: [new Paragraph({ children: [new TextRun({ text: h, bold: true, size: 20, font: "Arial", color: "FFFFFF" })] })]
          })
        )
      }),
      ...dataRows.map(([clip, dur, total], i) => new TableRow({
        children: [clip, dur, total].map((val, j) =>
          new TableCell({
            borders, width: { size: colWidths[j], type: WidthType.DXA },
            shading: { fill: i % 2 === 0 ? "F0F0FF" : "FFFFFF", type: ShadingType.CLEAR },
            margins: cellMargins,
            children: [new Paragraph({ children: [new TextRun({ text: val, size: 20, font: "Arial", bold: j === 2 && i === 5 })] })]
          })
        )
      }))
    ]
  });
}

function toolsTable() {
  const colWidths = [2400, 2000, 2000, 2960];
  const headerShade = { fill: "1A1A2E", type: ShadingType.CLEAR };
  const rows = [
    ["Kling AI", "★★★★★", "5 videos/dia gratis", "Facil — recomendada"],
    ["Hailuo AI", "★★★★☆", "Creditos diarios", "Facil"],
    ["Pixverse", "★★★☆☆", "Ilimitado", "Muy facil"],
    ["LTX Studio", "★★★☆☆", "Ilimitado", "Facil"],
  ];
  return new Table({
    width: { size: 9360, type: WidthType.DXA },
    columnWidths: colWidths,
    rows: [
      new TableRow({
        children: ["Herramienta", "Calidad", "Gratis", "Facilidad"].map((h, i) =>
          new TableCell({
            borders, width: { size: colWidths[i], type: WidthType.DXA },
            shading: headerShade, margins: cellMargins,
            children: [new Paragraph({ children: [new TextRun({ text: h, bold: true, size: 20, font: "Arial", color: "FFFFFF" })] })]
          })
        )
      }),
      ...rows.map((cols, ri) => new TableRow({
        children: cols.map((val, ci) =>
          new TableCell({
            borders, width: { size: colWidths[ci], type: WidthType.DXA },
            shading: { fill: ri % 2 === 0 ? "F0F0FF" : "FFFFFF", type: ShadingType.CLEAR },
            margins: cellMargins,
            children: [new Paragraph({ children: [new TextRun({ text: val, size: 20, font: "Arial" })] })]
          })
        )
      }))
    ]
  });
}

function clipSection(num, title, color, startPrompt, endPrompt, animPrompt, configRows, tips) {
  const items = [
    spacer(),
    heading2(`CLIP ${num} — ${title}`),
    spacer(),
    heading3("Fotograma de INICIO", "006644"),
    spacer(),
    promptBox(startPrompt),
    spacer(),
    heading3("Fotograma de FIN", "880044"),
    spacer(),
    promptBox(endPrompt),
    spacer(),
    heading3("Prompt de ANIMACION (Kling AI / Hailuo AI)", "1A1A8E"),
    spacer(),
    promptBox(animPrompt),
    spacer(),
    heading3("Configuracion recomendada", "333333"),
    spacer(),
    configTable(configRows),
    spacer(),
  ];
  if (tips.length > 0) {
    items.push(heading3("Tips", "885500"));
    tips.forEach(t => items.push(body("• " + t)));
    items.push(spacer());
  }
  return items;
}

const doc = new Document({
  styles: {
    default: {
      document: { run: { font: "Arial", size: 22 } }
    }
  },
  sections: [{
    properties: {
      page: {
        size: { width: 12240, height: 15840 },
        margin: { top: 1440, right: 1440, bottom: 1440, left: 1440 }
      }
    },
    headers: {
      default: new Header({
        children: [new Paragraph({
          alignment: AlignmentType.RIGHT,
          border: { bottom: { style: BorderStyle.SINGLE, size: 4, color: "1A1A2E", space: 1 } },
          children: [new TextRun({ text: "OCTUPASS — Guia de Produccion de Video", size: 18, font: "Arial", color: "555555" })]
        })]
      })
    },
    footers: {
      default: new Footer({
        children: [new Paragraph({
          alignment: AlignmentType.CENTER,
          border: { top: { style: BorderStyle.SINGLE, size: 4, color: "1A1A2E", space: 1 } },
          children: [
            new TextRun({ text: "Pagina ", size: 18, font: "Arial", color: "888888" }),
            new TextRun({ children: [PageNumber.CURRENT], size: 18, font: "Arial", color: "888888" }),
            new TextRun({ text: " de ", size: 18, font: "Arial", color: "888888" }),
            new TextRun({ children: [PageNumber.TOTAL_PAGES], size: 18, font: "Arial", color: "888888" }),
          ]
        })]
      })
    },
    children: [
      // PORTADA
      spacer(),
      spacer(),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 480, after: 120 },
        children: [new TextRun({ text: "OCTUPASS", size: 64, bold: true, font: "Arial", color: "1A1A2E" })]
      }),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 60, after: 60 },
        children: [new TextRun({ text: "Guia Completa de Produccion de Video", size: 32, font: "Arial", color: "3D3D8F" })]
      }),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 60, after: 480 },
        children: [new TextRun({ text: "Sitio Web Activado por Desplazamiento (Scroll-Driven)", size: 24, font: "Arial", color: "888888", italics: true })]
      }),
      new Paragraph({ children: [new TextRun("")], pageBreakBefore: true }),

      // INTRO
      heading1("INTRODUCCION"),
      body("Este documento contiene todos los prompts necesarios para generar el video scroll-driven del sitio web de Octupass, una agencia de desarrollo web cuyo logo es un pulpo."),
      spacer(),
      body("El video se divide en 6 clips. Cada clip tiene:"),
      body("  • Un fotograma de inicio (para generar con IA de imagen)"),
      body("  • Un fotograma de fin (para generar con IA de imagen)"),
      body("  • Un prompt de animacion (para animar con Kling AI o Hailuo AI)"),
      spacer(),
      body("Herramienta recomendada para generar imagenes: ChatGPT (DALL-E 3) o Microsoft Designer (gratis)"),
      body("Herramienta recomendada para animar: Kling AI (5 videos gratis por dia)"),
      body("Herramienta para unir clips: CapCut o DaVinci Resolve (gratis)"),

      spacer(),
      spacer(),

      // HERRAMIENTAS GRATUITAS
      heading1("HERRAMIENTAS GRATUITAS RECOMENDADAS"),
      spacer(),
      toolsTable(),
      spacer(),
      body("Flujo de trabajo recomendado:", { bold: true }),
      body("  1. Genera los fotogramas de inicio y fin con ChatGPT / Microsoft Designer"),
      body("  2. Sube el fotograma de inicio a Kling AI con el prompt de animacion"),
      body("  3. Descarga cada clip (4-6 segundos por clip)"),
      body("  4. Une los 6 clips en orden en CapCut o DaVinci Resolve"),
      body("  5. Exporta el video final e integra al sitio web scroll-driven"),

      new Paragraph({ children: [new TextRun("")], pageBreakBefore: true }),

      // CLIPS
      ...clipSection(
        1, "Apertura — Las profundidades", "6633cc",
        "Cinematic shot, extreme deep ocean darkness, absolute black void, single point of bioluminescent light far in the distance, ethereal purple-violet glow, microscopic particles of light floating upward slowly, no subject visible yet, sense of infinite depth, ultra photorealistic, anamorphic lens flare, 4K, shallow depth of field, film grain, underwater atmosphere",
        "Cinematic shot, deep ocean, a massive octopus silhouette emerging slowly from darkness, body translucent and bioluminescent in deep violet and electric blue, tentacles spread wide forming a radial geometric pattern like a web or circuit board, glowing nodes at tentacle tips, eerie beauty, ultra photorealistic, underwater caustic light rays, 4K, anamorphic lens",
        "Slow cinematic camera push forward through absolute black ocean darkness, a single bioluminescent purple-violet light grows larger in the distance, microscopic light particles float upward gently, then a massive translucent octopus silhouette gradually emerges from the void, its body glowing in deep violet and electric blue, tentacles slowly spreading outward forming a radial geometric web pattern with glowing nodes at the tips, underwater caustic light rays appear, smooth and ethereal motion, 4K cinematic, film grain, anamorphic lens",
        [
          ["Imagen de entrada", "Fotograma de inicio (oscuridad con punto de luz)"],
          ["Duracion", "6 segundos"],
          ["Motion Brush", "Aplica movimiento hacia adelante en el centro"],
          ["Camera Motion", "Slow push in"],
          ["Herramienta", "Kling AI o Hailuo AI"],
        ],
        ["Si el pulpo aparece demasiado rapido, añade al inicio del prompt: 'very slow reveal, subject barely visible until the final 2 seconds'"]
      ),

      ...clipSection(
        2, "Transformacion digital", "1565c0",
        "Cinematic macro shot, octopus tentacle filling the frame, skin texture transforming, skin cells becoming glowing circuit board traces, copper and electric blue lines spreading across the tentacle surface, organic meets technological, bioluminescent nodes pulse, dark background, ultra photorealistic, 4K, macro lens, extreme detail",
        "Cinematic wide shot, the tentacle circuit lines have spread outward and constructed a glowing wireframe city skyline, neon blue and cyan grid city floating in space, each building a data node, streets are optical fiber lines, the octopus tentacle visible in foreground morphing into this digital infrastructure, ultra photorealistic, 4K, dramatic cinematic lighting",
        "Extreme macro cinematic shot, octopus tentacle skin slowly transforms, organic biological texture morphs into glowing copper and electric blue circuit board traces spreading across the surface like veins, bioluminescent nodes pulse rhythmically along the lines, camera slowly pulls back to reveal the circuit lines extending outward beyond the tentacle, the traces multiply and expand into the darkness constructing a glowing wireframe city skyline, neon blue and cyan grid buildings rise from the circuit paths, optical fiber streets light up one by one, smooth organic-to-digital transformation, 4K cinematic, dramatic lighting, anamorphic lens flare",
        [
          ["Imagen de entrada", "Fotograma de inicio (tentaculo macro)"],
          ["Duracion", "5 segundos"],
          ["Motion Brush", "Aplica movimiento expansivo desde el tentaculo hacia afuera"],
          ["Camera Motion", "Slow zoom out"],
          ["Herramienta", "Kling AI o Hailuo AI"],
        ],
        ["Si la transicion organico→digital se siente brusca, añade: 'gradual seamless morphing, the transformation happens at the midpoint of the animation'"]
      ),

      ...clipSection(
        3, "Construccion del sitio web", "1b5e20",
        "Cinematic shot, floating UI components in dark space, glowing emerald green, translucent cards, navigation bars, buttons, typography blocks and image placeholders drifting apart in zero gravity, scattered like puzzle pieces, each element emitting soft light, dark background with subtle star field, ultra photorealistic 3D render, 4K, depth of field",
        "Cinematic shot, all UI components have assembled into a stunning modern website displayed on a floating glass screen in space, website shows a bold hero section, clean grid layout, professional typography, the screen glows with emerald and white light, reflection visible on invisible floor below, octopus tentacle holding the screen in background, ultra photorealistic, 4K",
        "Cinematic shot in dark space, scattered glowing emerald green UI components floating in zero gravity, translucent cards, navigation bars, buttons and typography blocks slowly begin pulling toward a central point, each piece accelerates and snaps into place like a magnetic puzzle assembling itself, the components lock together building a stunning modern website on a floating glass screen, the screen illuminates with emerald and white light, an octopus tentacle gently holds the screen from behind, smooth assembly motion, 4K cinematic, depth of field, subtle particle effects during assembly",
        [
          ["Imagen de entrada", "Fotograma de inicio (piezas flotando dispersas)"],
          ["Duracion", "6 segundos"],
          ["Camera Motion", "Static / muy leve push in al final"],
          ["Movimiento clave", "Piezas se atraen al centro y encajan"],
          ["Herramienta", "Kling AI o Hailuo AI"],
        ],
        [
          "Si las piezas no se mueven hacia el centro, añade: 'pieces fly inward with magnetic force, fast at midpoint then slow snap into final position'",
          "Si el tentaculo no aparece, genera el fotograma de fin con el tentaculo visible y usalo como imagen de referencia secundaria en Kling"
        ]
      ),

      ...clipSection(
        4, "Los 8 poderes", "bf360c",
        "Cinematic shot, eight glowing amber-orange orbs floating in a circular formation in dark space, each orb contains a holographic icon: design palette, code brackets, SEO magnifier, mobile phone, speed lightning bolt, analytics chart, shopping cart, support headset, orbs pulse with warm light, dark cosmic background, ultra photorealistic 3D, 4K",
        "Cinematic shot, the eight orbs are now connected by glowing amber energy streams radiating from a central point, forming the shape of an octopus viewed from above, each orb is a tentacle tip, the central core blazes with bright warm light, the whole structure rotates slowly, powerful and unified, dark background, ultra photorealistic, 4K, top-down angle",
        "Cinematic top-down shot in dark space, eight glowing amber-orange orbs float in a wide circular formation, each orb pulses with warm light revealing holographic icons inside, the camera slowly descends from above, the eight orbs begin moving inward simultaneously, golden energy streams shoot from each orb toward the center connecting them like tentacles of an octopus, the streams thicken and glow brighter as the orbs lock into position, the central core explodes with a burst of bright amber light illuminating the entire formation, the complete octopus shape glows and slowly rotates, 4K cinematic, top-down angle, dramatic lighting, lens flare at the central burst",
        [
          ["Imagen de entrada", "Fotograma de inicio (8 orbes en circulo)"],
          ["Duracion", "5 segundos"],
          ["Camera Motion", "Slow descend / top-down push in"],
          ["Movimiento clave", "Orbes se acercan y las lineas los conectan"],
          ["Motion Intensity", "High (opcion en Kling AI)"],
        ],
        [
          "Si los orbes no se mueven, añade al inicio: 'orbs accelerate toward center at the 2 second mark'",
          "Si el burst central no aparece, añade al final: 'climactic light explosion at center point in the last second'",
          "Para mejor resultado en Kling AI, activa la opcion Motion Intensity: High"
        ]
      ),

      ...clipSection(
        5, "Impacto real — El cliente", "880e4f",
        "Cinematic shot, small business storefront on a rainy night street, dark and dim, shop window barely visible, no foot traffic, neon sign flickering weakly, puddles reflecting gray light, melancholy atmosphere, no digital presence, isolated, ultra photorealistic, 4K, anamorphic bokeh in background, film noir mood",
        "Cinematic shot, same small business storefront now radiant and vibrant, warm rose-pink neon glow, elegant illuminated window display, glowing digital connection lines extend from the building outward into the sky connecting to a global network grid above, people visible inside, bustling, a holographic website preview floats above the rooftop, ultra photorealistic, 4K, golden hour mood",
        "Cinematic street level shot, a small dark business storefront on a rainy night, dim and lifeless, puddles reflecting gray light, suddenly a warm rose-pink light begins glowing from inside the shop and spreads outward through the window, the neon sign stabilizes and brightens, the rain continues but the atmosphere transforms, glowing digital connection lines emerge from the rooftop shooting upward into the sky forming a global network grid above the building, a holographic website preview materializes floating above the roof, people appear inside the warmly lit shop, the entire scene transitions from melancholy darkness to vibrant warm energy, slow cinematic camera pull back, 4K, anamorphic lens, golden warm light bloom",
        [
          ["Imagen de entrada", "Fotograma de inicio (local oscuro y lluvioso)"],
          ["Duracion", "6 segundos"],
          ["Camera Motion", "Slow pull back / zoom out"],
          ["Movimiento clave", "Luz calida se expande de adentro hacia afuera"],
          ["Herramienta", "Kling AI o Hailuo AI — modo Cinematic"],
        ],
        [
          "Si la transformacion es muy brusca, añade: 'very gradual light transition, darkness fades slowly over the first 3 seconds'",
          "Si las lineas digitales no aparecen, añade: 'glowing fiber optic lines shoot upward from rooftop at the 4 second mark'",
          "Este clip es el mas emocional — en Hailuo AI prueba el modo Cinematic para mayor dramatismo"
        ]
      ),

      ...clipSection(
        6, "Reveal — Logotipo Octupass", "3f3f6f",
        "Cinematic shot, thousands of tiny luminous particles swirling in a vortex in deep space, white and soft lavender light, particles moving inward toward a gravitational center point, chaotic yet beautiful, the particles carry fragments of light like dissolved text and icons, dark background, ultra photorealistic, 4K, long exposure effect, motion blur on particles",
        "Cinematic hero shot, the particles have converged to form the word OCTUPASS in bold modern sans-serif typography, the letters made of light and energy, glowing bright white with a purple-violet halo, a stylized octopus emblem above the wordmark also formed from particles, centered in frame, majestic and powerful, dark space background, lens flare, ultra photorealistic, 4K, dramatic lighting",
        "Cinematic hero shot in deep space, thousands of tiny luminous white and lavender particles swirl in a wide chaotic vortex, the camera slowly pushes in toward the center, the particles accelerate spinning faster and faster inward, at the midpoint the vortex collapses violently toward the center with a flash of bright white light, the particles crystallize and solidify forming the bold word OCTUPASS in glowing energy typography, a stylized octopus emblem forms above the wordmark simultaneously, the letters pulse once with a purple-violet halo then stabilize radiating steady powerful light, the camera holds on the final logo centered in frame, lens flare, 4K cinematic, epic dramatic lighting, motion blur on particles during collapse",
        [
          ["Imagen de entrada", "Fotograma de inicio (particulas en vortice)"],
          ["Duracion", "6 segundos"],
          ["Camera Motion", "Slow push in hacia el centro"],
          ["Movimiento clave", "Vortice colapsa y cristaliza en el logotipo"],
          ["Motion Intensity", "High — Kling AI recomendado"],
        ],
        [
          "Si el texto OCTUPASS no se forma correctamente, genera el fotograma de fin y subelo como imagen de referencia en Kling AI",
          "Si el emblema del pulpo no aparece, añade: 'octopus silhouette emblem materializes above the text from the same particles'",
          "Este es el clip mas dificil — prueba 3 o 4 veces con distintos seeds hasta obtener el mejor resultado",
          "Para el flash de luz añade: 'blinding white light flash at exactly the 3 second mark before the logo reveals'"
        ]
      ),

      new Paragraph({ children: [new TextRun("")], pageBreakBefore: true }),

      // RESUMEN FINAL
      heading1("RESUMEN COMPLETO DEL VIDEO"),
      spacer(),
      summaryTable(),
      spacer(),
      body("Duracion total del video: 34 segundos", { bold: true }),
      spacer(),
      body("Una vez tengas los 6 clips generados, unalos en orden en CapCut o DaVinci Resolve (ambos gratuitos) y tendras el video completo listo para integrar al sitio web scroll-driven de Octupass."),
    ]
  }]
});

Packer.toBuffer(doc).then(buffer => {
  fs.writeFileSync("octupass_guia_video.docx", buffer);
  console.log("Documento creado: octupass_guia_video.docx");
});
