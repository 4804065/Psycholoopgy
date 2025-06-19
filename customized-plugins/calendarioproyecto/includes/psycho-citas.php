<?php
function psycho_citas(){
	global $wpdb;
	$fecha_hoy = date('Y-m-d');
	$hora_actual = date('H:i');
	$numero_dia_semana = date('N');
	$ndias = intval($numero_dia_semana - 1);
	$fechalunes = date('Y-m-d', strtotime($fecha_hoy . " -$ndias days"));
	$fecha_seleccionada = $fechalunes;
	
	if (isset($_GET['fecha'])){ $fecha_seleccionada = $_GET['fecha']; }
?>
<style>
	#contenedor_tabla_citas{
		width: 100%!important;
		padding-left: 5%;
	}
	#tabla_citas{
		width: 90%;
		table-layout: fixed;:
	}
	.celdahora{
		width: 50px!important;
		border: 1px solid #1976d2;
		background-color: #1976d2;
		color: #ffffff;
		text-align: center;
	}
	.celdacita{
		width: auto;
		min-width:50px;
		border: 1px solid #000000;
		cursor: pointer; cursor: hand;
		color: #ffffff;
		padding-left: 5px;
	}
	.celdacita:hover{
		background-color: #1C7DBD;
	}

	.celdacabeceracita{
		width: auto;
		min-width:50px;
		border: 1px solid #1976d2;
		background-color: #1976d2;
		color: #ffffff;
		cursor: pointer; cursor: hand;
	}
	
.modal-cita {
  display: none; 
  position: fixed; 
  z-index: 9999; 
  left: 0; top: 0;
  width: 100%; height: 100%;
  overflow: auto; 
  background-color: rgba(0,0,0,0.4);
}
.modal-cita-content {
  background-color: #fff;
  margin: 10% auto;
  padding: 20px 30px;
  border: 1px solid #888;
  width: 320px;
  border-radius: 8px;
  position: relative;
}
.close {
  color: #aaa;
  position: absolute;
  right: 16px;
  top: 12px;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
}
.close:hover {
  color: #333;
}
.modal-cita-botones {
  margin-top: 16px;
  display: flex;
  justify-content: space-between;
}
.modal-cita-content label {
  display:block;
  margin-top:10px;
}
.modal-cita-content input {
  width: 100%;
  padding: 6px 8px;
  margin-top: 4px;
  box-sizing: border-box;
}

	
</style>

<div id="modalCita" class="modal-cita">
  <div class="modal-cita-content">
    <span class="close" id="cerrarModal">&times;</span>
    <h2 id="titulocita">Datos Cita</h2>
    <form id="formCita">
    	<input type="hidden" id="amb" value="insert"/>
    	<input type="hidden" id="idcita">
      <label for="fecha">Fecha:</label>
      <input type="text" id="fecha" name="fecha" readonly>

      <label for="hora">Hora:</label>
      <input type="text" id="hora" name="hora" readonly>

      <label for="nombre">Nombre:</label>
      <input type="text" id="nombre" name="nombre" required>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>

      <label for="telefono">Teléfono:</label>
      <input type="tel" id="telefono" name="telefono" required>

      <div class="modal-cita-botones">
        <button type="submit">Aceptar</button>
        <button style="display:none;" type="button" id="eliminarCita">Eliminar</button>
        <button type="button" id="cancelarCita">Cancelar</button>        
      </div>
    </form>
  </div>
</div>

<h2 style="margin-top: 50px; width: 100%; text-align: center;">CALENDARIO DE CITAS</h2>
<div style="margin-top: 50px;" id="calendario_citas">
	<div style="margin-left: 5%;" id="selec_fecha">
		<button style="display: flex; align-items: center; gap: 12px; padding: 8px 16px; font-size: 16px; border-radius: 6px; border: 1px solid #ccc; background: #fff; cursor: pointer;">
		  <span style="font-size: 20px;" onclick="cambiafecha('menos')">&#8592;</span>
  		<span style="background: #1976d2; color: #fff; padding: 4px 18px; border-radius: 4px; font-weight: bold;" onclick="cambiafecha('hoy')">Hoy</span>
  		<span style="font-size: 20px;" onclick="cambiafecha('mas')">&#8594;</span>
		</button>
	</div>
	<div style=cleat:both;"></div>

