<?php
/*
Plugin Name: PLUGIN DE FORMULARIO DE CONTACTO
Description: PLUGIN PARA FORMULARIO DE CONTACTO
Version:     1.0
Author:      Psycholoopgy
License:     GPL2
*/

add_shortcode('formulariocontacto', 'psform');


function psform()
{
    if (isset($_POST["enviar"])) {
        $nombre = $_POST["nombre"];
        $email = $_POST["email"];
        $telefono = $_POST["telefono"];
        $mensaje = $_POST["mensaje"];

        $errores = [];

    $nombre = trim($_POST["nombre"]);
    if (empty($nombre)) {
        $errores[] = "El nombre es obligatorio";
    }

    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El email no es válido";
    }
    
    $telefono = trim($_POST["telefono"]);
    if (empty($telefono) || !preg_match('/^\+?\d[\d\s]{7,}$/', $telefono)) {
        $errores[] = "El teléfono no es válido";
    }

    $mensaje = trim($_POST["mensaje"]);
    if (empty($mensaje)) {
        $errores[] = "El mensaje es obligatorio";
    }

    if (!empty($errores)) {
        echo "<script>
            alert('Parece que ha habido un problema: " . implode(", ", $errores) . "');
            </script>";
        return;
    }

        $destinatario = "javimalo80@gmail.com";
        $asunto = "nuevo mensaje de $email";

        $contenido = '
<html>
<head>
  <meta charset="UTF-8">
  <title>Nuevo mensaje de contacto</title>
  <style>
        body {
  margin: 0;
  padding: 0;
  background: linear-gradient(135deg, #003366 0%, #005FAA 100%);
  min-height: 100vh;
}
.mail-bg {
  background: linear-gradient(135deg, #003366 0%, #005FAA 100%);
  min-height: 100vh;
}
.mail-container {
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
  overflow: hidden;
  max-width: 600px;
}
.mail-header {
  background: linear-gradient(90deg,#003366 0%,#005FAA 100%);
  padding: 32px 0 16px 0;
  text-align: center;
}
.mail-logo {
  display: block;
  margin: 0 auto 12px auto;
  border-radius: 16px;
  max-width: 120px;
}
.mail-title {
  color: #fff;
  font-family: Segoe UI,Arial,sans-serif;
  font-size: 2.2em;
  margin: 0;
  letter-spacing: 1px;
}
.mail-content {
  padding: 32px 40px 24px 40px;
}
.mail-table {
  width: 100%;
}
.mail-table td {
  padding: 12px 0;
  border-bottom: 1px solid #FFFFFF;
}
.mail-table td:last-child {
  border-bottom: none;
}
.mail-label {
  font-weight: 600;
  color: #005FAA;
  font-size: 18px;
}
.mail-value {
  color: #003366;
  font-size: 17px;
}
.mail-message {
  background: #FFFFFF;
  color: #003366;
  border-radius: 10px;
  padding: 18px 16px;
  margin-top: 8px;
  font-size: 17px;
}
.mail-footer {
  background: #FFFFFF;
  padding: 20px 40px;
  text-align: center;
  border-bottom-left-radius: 20px;
  border-bottom-right-radius: 20px;
}
.mail-footer span {
  color: #003366;
  font-size: 15px;
  font-family: Segoe UI,Arial,sans-serif;
}
.mail-footer a {
  color: #005FAA;
  text-decoration: underline;
}

  </style>
</head>
<body>
  <table width="100%" cellpadding="0" cellspacing="0" class="mail-bg">
    <tr>
      <td align="center" style="padding:40px 0;">
        <table width="600" cellpadding="0" cellspacing="0" class="mail-container">
          <tr>
            <td class="mail-header">
              <img src="https://psycholoopgy.com/wp-content/uploads/2025/04/logo-final-1.jpg" alt="Zambu Psicología" class="mail-logo">
              <h1 class="mail-title">¡Nuevo mensaje de contacto!</h1>
            </td>
          </tr>
          <tr>
            <td class="mail-content">
              <table class="mail-table">
                <tr>
                  <td>
                    <span class="mail-label">Nombre:</span>
                    <span class="mail-value">'.$nombre.'</span>
                  </td>
                </tr>
                <tr>
                  <td>
                    <span class="mail-label">Email:</span>
                    <a href="mailto:'.$email.'" class="mail-value">'.$email.'</a>
                  </td>
                </tr>
                <tr>
                  <td>
                    <span class="mail-label">Número de teléfono:</span>
                    <span class="mail-value">'.$telefono.'</span>
                  </td>
                </tr>
                <tr>
                  <td>
                    <span class="mail-label">Mensaje:</span>
                    <div class="mail-message">'.$mensaje.'</div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td class="mail-footer">
              <span>
                Este mensaje ha sido enviado desde el formulario de contacto de <strong>Zambu Psicología</strong>.<br>
                <a href="mailto:javimalo80@gmail.com">Contactar</a> &bull;
                <a href="https://zambupsi.com/politica-privacidad">Política de Privacidad</a>
              </span>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
        ';



        $header = "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html; charset=UTF-8\r\n";
        $header .= "From: ejemplo@correo.com\r\n";
        $header .= "Reply-To: example@example.com\r\n";

    $mail = mail($destinatario, $asunto, $contenido, $header);

        if ($mail) {
            echo "<script>
                alert('El correo se envió correctamente :)');
                window.location.href = 'https://psycholoopgy.com/contacto/?mensaje=ok';
            </script>";
        } else {
            echo "<script>
                alert('El correo no se pudo enviar, intente nuevamente :(');
                window.location.href = 'https://psycholoopgy.com/contacto/?mensaje=error';
            </script>";
        }
        exit;
  }
}
