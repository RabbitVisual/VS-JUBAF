/**
 * Notification Hub Pro – Híbrido Pusher + Smart Polling.
 * Tenta Pusher primeiro; em falha ou "Limit Exceeded" usa polling (20s, count-first).
 * Vertex Solutions LTDA © 2025
 */

const POLL_INTERVAL_MS = 20000;
const UNREAD_COUNT_URL = "/api/v1/notifications/unread-count";
const LIST_URL = "/api/v1/notifications?per_page=5";
const READ_ALL_URL = "/api/v1/notifications/read-all";
const MARK_READ_URL = (id) => `/api/v1/notifications/${id}/read`;

let lastCount = null;
let lastUpdatedAt = null;
let pollTimer = null;
let connectionMode = "polling"; // 'pusher' | 'polling'

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
}

function getHeaders() {
    return {
        Accept: "application/json",
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": getCsrfToken(),
    };
}

function setMode(mode) {
    connectionMode = mode;
    if (window.NotificationSystem) window.NotificationSystem.mode = mode;
    document.body?.setAttribute("data-notification-mode", mode);
    try {
        document.dispatchEvent(new CustomEvent("notification:mode-changed", { detail: { mode } }));
    } catch (_) {}
}

function isTabVisible() {
    return typeof document !== "undefined" && document.visibilityState === "visible";
}

function updateNotificationBadge(count) {
    const badge = document.getElementById("notification-badge");
    if (badge) {
        badge.textContent = count > 99 ? "99+" : String(count);
        badge.classList.toggle("hidden", count <= 0);
        badge.classList.add("transition-all", "duration-300");
    }
    const label = document.getElementById("notification-count-label");
    if (label) {
        label.textContent = count > 0 ? `${count} não lida${count !== 1 ? "s" : ""}` : "";
        label.classList.toggle("hidden", count <= 0);
    }
}

function relativeTime(iso) {
    if (!iso) return "";
    const date = new Date(iso);
    const now = new Date();
    const sec = Math.floor((now - date) / 1000);
    if (sec < 60) return "agora";
    const min = Math.floor(sec / 60);
    if (min < 60) return `há ${min} min`;
    const h = Math.floor(min / 60);
    if (h < 24) return `há ${h} h`;
    const d = Math.floor(h / 24);
    return d === 1 ? "ontem" : `há ${d} dias`;
}

/** Ícone e cores por tipo (alinhado ao navbar Blade: bg + iconColor, sem border-l). */
function getTypeConfig(type, priority, notificationType) {
    const byCategory = {
        treasury_approval: { icon: "coins", bg: "bg-amber-100 dark:bg-amber-900/30", iconColor: "text-amber-600 dark:text-amber-400" },
        payment_completed: { icon: "coins", bg: "bg-amber-100 dark:bg-amber-900/30", iconColor: "text-amber-600 dark:text-amber-400" },
        churchcouncil_minutes: { icon: "scale-balanced", bg: "bg-violet-100 dark:bg-violet-900/30", iconColor: "text-violet-600 dark:text-violet-400" },
        churchcouncil_approval: { icon: "triangle-exclamation", bg: "bg-yellow-100 dark:bg-yellow-900/30", iconColor: "text-yellow-600 dark:text-yellow-400" },
        ebd_lesson: { icon: "graduation-cap", bg: "bg-emerald-100 dark:bg-emerald-900/30", iconColor: "text-emerald-600 dark:text-emerald-400" },
        student_leveled_up: { icon: "trophy", bg: "bg-yellow-100 dark:bg-yellow-900/30", iconColor: "text-yellow-600 dark:text-yellow-400" },
        worship_roster: { icon: "music", bg: "bg-sky-100 dark:bg-sky-900/30", iconColor: "text-sky-600 dark:text-sky-400" },
        event_registration: { icon: "calendar-check", bg: "bg-blue-100 dark:bg-blue-900/30", iconColor: "text-blue-600 dark:text-blue-400" },
        sermon_collaboration: { icon: "book-bible", bg: "bg-indigo-100 dark:bg-indigo-900/30", iconColor: "text-indigo-600 dark:text-indigo-400" },
        family_relationship_invite: { icon: "people-group", bg: "bg-emerald-100 dark:bg-emerald-900/30", iconColor: "text-emerald-600 dark:text-emerald-400" },
    };
    const base =
        (notificationType && byCategory[notificationType]) ||
        { icon: "bell", bg: "bg-gray-100 dark:bg-gray-700/50", iconColor: "text-gray-600 dark:text-gray-400" };
    const typeMap = {
        info: { icon: "circle-info", bg: "bg-blue-100 dark:bg-blue-900/30", iconColor: "text-blue-600 dark:text-blue-400" },
        success: { icon: "circle-check", bg: "bg-green-100 dark:bg-green-900/30", iconColor: "text-green-600 dark:text-green-400" },
        warning: { icon: "triangle-exclamation", bg: "bg-yellow-100 dark:bg-yellow-900/30", iconColor: "text-yellow-600 dark:text-yellow-400" },
        error: { icon: "circle-xmark", bg: "bg-red-100 dark:bg-red-900/30", iconColor: "text-red-600 dark:text-red-400" },
    };
    const fromType = typeMap[type] || typeMap.info;
    if (priority === "urgent" || priority === "high") {
        return { icon: "circle-exclamation", bg: "bg-red-100 dark:bg-red-900/30", iconColor: "text-red-600 dark:text-red-400" };
    }
    return notificationType && byCategory[notificationType] ? base : fromType;
}

