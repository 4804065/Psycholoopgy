<?php
/*
Plugin Name: PLUGIN TEST VIDEOJUEGOS
Description: PLUGIN TEST VIDEOJUEGOS
Version: 1.0
Author: Psycholoopgy
*/

defined('ABSPATH') or die('Acceso denegado');

register_activation_hook(__FILE__, function () {
    global $wpdb;
    $tabla_resultados = $wpdb->prefix . 'videojuegos_resultados';
    $tabla_respuestas = $wpdb->prefix . 'videojuegos_respuestas';
    $charset_collate = $wpdb->get_charset_collate();

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $sql1 = "CREATE TABLE $tabla_resultados (
        cliente_id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(255) NOT NULL,
        apellido varchar(255) NOT NULL,
        puntuacion int NOT NULL,
        diagnostico varchar(255) NOT NULL,
        fecha datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (cliente_id)
    ) $charset_collate;";

    $sql2 = "CREATE TABLE $tabla_respuestas (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        cliente_id mediumint(9) NOT NULL,
        pregunta_numero int NOT NULL,
        respuesta_valor int NOT NULL,
        FOREIGN KEY (cliente_id) REFERENCES $tabla_resultados(cliente_id) ON DELETE CASCADE,
        PRIMARY KEY (id)
    ) $charset_collate;";

    dbDelta($sql1);
    dbDelta($sql2);
});

add_shortcode('videojuegos', function () {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respuestas']) && isset($_POST['nombre']) && isset($_POST['apellido'])) {
        $nombre = sanitize_text_field($_POST['nombre']);
        $apellido = sanitize_text_field($_POST['apellido']);
        $respuestas = array_map('intval', $_POST['respuestas']);
        $total = array_sum($respuestas);

        if ($total <= 20) {
            $diagnostico = "No hay adicción";
        } elseif ($total <= 35) {
            $diagnostico = "Adicción leve";
        } elseif ($total <= 60) {
            $diagnostico = "Adicción moderada";
        } else {
            $diagnostico = "Adicción alta";
        }

        global $wpdb;
        $tabla_resultados = $wpdb->prefix . 'videojuegos_resultados';
        $tabla_respuestas = $wpdb->prefix . 'videojuegos_respuestas';

        $wpdb->insert($tabla_resultados, [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'puntuacion' => $total,
            'diagnostico' => $diagnostico,
            'fecha' => current_time('mysql')
        ]);

        $cliente_id = $wpdb->insert_id;

        foreach ($respuestas as $num => $valor) {
            $wpdb->insert($tabla_respuestas, [
                'cliente_id' => $cliente_id,
                'pregunta_numero' => $num + 1,
                'respuesta_valor' => $valor
            ]);
        }

        $url_autotest = 'https://psycholoopgy.com/autotest/';
        
        return "
        <div class='psicotest-result'>
            <h2>Resultado</h2>
            <p><strong>$nombre $apellido</strong>, tu puntuación es: <strong>$total</strong></p>
            <p>Diagnóstico: $diagnostico</p>
            <a href='$url_autotest' class='back-button'>Volver a los tests</a>
        </div>";
    }

    ob_start();
    ?>
    <div class="psicotest-container">
        <h2>Test de Adicción a los Videojuegos</h2>
        <form method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required style="width:100%;margin-bottom:10px;padding:8px;border:1px solid #ccc;border-radius:5px;">
            
            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" id="apellido" required style="width:100%;margin-bottom:20px;padding:8px;border:1px solid #ccc;border-radius:5px;">

            <?php
            $preguntas = [
                "1. Paso muchas más horas jugando de las que tenía planeado.*",
                "2. Pienso constantemente en los videojuegos incluso cuando no estoy jugando.*",
                "3. Me irrito o pongo ansioso/a si no puedo jugar.*",
                "4. He dejado de hacer tareas o compromisos importantes por jugar.*",
                "5. Juego para escapar de mis problemas o emociones negativas.*",
                "6. He mentido sobre cuánto tiempo paso jugando.*",
                "7. Me siento culpable o frustrado/a después de largas sesiones de juego.*",
                "8. He perdido interés en otras actividades que antes disfrutaba.*",
                "9. Mis relaciones personales se han deteriorado por mi forma de jugar.*",
                "10. He tenido discusiones familiares por el tiempo que paso jugando.*",
                "11. Me cuesta dejar de jugar aunque quiera hacerlo.*",
                "12. Juego durante la noche y me afecta el sueño o el rendimiento diario.*",
                "13. Me siento más feliz o realizado/a en los videojuegos que en la vida real.*",
                "14. Juego incluso cuando sé que debería estar haciendo otras cosas.*",
                "15. He intentado reducir el tiempo de juego sin éxito.*",
                "16. Me aíslo socialmente y paso mucho tiempo frente a la pantalla.*",
                "17. He descuidado mi salud física o mental por jugar en exceso.*",
                "18. Solo me siento bien o tranquilo/a cuando estoy jugando.*",
                "19. A veces juego para no pensar en lo que me preocupa.*",
                "20. Estoy considerando buscar ayuda profesional por mi relación con los videojuegos.*"
          ];

            $opciones = [
                "Nada de acuerdo" => 1,
                "Poco de acuerdo" => 2,
                "Algo de acuerdo" => 3,
                "Muy de acuerdo" => 4
            ];

            foreach ($preguntas as $i => $pregunta) {
    echo "<div class='pregunta-bloque' data-pregunta='$i'>";
    echo "<p><strong>$pregunta</strong></p>";
    foreach ($opciones as $texto => $valor) {
        $id = "preg_{$i}_{$valor}";
        echo "<div><input type='radio' id='$id' name='respuestas[$i]' value='$valor'>";
        echo "<label for='$id'>$texto</label></div>";
    }
    echo "</div>";
}

            ?>
            <button type="submit">Ver resultado</button>
        </form>
         <style>
        .pregunta-bloque {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }

        .pregunta-bloque.incompleta {
            border: 2px solid red;
            background-color: #ffe5e5;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('.psicotest-container form');

            form.addEventListener('submit', function (e) {
                let preguntas = document.querySelectorAll('.pregunta-bloque');
                let valido = true;

                preguntas.forEach(bloque => {
                    const radios = bloque.querySelectorAll('input[type="radio"]');
                    const respondida = Array.from(radios).some(r => r.checked);
                    
                    if (!respondida) {
                        bloque.classList.add('incompleta');
                        valido = false;
                    } else {
                        bloque.classList.remove('incompleta');
                    }
                });

                if (!valido) {
                    e.preventDefault();
                    alert('Por favor responde todas las preguntas antes de enviar.');
                }
            });
        });
    </script>
    </div>
    <?php
    return ob_get_clean();
});