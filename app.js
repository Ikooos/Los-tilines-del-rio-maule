document.addEventListener('DOMContentLoaded', () => {
    
    const contenedorTemas = document.getElementById('lista-temas');

    fetch('api_temas.php')
        .then(respuesta => respuesta.json())
        .then(datos => {
         
            contenedorTemas.innerHTML = '';

        
            if(datos.length === 0) {
                contenedorTemas.innerHTML = '<p>No hay temas todavía. Anda a phpMyAdmin e inserta algunos.</p>';
                return;
            }

      
            datos.forEach(tema => {
                const div = document.createElement('div');
                div.className = 'tarjeta-tema';
                div.innerHTML = `
                    <h3>${tema.nombre}</h3>
                    <p>${tema.descripcion}</p>
                `;
                
              
                div.addEventListener('click', () => {
                    alert(`¡Elegiste ${tema.nombre}! Preparando IA...`);
                });

                contenedorTemas.appendChild(div);
            });
        })
        .catch(error => {
            console.error("Hubo un problema con la conexión:", error);
            contenedorTemas.innerHTML = '<p>No nos pudimos conectar a la base de datos.</p>';
        });
});