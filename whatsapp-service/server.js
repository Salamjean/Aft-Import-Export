import makeWASocket, {
    useMultiFileAuthState,
    DisconnectReason,
    fetchLatestBaileysVersion
} from '@whiskeysockets/baileys';

import express from 'express';
import cors from 'cors';
import QRCode from 'qrcode';
import pino from 'pino';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

let sock = null;
let qrCodeDataURL = null;
let qrCodeRaw = null;
let connectionStatus = 'initializing'; // initializing, scan_needed, connecting, connected, disconnected
let connectedPhone = null;

const authFolder = path.join(__dirname, 'auth_info_baileys');

// Initialisation du socket WhatsApp
async function connectToWhatsApp() {
    connectionStatus = 'connecting';
    const { state, saveCreds } = await useMultiFileAuthState(authFolder);
    const { version, isLatest } = await fetchLatestBaileysVersion();

    console.log(`Initialisation de WhatsApp avec Baileys v${version.join('.')} (dernière version: ${isLatest})`);

    sock = makeWASocket({
        version,
        logger: pino({ level: 'silent' }),
        auth: state,
        browser: ['AFT Import-Export', 'Chrome', '1.0.0'],
        connectTimeoutMs: 60000,
        defaultQueryTimeoutMs: 60000,
        keepAliveIntervalMs: 25000,
    });

    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            qrCodeRaw = qr;
            qrCodeDataURL = await QRCode.toDataURL(qr, { scale: 8, margin: 2 });
            connectionStatus = 'scan_needed';
            console.log('Nouveau QR Code généré. Scannez-le sur http://localhost:' + PORT);
        }

        if (connection === 'close') {
            const statusCode = lastDisconnect?.error?.output?.statusCode;
            const shouldReconnect = statusCode !== DisconnectReason.loggedOut;
            connectionStatus = 'disconnected';
            qrCodeDataURL = null;
            connectedPhone = null;
            console.log(`Connexion fermée (code ${statusCode}). Reconnexion : ${shouldReconnect}`);

            if (shouldReconnect) {
                setTimeout(connectToWhatsApp, 3000);
            } else {
                console.log('Session déconnectée. Suppression des identifiants...');
                try {
                    fs.rmSync(authFolder, { recursive: true, force: true });
                } catch (e) {
                    console.error('Erreur lors de la suppression de la session:', e);
                }
                setTimeout(connectToWhatsApp, 2000);
            }
        } else if (connection === 'open') {
            connectionStatus = 'connected';
            qrCodeDataURL = null;
            qrCodeRaw = null;
            connectedPhone = sock.user?.id ? sock.user.id.split(':')[0] : 'Inconnu';
            console.log(`✅ Connecté avec succès à WhatsApp ! Numéro : ${connectedPhone}`);
        }
    });

    sock.ev.on('creds.update', saveCreds);
}

