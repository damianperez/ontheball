/* ==========================================================
   Telegram WebApp Debug Studio
   app.js
   BLOQUE 1
   Inicialización
   ========================================================== */

"use strict";

const APP = {

    version: "1.0.0",

    tg: null,

    user: null,

    chat: null,

    startTime: Date.now(),

    polling: null,

    pollingSeconds: 5,

    lastResponse: null,

    stats: {

        sendData: 0,

        post: 0,

        estado: 0,

        telegram: 0,

        errores: 0

    }

};

/* ========================================================== */

window.addEventListener("load", initApplication);

/* ========================================================== */

function initApplication() {

    UI.init();

    UI.separator();

    UI.success("Aplicación iniciada");

    initTelegram();

    installEvents();

    startPolling();

}

/* ========================================================== */

function initTelegram() {

    if (typeof Telegram === "undefined") {

        UI.error("telegram-web-app.js NO cargado");

        return;

    }

    APP.tg = window.Telegram.WebApp;

    APP.tg.ready();

    registrarAperturaWebApp();

    APP.tg.expand();

    APP.user = APP.tg.initDataUnsafe.user || {};

    APP.chat = APP.tg.initDataUnsafe.chat || {};

    UI.success("Telegram inicializado");

    UI.showTelegram(APP.tg);

    debugTelegram();

}

async function registrarAperturaWebApp(){

    try {


        const tg =
            window.Telegram.WebApp;


        const user =
            tg.initDataUnsafe?.user || {};



        const payload = {


            evento:"WEBAPP_OPEN",


            telegram:{

                id:user.id || null,

                username:user.username || null,

                first_name:user.first_name || null

            },


            app:{

                platform:
                    tg.platform || null,

                version:
                    tg.version || null

            }


        };


    const r = await request("guardar.php", payload);

    UI.response(r);       
        


        UI.log(
            "WEBAPP_OPEN registrado"
        );


    }
    catch(e){


        UI.error(
            "Error WEBAPP_OPEN: "
            + e.message
        );


    }

}
/* ========================================================== */
function closeWebApp() {

    UI.log("Cerrando WebApp...");

    const tg = window.Telegram?.WebApp;

    if (!tg) {

        UI.error("Telegram.WebApp no disponible.");

        return;

    }

    tg.close();

}
function debugTelegram() {

    UI.separator();

    UI.log("Telegram Debug");

    UI.log("------------------------------");

    UI.log("Version : " + APP.tg.version);

    UI.log("Platform: " + APP.tg.platform);

    UI.log("Theme   : " + APP.tg.colorScheme);

    UI.log("Viewport: " + APP.tg.viewportHeight);

    UI.log("Expanded: " + APP.tg.isExpanded);

    UI.log("Closing : " + APP.tg.isClosingConfirmationEnabled);

    if (APP.user.id) {

        UI.success("Usuario: " + APP.user.id);

    }
    else {

        UI.warning("No hay usuario");

    }

}

/* ========================================================== */

function installEvents() {

    UI.el.btnSendData.onclick = sendDataNative;

    UI.el.btnPOST.onclick = sendPOST;

    UI.el.btnEstado.onclick = consultarEstado;

    UI.el.btnTelegram.onclick = enviarMensajeBot;

    UI.el.btnClear.onclick = clearDebug;

    UI.el.btnPing.onclick = pingServidor;
    UI.el.btnVerify.onclick = verifySistema;
    UI.el.btnSendData.onclick =  sendDataTelegram;
    UI.el.btnClose.onclick = closeWebApp;

    UI.success("Eventos registrados");

}

/* ========================================================== */
async function sendDataTelegram(){

    const tg =
        window.Telegram.WebApp;


    const payload = {

        evento:"SEND_DATA",

        time:
            new Date().toISOString(),

        message:
            "Hola desde Mini App"

    };


    UI.log(
        "Enviando sendData..."
    );


    tg.sendData(
        JSON.stringify(payload)
    );

}
async function verifySistema(){

    UI.separator();

    UI.log(
        "Ejecutando VERIFY"
    );


    const r =
        await request(

            "verify.php",

            {}

        );


    if(!r)
        return;


    UI.success(
        "Diagnóstico terminado"
    );


}
async function clearDebug(){

    UI.separator();

    UI.log(
        "Solicitando limpieza"
    );


    const r =
        await request(

            "clear.php",

            {}

        );


    if(!r)
        return;


    UI.success(
        "Sistema limpiado"
    );


    UI.clearLog();


}

