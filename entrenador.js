let respuestaCorrectaActual = "";
let enunciadoActual = "";
let temaActual = "Álgebra";
let idUsuario = 1; 

async function pedirEjercicio() {
    const feedbackDiv = document.getElementById("feedback");
    const zonaEjercicio = document.getElementById("zona-ejercicio");
    
    feedbackDiv.style.display = "block";
    feedbackDiv.style.backgroundColor = "#e2e8f0";
    feedbackDiv.style.color = "#334155";
    feedbackDiv.innerText = "Pensando el ejercicio... Espera un cachito.";
    zonaEjercicio.style.display = "none";
    
    try {
        const respuesta = await fetch("./generar_ejercicio.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ 
                tema: temaActual, 
                dificultad: "Intermedio" 
            })
        });

        const data = await respuesta.json();

        if(data.opcion_a === "-") {
            feedbackDiv.style.backgroundColor = "#f8d7da";
            feedbackDiv.style.color = "#842029";
            feedbackDiv.innerText = data.enunciado; 
            return;
        }

        respuestaCorrectaActual = data.respuesta_correcta;
        enunciadoActual = data.enunciado;

        document.getElementById("enunciado").innerText = data.enunciado;
        
        const contenedorOpciones = document.getElementById("opciones");
        contenedorOpciones.innerHTML = `
            <label class="opcion"><input type="radio" name="alternativa" value="A"> A) ${data.opcion_a}</label>
            <label class="opcion"><input type="radio" name="alternativa" value="B"> B) ${data.opcion_b}</label>
            <label class="opcion"><input type="radio" name="alternativa" value="C"> C) ${data.opcion_c}</label>
            <label class="opcion"><input type="radio" name="alternativa" value="D"> D) ${data.opcion_d}</label>
            <button onclick="revisarRespuesta()">Enviar Respuesta</button>
        `;

        zonaEjercicio.style.display = "block";
        feedbackDiv.style.display = "none";
    } catch (error) {
        feedbackDiv.style.backgroundColor = "#f8d7da";
        feedbackDiv.style.color = "#842029";
        feedbackDiv.innerText = "Error al conectar con el servidor PHP.";
    }
}

async function revisarRespuesta() {
    const seleccion = document.querySelector('input[name="alternativa"]:checked');
    const feedbackDiv = document.getElementById("feedback");

    if (!seleccion) {
        alert("¡Oye, marca una alternativa primero!");
        return;
    }

    feedbackDiv.style.display = "block";
    feedbackDiv.style.backgroundColor = "#e2e8f0";
    feedbackDiv.style.color = "#334155";
    feedbackDiv.innerText = "Revisando con la IA...";

    try {
        const respuesta = await fetch("./evaluar_respuesta.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                id_usuario: idUsuario,
                tema: temaActual,
                enunciado: enunciadoActual,
                respuesta_usuario: seleccion.value,
                respuesta_correcta: respuestaCorrectaActual
            })
        });

        const data = await respuesta.json();
        
        if (data.correcto) {
            feedbackDiv.style.backgroundColor = "#d1e7dd";
            feedbackDiv.style.color = "#0f5132";
        } else {
            feedbackDiv.style.backgroundColor = "#f8d7da";
            feedbackDiv.style.color = "#842029";
        }
        feedbackDiv.innerText = data.mensaje;
    } catch (error) {
        feedbackDiv.style.backgroundColor = "#f8d7da";
        feedbackDiv.style.color = "#842029";
        feedbackDiv.innerText = "Hubo un problema al evaluar tu respuesta.";
    }
}