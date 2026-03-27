/**
 * Headers para peticiones fetch que requieren CSRF (POST/PUT/PATCH/DELETE).
 * Usar en todas las llamadas fetch() a rutas Laravel para evitar 419.
 */
export function getCsrfHeaders() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...(token && { 'X-CSRF-TOKEN': token }),
    };
}
