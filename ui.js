/* ==========================================================
   Telegram WebApp Debug Studio
   ui.js
   ----------------------------------------------------------
   Manejo de toda la interfaz
   ========================================================== */

"use strict";

class UI {

    static el = {};

    /* ============================================= */

    static init() {
        console.log("=== UI.init() ===");
        const ids = [

            "tg_id",
            "tg_name",
            "tg_username",
            "tg_language",
            "tg_platform",
            "tg_version",
            "tg_theme",
            "tg_viewport",

            "nombre",
            "mensaje",

            "respuesta",
            "debug",

            "initData",
            "initDataUnsafe",

            "btnSendData",
            "btnPOST",
            "btnEstado",
            "btnTelegram",
            "btnClear",
            "events",
            "btnVerify",
            "btnHealth",
            "btnClose"

        ];

        ids.forEach(id => {

            this.el[id] = document.getElementById(id);

        });
        console.log("btnClose:", this.el.btnClose);
        this.log("UI inicializada");

    }

    /* ============================================= */

    static set(id, value) {

        if (!this.el[id]) return;

        this.el[id].textContent = value;

    }

    /* ============================================= */

    static get(id) {

        if (!this.el[id]) return "";

        return this.el[id].value;

    }

    /* ============================================= */

    static setValue(id, value) {

        if (!this.el[id]) return;

        this.el[id].value = value;

    }

    /* ============================================= */

    static json(id, obj) {

        this.set(
            id,
            JSON.stringify(obj, null, 4)
        );

    }

    /* ============================================= */

    static log(msg) {

        let box = this.el.debug;

        if (!box) return;

        const now = new Date();

        const h = now.toLocaleTimeString();

        box.value += "[" + h + "] " + msg + "\n";

        box.scrollTop = box.scrollHeight;

        console.log(msg);

    }

    /* ============================================= */

    static separator() {

        this.log("------------------------------------------");

    }

    /* ============================================= */

    static clearLog() {

        this.el.debug.value = "";

    }

    static showHealth(data) {

    let html = `
        <div class="health">
            <h3>❤️ Health Check</h3>
            <table class="health-table">
    `;

    Object.entries(data.checks).forEach(([k, v]) => {

        html += `
            <tr>
                <td>${k}</td>
                <td>${v.ok ? "🟢 OK" : "🔴 ERROR"}</td>
            </tr>
        `;

    });

    html += `
            </table>

            <hr>

            <b>Tiempo:</b> ${data.response_ms} ms<br>
            <b>Servidor:</b> ${data.server_time}

        </div>
    `;

    this.el.respuesta.innerHTML = html;

}
    /* ============================================= */

static showResponse(data, type = "auto") {

    const el = this.el.respuesta;

    if (!el) return;

    const isTextarea =
        el.tagName === "TEXTAREA";

    if (type === "auto") {

        if (typeof data === "string") {

            if (
                data.trim().startsWith("<")
            ) {
                type = "html";
            } else {
                type = "text";
            }

        } else {

            type = "json";

        }

    }

    if (isTextarea) {

        // Un textarea nunca puede mostrar HTML

        if (type === "json") {

            el.value =
                JSON.stringify(data, null, 4);

        } else {

            el.value = data;

        }

        return;

    }

    // Es un DIV

    switch (type) {

        case "html":
            el.innerHTML = data;
            break;

        case "json":
            el.textContent =
                JSON.stringify(data, null, 4);
            break;
        default:
            el.textContent = data;
    }

}
static showJson(obj) {

    this.el.respuesta.value =
        JSON.stringify(obj, null, 4);

}
static showHTML(html) {

    this.el.respuesta.innerHTML = html;

}
static response(r) {

    if (r.ok) {

        UI.success(
            r.data?.message ?? "Operación exitosa"
        );

    } else {

        UI.error(
            r.data?.error ?? "Error desconocido"
        );

    }

    if (Array.isArray(r.debug)) {

        r.debug.forEach(msg => UI.log(msg));

    }

}
    /* ============================================= */

    static showTelegram(tg) {

        const u = tg.initDataUnsafe.user || {};

        this.set("tg_id", u.id || "");

        this.set("tg_name",

            (u.first_name || "") +

            " " +

            (u.last_name || "")

        );

        this.set(

            "tg_username",

            u.username || ""

        );

        this.set(

            "tg_language",

            u.language_code || ""

        );

        this.set(

            "tg_platform",

            tg.platform

        );

        this.set(

            "tg_version",

            tg.version

        );

        this.set(

            "tg_theme",

            tg.colorScheme

        );

        this.set(

            "tg_viewport",

            tg.viewportHeight

        );

        this.json(

            "initDataUnsafe",

            tg.initDataUnsafe

        );

        this.set(

            "initData",

            tg.initData

        );

    }

    /* ============================================= */

    static enableButtons(enable = true) {

        [
        "btnHealth",
            "btnSendData",
            "btnPOST",
            "btnEstado",
            "btnTelegram",
            "btnVerify",
            "btnClear"

        ].forEach(id => {

            this.el[id].disabled = !enable;

        });

    }

    /* ============================================= */

    static busy() {

        this.enableButtons(false);

    }

    /* ============================================= */

    static ready() {

        this.enableButtons(true);

    }

    /* ============================================= */

     static success(message) {

        const el = document.getElementById("respuesta");

        if (!el) return;

        el.innerHTML = `
            <div class="alert success">
                <strong>✅ OK</strong><br>
                ${message}
            </div>
        `;

    }

    static error(message) {

        const el = document.getElementById("respuesta");

        if (!el) return;

        el.innerHTML = `
            <div class="alert error">
                <strong>❌ Error</strong><br>
                ${message}
            </div>
        `;

    }

    /* ============================================= */

    static warning(msg) {

        this.log("⚠ " + msg);

    }
    static table(obj){

    this.log("");

    for(const k in obj){

        this.log(

            k.padEnd(20,".")

            + " "

            + obj[k]

        );

    }

    this.log("");

    }
    /* ============================================= */

static showEvents(events){

    let html="";

    events.reverse().forEach((e,i)=>{

        html+=`

<div class="event"

onclick="UI.toggleEvent(${i})">

<div>

<span class="eventTime">

${e.time}

</span>

&nbsp;

<span class="eventType">

${e.type}

</span>

</div>

<pre

id="event_${i}"

class="eventJson">

${JSON.stringify(e.data,null,4)}

</pre>

</div>

`;

    });

    this.el.events.innerHTML=html;

}

/* ============================================= */

static toggleEvent(id){

    let d=document.getElementById(

        "event_"+id

    );

    if(!d)return;

    if(d.style.display=="block")

        d.style.display="none";

    else

        d.style.display="block";

}
}