function renderNotificationItem(item, allUrl) {
    const n = item.notification || {};
    const config = getTypeConfig(n.type || "info", n.priority, n.notification_type);
    const href = n.action_url || allUrl;
    const unreadClass = !item.is_read ? "bg-blue-50/50 dark:bg-blue-900/10" : "";
    const dot = !item.is_read ? '<span class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-blue-500" aria-hidden="true"></span>' : "";
    const msg = (n.message || "").length > 90 ? (n.message || "").slice(0, 90) + "…" : (n.message || "");
    const actionSpan =
        n.action_url && n.action_text
            ? `<span class="inline-flex items-center px-2.5 py-1 text-[11px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 rounded-lg">${escapeHtml(n.action_text)}</span>`
            : "";
    return `<a href="${escapeHtml(href)}" class="flex gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors ${unreadClass} border-b border-gray-100 dark:border-gray-800 last:border-b-0 notification-item" data-id="${item.id}">
      <div class="flex-shrink-0 w-9 h-9 rounded-xl ${config.bg} flex items-center justify-center ${config.iconColor}">
        <i class="fa-duotone fa-${config.icon} w-4 h-4" aria-hidden="true"></i>
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate flex items-center gap-2">${escapeHtml(n.title || "")} ${dot}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2 leading-snug">${escapeHtml(msg)}</p>
        <div class="mt-2 flex items-center justify-between gap-2 flex-wrap">
          ${actionSpan || "<span></span>"}
          <span class="text-[10px] font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wide">${relativeTime(n.created_at)}</span>
        </div>
      </div>
    </a>`;
}

function escapeHtml(s) {
    if (s == null) return "";
    const div = document.createElement("div");
    div.textContent = s;
    return div.innerHTML;
}

function renderListContainer(items, emptyMessage, allUrl) {
    const container = document.getElementById("notification-list-container");
    if (!container) return;
    if (!items || items.length === 0) {
        container.innerHTML = `
    <div class="px-6 py-12 text-center">
      <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 mb-3 text-gray-400">
        <i class="fa-duotone fa-bell-slash w-6 h-6"></i>
      </div>
      <p class="text-sm font-medium text-gray-900 dark:text-white" style="font-family: 'Poppins', sans-serif;">Nada por aqui</p>
      <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">${escapeHtml(emptyMessage)}</p>
    </div>`;
        return;
    }
    container.innerHTML = items.map((item) => renderNotificationItem(item, allUrl)).join("");
}

async function fetchUnreadCount() {
    if (!window.Laravel?.user?.id) return;
    try {
        const res = await fetch(UNREAD_COUNT_URL, { method: "GET", headers: getHeaders(), credentials: "same-origin" });
        if (!res.ok) return;
        const json = await res.json();
        const data = json?.data ?? {};
        const count = data.count ?? 0;
        const updatedAt = data.last_updated_at ?? null;
        const changed = lastCount !== count || lastUpdatedAt !== updatedAt;
        lastCount = count;
        lastUpdatedAt = updatedAt;
        updateNotificationBadge(count);
        if (changed) await fetchAndRenderList();
    } catch (_) {}
}

async function fetchAndRenderList() {
    if (!window.Laravel?.user?.id) return;
    try {
        const res = await fetch(LIST_URL, { method: "GET", headers: getHeaders(), credentials: "same-origin" });
        if (!res.ok) return;
        const json = await res.json();
        const items = json?.data ?? [];
        const allUrl = document.querySelector('[data-notifications-all-url]')?.getAttribute("data-notifications-all-url") || "#";
        renderListContainer(items, "Você está em dia com as notificações!", allUrl);
    } catch (_) {}
}

function tick() {
    if (!window.Laravel?.user?.id) return;
    if (!isTabVisible()) return;
    fetchUnreadCount();
}

function startPolling() {
    if (!window.Laravel?.user?.id) return;
    if (pollTimer) clearInterval(pollTimer);
    tick();
    pollTimer = setInterval(tick, POLL_INTERVAL_MS);
}

function stopPolling() {
    if (pollTimer) {
        clearInterval(pollTimer);
        pollTimer = null;
    }
}

