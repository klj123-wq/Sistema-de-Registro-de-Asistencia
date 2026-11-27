/**
 * Función centralizada para peticiones al Backend
 */
async function enviarPeticion(url, datos) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        });

        const res = await response.json();

        if (res.success) {
            // Aquí puedes llamar a una librería como Toastr o SweetAlert2
            console.log("Éxito:", res.message);
            return res.data; // Retornamos los datos limpios
        } else {
            console.error("Error del servidor:", res.message);
            // Aquí manejas el mensaje de error (ej. mostrarlo en un div rojo)
            throw new Error(res.message);
        }
    } catch (error) {
        console.error("Error en la petición:", error);
    }
}