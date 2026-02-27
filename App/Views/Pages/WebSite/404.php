<!-- VISTA DE ERROR 404 -->
<!-- Este archivo se inyectará en el main_layout.php cuando el slug no se encuentre -->

<style>
    /* Estilos específicos para la vista 404 */
    .error-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
        text-align: center;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
        padding: 20px;
    }

    .error-code {
        font-size: 8rem;
        font-weight: 800;
        color: #ffb6c1; /* Rosa pastel para combinar con Yuky */
        margin: 0;
        line-height: 1;
        text-shadow: 2px 2px 0px rgba(0,0,0,0.1);
    }

    .error-message {
        font-size: 1.5rem;
        font-weight: 500;
        margin-top: 10px;
        color: #666;
    }

    .error-description {
        margin-top: 15px;
        color: #888;
        max-width: 500px;
    }

    /* Estilos para el botón de regreso */
    .btn-home {
        display: inline-block;
        margin-top: 30px;
        padding: 12px 25px;
        background-color: #ff9eb5; /* Rosa un poco más fuerte */
        color: white;
        text-decoration: none;
        border-radius: 25px;
        font-weight: bold;
        transition: background-color 0.3s ease, transform 0.2s ease;
        box-shadow: 0 4px 6px rgba(255, 158, 181, 0.3);
    }

    .btn-home:hover {
        background-color: #ff7f9c;
        transform: translateY(-2px);
    }

    /* Contenedor y animación de Yuky */
    .yuky-container {
        margin-top: 40px;
        position: relative;
        width: 150px;
        height: 150px;
    }

    .yuky-avatar {
        width: 100%;
        height: auto;
        /* Animación suave de flotación */
        animation: floatYuky 3s ease-in-out infinite; 
    }

    @keyframes floatYuky {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }

    /* Pequeña animación para la flor de Yuky */
    .yuky-flor {
        position: absolute;
        top: -10px;
        right: 40px;
        font-size: 1.5rem;
        animation: spinFlower 4s linear infinite;
    }

    @keyframes spinFlower {
        100% { transform: rotate(360deg); }
    }
</style>

<div class="error-container">
    
    <h1 class="error-code">404</h1>
    <h2 class="error-message">¡Uy! Parece que nos hemos perdido.</h2>
    <p class="error-description">
        La página que estás buscando en el sistema ASRS no existe, cambió de nombre o está temporalmente indisponible.
    </p>

    <!-- Integración de Yuky la Alpaca -->
    <div class="yuky-container" id="yuky-interactive">
        <!-- Puedes reemplazar este div con una imagen real de Yuky si la tienes (ej: <img src="/img/yuky.png" class="yuky-avatar">) -->
        <div style="font-size: 5rem; line-height: 1;" class="yuky-avatar">🦙</div>
        <div class="yuky-flor">🌸</div>
    </div>

    <a href="/" class="btn-home">Volver al Inicio</a>

</div>

<script>
    // Pequeño script interactivo: Si haces clic en Yuky, te saluda o hace algo divertido
    document.addEventListener('DOMContentLoaded', function() {
        const yuky = document.getElementById('yuky-interactive');
        const message = document.querySelector('.error-message');
        const originalMessage = message.innerText;

        if(yuky) {
            yuky.addEventListener('click', function() {
                // Cambia el mensaje temporalmente
                message.innerText = "¡Yuky dice que por aquí no es el camino!";
                message.style.color = "#ff7f9c";
                
                // Hace un pequeño salto
                yuky.style.transform = "scale(1.1) translateY(-20px)";
                yuky.style.transition = "transform 0.2s ease";

                // Vuelve a la normalidad después de 2 segundos
                setTimeout(() => {
                    message.innerText = originalMessage;
                    message.style.color = "#666";
                    yuky.style.transform = "none";
                }, 2000);
            });
            
            // Cambia el cursor para indicar que es clickeable
            yuky.style.cursor = "pointer";
        }
    });
</script>