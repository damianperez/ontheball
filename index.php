<?php

declare(strict_types=1);

require_once "config.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title><?= PROJECT_NAME ?></title>

<meta
    name="viewport"
    content="width=device-width,initial-scale=1">

<script src="https://telegram.org/js/telegram-web-app.js"></script>

<link rel="stylesheet" href="style.css">

</head>

<body>

<header>

<h1>

Telegram WebApp Debug Studio

</h1>

<div>

Versión <?= VERSION ?>

</div>

</header>

<div id="container">

<!-- ===================================================== -->

<section class="panel">

<h2>Telegram</h2>

<table class="info">

<tr>

<td>ID</td>

<td id="tg_id">-</td>

</tr>

<tr>

<td>Nombre</td>

<td id="tg_name">-</td>

</tr>

<tr>

<td>Username</td>

<td id="tg_username">-</td>

</tr>

<tr>

<td>Idioma</td>

<td id="tg_language">-</td>

</tr>

<tr>

<td>Platform</td>

<td id="tg_platform">-</td>

</tr>

<tr>

<td>Version</td>

<td id="tg_version">-</td>

</tr>

<tr>

<td>Color</td>

<td id="tg_theme">-</td>

</tr>

<tr>

<td>Viewport</td>

<td id="tg_viewport">-</td>

</tr>

</table>

</section>

<!-- ===================================================== -->

<section class="panel">

<h2>Enviar Datos</h2>

<label>

Nombre

</label>

<input
id="nombre"
type="text">

<label>

Mensaje

</label>

<textarea
id="mensaje"
rows="5"></textarea>

<div class="buttons">

<button id="btnSendData">sendData()</button>
<button id="btnPOST">POST</button>
<button id="btnEstado">Estado</button>
<button id="btnTelegram">Enviar Bot</button>
<button id="btnClear">Limpiar</button>
<button id="btnPing">Ping</button>
<button id="btnVerify">
    Verify
</button>
<button id="btnClose" class="btn btn-danger">
    ❌ Cerrar WebApp
</button>

</div>

</section>

<!-- ===================================================== -->

<section class="panel">

<h2>Respuesta</h2>

<textarea
    id="respuesta"
    readonly
    rows="18">
</textarea>

</section>
<section class="panel">

<h2>Eventos</h2>

<div id="events">

</div>

</section>
<!-- ===================================================== -->

<section class="panel">

<h2>Debug JS</h2>

<textarea
id="debug"
readonly></textarea>

</section>

<!-- ===================================================== -->

<section class="panel">

<h2>initDataUnsafe</h2>

<pre id="initDataUnsafe"></pre>

</section>

<!-- ===================================================== -->

<section class="panel">

<h2>initData</h2>

<pre id="initData"></pre>

</section>

<!-- ===================================================== -->

<section class="panel">

<h2>Servidor</h2>

<table class="info">

<tr>

<td>PHP</td>

<td><?= PHP_VERSION ?></td>

</tr>

<tr>

<td>DEBUG</td>

<td><?= DEBUG ? "ON":"OFF" ?></td>

</tr>

<tr>

<td>Proyecto</td>

<td><?= PROJECT_NAME ?></td>

</tr>

<tr>

<td>BOT API</td>

<td><?= BOT_API ?></td>

</tr>

</table>

</section>

</div>

<footer>

Telegram WebApp Debug Studio

</footer>

<script src="ui.js"></script>
<script src="app.js"></script>

</body>

</html>