<div style="margin-top: 50px;" id="contenedor_tabla_citas">	
	<input type="hidden" id="fechalunes" value="<?php echo $fecha_seleccionada; ?>"/>
	<table id="tabla_citas">
		<thead>
			<tr>
				<?php
					echo '<tr><th class="celdahora">Hora</th><th class="celdacabeceracita">Lunes '.date('d-m-Y', strtotime($fecha_seleccionada)).'</th><th class="celdacabeceracita">Martes '.date('d-m-Y', strtotime($fecha_seleccionada . " +1 day")).'</th><th class="celdacabeceracita">Miércoles '.date('d-m-Y', strtotime($fecha_seleccionada . " +2 day")).'</th><th class="celdacabeceracita">Jueves '.date('d-m-Y', strtotime($fecha_seleccionada . " +3 day")).'</th><th class="celdacabeceracita">Viernes '.date('d-m-Y', strtotime($fecha_seleccionada . " +4 day")).'</th></tr>';
				?>
			</tr>
		</thead>
		<tbody>
			<?php

			$citas = $wpdb->get_results("SELECT * FROM wp_citas WHERE fecha_cita >= '$fecha_seleccionada' AND fecha_cita <= DATE_ADD('$fecha_seleccionada', INTERVAL 6 DAY) ORDER BY fecha_cita ASC");
			
			$citas_indice = [];
			foreach ($citas as $cita) {
    			$indice = $cita->fecha_cita . ' ' . substr($cita->hora_cita, 0, 5);
    			$citas_indice[$indice] = [
    				'idcita'   => $cita->id,
        		'nombre'   => $cita->nombre_cliente,
       			'email'    => $cita->email_cliente,
       			'telefono' => $cita->tel_cliente,
    			];
			}
			
			$hora_apertura = 10;
			$hora_cierre = 20;
			$minuto = 0;
			$intervalo = 60;
			$total_minutos = 600;
			$clasecelda = '1'; $casilla = '2'; $reservadatos = '3'; $cliente_reserva = '4';

			$fecha_lunes = $fecha_seleccionada;
			$fecha_martes = date('Y-m-d', strtotime($fecha_seleccionada . " +1 day"));
			$fecha_miercoles = date('Y-m-d', strtotime($fecha_seleccionada . " +2 days"));
			$fecha_jueves = date('Y-m-d', strtotime($fecha_seleccionada . " +3 days"));
			$fecha_viernes = date('Y-m-d', strtotime($fecha_seleccionada . " +4 days"));
			
			
			while ($minuto <= $total_minutos){
				$hora = date("H:i",mktime($hora_apertura,$minuto));
				

				$indice_lunes = $fecha_lunes.' '.$hora; $celda_lunes = ''; $lunes_idcita = ""; $lunes_nombre = ""; $lunes_email = ""; $lunes_tel = "";
				if (isset($citas_indice[$indice_lunes])) {
    				$datos = $citas_indice[$indice_lunes];
						$celda_lunes = 'background-color: #ff0000!important;';
						$lunes_idcita = $datos['idcita'];
						$lunes_nombre = $datos['nombre'];
						$lunes_email = $datos['email'];
						$lunes_tel = $datos['telefono'];						
				}

				$indice_martes = $fecha_martes.' '.$hora; $celda_martes = ''; $martes_idcita = ""; $martes_nombre = ""; $martes_email = ""; $martes_tel = "";
				if (isset($citas_indice[$indice_martes])) {
    				$datos = $citas_indice[$indice_martes];
						$celda_martes = 'background-color: #ff0000!important;';
						$martes_idcita = $datos['idcita'];
						$martes_nombre = $datos['nombre'];
						$martes_email = $datos['email'];
						$martes_tel = $datos['telefono'];						
				}

				$indice_miercoles = $fecha_miercoles.' '.$hora; $celda_miercoles = ''; $miercoles_idcita = ""; $miercoles_nombre = ""; $miercoles_email = ""; $miercoles_tel = "";
				if (isset($citas_indice[$indice_miercoles])) {
    				$datos = $citas_indice[$indice_miercoles];
						$celda_miercoles = 'background-color: #ff0000!important;';
						$miercoles_idcita = $datos['idcita'];
						$miercoles_nombre = $datos['nombre'];
						$miercoles_email = $datos['email'];
						$miercoles_tel = $datos['telefono'];						
				}

				$indice_jueves = $fecha_jueves.' '.$hora; $celda_jueves = ''; $jueves_idcita = ""; $jueves_nombre = ""; $jueves_email = ""; $jueves_tel = "";
				if (isset($citas_indice[$indice_jueves])) {
    				$datos = $citas_indice[$indice_jueves];
						$celda_jueves = 'background-color: #ff0000!important;';
						$jueves_idcita = $datos['idcita'];
						$jueves_nombre = $datos['nombre'];
						$jueves_email = $datos['email'];
						$jueves_tel = $datos['telefono'];						
				}

				$indice_viernes = $fecha_viernes.' '.$hora; $celda_viernes = ''; $viernes_idcita = ""; $viernes_nombre = ""; $viernes_email = ""; $viernes_tel = "";
				if (isset($citas_indice[$indice_viernes])) {
    				$datos = $citas_indice[$indice_viernes];
						$celda_viernes = 'background-color: #ff0000!important;';
						$viernes_idcita = $datos['idcita'];
						$viernes_nombre = $datos['nombre'];
						$viernes_email = $datos['email'];
						$viernes_tel = $datos['telefono'];						
				}
				
				if ($hora != '14:00' && $hora != '15:00' && $hora != '16:00'){
				echo '<tr>';
				echo '<td class="celdahora">'.$hora.'</td>
							<td style="'.$celda_lunes.'" class="celdacita" data-idcita="'.$lunes_idcita.'" data-fecha="'.$fecha_lunes.'" data-hora="'.$hora.'" data-nombre="'.$lunes_nombre.'" data-email="'.$lunes_email.'" data-tel="'.$lunes_tel.'">'.$lunes_nombre.'<br>'.$lunes_tel.'</td>
							<td style="'.$celda_martes.'" class="celdacita" data-idcita="'.$martes_idcita.'" data-fecha="'.$fecha_martes.'" data-hora="'.$hora.'" data-nombre="'.$martes_nombre.'" data-email="'.$martes_email.'" data-tel="'.$martes_tel.'">'.$martes_nombre.'<br>'.$martes_tel.'</td>
              <td style="'.$celda_miercoles.'" class="celdacita" data-idcita="'.$miercoles_idcita.'" data-fecha="'.$fecha_miercoles.'" data-hora="'.$hora.'" data-nombre="'.$miercoles_nombre.'" data-email="'.$miercoles_email.'" data-tel="'.$miercoles_tel.'">'.$miercoles_nombre.'<br>'.$miercoles_tel.'</td>
              <td style="'.$celda_jueves.'" class="celdacita" data-idcita="'.$jueves_idcita.'" data-fecha="'.$fecha_jueves.'" data-hora="'.$hora.'" data-nombre="'.$jueves_nombre.'" data-email="'.$jueves_email.'" data-tel="'.$jueves_tel.'">'.$jueves_nombre.'<br>'.$jueves_tel.'</td>
              <td style="'.$celda_viernes.'" class="celdacita" data-idcita="'.$viernes_idcita.'" data-fecha="'.$fecha_viernes.'" data-hora="'.$hora.'" data-nombre="'.$viernes_nombre.'" data-email="'.$viernes_email.'" data-tel="'.$viernes_tel.'">'.$viernes_nombre.'<br>'.$viernes_tel.'</td>';
				echo '</tr>';	
				}
				$minuto += $intervalo;
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<th style="height: 35px;" class="tabla_reservas_colfija"></th><th style="height: 35px!important; width: 75px!important; min-width: 75px!important; max-width: 75px!important;"></th>

			</tr>
		</tfoot>		
	</table>
</div>	
</div>

<script>
	
	
	
document.querySelectorAll('.celdacita').forEach(function(celda) {
  celda.addEventListener('click', function() {
  	document.getElementById('idcita').value = this.dataset.idcita;
    document.getElementById('fecha').value = this.dataset.fecha;
    document.getElementById('hora').value = this.dataset.hora;
    document.getElementById('nombre').value = this.dataset.nombre;
    document.getElementById('email').value = this.dataset.email;
    document.getElementById('telefono').value = this.dataset.tel;
    document.getElementById('modalCita').style.display = 'block';
    if (document.getElementById('nombre').value == ""){
    	document.getElementById('eliminarCita').style.display="none";
    	document.getElementById('amb').value = "insert";
    }else{
    	document.getElementById('eliminarCita').style.display="block";
    	document.getElementById('amb').value = "update";
    }
  });
});

document.getElementById('cerrarModal').onclick = function() {
  document.getElementById('modalCita').style.display = 'none';
}

document.getElementById('cancelarCita').onclick = function() {
  document.getElementById('modalCita').style.display = 'none';
}

document.getElementById('eliminarCita').onclick = function() {
  if (document.getElementById('amb').value = "update"){
  const formData = new FormData();
  formData.append('idcita', document.getElementById('idcita').value);
  formData.append('amb', 'delete');
  fetch('https://psycholoopgy.com/wp-content/plugins/calendarioproyecto/includes/guardar-cita.php', {
    method: 'POST',
    body: formData
  })
  .then(resp => resp.json())
  .then(data => {
    if(data.success) {
      document.getElementById('modalCita').style.display = 'none';
      alert('¡Cita eliminada!');
      location.reload();
    } else {
      alert('Error: ' + data.error);
    }
  })
  .catch(() => alert('Error en la comunicación con el servidor'));  
}
}


window.onclick = function(event) {
  if (event.target == document.getElementById('modalCita')) {
    document.getElementById('modalCita').style.display = 'none';
  }
}

document.getElementById('formCita').addEventListener('submit', function(e) {
  e.preventDefault();

	amb = jQuery('#amb').val();

  const formData = new FormData();
  formData.append('idcita', document.getElementById('idcita').value);
  formData.append('fecha', document.getElementById('fecha').value);
  formData.append('hora', document.getElementById('hora').value);
  formData.append('nombre', document.getElementById('nombre').value);
  formData.append('email', document.getElementById('email').value);
  formData.append('telefono', document.getElementById('telefono').value);
  formData.append('amb', document.getElementById('amb').value);

  fetch('https://psycholoopgy.com/wp-content/plugins/calendarioproyecto/includes/guardar-cita.php', {
    method: 'POST',
    body: formData
  })
  .then(resp => resp.json())
  .then(data => {
    if(data.success) {
      document.getElementById('modalCita').style.display = 'none';
      alert('¡Cita guardada!');
      location.reload();
    } else {
      alert('Error: ' + data.error);
    }
  })
  .catch(() => alert('Error en la comunicación con el servidor'));
});
	
function cambiafecha (variacion){
	fl = jQuery('#fechalunes').val();
	fechalunes = new Date(fl);
	switch (variacion) {
  	case "mas":
    	fechalunes.setDate(fechalunes.getDate() + 7);
    	break;
  	case "hoy":
    	fechalunes = new Date(obtenerLunesSemanaActual());
    	break;
  	case "menos":
    	fechalunes.setDate(fechalunes.getDate() - 7);
    	break;    
}

const ano = fechalunes.getFullYear();
const mes = String(fechalunes.getMonth() + 1).padStart(2, '0');
const dia = String(fechalunes.getDate()).padStart(2, '0');
const fechaSumada = `${ano}-${mes}-${dia}`;


const params = new URLSearchParams(window.location.search);

params.set('fecha', fechaSumada);

const urlBase = window.location.origin + window.location.pathname;
const nuevaUrl = `${urlBase}?${params.toString()}`;

window.location.href = nuevaUrl;	
}


function obtenerLunesSemanaActual(fecha = new Date()) {
  const f = new Date(fecha);
  const diaSemana = f.getDay();
  const diff = (diaSemana === 0 ? -6 : 1 - diaSemana);
  f.setDate(f.getDate() + diff);
  const ano = f.getFullYear();
  const mes = String(f.getMonth() + 1).padStart(2, '0');
  const dia = String(f.getDate()).padStart(2, '0');
  return `${ano}-${mes}-${dia}`;
}

</script>

<?php
}
?>