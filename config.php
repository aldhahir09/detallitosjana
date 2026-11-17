<?php
/**
 * Configuración central del proyecto.
 * Edita SOLO este archivo para adaptar el sitio a tu servidor.
 */

// --- Base de datos ---
const DB_HOST = 'localhost';
const DB_NAME = 'detallitos_jana';
const DB_USER = 'root';
const DB_PASS = '';

// --- Login con Google ---
// Crea un "ID de cliente OAuth" en https://console.cloud.google.com/apis/credentials
// y pégalo aquí. Mientras tenga el valor de ejemplo, el botón de Google no funcionará.
const GOOGLE_CLIENT_ID = 'TU_GOOGLE_CLIENT_ID.apps.googleusercontent.com';

// --- Recuperación de contraseña ---
// URL pública del sitio SIN barra final (ej. 'https://detallitosjana.com').
// Se usa para armar el enlace del correo. Déjalo vacío solo en desarrollo.
const BASE_URL = '';
const CORREO_REMITENTE = 'no-responder@detallitosjana.com';
// En desarrollo (XAMPP) normalmente no hay servidor de correo: con esto en true,
// el enlace de recuperación se muestra en pantalla SOLO cuando entras desde
// localhost. En producción nunca se muestra, aunque esté en true.
const MODO_DESARROLLO = true;
