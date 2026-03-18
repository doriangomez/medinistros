<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Simulador omnicanal de cotizaciones | Medinistros</title>
  <style>
    :root {
      --bg: #0f172a;
      --card: #111827;
      --muted: #94a3b8;
      --text: #e2e8f0;
      --primary: #22c55e;
      --secondary: #38bdf8;
      --warn: #f59e0b;
      --danger: #ef4444;
      --border: #1f2937;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
      color: var(--text);
      background: radial-gradient(circle at top right, #1e293b, var(--bg) 45%);
    }
    header {
      padding: 18px 24px;
      border-bottom: 1px solid var(--border);
      background: rgba(2, 6, 23, 0.8);
      position: sticky;
      top: 0;
      z-index: 4;
      backdrop-filter: blur(8px);
    }
    header h1 { margin: 0; font-size: 1.2rem; }
    header p { margin: 4px 0 0; color: var(--muted); }
    .layout {
      display: grid;
      grid-template-columns: 48% 52%;
      min-height: calc(100vh - 88px);
    }
    .panel {
      padding: 16px;
      border-right: 1px solid var(--border);
    }
    .right { border-right: none; }
    .card {
      background: linear-gradient(180deg, rgba(17, 24, 39, 0.95), rgba(2, 6, 23, 0.95));
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 12px;
      margin-bottom: 12px;
      box-shadow: 0 0 0 1px rgba(255,255,255,0.02) inset;
    }
    .card h3 { margin: 0 0 8px; font-size: 0.95rem; }
    .sub { color: var(--muted); font-size: 0.85rem; margin: 0 0 10px; }
    .channels {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
    }
    .mail-item, .call-item, .internal-item {
      border: 1px solid #243042;
      border-radius: 8px;
      padding: 8px;
      margin-bottom: 8px;
      cursor: pointer;
      transition: .15s;
    }
    .mail-item:hover, .call-item:hover, .internal-item:hover {
      border-color: var(--secondary);
      transform: translateY(-1px);
    }
    .mail-detail {
      white-space: pre-line;
      border-top: 1px dashed #334155;
      margin-top: 8px;
      padding-top: 8px;
      color: #cbd5e1;
      min-height: 110px;
    }
    .chat {
      max-height: 240px;
      overflow: auto;
      padding-right: 4px;
    }
    .bubble {
      display: inline-block;
      max-width: 88%;
      padding: 7px 10px;
      border-radius: 10px;
      margin: 4px 0;
      font-size: 0.88rem;
      line-height: 1.3;
    }
    .from-client { background: #164e63; }
    .from-team { background: #1f2937; }
    .controls {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      margin-top: 10px;
    }
    button {
      border: 1px solid #334155;
      background: #0b1220;
      color: var(--text);
      border-radius: 8px;
      padding: 8px 10px;
      cursor: pointer;
      font-size: 0.85rem;
    }
    button:hover { border-color: var(--secondary); }
    .btn-main { background: #052e1f; border-color: #14532d; }
    .kpis {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 8px;
    }
    .kpi {
      border: 1px solid #243042;
      border-radius: 8px;
      padding: 8px;
      background: #0b1220;
    }
    .kpi strong { display: block; font-size: 1rem; }
    .kpi span { color: var(--muted); font-size: 0.75rem; }
    .timeline {
      max-height: 250px;
      overflow: auto;
      border: 1px solid #243042;
      border-radius: 8px;
      padding: 8px;
      background: #0a101c;
    }
    .event {
      border-left: 3px solid #334155;
      padding: 6px 8px;
      margin-bottom: 7px;
      background: rgba(15, 23, 42, 0.7);
      font-size: 0.82rem;
    }
    .event[data-state="aplicado"] { border-color: var(--primary); }
    .event[data-state="pendiente validación"] { border-color: var(--warn); }
    .event[data-state="alerta"] { border-color: var(--danger); }
    .alerts .alert {
      border-radius: 8px;
      padding: 7px 9px;
      margin-bottom: 7px;
      font-size: 0.84rem;
      border: 1px solid;
    }
    .alert.high { background: #3f1212; border-color: #7f1d1d; }
    .alert.medium { background: #422006; border-color: #92400e; }
    .quote {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      font-size: 0.86rem;
    }
    .field {
      border: 1px solid #243042;
      border-radius: 8px;
      padding: 8px;
      background: #0b1220;
    }
    .status-pill {
      display: inline-block;
      border-radius: 999px;
      padding: 4px 9px;
      font-size: .77rem;
      font-weight: 600;
      border: 1px solid #334155;
      background: #0b1220;
    }
    .state-bad { color: #fecaca; border-color: #7f1d1d; background: #2d0f0f; }
    .state-mid { color: #fef3c7; border-color: #92400e; background: #3b220c; }
    .state-good { color: #bbf7d0; border-color: #166534; background: #0e2818; }
    @media (max-width: 1080px) {
      .layout { grid-template-columns: 1fr; }
      .panel { border-right: none; }
      .kpis { grid-template-columns: repeat(2, 1fr); }
      .quote { grid-template-columns: 1fr; }
      .channels { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
<header>
  <h1>Simulador funcional omnicanal de cotizaciones · Medinistros</h1>
  <p>Demostración "antes vs después": entradas dispersas vs consolidación con trazabilidad y control.</p>
</header>
<main class="layout">
  <section class="panel">
    <div class="card">
      <h3>Fase 1 · Recepción multicanal (operación actual)</h3>
      <p class="sub">Cada fuente llega con nivel de estructura y confiabilidad distinto.</p>
      <div class="controls">
        <button class="btn-main" id="loadBase">Cargar cotización base</button>
        <button id="nextEvent">Simular siguiente entrada</button>
        <button id="runAll">Ejecutar flujo completo</button>
        <button id="reset">Reiniciar</button>
      </div>
    </div>

    <div class="channels">
      <div class="card">
        <h3>📧 Bandeja de correo</h3>
        <div id="mailList"></div>
        <div class="mail-detail" id="mailDetail">Seleccione un correo para leer su contenido.</div>
      </div>

      <div class="card">
        <h3>💬 WhatsApp</h3>
        <div class="chat" id="chatBox"></div>
      </div>

      <div class="card">
        <h3>📞 Llamadas</h3>
        <div id="callList"></div>
      </div>

      <div class="card">
        <h3>🧾 Acciones internas</h3>
        <div id="internalList"></div>
      </div>
    </div>
  </section>

  <section class="panel right">
    <div class="card">
      <h3>Fase 2-4 · Motor de solución (interpretación + consolidación + control)</h3>
      <p class="sub">La plataforma convierte mensajes en eventos estructurados y alerta contradicciones.</p>
      <div class="kpis">
        <div class="kpi"><strong id="kpiEvents">0</strong><span>Eventos totales</span></div>
        <div class="kpi"><strong id="kpiChannels">0</strong><span>Canales involucrados</span></div>
        <div class="kpi"><strong id="kpiAmbiguous">0</strong><span>Eventos ambiguos</span></div>
        <div class="kpi"><strong id="kpiConflicts">0</strong><span>Inconsistencias</span></div>
      </div>
    </div>

    <div class="card">
      <h3>Timeline unificado</h3>
      <div class="timeline" id="timeline"></div>
    </div>

    <div class="card alerts">
      <h3>Detección de inconsistencias</h3>
      <div id="alertList"><p class="sub">Sin alertas por ahora.</p></div>
    </div>

    <div class="card">
      <h3>Fase 5 · Cotización consolidada sugerida</h3>
      <p class="sub">Estado del proceso: <span class="status-pill state-mid" id="processState">Recibiendo cambios</span></p>
      <div class="quote" id="quoteView"></div>
      <div class="controls">
        <button id="showWithout">Ver resultado sin solución</button>
        <button id="showWith">Ver resultado con solución</button>
      </div>
      <p class="sub" id="businessMessage"></p>
    </div>
  </section>
</main>

<script>
const baseQuote = {
  cliente: "Clínica XYZ",
  referencia: "Monitor multiparámetro M-900",
  cantidad: 10,
  moneda: "COP",
  descuento: "3%",
  condiciones: "Entrega 12 días / pago 30 días",
  prioridad: "Media",
  observaciones: "Versión base cargada"
};

const eventFeed = [
  {
    channel: "Correo",
    type: "Cambio de cantidad",
    raw: "Actualizar cantidad de 10 a 20 unidades para urgencias.",
    apply: q => q.cantidad = 20,
    status: "aplicado",
    ambiguity: false,
    documentary: true,
    at: "10:01"
  },
  {
    channel: "WhatsApp",
    type: "Cambio ambiguo",
    raw: "súbele eso a 20 y urge hoy",
    apply: q => q.prioridad = "Alta",
    status: "pendiente validación",
    ambiguity: true,
    documentary: false,
    at: "10:03"
  },
  {
    channel: "Llamada",
    type: "Descuento verbal",
    raw: "Cliente aprueba 5% de descuento verbalmente.",
    apply: q => q.descuento = "5% (pendiente confirmación)",
    status: "pendiente validación",
    ambiguity: false,
    documentary: false,
    at: "10:06"
  },
  {
    channel: "Interno",
    type: "Ajuste comercial",
    raw: "Comercial cambia moneda a USD por confusión de versión.",
    apply: q => q.moneda = "USD",
    status: "alerta",
    ambiguity: false,
    documentary: true,
    at: "10:08"
  },
  {
    channel: "Correo",
    type: "Corrección formal",
    raw: "Moneda correcta COP. Mantener descuento y agregar observación de prioridad alta.",
    apply: q => { q.moneda = "COP"; q.observaciones = "Cliente prioriza despacho hoy"; },
    status: "aplicado",
    ambiguity: false,
    documentary: true,
    at: "10:12"
  },
  {
    channel: "WhatsApp",
    type: "Cambio de referencia sin contexto",
    raw: "quita una referencia",
    apply: q => q.referencia = "Por validar con cliente",
    status: "pendiente validación",
    ambiguity: true,
    documentary: false,
    at: "10:15"
  }
];

const emailSamples = [
  { from: "compras@clinicaxyz.com", subject: "Ajuste de cantidades", time: "10:01", body: "Solicitamos actualizar cantidad a 20 unidades.\nMantener condiciones comerciales base." },
  { from: "operaciones@clinicaxyz.com", subject: "Corrección moneda", time: "10:12", body: "El proceso queda en COP.\nSe mantiene descuento ofrecido y prioridad alta." }
];
const calls = [
  { who: "Llamada cliente", note: "Aprueba descuento 5% verbal", time: "10:06" }
];
const internalActions = [
  { who: "Ejecutivo comercial", note: "Ajusta moneda a USD sin correo de soporte", time: "10:08" }
];
const chats = [
  { from: "client", text: "súbele eso a 20", time: "10:03" },
  { from: "client", text: "urge hoy", time: "10:03" },
  { from: "client", text: "quita una referencia", time: "10:15" }
];

let quote = {};
let appliedEvents = [];
let pointer = 0;

function resetState() {
  quote = JSON.parse(JSON.stringify(baseQuote));
  appliedEvents = [];
  pointer = 0;
  renderAll();
}

function loadBase() {
  quote = JSON.parse(JSON.stringify(baseQuote));
  appliedEvents = [];
  pointer = 0;
  renderQuote("with");
  renderTimeline();
  renderAlerts();
  updateKpis();
  updateState();
}

function processNext() {
  if (pointer >= eventFeed.length) return;
  const ev = eventFeed[pointer++];
  ev.apply(quote);
  appliedEvents.push(ev);
  detectRules();
  renderAll();
}

function runAll() {
  while (pointer < eventFeed.length) processNext();
}

function detectRules() {
  // reglas implícitas vía generación de alertas en renderAlerts
}

function renderMail() {
  const list = document.getElementById("mailList");
  list.innerHTML = "";
  emailSamples.forEach(mail => {
    const div = document.createElement("div");
    div.className = "mail-item";
    div.innerHTML = `<strong>${mail.subject}</strong><br><small>${mail.from} · ${mail.time}</small>`;
    div.onclick = () => {
      document.getElementById("mailDetail").textContent = mail.body;
    };
    list.appendChild(div);
  });
}

function renderChat() {
  const chat = document.getElementById("chatBox");
  chat.innerHTML = "";
  chats.forEach(m => {
    const b = document.createElement("div");
    b.className = `bubble ${m.from === "client" ? "from-client" : "from-team"}`;
    b.textContent = `${m.time} · ${m.text}`;
    chat.appendChild(b);
    chat.appendChild(document.createElement("br"));
  });
}

function renderCalls() {
  const node = document.getElementById("callList");
  node.innerHTML = calls.map(c => `<div class="call-item"><strong>${c.who}</strong><br><small>${c.note} · ${c.time}</small></div>`).join("");
}

function renderInternals() {
  const node = document.getElementById("internalList");
  node.innerHTML = internalActions.map(c => `<div class="internal-item"><strong>${c.who}</strong><br><small>${c.note} · ${c.time}</small></div>`).join("");
}

function renderTimeline() {
  const node = document.getElementById("timeline");
  node.innerHTML = appliedEvents.length ? "" : '<p class="sub">Aún no hay eventos procesados.</p>';
  appliedEvents.forEach(ev => {
    const e = document.createElement("div");
    e.className = "event";
    e.dataset.state = ev.status;
    e.innerHTML = `<strong>${ev.at} | ${ev.channel}</strong> | ${ev.type}<br>${ev.raw}<br><small>Estado: ${ev.status}</small>`;
    node.appendChild(e);
  });
}

function collectAlerts() {
  const alerts = [];
  const hasUSD = appliedEvents.some(e => e.channel === "Interno" && e.raw.includes("USD"));
  const hasCOP = appliedEvents.some(e => e.channel === "Correo" && e.raw.includes("COP"));
  if (hasUSD && hasCOP) alerts.push({ level: "high", text: "Moneda contradictoria entre canal interno y correo formal." });
  if (appliedEvents.some(e => e.type === "Descuento verbal")) alerts.push({ level: "medium", text: "Descuento aprobado solo verbalmente: requiere soporte documental." });
  if (appliedEvents.some(e => e.type === "Cambio ambiguo")) alerts.push({ level: "medium", text: "Inconsistencia detectada en cantidad/prioridad por mensaje ambiguo de WhatsApp." });
  if (appliedEvents.some(e => e.type === "Cambio de referencia sin contexto")) alerts.push({ level: "high", text: "Referencia modificada sin detalle suficiente." });
  return alerts;
}

function renderAlerts() {
  const alerts = collectAlerts();
  const node = document.getElementById("alertList");
  if (!alerts.length) {
    node.innerHTML = '<p class="sub">Sin alertas por ahora.</p>';
    return;
  }
  node.innerHTML = alerts.map(a => `<div class="alert ${a.level}">${a.text}</div>`).join("");
}

function updateKpis() {
  const channels = new Set(appliedEvents.map(e => e.channel));
  const ambiguos = appliedEvents.filter(e => e.ambiguity).length;
  const inconsistencias = collectAlerts().length;
  document.getElementById("kpiEvents").textContent = appliedEvents.length;
  document.getElementById("kpiChannels").textContent = channels.size;
  document.getElementById("kpiAmbiguous").textContent = ambiguos;
  document.getElementById("kpiConflicts").textContent = inconsistencias;
}

function updateState() {
  const alerts = collectAlerts().length;
  const state = document.getElementById("processState");
  let text = "Recibiendo cambios", cls = "state-mid";
  if (appliedEvents.length > 0) text = "En consolidación";
  if (alerts > 0) { text = "Con inconsistencias"; cls = "state-bad"; }
  if (appliedEvents.length === eventFeed.length && alerts <= 2) { text = "Lista para revisión"; cls = "state-good"; }
  if (appliedEvents.length === eventFeed.length && alerts === 0) { text = "Lista para emitir"; cls = "state-good"; }
  state.textContent = text;
  state.className = `status-pill ${cls}`;
}

function renderQuote(mode = "with") {
  const node = document.getElementById("quoteView");
  const alerts = collectAlerts();
  const without = {
    ...quote,
    moneda: "USD/COP (sin confirmar)",
    descuento: "5% verbal no validado",
    observaciones: "Riesgo alto: datos de múltiples canales sin consolidación"
  };
  const q = mode === "without" ? without : quote;
  node.innerHTML = Object.entries(q).map(([k, v]) => `<div class="field"><strong>${k[0].toUpperCase() + k.slice(1)}</strong><br>${v}</div>`).join("");
  document.getElementById("businessMessage").textContent = mode === "without"
    ? "Resultado sin solución: alta probabilidad de retrabajo, errores y trazabilidad incompleta."
    : `Resultado con solución: versión consolidada con ${alerts.length} alerta(s) visibles para revisión controlada.`;
}

function renderAll() {
  renderMail();
  renderChat();
  renderCalls();
  renderInternals();
  renderTimeline();
  renderAlerts();
  updateKpis();
  updateState();
  renderQuote("with");
}

document.getElementById("loadBase").onclick = loadBase;
document.getElementById("nextEvent").onclick = processNext;
document.getElementById("runAll").onclick = runAll;
document.getElementById("reset").onclick = resetState;
document.getElementById("showWithout").onclick = () => renderQuote("without");
document.getElementById("showWith").onclick = () => renderQuote("with");

resetState();
</script>
</body>
</html>