// Fonction intelligente pour résoudre le bon JID WhatsApp (compatible ancien 8 chiffres et nouveau 10 chiffres en Côte d'Ivoire, et international)
async function resolveWhatsAppJid(number) {
    if (!number) return null;
    let cleaned = number.toString().replace(/[^0-9]/g, '');

    const candidates = [];

    // Cas Côte d'Ivoire (225)
    if (cleaned.startsWith('225')) {
        const local = cleaned.substring(3);
        candidates.push(cleaned); // ex: 2250798278981 (13 chiffres)
        if (local.length === 10) {
            // Ancien compte WhatsApp créé avant 2021 (8 chiffres, sans le 01/05/07)
            candidates.push('225' + local.substring(2)); // ex: 22598278981 (11 chiffres)
            // Compte WhatsApp enregistré sans le 0 (9 chiffres)
            candidates.push('225' + local.substring(1)); // ex: 225798278981 (12 chiffres)
        }
    } else if (cleaned.length === 10 && (cleaned.startsWith('01') || cleaned.startsWith('05') || cleaned.startsWith('07') || cleaned.startsWith('08') || cleaned.startsWith('09'))) {
        candidates.push('225' + cleaned); // 2250798278981
        candidates.push('225' + cleaned.substring(2)); // 22598278981
        candidates.push('225' + cleaned.substring(1)); // 225798278981
    } else if (cleaned.length === 8 && !cleaned.startsWith('225')) {
        // Ancien format ivoirien direct à 8 chiffres
        candidates.push('225' + cleaned);
    }
    // Cas France (33)
    else if (cleaned.startsWith('33')) {
        const local = cleaned.substring(2);
        if (local.startsWith('0')) {
            candidates.push('33' + local.substring(1));
        } else {
            candidates.push(cleaned);
        }
    } else if (cleaned.length === 10 && (cleaned.startsWith('06') || cleaned.startsWith('07'))) {
        candidates.push('33' + cleaned.substring(1));
    } else {
        candidates.push(cleaned);
    }

    const uniqueCandidates = [...new Set(candidates)];
    console.log(`[WhatsApp] Recherche du compte actif parmi les formats :`, uniqueCandidates);

    // Vérifier avec WhatsApp lequel de ces formats existe réellement
    if (sock) {
        try {
            // Requête auprès des serveurs WhatsApp pour tester tous les candidats
            const results = await sock.onWhatsApp(...uniqueCandidates);
            console.log(`[WhatsApp] Résultat de vérification WhatsApp :`, results);

            if (results && results.length > 0) {
                const match = results.find(r => r.exists);
                if (match) {
                    console.log(`[WhatsApp] ✅ Compte valide trouvé : ${match.jid} (pour entrée : ${number})`);
                    return match.jid;
                }
            }
        } catch (e) {
            console.error('[WhatsApp] Erreur lors de onWhatsApp:', e.message);
        }
    }

    // Par défaut si non résolu par l'API
    return `${uniqueCandidates[0]}@s.whatsapp.net`;
}

