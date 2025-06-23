<?php
/*
Plugin Name: PLUGIN TEST ALCOHOLISMO
Description: PLUGIN TEST ALCOHOLISMO
Version: 1.0
Author: Psycholoopgy
*/

defined('ABSPATH') or die('Acceso denegado');

register_activation_hook(__FILE__, function () {
    global $wpdb;
    $tabla_resultados = $wpdb->prefix . 'alcoholismo_resultados';
    $tabla_respuestas = $wpdb->prefix . 'alcoholismo_respuestas';
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

add_shortcode('alcoholismo', function () {
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
        $tabla_resultados = $wpdb->prefix . 'alcoholismo_resultados';
        $tabla_respuestas = $wpdb->prefix . 'alcoholismo_respuestas';

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
        <h2>Test de Adicción al Alcoholismo</h2>
        <form method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" required style="width:100%;margin-bottom:10px;padding:8px;border:1px solid #ccc;border-radius:5px;">
            
            <label for="apellido">Apellido:</label>
            <input type="text" name="apellido" id="apellido" required style="width:100%;margin-bottom:20px;padding:8px;border:1px solid #ccc;border-radius:5px;">

            <?php
            $preguntas = [
                "1. Siento que necesito beber alcohol para relajarme o sentirme bien.",
                "2. He intentado reducir o dejar el alcohol, pero no lo he conseguido.",
                "3. Me resulta difícil controlar la cantidad que bebo una vez que empiezo.",
                "4. He tenido problemas personales, laborales o académicos por mi consumo de alcohol.",
                "5. He bebido en momentos o lugares inapropiados (por ejemplo, en el trabajo, antes de conducir).",
                "6. Me he sentido culpable o avergonzado/a después de beber.",
                "7. He mentido a otros sobre cuánto o con qué frecuencia bebo.",
                "8. He tenido lagunas mentales o pérdidas de memoria tras beber.",
                "9. He necesitado beber más cantidad que antes para sentir el mismo efecto.",
                "10. He tenido síntomas físicos o emocionales desagradables al dejar de beber.",
                "11. He bebido a solas o en secreto con frecuencia.",
                "12. El alcohol ha interferido en mis relaciones personales.",
                "13. He puesto en riesgo mi salud o la de otros al beber (por ejemplo, al conducir ebrio/a).",
                "14. Siento ansiedad o irritabilidad cuando no puedo beber.",
                "15. He dejado de  hacer actividades que antes disfrutaba por priorizar el consumo.",
                "16. Me cuesta imaginar una reunión social sin alcohol.",
                "17. El consumo de alcohol se ha convertido en parte central de mi rutina.",
                "18. He recibido advertencias o preocupaciones de familiares o amigos por mi forma de beber.",
                "19. Me levanté sintiéndome mal por haber bebido la noche anterior.",
                "20. He considerado buscar ayuda profesional por mi relación con el alcohol."
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