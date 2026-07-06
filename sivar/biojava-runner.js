const express = require('express');
const cors = require('cors');
const { spawn } = require('child_process');
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

const app = express();
app.use(cors());
app.use(express.json());

// Servir la carpeta public estáticamente para poder ver los resultados generados
app.use('/public', express.static(path.join(__dirname, 'public')));

const envFilePath = path.join(__dirname, '.env.biojava');
// Buscar biojava.jar por defecto dentro de la carpeta "biojava" en la raíz de SIVAR
let jarPath = path.join(__dirname, 'biojava', 'biojava.jar');

if (fs.existsSync(envFilePath)) {
    const envContent = fs.readFileSync(envFilePath, 'utf-8');
    const match = envContent.match(/BIOJAVA_JAR_PATH=(.*)/);
    if (match && match[1]) {
        jarPath = match[1].trim();
    }
}

const PORT = 3001;

// Almacén de trabajos activos para SSE
const activeJobs = new Map();

app.post('/run-comp-gen', (req, res) => {
    try {
        const { collinearity, gff1, gff2, annot1, annot2, cds1, cds2, prot1, prot2, vcf, kaks, name1, name2, outputFile, organism, outputHTML } = req.body;

        if (!collinearity || !gff1 || !gff2 || !outputHTML) {
            return res.status(400).json({ error: 'Faltan parámetros requeridos (collinearity, gff1, gff2, outputHTML)' });
        }

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

        let args = ['-jar', jarPath, 'comp-gen'];
        args.push(`--collinearity=${collinearity}`);
        args.push(`--gff1=${gff1}`);
        args.push(`--gff2=${gff2}`);
        args.push(`--viz=${outputHTML}`);

        if (annot1) args.push(`--annot1=${annot1}`);
        if (annot2) args.push(`--annot2=${annot2}`);
        if (cds1) args.push(`--cds1=${cds1}`);
        if (cds2) args.push(`--cds2=${cds2}`);
        if (prot1) args.push(`--prot1=${prot1}`);
        if (prot2) args.push(`--prot2=${prot2}`);
        if (vcf) args.push(`--vcf=${vcf}`);
        if (kaks) args.push(`--kaks=${kaks}`);
        if (name1) args.push(`--name1=${name1}`);
        if (name2) args.push(`--name2=${name2}`);
        if (outputFile) { args.push(`-o`); args.push(outputFile); }
        if (organism) args.push(`--organism=${organism}`);

        const jobId = crypto.randomUUID();
        activeJobs.set(jobId, {
            logs: [],
            clients: [],
            status: 'running'
        });

        console.log(`[Job ${jobId}] Iniciando comando: java ${args.join(' ')}`);

        const child = spawn('java', args);

        const broadcast = (event, data) => {
            const job = activeJobs.get(jobId);
            if (!job) return;
            const message = `event: ${event}\ndata: ${JSON.stringify(data)}\n\n`;
            job.clients.forEach(client => client.write(message));
        };

        const onData = (data) => {
            const lines = data.toString().split('\n');
            const job = activeJobs.get(jobId);
            if (job) {
                lines.forEach(line => {
                    if (line.trim()) {
                        job.logs.push(line);
                        broadcast('log', line);
                    }
                });
            }
        };

        child.stdout.on('data', onData);
        child.stderr.on('data', onData);

        child.on('close', (code) => {
            console.log(`[Job ${jobId}] Finalizado con código ${code}`);
            const job = activeJobs.get(jobId);
            if (job) {
                job.status = code === 0 ? 'success' : 'error';
                broadcast(job.status, { code, message: code === 0 ? 'Completado' : 'Error en el proceso' });
                // Limpiar clientes después de unos segundos
                setTimeout(() => {
                    job.clients.forEach(client => client.end());
                    activeJobs.delete(jobId);
                }, 5000);
            }
        });
        
        child.on('error', (err) => {
            console.error(`[Job ${jobId}] Error:`, err);
            const job = activeJobs.get(jobId);
            if (job) {
                job.status = 'error';
                broadcast('error', { code: -1, message: err.message });
            }
        });

        res.json({ success: true, jobId, message: 'Proceso iniciado' });

    } catch (e) {
        res.status(400).json({ error: 'Error procesando la solicitud: ' + e.message });
    }
});

app.get('/comp-gen-logs/:jobId', (req, res) => {
    const jobId = req.params.jobId;
    const job = activeJobs.get(jobId);

    if (!job) {
        return res.status(404).json({ error: 'Job no encontrado o expirado' });
    }

    res.setHeader('Content-Type', 'text/event-stream');
    res.setHeader('Cache-Control', 'no-cache');
    res.setHeader('Connection', 'keep-alive');

    // Enviar logs pasados
    job.logs.forEach(log => {
        res.write(`event: log\ndata: ${JSON.stringify(log)}\n\n`);
    });

    if (job.status !== 'running') {
        res.write(`event: ${job.status}\ndata: ${JSON.stringify({ message: 'El proceso ya había terminado' })}\n\n`);
        return res.end();
    }

    job.clients.push(res);
    req.on('close', () => {
        job.clients = job.clients.filter(client => client !== res);
    });
});

app.get('/list-directory', (req, res) => {
  try {
    // Si no se envía path, por defecto arranca en la raíz de la máquina o SIVAR
    const defaultPath = process.platform === 'win32' ? 'C:\\' : '/';
    const targetPath = req.query.path || process.cwd(); 
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

      fileList.sort((a, b) => {
        if (a.isDirectory === b.isDirectory) return a.name.localeCompare(b.name);
        return a.isDirectory ? -1 : 1;
      });

      res.json({ currentPath: absolutePath, parentPath: path.dirname(absolutePath), files: fileList });
    });
  } catch (error) {
    res.status(500).json({ error: 'Server error', details: error.message });
  }
});

app.listen(PORT, () => {
    console.log(`Micro-servicio de BioJava corriendo en http://localhost:${PORT}`);
    console.log(`Usando BIOJAVA_JAR_PATH: ${jarPath}`);
});
