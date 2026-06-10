document.addEventListener('DOMContentLoaded', function () {
  const formulario = document.querySelector('[data-form-consulta]');
  const inputCodigo = document.querySelector('#codigo');

  if (inputCodigo) {
    inputCodigo.focus();
  }

  if (!formulario || !inputCodigo) {
    return;
  }

  formulario.addEventListener('submit', function (evento) {
    const valor = inputCodigo.value.trim();
    if (valor === '' || Number(valor) <= 0) {
      evento.preventDefault();
      inputCodigo.focus();
      inputCodigo.setCustomValidity('Debes ingresar un codigo activo valido.');
      inputCodigo.reportValidity();
      return;
    }

    inputCodigo.setCustomValidity('');
  });
});
