const express = require('express');
const cors = require('cors');
const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');

const app = express();
app.use(cors());
app.use(express.json());

// Lee de un archivo .env si existe de manera muy simple
const envFilePath = path.join(__dirname, '.env.biojava');
let jarPath = '/Users/estuvar4/Documents/2. software/17.biojava/target/biojava.jar'; // default fallback
if (fs.existsSync(envFilePath)) {
    const envContent = fs.readFileSync(envFilePath, 'utf-8');
    const match = envContent.match(/BIOJAVA_JAR_PATH=(.*)/);
    if (match && match[1]) {
        jarPath = match[1].trim();
    }
}

const PORT = 3001;

app.post('/run-comp-gen', (req, res) => {
    try {
        const { collinearity, gff1, gff2, annot1, annot2, cds1, cds2, prot1, prot2, vcf, kaks, name1, name2, outputFile, organism, outputHTML } = req.body;

        // Validación básica
        if (!collinearity || !gff1 || !gff2 || !outputHTML) {
            return res.status(400).json({ error: 'Faltan parámetros requeridos (collinearity, gff1, gff2, outputHTML)' });
        }

        // Asegurar que existan los directorios de salida
        const outHtmlDir = path.dirname(outputHTML);
        if (!fs.existsSync(outHtmlDir)) {
            fs.mkdirSync(outHtmlDir, { recursive: true });
        }
        
        if (outputFile) {
            const outFileDir = path.dirname(outputFile);
            if (!fs.existsSync(outFileDir)) {
                fs.mkdirSync(outFileDir, { recursive: true });
            }
        }

        // Armar comando
        let command = `java -jar "${jarPath}" comp-gen `;
        command += `--collinearity="${collinearity}" `;
        command += `--gff1="${gff1}" `;
        command += `--gff2="${gff2}" `;
        command += `--viz="${outputHTML}" `;

        if (annot1) command += `--annot1="${annot1}" `;
        if (annot2) command += `--annot2="${annot2}" `;
        if (cds1) command += `--cds1="${cds1}" `;
        if (cds2) command += `--cds2="${cds2}" `;
        if (prot1) command += `--prot1="${prot1}" `;
        if (prot2) command += `--prot2="${prot2}" `;
        if (vcf) command += `--vcf="${vcf}" `;
        if (kaks) command += `--kaks="${kaks}" `;
        if (name1) command += `--name1="${name1}" `;
        if (name2) command += `--name2="${name2}" `;
        if (outputFile) command += `-o "${outputFile}" `;
        if (organism) command += `--organism="${organism}" `;

        console.log(`Ejecutando comando:\n${command}`);

        // Ejecutar el comando de sistema
        exec(command, { maxBuffer: 1024 * 1024 * 50 }, (error, stdout, stderr) => {
            if (error) {
                console.error(`Error de ejecución: ${error.message}`);
                return res.status(500).json({ error: error.message, stderr: stderr });
            }
            
            console.log('Comando finalizado con éxito.');
            res.json({ 
                success: true, 
                message: 'Análisis comp-gen completado con éxito',
                stdout: stdout,
                vizPath: outputHTML
            });
        });

    } catch (e) {
        res.status(400).json({ error: 'Error procesando la solicitud: ' + e.message });
    }
});

// Endpoint para listar archivos del servidor
app.get('/list-directory', (req, res) => {
  try {
    const targetPath = req.query.path || '/Users/estuvar4/Documents/2. software'; 
    const absolutePath = path.resolve(targetPath);

    fs.readdir(absolutePath, { withFileTypes: true }, (err, files) => {
      if (err) {
        return res.status(500).json({ error: 'Error reading directory', details: err.message });
      }

      const fileList = files.map(file => ({
        name: file.name,
        isDirectory: file.isDirectory(),
        path: path.join(absolutePath, file.name)
      }));

      // Ordenar: carpetas primero
      fileList.sort((a, b) => {
        if (a.isDirectory === b.isDirectory) return a.name.localeCompare(b.name);
        return a.isDirectory ? -1 : 1;
      });

      res.json({
        currentPath: absolutePath,
        parentPath: path.dirname(absolutePath),
        files: fileList
      });
    });
  } catch (error) {
    res.status(500).json({ error: 'Server error', details: error.message });
  }
});

app.listen(PORT, () => {
    console.log(`Micro-servicio de BioJava corriendo en http://localhost:${PORT}`);
    console.log(`Usando BIOJAVA_JAR_PATH: ${jarPath}`);
});
