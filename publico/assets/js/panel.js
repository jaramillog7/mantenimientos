(() => {
  const body = document.body;
  const desktopKey = 'panel_sidebar_colapsado';
  let cargandoPanel = false;

  const setDesktopSidebar = (collapsed) => {
    body.classList.toggle('sidebar-colapsado', collapsed);
    try {
      localStorage.setItem(desktopKey, collapsed ? '1' : '0');
    } catch (error) {
      void error;
    }
  };

  const openMobileSidebar = () => body.classList.add('sidebar-movil-abierto');
  const closeMobileSidebar = () => body.classList.remove('sidebar-movil-abierto');

  const bindClickOnce = (element, callback) => {
    if (!element || element.dataset.boundClick === '1') {
      return;
    }
    element.addEventListener('click', callback);
    element.dataset.boundClick = '1';
  };

  const inicializarSidebar = () => {
    bindClickOnce(document.querySelector('[data-sidebar-toggle]'), () => {
      setDesktopSidebar(!body.classList.contains('sidebar-colapsado'));
    });
    bindClickOnce(document.querySelector('[data-sidebar-open]'), openMobileSidebar);
    bindClickOnce(document.querySelector('[data-sidebar-close]'), closeMobileSidebar);
    bindClickOnce(document.querySelector('[data-panel-overlay]'), closeMobileSidebar);
  };

  const inicializarOrdenamientoTablas = (root = document) => {
    root.querySelectorAll('[data-sort-table]').forEach((table) => {
      if (table.dataset.sortReady === '1') {
        return;
      }
      table.dataset.sortReady = '1';

      const headers = Array.from(table.querySelectorAll('thead th'));
      const tbody = table.querySelector('tbody');
      if (!tbody) {
        return;
      }

      headers.forEach((header, index) => {
        if (header.closest('.fila-grupo')) {
          return;
        }

        header.style.cursor = 'pointer';
        header.addEventListener('click', () => {
          const rows = Array.from(tbody.querySelectorAll('tr')).filter((row) => !row.classList.contains('fila-grupo'));
          const currentDirection = header.getAttribute('data-sort-direction') === 'asc' ? 'asc' : 'desc';
          const nextDirection = currentDirection === 'asc' ? 'desc' : 'asc';

          headers.forEach((item) => item.removeAttribute('data-sort-direction'));
          header.setAttribute('data-sort-direction', nextDirection);

          rows.sort((rowA, rowB) => {
            const cellA = rowA.children[index];
            const cellB = rowB.children[index];
            const valueA = cellA ? cellA.textContent.trim().toLowerCase() : '';
            const valueB = cellB ? cellB.textContent.trim().toLowerCase() : '';

            if (!Number.isNaN(Number(valueA)) && !Number.isNaN(Number(valueB)) && valueA !== '' && valueB !== '') {
              return nextDirection === 'asc' ? Number(valueA) - Number(valueB) : Number(valueB) - Number(valueA);
            }

            return nextDirection === 'asc'
              ? valueA.localeCompare(valueB, 'es')
              : valueB.localeCompare(valueA, 'es');
          });

          rows.forEach((row) => tbody.appendChild(row));
        });
      });
    });
  };

  const esNavegacionPanel = (url) => {
    const current = new URL(window.location.href);
    if (url.origin !== current.origin) {
      return false;
    }
    if (url.pathname !== current.pathname) {
      return false;
    }
    return url.searchParams.has('apartado');
  };

  const reemplazarSeccionesPanel = (docNuevo) => {
    const sidebarActual = document.querySelector('[data-sidebar-panel]');
    const contenidoActual = document.querySelector('.contenido');
    const sidebarNuevo = docNuevo.querySelector('[data-sidebar-panel]');
    const contenidoNuevo = docNuevo.querySelector('.contenido');

    if (!sidebarActual || !contenidoActual || !sidebarNuevo || !contenidoNuevo) {
      return false;
    }

    sidebarActual.replaceWith(sidebarNuevo);
    contenidoActual.replaceWith(contenidoNuevo);
    return true;
  };

  const navegarPanelSinRecargar = async (url, pushHistorial = true) => {
    if (cargandoPanel) {
      return;
    }

    cargandoPanel = true;
    body.classList.add('panel-cargando');

    try {
      const respuesta = await fetch(url.toString(), {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        },
      });
      if (!respuesta.ok) {
        window.location.href = url.toString();
        return;
      }

      const html = await respuesta.text();
      const parser = new DOMParser();
      const docNuevo = parser.parseFromString(html, 'text/html');
      const seccionesReemplazadas = reemplazarSeccionesPanel(docNuevo);

      if (!seccionesReemplazadas) {
        window.location.href = url.toString();
        return;
      }

      document.title = docNuevo.title || document.title;

      if (pushHistorial) {
        window.history.pushState({}, '', url.toString());
      } else {
        window.history.replaceState({}, '', url.toString());
      }

      inicializarSidebar();
      inicializarOrdenamientoTablas(document);
      closeMobileSidebar();

      if (url.hash) {
        const destino = document.querySelector(url.hash);
        if (destino) {
          destino.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      } else {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    } catch (error) {
      window.location.href = url.toString();
      void error;
    } finally {
      cargandoPanel = false;
      body.classList.remove('panel-cargando');
    }
  };

  try {
    if (localStorage.getItem(desktopKey) === '1' && window.innerWidth > 980) {
      body.classList.add('sidebar-colapsado');
    }
  } catch (error) {
    void error;
  }

  inicializarSidebar();

  window.addEventListener('resize', () => {
    if (window.innerWidth > 980) {
      closeMobileSidebar();
    }
  });

  document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');
    if (!link) {
      return;
    }
    if (link.classList.contains('deshabilitado')) {
      event.preventDefault();
      return;
    }
    if (link.hasAttribute('download') || (link.getAttribute('target') || '').toLowerCase() === '_blank') {
      return;
    }

    const url = new URL(link.href, window.location.href);
    if (!esNavegacionPanel(url)) {
      return;
    }

    event.preventDefault();
    navegarPanelSinRecargar(url);
  });

  document.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) {
      return;
    }

    if ((form.method || 'get').toLowerCase() !== 'get') {
      return;
    }

    const action = form.getAttribute('action') || window.location.pathname;
    const url = new URL(action, window.location.href);
    const formData = new FormData(form);

    formData.forEach((value, key) => {
      url.searchParams.set(key, String(value));
    });

    if (!esNavegacionPanel(url)) {
      return;
    }

    event.preventDefault();
    navegarPanelSinRecargar(url);
  });

  window.addEventListener('popstate', () => {
    const url = new URL(window.location.href);
    if (!esNavegacionPanel(url)) {
      return;
    }
    navegarPanelSinRecargar(url, false);
  });

  inicializarOrdenamientoTablas(document);
})();
