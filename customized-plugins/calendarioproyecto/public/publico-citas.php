<?php

function shcalendario_publico(){

?>
<head>
  <style>
    body {
      font-family: 'Inter', Arial, sans-serif;
      background: #fff;
      margin: 0;
      padding: 0;
    }
    .container {
      display: flex;
      max-width: 750px;
      margin: 40px auto;
      border: 5px solid #e6e6e6;
      border-radius: 12px;
      box-shadow: 0 2px 8px #f3f3f4;
      overflow: hidden;
      min-height: 410px;
      background: #fff;
      border-color: #2563eb;
    }
    .calendar-panel {
      flex: 1;
      min-width: 320px;
      background: #fff;
      padding: 32px 24px 24px 24px;
      border-right: 1px solid #eee;
    }
    .calendar-panel h2 {
      font-size: 18px;
      margin: 0 0 6px 0;
      font-weight: 500;
      letter-spacing: 0.01em;
    }
    .calendar-panel .timezone {
      color: #3b82f6;
      font-size: 13px;
      float: right;
      margin-top: -24px;
      font-weight: 400;
      cursor: pointer;
    }
    .calendar-nav {
      display: flex;
      align-items: center;
      margin: 24px 0 8px 0;
    }
    .calendar-nav button {
      background: none;
      border: none;
      font-size: 18px;
      color: #4b5563;
      cursor: pointer;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      transition: background 0.1s;
    }
    .calendar-nav button:hover {
      background: #f5f7fa;
    }
    .calendar-nav .month-year {
      font-size: 16px;
      font-weight: 500;
      flex: 1;
      text-align: center;
    }
    .calendar-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 2px;
      table-layout: fixed;
    }
    .calendar-table th {
      color: #64748b;
      font-size: 13px;
      font-weight: 500;
      padding-bottom: 8px;
      text-align: center;
      letter-spacing: 0.02em;
    }
    .calendar-table td {
      text-align: center;
      padding: 0;
      height: 38px;
    }
    .calendar-day-btn {
      width: 38px;
      height: 38px;
      border: none;
      background: none;
      font-size: 15px;
      border-radius: 6px;
      cursor: pointer;
      color: #222;
      transition: background 0.1s, color 0.1s, border 0.1s;
      margin: 0 auto;
      outline: none;
    }
    .calendar-day-btn:hover {
      background: #2563eb;
      color: #f3f4f6;
    }
    .calendar-day-btn.selected, .calendar-day-btn:active {
      background: #2563eb;
      color: #fff !important;
      font-weight: 600;
      border: 1.5px solid #2563eb;
    }
    .calendar-day-btn.today {
      border: 1.5px solid #2563eb;
      color: #2563eb;
      font-weight: 600;
    }
    .calendar-day-btn:disabled {
      color: #cbd5e1;
      cursor: default;
      background: none;
      border: none;
      opacity: 0.6;
    }
    .calendar-day-btn.weekend {
      color: #cbd5e1;
      background: #fafbfc;
      cursor: default;
    }
    .calendar-table tr {
      height: 38px;
    }
    .slots-panel {
      flex: 1.2;
      padding: 32px 32px;
      background: #fcfcfc;
      min-width: 330px;
    }
    .slots-panel .date-title {
      font-size: 20px;
      font-weight: 500;
      margin-bottom: 22px;
      color: #222;
      letter-spacing: 0.01em;
    }
    .slots-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 14px 16px;
    }
    .slot-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 15px;
      background: #fff;
      border: 1.5px solid #e5e7eb;
      border-radius: 6px;
      height: 42px;
      color: #1e293b;
      cursor: pointer;
      font-weight: 500;
      transition: border 0.13s, box-shadow 0.13s, color 0.13s, background 0.13s;
      box-shadow: 0 1px 2px #f0f1f3;
    }
    .slot-btn:hover {
      border: 1.5px solid #2563eb;
      background: #2563eb;
      color: #f3f8ff;
    }
    
    .slot-btn.selected, .slot-btn:active {
      border: 1.5px solid #2563eb;
      background: #f5f8fe;
      color: #2563eb;
      font-weight: 600;
      box-shadow: 0 1px 6px #e0e7ffb7;
    }
    .slot-btn:disabled {
      color: #cbd5e1 !important;
      border: 1.5px solid #e5e7eb;
      background: #f8fafc;
      cursor: not-allowed;
      font-weight: 500;
      box-shadow: none;
    }
    .slot-btn.ocupada {
  		opacity: 0.6;
  		background: #f8d7da;
  		border-color: #e11d48;
  		color: #a71d2a;
  		cursor: not-allowed;
		}	    
    .modal-bg {
      display: none;
      position: fixed;
      z-index: 999;
      left: 0; top: 0; width: 100vw; height: 100vh;
      background: rgba(51, 65, 85, 0.3);
      justify-content: center;
      align-items: center;
    }
    .modal-bg.active {
      display: flex;
    }
    .modal {
      background: #fff;
      border-radius: 12px;
      padding: 30px 28px 22px 28px;
      min-width: 330px;
      box-shadow: 0 2px 16px #0001;
      max-width: 95vw;
      max-height: 97vh;
      position: relative;
      animation: modalFadeIn 0.17s;
    }
    @keyframes modalFadeIn {
      from { transform: scale(0.95); opacity: 0; }
      to   { transform: scale(1); opacity: 1; }
    }
    .modal-close {
      position: absolute;
      top: 14px; right: 16px;
      font-size: 22px;
      color: #64748b;
      background: none;
      border: none;
      cursor: pointer;
      border-radius: 50%;
      width: 32px; height: 32px;
      display: flex; align-items: center; justify-content: center;
      transition: background 0.12s;
    }
    .modal-close:hover {
      background: #f3f4f6;
      color: #e11d48;
    }
    .modal h3 {
      font-size: 20px;
      font-weight: 600;
      margin: 0 0 18px 0;
      color: #222;
    }
    .modal label {
      font-size: 15px;
      color: #374151;
      display: block;
      margin-top: 12px;
      margin-bottom: 4px;
    }
    .modal input[type="text"], .modal input[type="email"] {
      width: 100%;
      padding: 9px 10px;
      font-size: 15px;
      border: 1.3px solid #e5e7eb;
      border-radius: 6px;
      background: #f8fafc;
      margin-bottom: 2px;
      outline: none;
      box-sizing: border-box;
      transition: border 0.13s;
    }
    .modal input[type="text"]:focus, .modal input[type="email"]:focus {
      border: 1.3px solid #2563eb;
      background: #f3f8ff;
    }
    .modal input[readonly] {
      background: #f3f4f6;
      color: #666;
      font-weight: 500;
      border: 1.3px solid #e5e7eb;
    }
    .modal .modal-btns {
      display: flex;
      justify-content: flex-end;
      gap: 14px;
      margin-top: 18px;
    }
    .modal-btn {
      border: none;
      border-radius: 6px;
      padding: 9px 18px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.14s, color 0.12s;
      outline: none;
      box-shadow: 0 1px 2px #f1f2f3;
    }
    .modal-btn.ok {
      background: #2563eb;
      color: #fff;
    }
    .modal-btn.ok:hover {
      background: #1d4ed8;
    }
    .modal-btn.cancel {
      background: #e5e7eb;
      color: #222;
    }
    .modal-btn.cancel:hover {
      background: #cbd5e1;
      color: #e11d48;
    }
    @media (max-width: 900px) {
      .container { flex-direction: column; }
      .slots-panel, .calendar-panel { padding: 18px 12px; }
    }
    @media (max-width: 600px) {
      .container { width: 100vw; min-width: 0; box-shadow: none; border: 0; border-radius: 0; }
      .slots-panel, .calendar-panel { padding: 12px 6px; }
      .modal { min-width: 95vw; padding: 18px 5vw; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="calendar-panel">
      <h2>Selecciona Fecha y Hora</h2>
      <div style="clear:both"></div>
      <div class="calendar-nav">
        <button id="prevMonthBtn">&#8249;</button>
        <div class="month-year" id="monthYear"></div>
        <button id="nextMonthBtn">&#8250;</button>
      </div>
      <table class="calendar-table">
        <thead>
          <tr>
            <th>Lun</th>
            <th>Mar</th>
            <th>Mié</th>
            <th>Jue</th>
            <th>Vie</th>
            <th>Sáb</th>
            <th>Dom</th>
          </tr>
        </thead>
        <tbody id="calendarBody">
        </tbody>
      </table>
    </div>
    <div class="slots-panel">
      <div class="date-title" id="selectedDateTitle"></div>
      <div class="slots-grid" id="slotsGrid">
      </div>
    </div>
  </div>

  <div class="modal-bg" id="modalBg">
    <div class="modal">
      <button class="modal-close" id="modalCloseBtn" title="Cerrar">&times;</button>
      <h3>Datos Cita</h3>
      <form id="modal-cita-form" autocomplete="off" onsubmit="return false;">
        <label for="modalFecha">Fecha:</label>
        <input type="text" id="campo-fecha" name="fecha" readonly>
        <label for="modalHora">Hora:</label>
        <input type="text" id="campo-hora" name="hora" readonly>
        <label for="modalNombre">Nombre:</label>
        <input type="text" id="campo-nombre" name="nombre" autocomplete="name" required>
        <label for="modalEmail">Email:</label>
        <input type="email" id="campo-email" name="email" autocomplete="email" required>
        <label for="modalTelefono">Teléfono:</label>
        <input type="text" id="campo-telefono" name="telefono" autocomplete="tel">
        <div class="modal-btns">
          <button class="modal-btn ok" type="submit" id="confirmar-cita-btn">Confirmar Cita</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const lng = document.documentElement.lang;
    if (lng == 'en-GB') {
      document.querySelector('.calendar-panel > h2').textContent = "Select Date and Time";
      document.querySelector('.calendar-table th:nth-child(1)').textContent = "Mon";
      document.querySelector('.calendar-table th:nth-child(2)').textContent = "Tue";
      document.querySelector('.calendar-table th:nth-child(3)').textContent = "Wed";
      document.querySelector('.calendar-table th:nth-child(4)').textContent = "Thu";
      document.querySelector('.calendar-table th:nth-child(5)').textContent = "Fri";
      document.querySelector('.calendar-table th:nth-child(6)').textContent = "Sat";
      document.querySelector('.calendar-table th:nth-child(7)').textContent = "Sun";
      document.querySelector('.modal > h3').textContent = "Appointment Details";
      document.querySelector('label[for="modalFecha"]').textContent = "Date:";
      document.querySelector('label[for="modalHora"]').textContent = "Time:";
      document.querySelector('label[for="modalNombre"]').textContent = "Name:";
      document.querySelector('label[for="modalEmail"]').textContent = "Email:";
      document.querySelector('label[for="modalTelefono"]').textContent = "Phone:";
      document.getElementById('confirmar-cita-btn').textContent = "Confirm Appointment";
    }
  
    const calendarBody = document.getElementById('calendarBody');
    const monthYear = document.getElementById('monthYear');
    const selectedDateTitle = document.getElementById('selectedDateTitle');
    const slotsGrid = document.getElementById('slotsGrid');

    const modalBg = document.getElementById('modalBg');
    const modalCloseBtn = document.getElementById('modalCloseBtn');
    const modalForm = document.getElementById('modal-cita-form');
    const modalFecha = document.getElementById('campo-fecha');
    const modalHora = document.getElementById('campo-hora');
    const modalNombre = document.getElementById('campo-nombre');
    const modalEmail = document.getElementById('campo-email');
    const modalTelefono = document.getElementById('campo-telefono');
    const modalAceptar = document.getElementById('confirmar-cita-btn');

    const slotTimes = [
      "10:00", "11:00",
      "12:00", "13:00",
      "17:00", "18:00",
      "19:00", "20:00"
    ];

    const locale = lng;
    let today = new Date();
    today.setHours(0,0,0,0);

    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();

    let selectedDate = nextWeekday(new Date(today));

    function nextWeekday(date) {
      let day = date.getDay();
      let offset = 0;
      if (day === 6) offset = 2;
      else if (day === 0) offset = 1;
      if (offset > 0) {
        let d = new Date(date);
        d.setDate(d.getDate() + offset);
        return d;
      }
      return date;
    }

    function isSameDay(date1, date2) {
      return date1.getFullYear() === date2.getFullYear() &&
             date1.getMonth() === date2.getMonth() &&
             date1.getDate() === date2.getDate();
    }

    function renderCalendar(month, year) {
      const monthName = new Date(year, month).toLocaleString(locale, {month:'long'});
      monthYear.textContent = `${monthName.charAt(0).toUpperCase() + monthName.slice(1)} ${year}`;

      const firstDay = new Date(year, month, 1);
      let startDayOfWeek = firstDay.getDay() - 1;
      if (startDayOfWeek < 0) startDayOfWeek = 6;

      const daysInMonth = new Date(year, month + 1, 0).getDate();

      let html = '';
      let day = 1;
      for (let i = 0; i < 6; i++) {
        html += '<tr>';
        for (let j = 0; j < 7; j++) {
          if ((i === 0 && j < startDayOfWeek) || day > daysInMonth) {
            html += '<td></td>';
          } else {
            const cellDate = new Date(year, month, day, 0, 0, 0, 0);
            cellDate.setHours(0,0,0,0);
            const dayOfWeek = cellDate.getDay();

            const isWeekend = (dayOfWeek === 6 || dayOfWeek === 0);
            const isToday = isSameDay(cellDate, today);
            const isSelected = isSameDay(cellDate, selectedDate);

            html += `<td>
              <button class="calendar-day-btn${isToday ? ' today' : ''}${isSelected ? ' selected' : ''}${isWeekend ? ' weekend' : ''}"
                data-date="${cellDate.getFullYear()}-${String(cellDate.getMonth()+1).padStart(2,'0')}-${String(cellDate.getDate()).padStart(2,'0')}"
                ${cellDate < today || isWeekend ? 'disabled' : ''}
              >${day}</button>
            </td>`;
            day++;
          }
        }
        html += '</tr>';
        if (day > daysInMonth) break;
      }
      calendarBody.innerHTML = html;

      document.querySelectorAll('.calendar-day-btn').forEach(btn => {
        btn.onclick = function() {
          if (btn.disabled) return;
          const parts = btn.getAttribute('data-date').split('-');
          selectedDate = new Date(Number(parts[0]), Number(parts[1])-1, Number(parts[2]), 0, 0, 0, 0);
          renderCalendar(currentMonth, currentYear);
          renderSlots();
        };
      });
    }

function renderSlots() {
  const options = {weekday:'long', year: 'numeric', month: 'long', day: 'numeric'};
  let formatted = selectedDate.toLocaleDateString(locale, options);
  formatted = formatted.charAt(0).toUpperCase() + formatted.slice(1);
  selectedDateTitle.textContent = formatted;

  let html = '';
  const now = new Date();
  now.setHours(0,0,0,0);
  const isToday = isSameDay(selectedDate, now);
  const nowHour = (new Date()).getHours();
  const nowMinute = (new Date()).getMinutes();

  for (let i = 0; i < slotTimes.length; i += 2) {
    html += slotHtml(slotTimes[i], isToday, nowHour, nowMinute, selectedDate);
    if (slotTimes[i+1]) {
      html += slotHtml(slotTimes[i+1], isToday, nowHour, nowMinute, selectedDate);
    }
  }
  slotsGrid.innerHTML = html;

  let fechaSeleccionada = selectedDate.getFullYear() + '-' +
      String(selectedDate.getMonth() + 1).padStart(2, '0') + '-' +
      String(selectedDate.getDate()).padStart(2, '0');

  obtenerHorasOcupadas(fechaSeleccionada, function(horasOcupadas) {
    document.querySelectorAll('.slot-btn').forEach(function(btn) {
      let btnText = btn.textContent.trim();
      if (horasOcupadas.includes(btnText)) {
        btn.disabled = true;
        btn.classList.add('ocupada');
      }
    });
  });

  document.querySelectorAll('.slot-btn').forEach(btn => {
    btn.onclick = function() {
      if(btn.disabled) return;
      document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
      btn.classList.add('selected');
      showModal(btn.textContent);
    };
  });
}

function obtenerHorasOcupadas(fecha, callback) {
  var url = 'https://psycholoopgy.com/wp-content/plugins/calendarioproyecto/public/horas-ocupadas.php';
  var data = new FormData();
  data.append('fecha', fecha);

  fetch(url, {
    method: 'POST',
    body: data
  })
  .then(response => response.json())
  .then(horasOcupadas => {
  	console.log(horasOcupadas);
    callback(horasOcupadas);
  })
  .catch(error => {
    console.error('Error obteniendo horas ocupadas:', error);
    callback([]);
  });
}

    function slotHtml(time, isToday, currentHour, currentMinute, selectedDate) {
      let [hm, ampm] = time.split(" ");
      let [h, m] = hm.split(":").map(x => parseInt(x,10));
      if(ampm==="pm" && h < 12) h += 12;
      if(ampm==="am" && h===12) h = 0;

      let disabled = false;
      if(isToday) {
        const slotTime = h*60 + m;
        const nowTime = (new Date()).getHours()*60 + (new Date()).getMinutes();
        if(slotTime <= nowTime) {
          disabled = true;
        }
      }
      if(selectedDate < today) disabled = true;

      return `<button class="slot-btn"${disabled?' disabled':''}>${time}</button>`;
    }

    document.getElementById('prevMonthBtn').onclick = function() {
      if (currentMonth === 0) {
        currentMonth = 11;
        currentYear--;
      } else {
        currentMonth--;
      }
      renderCalendar(currentMonth, currentYear);
    };
    document.getElementById('nextMonthBtn').onclick = function() {
      if (currentMonth === 11) {
        currentMonth = 0;
        currentYear++;
      } else {
        currentMonth++;
      }
      renderCalendar(currentMonth, currentYear);
    };

    function showModal(hora) {
      modalFecha.value = `${selectedDate.getFullYear()}-${String(selectedDate.getMonth()+1).padStart(2,'0')}-${String(selectedDate.getDate()).padStart(2,'0')}`;
      modalHora.value = hora;
      modalNombre.value = '';
      modalEmail.value = '';
      modalTelefono.value = '';
      modalBg.classList.add('active');
      modalNombre.focus();
    }

    function closeModal() {
      modalBg.classList.remove('active');
    }
    modalCloseBtn.onclick = closeModal;
    modalBg.onclick = function(e){
      if(e.target === modalBg) closeModal();
    };
    renderCalendar(currentMonth, currentYear);
    renderSlots();

 
 
 document.getElementById('confirmar-cita-btn').onclick = function(e) {
  e.preventDefault();

  const fecha = document.getElementById('campo-fecha').value.trim();
  const hora = document.getElementById('campo-hora').value.trim();
  const nombre = document.getElementById('campo-nombre').value.trim();
  const email = document.getElementById('campo-email').value.trim();
  const telefono = document.getElementById('campo-telefono').value.trim();

  let errores = [];
  if (!nombre) errores.push("El nombre es obligatorio.");
  if (!email || !/^[^@]+@[^@]+\.[a-z]{2,}$/i.test(email)) errores.push("El email es inválido.");
  if (!telefono || telefono.length < 6) errores.push("El teléfono es obligatorio.");
  if (!fecha || !hora) errores.push("Fecha y hora son obligatorias.");

  let errorDiv = document.getElementById('cita-errores');
  if (!errorDiv) {
    errorDiv = document.createElement('div');
    errorDiv.id = 'cita-errores';
    errorDiv.style.color = 'red';
    document.querySelector('.modal form').prepend(errorDiv);
  }
  errorDiv.innerHTML = errores.join("<br>");
  if (errores.length) return;

  const data = new FormData();
  data.append('fecha', fecha);
  data.append('hora', hora);
  data.append('nombre', nombre);
  data.append('email', email);
  data.append('telefono', telefono);

  fetch('https://psycholoopgy.com/wp-content/plugins/calendarioproyecto/public/guardar-cita.php', {
    method: 'POST',
    body: data
  })
  .then(response => response.json())
  .then(res => {
  	console.log(res);
    if (res.success) {
      document.querySelector('.modal').style.display = 'none';
      document.querySelector('.modal-bg').style.display = 'none';
      document.querySelectorAll('.slot-btn').forEach(btn => {
        if (btn.textContent.trim() === hora) {
          btn.disabled = true;
          btn.classList.add('ocupada');
        }
      });
    } else {
      errorDiv.innerHTML = res.error || "Error al guardar la cita.";
    }
  })
  .catch(() => {
    errorDiv.innerHTML = "Error de comunicación con el servidor.";
  });
};

   
  </script>
</body>
</html>

<?php
}

?>