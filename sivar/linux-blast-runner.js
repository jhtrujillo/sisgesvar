const express = require('express');
const cors = require('cors');
const { spawn } = require('child_process');
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');

const app = express();
app.use(cors());
app.use(express.json());

// Servir la carpeta public estáticamente
app.use('/public', express.static(path.join(__dirname, 'public')));

const PORT = 3002;

// Almacén de trabajos activos para SSE
const activeJobs = new Map();

app.post('/run-blast', (req, res) => {
    try {
        const { sequence, program, database, expect } = req.body;

        if (!sequence || !program || !database) {
            return res.status(400).json({ error: 'Faltan parámetros requeridos (sequence, program, database)' });
        }

        const jobId = crypto.randomUUID();
        
        // Crear carpeta para outputs
        const outDir = path.join(__dirname, 'public', 'blast_outputs', jobId);
        if (!fs.existsSync(outDir)) {
            fs.mkdirSync(outDir, { recursive: true });
        }
        
        const queryFile = path.join(outDir, 'query.fasta');
        const outFile = path.join(outDir, 'results.json');
        
        // Guardar secuencia en archivo temporal
        let fastaContent = sequence;
        if (!sequence.startsWith('>')) {
            fastaContent = `>User_Query\n${sequence}`;
        }
        fs.writeFileSync(queryFile, fastaContent);

        // Preparamos el comando. Nota: Asegúrate de que los binarios de BLAST estén en el PATH del servidor.
        let args = [
            '-query', queryFile,
            '-db', database,
            '-outfmt', '15', // Formato JSON (single file JSON)
            '-out', outFile
        ];
        
        if (expect) {
            args.push('-evalue');
            args.push(expect.toString());
        }

        activeJobs.set(jobId, {
            logs: [],
            clients: [],
            status: 'running',
            resultFile: `/public/blast_outputs/${jobId}/results.json`
        });

        console.log(`[Job ${jobId}] Iniciando BLAST: ${program} ${args.join(' ')}`);

        // Ejecutamos el programa (blastn, blastp, blastx, tblastn, tblastx)
        const child = spawn(program, args);

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
                broadcast(job.status, { 
                    code, 
                    message: code === 0 ? 'Completado' : 'Error en el proceso',
                    resultUrl: code === 0 ? job.resultFile : null
                });
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

        res.json({ success: true, jobId, message: 'Proceso BLAST iniciado' });

    } catch (e) {
        res.status(400).json({ error: 'Error procesando la solicitud: ' + e.message });
    }
});

app.get('/blast-logs/:jobId', (req, res) => {
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
        res.write(`event: ${job.status}\ndata: ${JSON.stringify({ message: 'El proceso ya había terminado', resultUrl: job.resultFile })}\n\n`);
        return res.end();
    }

    job.clients.push(res);
    req.on('close', () => {
        job.clients = job.clients.filter(client => client !== res);
    });
});

app.listen(PORT, () => {
    console.log(`Micro-servicio Nativo de BLAST corriendo en http://localhost:${PORT}`);
});
