document.addEventListener('DOMContentLoaded', () => {
  const formulario = document.getElementById('form-contacto');
  const estado = document.getElementById('contacto-estado');
  if (!formulario || !estado) return;
  formulario.addEventListener('submit', evento => {
    evento.preventDefault();
    if (!formulario.reportValidity()) return;
    estado.textContent = '¡Formulario validado! Esta es una demostración: el mensaje no se envió.';
    estado.classList.add('exito');
  });
});