// Route Page d'accueil / Interface de scan
app.get(['/', '/whatsapp'], (req, res) => {
    res.send(`
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>AFT WhatsApp Gateway</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body { background: #f0f4f8; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
            .card-panel { background: white; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); padding: 2.5rem; max-width: 500px; width: 100%; text-align: center; }
            .brand-logo { font-size: 2rem; color: #25D366; margin-bottom: 0.5rem; }
            .status-badge { display: inline-block; padding: 0.4rem 1.2rem; border-radius: 50px; font-weight: 600; font-size: 0.9rem; margin-bottom: 1.5rem; }
            .status-connected { background: #d4edda; color: #155724; }
            .status-scan { background: #fff3cd; color: #856404; }
            .status-wait { background: #e2e3e5; color: #383d41; }
            .qr-container { padding: 1rem; background: #fff; border: 2px dashed #25D366; border-radius: 12px; display: inline-block; margin-bottom: 1.5rem; }
            .qr-image { width: 260px; height: 260px; display: block; border-radius: 8px; }
            .steps { text-align: left; background: #f8f9fa; border-radius: 10px; padding: 1.2rem; margin-bottom: 1.5rem; font-size: 0.88rem; }
            .steps ol { margin: 0; padding-left: 1.2rem; }
            .steps li { margin-bottom: 0.4rem; }
        </style>
    </head>
    <body>
        <div class="card-panel">
            <div class="brand-logo"><i class="fab fa-whatsapp"></i></div>
            <h3 class="fw-bold mb-1">Passerelle WhatsApp</h3>
            <p class="text-muted small mb-3">AFT Import-Export &bull; Envoi automatique gratuit</p>
            
            <div id="statusArea">
                ${
                    connectionStatus === 'connected'
                    ? `<div class="status-badge status-connected"><i class="fas fa-check-circle me-1"></i> Connecté : +${connectedPhone}</div>
                       <p class="text-success fw-semibold">Le service est opérationnel ! Vos messages envoyés depuis Laravel partiront directement via ce numéro.</p>
                       <div>
                           <button type="button" id="btnDisconnect" onclick="disconnectWhatsApp()" class="btn btn-outline-danger btn-sm mt-3"><i class="fas fa-sign-out-alt me-1"></i> Déconnecter ce compte</button>
                       </div>`
                    : connectionStatus === 'scan_needed' && qrCodeDataURL
                    ? `<div class="status-badge status-scan"><i class="fas fa-qrcode me-1"></i> Scan du QR Code requis</div>
                       <div class="qr-container"><img class="qr-image" src="${qrCodeDataURL}" alt="QR Code WhatsApp"></div>
                       <div class="steps">
                            <strong>Comment connecter :</strong>
                            <ol>
                                <li>Ouvrez <b>WhatsApp</b> sur le téléphone de l'entreprise.</li>
                                <li>Appuyez sur <b>Menu (⋮)</b> ou <b>Réglages</b> &gt; <b>Appareils connectés</b>.</li>
                                <li>Appuyez sur <b>Connecter un appareil</b> et scannez ce code.</li>
                            </ol>
                       </div>
                       <small class="text-muted"><i class="fas fa-sync fa-spin me-1"></i> Cette page s'actualise automatiquement dès la connexion...</small>`
                    : `<div class="status-badge status-wait"><i class="fas fa-spinner fa-spin me-1"></i> Initialisation en cours...</div>
                       <p class="text-muted">Génération de la session WhatsApp en cours, veuillez patienter...</p>`
                }
            </div>
        </div>

        <script>
            function getApiUrl(endpoint) {
                const isUnderWhatsapp = window.location.pathname.includes('/whatsapp');
                return (isUnderWhatsapp ? '/whatsapp' : '') + endpoint;
            }

            function renderQr(qrData) {
                const statusArea = document.getElementById('statusArea');
                if (!statusArea) return;
                statusArea.innerHTML = \`
                    <div class="status-badge status-scan"><i class="fas fa-qrcode me-1"></i> Scan du QR Code requis</div>
                    <div class="qr-container"><img class="qr-image" src="\${qrData}" alt="QR Code WhatsApp"></div>
                    <div class="steps">
                         <strong>Comment connecter :</strong>
                         <ol>
                             <li>Ouvrez <b>WhatsApp</b> sur le téléphone de l'entreprise.</li>
                             <li>Appuyez sur <b>Menu (⋮)</b> ou <b>Réglages</b> &gt; <b>Appareils connectés</b>.</li>
                             <li>Appuyez sur <b>Connecter un appareil</b> et scannez ce code.</li>
                         </ol>
                    </div>
                    <small class="text-muted"><i class="fas fa-sync fa-spin me-1"></i> Cette page détectera automatiquement la connexion...</small>
                \`;
            }

            // Déconnexion 100% en tâche de fond sans jamais recharger ni naviguer
            async function disconnectWhatsApp() {
                if (!confirm('Voulez-vous vraiment déconnecter ce compte WhatsApp ?')) return;
                const btn = document.getElementById('btnDisconnect');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Déconnexion en cours...';
                }

                const statusArea = document.getElementById('statusArea');
                if (statusArea) {
                    statusArea.innerHTML = \`
                        <div class="status-badge status-wait"><i class="fas fa-spinner fa-spin me-1"></i> Déconnexion et génération du nouveau code...</div>
                        <p class="text-muted">Veuillez patienter quelques instants...</p>
                    \`;
                }

                try {
                    await fetch(getApiUrl('/logout'), { method: 'POST', headers: { 'Accept': 'application/json' } });
                } catch(e) {}

                // Polling toutes les 1.5s pour afficher le nouveau QR Code dès qu'il arrive
                let attempts = 0;
                const timer = setInterval(async () => {
                    attempts++;
                    try {
                        const res = await fetch(getApiUrl('/status'));
                        const data = await res.json();
                        if (data.status === 'scan_needed' && data.qrCodeDataURL) {
                            clearInterval(timer);
                            renderQr(data.qrCodeDataURL);
                        } else if (data.status === 'connected') {
                            clearInterval(timer);
                            window.location.reload();
                        }
                    } catch(err) {}

                    if (attempts > 20) {
                        clearInterval(timer);
                        window.location.reload();
                    }
                }, 1500);
            }

            // Écouteur régulier pour actualiser dès que le scan est effectué
            setInterval(async () => {
                try {
                    const res = await fetch(getApiUrl('/status'));
                    const data = await res.json();
                    if (data.status === 'connected' && !document.querySelector('.status-connected')) {
                        window.location.reload();
                    }
                } catch(e) {}
            }, 3000);
        </script>
    </body>
    </html>
    `);
});

