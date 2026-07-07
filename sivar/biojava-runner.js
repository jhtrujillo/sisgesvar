'use strict';

/**
 * Micro-servicio BioJava — versión endurecida.
 * Correcciones de auditoría aplicadas: C-1 (auth), C-2 (CORS allow-list),
 * C-3 (/list-directory eliminado), C-4 (confinamiento de rutas E/S),
 * M-1 (ring buffer + concurrencia + timeout), M-7 (errores centrales), B-5 (límites).
 */

const express = require('express');
const cors = require('cors');
const { spawn } = require('child_process');
const fs = require('fs');
const fsp = require('fs/promises');
const path = require('path');
const crypto = require('crypto');

// ── 0. Carga opcional de .env (sin dependencias externas) ─────────────────────
(function loadDotEnv() {
  const envPath = path.join(__dirname, '.env');
  if (!fs.existsSync(envPath)) return;
  for (const line of fs.readFileSync(envPath, 'utf8').split('\n')) {
    const m = line.match(/^\s*([\w.]+)\s*=\s*(.*)\s*$/);
    if (m && !(m[1] in process.env)) {
      process.env[m[1]] = m[2].replace(/^["']|["']$/g, '');
    }
  }
})();

// ── 1. Configuración por entorno ──────────────────────────────────────────────
const CONFIG = {
  port: Number(process.env.BIOJAVA_PORT) || 3001,
  host: process.env.BIOJAVA_HOST || '127.0.0.1',            // NO exponer a 0.0.0.0
  jarPath: path.resolve(__dirname, 'biojava', 'biojava.jar'),
  dataRoot: path.resolve(process.env.BIOJAVA_DATA_ROOT || path.join(__dirname, 'data')),
  outputRoot: path.resolve(process.env.BIOJAVA_OUTPUT_ROOT || path.join(__dirname, 'public', 'results')),
  jwtSecret: process.env.BIOJAVA_JWT_SECRET,               // = JWT_SECRET de Laravel (HS256)
  allowedOrigin: (process.env.FRONTEND_ORIGIN || 'http://localhost:5173')
    .split(',').map(s => s.trim()).filter(Boolean),
  maxConcurrent: Number(process.env.BIOJAVA_MAX_JOBS) || 2,
  jobTimeoutMs: Number(process.env.BIOJAVA_JOB_TIMEOUT_MS) || 15 * 60 * 1000,
  maxLogLines: Number(process.env.BIOJAVA_MAX_LOG_LINES) || 2000,
};

if (!CONFIG.jwtSecret) {
  console.error('FATAL: falta BIOJAVA_JWT_SECRET (debe coincidir con JWT_SECRET de Laravel).');
  process.exit(1);
}
fs.mkdirSync(CONFIG.outputRoot, { recursive: true });

// ── 2. Errores tipados ────────────────────────────────────────────────────────
class AppError extends Error {
  constructor(status, message) { super(message); this.status = status; }
}

// ── 3. Seguridad de rutas (anti path-traversal) ───────────────────────────────
const ALLOWED_INPUT_EXT = new Set([
  '.gff', '.gff3', '.fasta', '.fa', '.faa', '.fna', '.vcf', '.txt', '.collinearity', '.tsv', '.cds'
]);

/** Resuelve userPath dentro de baseDir; lanza si escapa del directorio. */
function safeResolve(baseDir, userPath) {
  if (typeof userPath !== 'string' || userPath.trim() === '') {
    throw new AppError(400, 'Ruta inválida');
  }
  const resolved = path.resolve(baseDir, userPath);
  const rel = path.relative(baseDir, resolved);
  if (rel === '' || rel.startsWith('..') || path.isAbsolute(rel)) {
    throw new AppError(400, 'Ruta fuera del directorio permitido');
  }
  return resolved;
}

/** Valida que sea un archivo de entrada permitido dentro de dataRoot. */
function assertInputFile(userPath) {
  const abs = safeResolve(CONFIG.dataRoot, userPath);
  if (!ALLOWED_INPUT_EXT.has(path.extname(abs).toLowerCase())) {
    throw new AppError(400, `Extensión no permitida: ${path.basename(abs)}`);
  }
  return abs;
}

// ── 4. Verificación de JWT HS256 (reutiliza la auth de Laravel) ────────────────
function base64urlDecode(str) {
  return Buffer.from(str.replace(/-/g, '+').replace(/_/g, '/'), 'base64');
}
function timingSafeEqualStr(a, b) {
  const ba = Buffer.from(a), bb = Buffer.from(b);
  return ba.length === bb.length && crypto.timingSafeEqual(ba, bb);
}

/** Verifica firma HS256 y expiración; devuelve el payload o lanza AppError. */
function verifyJwt(token) {
  const parts = token.split('.');
  if (parts.length !== 3) throw new AppError(401, 'Token inválido');
  const [h, p, sig] = parts;
  const expected = crypto.createHmac('sha256', CONFIG.jwtSecret)
    .update(`${h}.${p}`).digest('base64')
    .replace(/=/g, '').replace(/\+/g, '-').replace(/\//g, '_');
  if (!timingSafeEqualStr(sig, expected)) throw new AppError(401, 'Firma inválida');

  let payload;
  try { payload = JSON.parse(base64urlDecode(p).toString('utf8')); }
  catch { throw new AppError(401, 'Token corrupto'); }
  if (payload.exp && Date.now() / 1000 > payload.exp) throw new AppError(401, 'Token expirado');
  return payload;
}

function authenticate(req, res, next) {
  const header = req.get('authorization') || '';
  const token = header.startsWith('Bearer ') ? header.slice(7) : '';
  try { req.user = verifyJwt(token); next(); }
  catch (e) { next(e instanceof AppError ? e : new AppError(401, 'No autorizado')); }
}

// ── 5. App + middlewares ──────────────────────────────────────────────────────
const app = express();
app.use(cors({ origin: CONFIG.allowedOrigin, methods: ['GET', 'POST'] }));
app.use(express.json({ limit: '32kb' }));
app.use('/public', express.static(path.join(__dirname, 'public')));

// ── 6. Gestor de trabajos (ring buffer + concurrencia) ────────────────────────
const activeJobs = new Map();
let runningCount = 0;

function broadcast(job, event, data) {
  const msg = `event: ${event}\ndata: ${JSON.stringify(data)}\n\n`;
  job.clients.forEach(c => c.write(msg));
}
function pushLog(job, line) {
  job.logs.push(line);
  if (job.logs.length > CONFIG.maxLogLines) job.logs.shift();
  broadcast(job, 'log', line);
}
function finishJob(job, status, payload) {
  job.status = status;
  broadcast(job, status, payload);
  job.clients.forEach(c => c.end());
  job.clients = [];
  setTimeout(() => activeJobs.delete(job.id), 30000);
}

// ── 7. Ejecución del análisis ─────────────────────────────────────────────────
app.post('/run-comp-gen', authenticate, (req, res, next) => {
  try {
    if (runningCount >= CONFIG.maxConcurrent) {
      throw new AppError(429, 'Servidor ocupado; reintente en unos minutos');
    }
    const b = req.body || {};
    if (!b.collinearity || !b.gff1 || !b.gff2) {
      throw new AppError(400, 'Faltan parámetros requeridos (collinearity, gff1, gff2)');
    }

    // Entradas: confinadas a dataRoot + allow-list de extensiones
    const args = ['-jar', CONFIG.jarPath, 'comp-gen'];
    const inputFlags = ['collinearity', 'gff1', 'gff2', 'annot1', 'annot2',
      'cds1', 'cds2', 'prot1', 'prot2', 'vcf', 'kaks'];
    for (const flag of inputFlags) {
      if (b[flag]) args.push(`--${flag}=${assertInputFile(b[flag])}`);
    }

    // Salidas: el servidor decide la ruta (el cliente NO puede escribir fuera)
    const jobId = crypto.randomUUID();
    const outDir = path.join(CONFIG.outputRoot, jobId);
    fs.mkdirSync(outDir, { recursive: true });
    args.push(`--viz=${path.join(outDir, 'visor_sintenia.html')}`);
    args.push('-o', path.join(outDir, 'reporte_comparativo.tsv'));

    // Metadatos de texto: saneados (no son rutas)
    for (const k of ['name1', 'name2', 'organism']) {
      if (b[k]) args.push(`--${k}=${String(b[k]).replace(/[^\w .\-]/g, '').slice(0, 100)}`);
    }

    const streamToken = crypto.randomBytes(24).toString('hex');
    const job = { id: jobId, streamToken, logs: [], clients: [], status: 'running' };
    activeJobs.set(jobId, job);
    runningCount++;

    const child = spawn('java', args, { cwd: outDir });   // sin shell => sin shell-injection
    const timer = setTimeout(() => child.kill('SIGKILL'), CONFIG.jobTimeoutMs);

    const onData = d => d.toString().split('\n').forEach(l => l.trim() && pushLog(job, l));
    child.stdout.on('data', onData);
    child.stderr.on('data', onData);
    child.on('error', () => {
      clearTimeout(timer); runningCount = Math.max(0, runningCount - 1);
      finishJob(job, 'error', { message: 'No se pudo iniciar el proceso' });
    });
    child.on('close', codeNum => {
      clearTimeout(timer); runningCount = Math.max(0, runningCount - 1);
      finishJob(job, codeNum === 0 ? 'success' : 'error', {
        code: codeNum,
        resultUrl: codeNum === 0 ? `/public/results/${jobId}/visor_sintenia.html` : null,
        message: codeNum === 0 ? 'Completado' : 'Error en el proceso'
      });
    });

    res.json({ success: true, jobId, streamToken });
  } catch (e) { next(e); }
});

// ── 8. SSE de logs (autorizado por streamToken de un solo trabajo) ────────────
app.get('/comp-gen-logs/:jobId', (req, res, next) => {
  const job = activeJobs.get(req.params.jobId);
  if (!job) return next(new AppError(404, 'Job no encontrado o expirado'));
  if (!timingSafeEqualStr(String(req.query.t || ''), job.streamToken)) {
    return next(new AppError(401, 'Token de stream inválido'));
  }
  res.writeHead(200, {
    'Content-Type': 'text/event-stream',
    'Cache-Control': 'no-cache',
    Connection: 'keep-alive',
    'X-Accel-Buffering': 'no'
  });
  const keepAlive = setInterval(() => res.write(': ping\n\n'), 15000);
  job.logs.forEach(l => res.write(`event: log\ndata: ${JSON.stringify(l)}\n\n`));
  if (job.status !== 'running') { clearInterval(keepAlive); return res.end(); }
  job.clients.push(res);
  req.on('close', () => {
    clearInterval(keepAlive);
    job.clients = job.clients.filter(c => c !== res);
  });
});

// ── 9. Listado confinado (reemplaza a /list-directory) ────────────────────────
app.get('/list-inputs', authenticate, async (req, res, next) => {
  try {
    const dir = req.query.path ? safeResolve(CONFIG.dataRoot, req.query.path) : CONFIG.dataRoot;
    const entries = await fsp.readdir(dir, { withFileTypes: true });
    const rel = path.relative(CONFIG.dataRoot, dir);
    res.json({
      currentPath: rel,
      files: entries
        .filter(e => e.isDirectory() || ALLOWED_INPUT_EXT.has(path.extname(e.name).toLowerCase()))
        .map(e => ({ name: e.name, isDirectory: e.isDirectory(), path: path.join(rel, e.name) }))
        .sort((a, b) => (a.isDirectory === b.isDirectory)
          ? a.name.localeCompare(b.name) : (a.isDirectory ? -1 : 1))
    });
  } catch (e) {
    next(e instanceof AppError ? e : new AppError(400, 'No se pudo leer el directorio'));
  }
});

// ── 10. Manejador de errores central (no filtra internals) ────────────────────
// eslint-disable-next-line no-unused-vars
app.use((err, req, res, next) => {
  const status = err.status || 500;
  if (status >= 500) console.error(err);
  res.status(status).json({ error: status >= 500 ? 'Error interno del servidor' : err.message });
});

app.listen(CONFIG.port, CONFIG.host, () => {
  console.log(`Micro-servicio BioJava en http://${CONFIG.host}:${CONFIG.port}`);
  console.log(`DATA_ROOT=${CONFIG.dataRoot}`);
  console.log(`OUTPUT_ROOT=${CONFIG.outputRoot}`);
});
