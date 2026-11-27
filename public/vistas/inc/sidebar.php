<div class="offcanvas offcanvas-start bg-dark text-white d-flex flex-column offcanvas-md"
  tabindex="-1"
  id="sidebar"
  data-bs-scroll="true"
  data-bs-backdrop="false"
  style="--bs-offcanvas-width: 250px;">

  <div class="offcanvas-header border-bottom border-secondary d-md-none">
    <div class="d-flex align-items-center">
      <div>
        <h6 class="mb-0 fw-bold"><?= htmlspecialchars($_SESSION["nombre"] ?? 'Usuario') ?></h6>
        <span class="text-muted small"><?= htmlspecialchars($_SESSION["permisos"]["rol"] ?? '') ?></span>
      </div>
    </div>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
  </div>

  <div class="offcanvas-body d-flex flex-column p-3">
    <ul class="nav flex-column gap-1 mb-auto">

      <!-- Inicio: solo con permiso general -->
      <?php if (!empty($_SESSION["permisos"]["general"])) : ?>
        <li class="nav-item">
          <a class="nav-link text-white rounded" href="/home">
            <i class="bi bi-house me-2"></i> Inicio
          </a>
        </li>
      <?php endif; ?>

      <!-- Escáner: universal -->
      <li class="nav-item">
        <a class="nav-link text-white rounded" href="/escaner">
          <i class="bi bi-qr-code-scan me-2"></i> Escáner
        </a>
      </li>

      <!-- Control del Personal: general -->
      <?php if (!empty($_SESSION["permisos"]["general"])) : ?>
        <li class="nav-item">
          <a class="nav-link text-white rounded" href="/personal">
            <i class="bi bi-file-person-fill me-2"></i> Control del Personal
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white rounded" href="/reportes">
            <i class="bi bi-clipboard2-data-fill me-2"></i> Reportes
          </a>
        </li>
      <?php endif; ?>

    </ul>

    <!-- ===== Tarjeta de usuario (siempre visible, rellena el fondo) ===== -->
    <div class="mt-auto pt-3 text-center text-light" style="border-top: 1px solid rgba(255,255,255,0.08);">

    </div>
  </div>
</div>

<style>
  @media (min-width: 768px) {
    .offcanvas-md {
      position: static !important;
      visibility: visible !important;
      transform: none !important;
      border-right: 1px solid rgba(255, 255, 255, 0.1);
      height: 100vh;
    }

    .offcanvas-md .offcanvas-header {
      display: none;
    }
  }

  /* Ícono de ancho fijo + sangría */
  /* Ícono de ancho fijo + sangría + tamaño generoso */
  #sidebar .nav-link {
    display: flex;
    align-items: center;
    padding: 0.85rem 1rem;
    font-size: 1.02rem;
    font-weight: 600;
    border-radius: 10px;
  }

  #sidebar .nav-link i {
    width: 1.5rem;
    text-align: center;
    flex-shrink: 0;
    font-size: 1.15rem;
  }

  #sidebar .nav-link:hover,
  #sidebar .nav-link.active {
    background-color: #198754;
    color: white !important;
  }
</style>

<script>
  // Cerrar offcanvas al hacer clic en un enlace (solo en móvil)
  document.querySelectorAll('#sidebar .nav-link').forEach(link => {
    link.addEventListener('click', () => {
      const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('sidebar'));
      if (offcanvas && window.innerWidth < 768) offcanvas.hide();
    });
  });



  // Activar solo la página actual
  document.addEventListener("DOMContentLoaded", function() {
    const rutaActual = window.location.pathname;
    let mejorMatch = null,
      mejorLongitud = 0;

    document.querySelectorAll('#sidebar .nav-link').forEach(link => {
      const href = link.getAttribute('href');
      if (rutaActual === href) {
        mejorMatch = link;
        mejorLongitud = href.length;
      } else if (rutaActual.startsWith(href + '/') && href.length > mejorLongitud) {
        mejorMatch = link;
        mejorLongitud = href.length;
      }
    });

    if (mejorMatch) {
      document.querySelectorAll('#sidebar .nav-link').forEach(l => l.classList.remove('active'));
      mejorMatch.classList.add('active');
    }
  });
</script>