// Route statut JSON
app.get(['/status', '/whatsapp/status'], (req, res) => {
    res.json({
        connected: connectionStatus === 'connected',
        status: connectionStatus,
        phone: connectedPhone,
        hasQr: !!qrCodeDataURL,
        qrCodeDataURL: qrCodeDataURL
    });
});

// Route de test de numéro pour vérifier la présence sur WhatsApp
app.get(['/check-number', '/whatsapp/check-number'], async (req, res) => {
    try {
        const number = req.query.number;
        if (!number) return res.status(400).json({ error: 'Paramètre number requis' });
        const jid = await resolveWhatsAppJid(number);
        const check = sock ? await sock.onWhatsApp(jid) : [];
        res.json({ number, resolvedJid: jid, exists: check?.[0]?.exists ?? false });
    } catch (e) {
        res.status(500).json({ error: e.message });
    }
});

// Route d'envoi de message (appelée par Laravel)
app.post(['/send-message', '/whatsapp/send-message'], async (req, res) => {
    try {
        const { number, message } = req.body;

        if (!number || !message) {
            return res.status(400).json({
                success: false,
                error: 'Numéro de téléphone et message obligatoires.'
            });
        }

        if (connectionStatus !== 'connected' || !sock) {
            return res.status(503).json({
                success: false,
                error: "WhatsApp n'est pas connecté. Veuillez scanner le QR Code sur http://localhost:" + PORT
            });
        }

        const jid = await resolveWhatsAppJid(number);
        if (!jid) {
            return res.status(400).json({
                success: false,
                error: 'Numéro de téléphone invalide.'
            });
        }

        // Vérification d'existence sur WhatsApp
        if (sock) {
            try {
                const check = await sock.onWhatsApp(jid);
                if (check && check.length > 0 && !check[0].exists) {
                    console.warn(`[WhatsApp] Le compte ${jid} n'existe pas sur WhatsApp.`);
                    return res.status(404).json({
                        success: false,
                        error: `Le numéro ${number} (${jid}) n'est pas enregistré sur WhatsApp.`
                    });
                }
            } catch (errCheck) {
                console.warn('[WhatsApp] Erreur lors de la vérification de l\'existence:', errCheck.message);
            }
        }

        // Envoi du message
        const sent = await sock.sendMessage(jid, { text: message });

        console.log(`[WhatsApp] Message envoyé avec succès à ${jid}`);
        return res.json({
            success: true,
            message: 'Message envoyé avec succès.',
            jid,
            messageId: sent.key.id
        });
    } catch (err) {
        console.error('[WhatsApp Error]', err);
        return res.status(500).json({
            success: false,
            error: err.message || "Erreur lors de l'envoi du message."
        });
    }
});

// Route de déconnexion (POST ou GET, avec ou sans préfixe /whatsapp)
app.all(['/logout', '/whatsapp/logout'], async (req, res) => {
    console.log('[WhatsApp] Demande de déconnexion reçue...');
    try {
        if (sock) {
            await sock.logout();
        }
    } catch (e) {
        console.warn('[WhatsApp] Erreur lors du logout:', e.message);
    }
    try {
        fs.rmSync(authFolder, { recursive: true, force: true });
        console.log('[WhatsApp] Session auth_info_baileys supprimée avec succès.');
    } catch (e) {
        console.warn('[WhatsApp] Erreur lors de la suppression de la session:', e.message);
    }
    sock = null;
    connectionStatus = 'scan_needed';
    qrCodeDataURL = null;
    qrCodeRaw = null;
    connectedPhone = null;
    setTimeout(connectToWhatsApp, 1000);

    if (req.xhr || req.headers.accept?.includes('json')) {
        return res.json({ success: true, message: 'Compte déconnecté avec succès.' });
    }
    res.redirect(req.originalUrl.includes('/whatsapp') ? '/whatsapp' : '/');
});

// Démarrage du serveur
app.listen(PORT, () => {
    console.log(`=======================================================`);
    console.log(`🚀 Passerelle WhatsApp AFT lancée sur http://localhost:${PORT}`);
    console.log(`=======================================================`);
    connectToWhatsApp();
});
