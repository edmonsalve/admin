/* Lectura de la sección en pantalla */
document.addEventListener('DOMContentLoaded', () => {
    const options = {
      threshold: 0.08
    };
  
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        const id = entry.target.getAttribute('id');
        const menuLink = document.querySelector(`#menu a[href="#${id}"]`);
  
        if (entry.isIntersecting) {
          menuLink.classList.add('active');
        } else {
          menuLink.classList.remove('active');
        }
      });
    }, options);
  
    // Observamos cada sección
    document.querySelectorAll('section').forEach(section => {
      observer.observe(section);
    });
  }
);

/* Cáculo del año actual */
document.addEventListener('DOMContentLoaded', () => {
    const currentYearSpan = document.getElementById('currentYear');
    const currentYear = new Date().getFullYear();
    currentYearSpan.textContent = currentYear;
  }
);

/* Ampliación de imágenes */