/** Toast flutuante: "Nova(s) notificação(ões)" – desaparece em 5s, clicável para abrir. */
function showNotificationToast(options = {}) {
    const container = document.getElementById("notification-toast-container");
    if (!container) return;
    const title = options.title || "Nova(s) notificação(ões)";
    const url = options.url || document.querySelector('[data-notifications-all-url]')?.getAttribute("data-notifications-all-url") || "#";
    const toast = document.createElement("div");
    toast.className =
        "notification-toast pointer-events-auto flex items-center gap-3 p-4 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 animate-in fade-in slide-in-from-right-5 duration-300 mb-2 max-w-sm cursor-pointer";
    toast.style.fontFamily = "'Inter', 'Poppins', sans-serif";
    toast.innerHTML = `
      <a href="${escapeHtml(url)}" class="absolute inset-0 rounded-xl z-0" aria-label="Abrir notificações"></a>
      <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center relative z-10">
        <i class="fa-duotone fa-bell text-blue-600 dark:text-blue-400 w-5 h-5"></i>
      </div>
      <div class="flex-1 min-w-0 relative z-10">
        <p class="text-sm font-semibold text-gray-900 dark:text-white">${escapeHtml(title)}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Clique para abrir</p>
      </div>
      <button type="button" class="relative z-20 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors toast-dismiss" aria-label="Fechar">
        <i class="fa-duotone fa-xmark w-4 h-4"></i>
      </button>
    `;
    toast.style.position = "relative";
    toast.querySelector(".toast-dismiss")?.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        toast.remove();
    });
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = "0";
        toast.style.transform = "translateX(100%)";
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

function onNotificationReceived() {
    lastCount = null;
    lastUpdatedAt = null;
    fetchUnreadCount();
    if (!isTabVisible()) showNotificationToast({ title: "Nova(s) notificação(ões)" });
}

function tryPusher() {
    const key = window.Laravel?.pusherKey;
    const userId = window.Laravel?.user?.id;
    if (!key || !userId) {
        setMode("polling");
        startPolling();
        return;
    }
    import("pusher-js").then(({ default: Pusher }) => {
        return import("laravel-echo").then(({ default: Echo }) => {
            window.Pusher = Pusher;
            const echo = new Echo({
                broadcaster: "pusher",
                key: key,
                cluster: window.Laravel?.pusherCluster || "mt1",
                wsHost: window.Laravel?.pusherHost || undefined,
                wsPort: window.Laravel?.pusherPort || undefined,
                wssPort: window.Laravel?.pusherPort || undefined,
                forceTLS: (window.Laravel?.pusherScheme || "https") === "https",
                authEndpoint: "/broadcasting/auth",
                auth: {
                    headers: {
                        "X-CSRF-TOKEN": getCsrfToken(),
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                },
            });
            echo.private(`user.${userId}`).listen(".notification.created", () => {
                setMode("pusher");
                stopPolling();
                onNotificationReceived();
            });
            const pusher = echo.connector?.pusher;
            if (pusher && pusher.connection) {
                pusher.connection.bind("state_change", (states) => {
                    if (states.current === "connected") {
                        setMode("pusher");
                        stopPolling();
                    } else if (states.current === "failed" || states.current === "unavailable") {
                        setMode("polling");
                        startPolling();
                    }
                });
                pusher.connection.bind("error", (err) => {
                    if (err?.data?.message && String(err.data.message).toLowerCase().includes("limit")) {
                        setMode("polling");
                        startPolling();
                    }
                });
            }
            fetchUnreadCount();
        });
    }).catch(() => {
        setMode("polling");
        startPolling();
    });
}

export function initNotifications() {
    if (!window.Laravel?.user?.id) return;
    setMode("polling");
    startPolling();
    tryPusher();
}

export function loadUnreadNotifications() {
    fetchUnreadCount();
}

export async function markNotificationAsRead(notificationId) {
    try {
        const res = await fetch(MARK_READ_URL(notificationId), {
            method: "POST",
            headers: getHeaders(),
            credentials: "same-origin",
        });
        const data = await res.json();
        if (data?.data?.success) {
            lastCount = null;
            lastUpdatedAt = null;
            fetchUnreadCount();
        }
        return { success: !!data?.data?.success, data };
    } catch (_) {
        return { success: false };
    }
}

export async function markAllAsRead() {
    try {
        const res = await fetch(READ_ALL_URL, {
            method: "POST",
            headers: getHeaders(),
            credentials: "same-origin",
        });
        const data = await res.json();
        if (data?.data?.success) {
            lastCount = 0;
            lastUpdatedAt = null;
            updateNotificationBadge(0);
            await fetchAndRenderList();
        }
        return { success: !!data?.data?.success, data };
    } catch (_) {
        return { success: false };
    }
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initNotifications);
} else {
    initNotifications();
}

window.NotificationSystem = {
    mode: connectionMode,
    init: initNotifications,
    markAsRead: markNotificationAsRead,
    markAllAsRead: markAllAsRead,
    loadUnread: loadUnreadNotifications,
};