/* ========================================================== */

function getPayload() {

    return {

        time: new Date().toISOString(),

        telegram: {

            id: APP.user.id || null,

            username: APP.user.username || "",

            first_name: APP.user.first_name || "",

            last_name: APP.user.last_name || "",

            language: APP.user.language_code || ""

        },

        form: {

            nombre: UI.get("nombre"),

            mensaje: UI.get("mensaje")

        }

    };

}

/* ========================================================== */

function printPayload(payload) {

    UI.separator();

    UI.log("Payload");

    UI.log(JSON.stringify(payload, null, 4));

}

/* ========================================================== */

async function request(url, data = {}) {

    UI.log("HTTP POST -> " + url);

    UI.busy();

    const start = performance.now();

    try {

        const response = await fetch(url, {

            method: "POST",

            headers: {

                "Content-Type": "application/json"

            },

            body: JSON.stringify(data)

        });

        const ms = Math.round(performance.now() - start);

        UI.success("HTTP " + response.status + " (" + ms + " ms)");

        const json = await response.json();

        UI.showResponse(json);

        APP.lastResponse = json;

        return json;

    }
    catch (e) {

        APP.stats.errores++;

        UI.error(e.message);

        return null;

    }
    finally {

        UI.ready();

    }

}
/* ==========================================================
   BLOQUE 2
   Comunicación
   ========================================================== */

/**
 * Enviar mediante Telegram.WebApp.sendData()
 */
function sendDataNative() {

    UI.separator();
    UI.log("Telegram.sendData()");

    if (!APP.tg) {

        UI.error("Telegram no inicializado");

        return;

    }

    const payload = getPayload();

    payload.origin = "sendData";

    APP.stats.sendData++;

    printPayload(payload);

    try {

        APP.tg.sendData(
            JSON.stringify(payload)
        );

        UI.success("sendData enviado");

    }
    catch (e) {

        APP.stats.errores++;

        UI.error(e.message);

    }

}

/* ========================================================== */

/**
 * POST normal hacia guardar.php
 */
async function sendPOST() {

    UI.separator();

    UI.log("POST -> guardar.php");

    const payload = getPayload();

    payload.origin = "post";

    APP.stats.post++;

    printPayload(payload);

   const r = await request(
    "guardar.php",
    payload
    );  

    if (!r)
        return;

    UI.success("guardar.php OK");

}

/* ========================================================== */

/**
 * Consulta estado.php
 */
async function consultarEstado() {

    UI.separator();

    UI.log("Consultando estado");

    APP.stats.estado++;

    const r = await request(

        "estado.php",

        {}

    );

    if (!r)
        return;
    UI.showResponse(r);
    if(r.data.events){
        UI.showEvents(

        r.data.events

    );

    }
    if (r.ok)
        UI.success("Estado actualizado");
    else
        UI.warning("Estado sin datos");

}

/* ========================================================== */

/**
 * Enviar mensaje usando Bot API
 */
async function enviarMensajeBot() {

    UI.separator();

    UI.log("telegram_send.php");

    APP.stats.telegram++;

    const payload = getPayload();

    payload.origin = "telegram";

    const r = await request(

        "telegram_send.php",

        payload

    );

    if (!r)
        return;

    UI.success("Mensaje enviado");

}

/* ========================================================== */

/**
 * Ping
 */
async function pingServidor() {

    UI.separator();

    UI.log("Ping");

    const start = performance.now();

    try {

        const response = await fetch(

            "verify.php"

        );

        const ms = Math.round(

            performance.now() - start

        );

        const json = await response.json();

        UI.showResponse(json);

        UI.success(

            "Ping " + ms + " ms"

        );

    }
    catch (e) {

        UI.error(e.message);

    }

}

/* ========================================================== */

/**
 * Polling automático
 */
function startPolling() {

    if (APP.polling)
        clearInterval(APP.polling);

    UI.log(

        "Polling cada "

        + APP.pollingSeconds +

        " segundos"

    );

    APP.polling = setInterval(

        consultarEstado,

        APP.pollingSeconds * 1000

    );

}

/* ========================================================== */

function stopPolling() {

    if (!APP.polling)
        return;

    clearInterval(APP.polling);

    APP.polling = null;

    UI.log("Polling detenido");

}